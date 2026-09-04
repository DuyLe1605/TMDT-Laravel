<?php

namespace App\Services\Shipping;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class GhnShippingService
{
    protected string $apiUrl;
    protected ?string $apiToken;
    protected int $shopId;
    protected int $originProvinceId;
    protected int $originDistrictId;
    protected string $originWardCode;

    public function __construct()
    {
        $this->apiUrl = config('services.ghn.api_url', 'https://dev-online-gateway.ghn.vn/shiip/public-api');
        $this->apiToken = config('services.ghn.api_token');
        $this->shopId = (int) config('services.ghn.shop_id', 216720);
        $this->originProvinceId = (int) config('services.ghn.origin_province_id', 201);
        $this->originDistrictId = (int) config('services.ghn.origin_district_id', 1492);
        $this->originWardCode = (string) config('services.ghn.origin_ward_code', '1A0501');
    }

    /**
     * Get list of provinces from GHN with Cache (30 days) and fallback.
     */
    public function getProvinces(): array
    {
        return Cache::remember('ghn_provinces_list', 86400 * 30, function () {
            try {
                if (empty($this->apiToken)) {
                    return $this->getStaticProvinces();
                }

                $response = Http::withHeaders([
                    'Token' => $this->apiToken,
                    'Content-Type' => 'application/json',
                ])->timeout(4)->get("{$this->apiUrl}/master-data/province");

                if ($response->successful()) {
                    $json = $response->json();
                    if (($json['code'] ?? 0) === 200 && !empty($json['data'])) {
                        // Sort provinces alphabetically
                        $data = $json['data'];
                        usort($data, fn($a, $b) => strcmp($a['ProvinceName'], $b['ProvinceName']));
                        return $data;
                    }
                }

                Log::warning('GHN getProvinces non-200 response: ' . $response->body());
                return $this->getStaticProvinces();
            } catch (Exception $e) {
                Log::warning('GHN getProvinces error: ' . $e->getMessage());
                return $this->getStaticProvinces();
            }
        });
    }

    /**
     * Get list of districts by province ID from GHN with Cache (30 days).
     */
    public function getDistricts(int $provinceId): array
    {
        return Cache::remember("ghn_districts_{$provinceId}", 86400 * 30, function () use ($provinceId) {
            try {
                if (empty($this->apiToken)) {
                    return [];
                }

                $response = Http::withHeaders([
                    'Token' => $this->apiToken,
                    'Content-Type' => 'application/json',
                ])->timeout(4)->post("{$this->apiUrl}/master-data/district", [
                    'province_id' => $provinceId,
                ]);

                if ($response->successful()) {
                    $json = $response->json();
                    if (($json['code'] ?? 0) === 200 && !empty($json['data'])) {
                        $data = $json['data'];
                        usort($data, fn($a, $b) => strcmp($a['DistrictName'], $b['DistrictName']));
                        return $data;
                    }
                }

                Log::warning("GHN getDistricts for province {$provinceId} non-200: " . $response->body());
                return [];
            } catch (Exception $e) {
                Log::warning("GHN getDistricts error for province {$provinceId}: " . $e->getMessage());
                return [];
            }
        });
    }

    /**
     * Get list of wards by district ID from GHN with Cache (30 days).
     */
    public function getWards(int $districtId): array
    {
        return Cache::remember("ghn_wards_{$districtId}", 86400 * 30, function () use ($districtId) {
            try {
                if (empty($this->apiToken)) {
                    return [];
                }

                $response = Http::withHeaders([
                    'Token' => $this->apiToken,
                    'Content-Type' => 'application/json',
                ])->timeout(4)->post("{$this->apiUrl}/master-data/ward", [
                    'district_id' => $districtId,
                ]);

                if ($response->successful()) {
                    $json = $response->json();
                    if (($json['code'] ?? 0) === 200 && !empty($json['data'])) {
                        $data = $json['data'];
                        usort($data, fn($a, $b) => strcmp($a['WardName'], $b['WardName']));
                        return $data;
                    }
                }

                Log::warning("GHN getWards for district {$districtId} non-200: " . $response->body());
                return [];
            } catch (Exception $e) {
                Log::warning("GHN getWards error for district {$districtId}: " . $e->getMessage());
                return [];
            }
        });
    }

    /**
     * Calculate Shipping Fee from Shop Origin to Destination.
     *
     * @param int $toDistrictId
     * @param string $toWardCode
     * @param int $weightInGrams (default 800g per bag)
     * @param float $insuranceValue
     * @param int $serviceTypeId (2: Standard delivery, 1: Express)
     * @return array
     */
    public function calculateFee(
        int $toDistrictId,
        string $toWardCode,
        int $weightInGrams = 800,
        float $insuranceValue = 0,
        int $serviceTypeId = 2
    ): array {
        // Fallback default response
        $fallback = [
            'success' => true,
            'fee' => 30000,
            'formatted_fee' => '30.000 ₫',
            'leadtime_text' => 'Giao trong 2 - 3 ngày',
            'is_fallback' => true,
        ];

        try {
            if (empty($this->apiToken)) {
                return $fallback;
            }

            // Cap insurance value to max allowed 5,000,000
            $insurance = min((int) $insuranceValue, 5000000);
            $weight = max(100, $weightInGrams);

            $payload = [
                'from_district_id' => $this->originDistrictId,
                'from_ward_code' => $this->originWardCode,
                'to_district_id' => $toDistrictId,
                'to_ward_code' => (string) $toWardCode,
                'service_type_id' => $serviceTypeId,
                'weight' => $weight,
                'insurance_value' => $insurance,
                'length' => 25,
                'width' => 20,
                'height' => 15,
            ];

            $response = Http::withHeaders([
                'Token' => $this->apiToken,
                'ShopId' => (string) $this->shopId,
                'Content-Type' => 'application/json',
            ])->timeout(5)->post("{$this->apiUrl}/v2/shipping-order/fee", $payload);

            if ($response->successful()) {
                $json = $response->json();
                if (($json['code'] ?? 0) === 200 && isset($json['data']['total'])) {
                    $totalFee = (float) $json['data']['total'];

                    // Optional: Get expected delivery leadtime
                    $leadtimeText = $this->calculateLeadtime($toDistrictId, $toWardCode);

                    return [
                        'success' => true,
                        'fee' => $totalFee,
                        'formatted_fee' => number_format($totalFee, 0, ',', '.') . ' ₫',
                        'service_fee' => (float) ($json['data']['service_fee'] ?? $totalFee),
                        'insurance_fee' => (float) ($json['data']['insurance_fee'] ?? 0),
                        'leadtime_text' => $leadtimeText,
                        'is_fallback' => false,
                    ];
                }
            }

            Log::warning('GHN calculateFee non-200: ' . $response->body());
            return $fallback;
        } catch (Exception $e) {
            Log::warning('GHN calculateFee error: ' . $e->getMessage());
            return $fallback;
        }
    }

    /**
     * Calculate expected delivery leadtime.
     */
    public function calculateLeadtime(int $toDistrictId, string $toWardCode): string
    {
        try {
            $response = Http::withHeaders([
                'Token' => $this->apiToken,
                'ShopId' => (string) $this->shopId,
                'Content-Type' => 'application/json',
            ])->timeout(3)->post("{$this->apiUrl}/v2/shipping-order/leadtime", [
                'from_district_id' => $this->originDistrictId,
                'from_ward_code' => $this->originWardCode,
                'to_district_id' => $toDistrictId,
                'to_ward_code' => (string) $toWardCode,
                'service_id' => 53320,
            ]);

            if ($response->successful()) {
                $json = $response->json();
                if (($json['code'] ?? 0) === 200 && isset($json['data']['leadtime'])) {
                    $timestamp = $json['data']['leadtime'];
                    // Format Vietnamese date: Thứ X, ngày dd/mm
                    $days = ['Chủ nhật', 'Thứ 2', 'Thứ 3', 'Thứ 4', 'Thứ 5', 'Thứ 6', 'Thứ 7'];
                    $dayName = $days[date('w', $timestamp)];
                    $dateStr = date('d/m/Y', $timestamp);
                    return "Dự kiến giao vào {$dayName}, {$dateStr}";
                }
            }
        } catch (Exception) {
            // Ignore error and return default
        }

        return 'Giao hàng sau 2 - 3 ngày làm việc';
    }

    /**
     * Resolve GHN ProvinceID, DistrictID, and WardCode by textual names.
     */
    public function resolveLocationCodes(?string $provinceName, ?string $districtName, ?string $wardName = null): ?array
    {
        if (empty($provinceName) || empty($districtName)) {
            return null;
        }

        $clean = fn($s) => mb_strtolower(trim(preg_replace('/\s+/', ' ', str_ireplace(
            ['thành phố', 'tỉnh', 'quận', 'huyện', 'thị xã', 'phường', 'xã', 'thị trấn', 'tp.', 'tp '],
            '',
            $s ?? ''
        ))));

        $cleanProv = $clean($provinceName);
        $provinces = $this->getProvinces();
        $matchedProvinceId = null;

        foreach ($provinces as $p) {
            $pName = $clean($p['ProvinceName']);
            if ($pName === $cleanProv || str_contains($pName, $cleanProv) || str_contains($cleanProv, $pName)) {
                $matchedProvinceId = $p['ProvinceID'];
                break;
            }
        }

        if (!$matchedProvinceId) {
            return null;
        }

        $cleanDist = $clean($districtName);
        $districts = $this->getDistricts($matchedProvinceId);
        $matchedDistrictId = null;

        foreach ($districts as $d) {
            $dName = $clean($d['DistrictName']);
            if ($dName === $cleanDist || str_contains($dName, $cleanDist) || str_contains($cleanDist, $dName)) {
                $matchedDistrictId = $d['DistrictID'];
                break;
            }
        }

        if (!$matchedDistrictId) {
            return null;
        }

        $matchedWardCode = null;
        if (!empty($wardName)) {
            $cleanWard = $clean($wardName);
            $wards = $this->getWards($matchedDistrictId);
            foreach ($wards as $w) {
                $wName = $clean($w['WardName']);
                if ($wName === $cleanWard || str_contains($wName, $cleanWard) || str_contains($cleanWard, $wName)) {
                    $matchedWardCode = (string) $w['WardCode'];
                    break;
                }
            }
        }

        return [
            'province_id' => $matchedProvinceId,
            'district_id' => $matchedDistrictId,
            'ward_code' => $matchedWardCode,
        ];
    }
}

