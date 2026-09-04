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
                    $leadtime = $this->calculateLeadtimeInfo($toDistrictId, $toWardCode);

                    return [
                        'success' => true,
                        'fee' => $totalFee,
                        'formatted_fee' => number_format($totalFee, 0, ',', '.') . ' ₫',
                        'service_fee' => (float) ($json['data']['service_fee'] ?? $totalFee),
                        'insurance_fee' => (float) ($json['data']['insurance_fee'] ?? 0),
                        'leadtime_text' => $leadtime['text'],
                        'leadtime_date' => $leadtime['date'],
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
     * Calculate expected delivery leadtime info with both formatted text and date.
     */
    public function calculateLeadtimeInfo(int $toDistrictId, string $toWardCode): array
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
                    $days = ['Chủ nhật', 'Thứ 2', 'Thứ 3', 'Thứ 4', 'Thứ 5', 'Thứ 6', 'Thứ 7'];
                    $dayName = $days[date('w', $timestamp)];
                    $dateStr = date('d/m/Y', $timestamp);
                    return [
                        'text' => "Dự kiến giao vào {$dayName}, {$dateStr}",
                        'date' => date('Y-m-d H:i:s', $timestamp),
                    ];
                }
            }
        } catch (Exception) {
            // Ignore error
        }

        return [
            'text' => 'Giao hàng sau 2 - 3 ngày làm việc',
            'date' => date('Y-m-d H:i:s', strtotime('+3 days')),
        ];
    }

    /**
     * Calculate expected delivery leadtime string.
     */
    public function calculateLeadtime(int $toDistrictId, string $toWardCode): string
    {
        return $this->calculateLeadtimeInfo($toDistrictId, $toWardCode)['text'];
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

    /**
     * Get available shipping services for a route.
     */
    public function getAvailableServices(int $toDistrictId): array
    {
        try {
            if (empty($this->apiToken)) {
                return $this->defaultServices();
            }

            $response = Http::withHeaders([
                'Token' => $this->apiToken,
                'ShopId' => (string) $this->shopId,
                'Content-Type' => 'application/json',
            ])->timeout(4)->post("{$this->apiUrl}/v2/shipping-order/available-services", [
                'shop_id' => $this->shopId,
                'from_district' => $this->originDistrictId,
                'to_district' => $toDistrictId,
            ]);

            if ($response->successful()) {
                $json = $response->json();
                if (($json['code'] ?? 0) === 200 && !empty($json['data'])) {
                    return $json['data'];
                }
            }

            Log::warning('GHN getAvailableServices non-200: ' . $response->body());
            return $this->defaultServices();
        } catch (Exception $e) {
            Log::warning('GHN getAvailableServices error: ' . $e->getMessage());
            return $this->defaultServices();
        }
    }

    /**
     * Create a shipping order on GHN.
     *
     * @param \App\Models\Order $order
     * @return array ['success' => bool, 'ghn_order_code' => string|null, 'expected_delivery_time' => string|null, 'total_fee' => float, 'message' => string]
     */
    public function createShippingOrder(\App\Models\Order $order): array
    {
        $failResult = fn(string $msg) => [
            'success' => false,
            'ghn_order_code' => null,
            'expected_delivery_time' => null,
            'total_fee' => 0,
            'message' => $msg,
        ];

        if (empty($this->apiToken)) {
            return $failResult('GHN API Token chưa được cấu hình.');
        }

        if (!$order->to_district_id || !$order->to_ward_code) {
            return $failResult('Đơn hàng thiếu thông tin District ID hoặc Ward Code.');
        }

        try {
            // Build items payload from order items
            $items = [];
            foreach ($order->items as $item) {
                $weight = 600; // default
                if ($item->product) {
                    $weight = $item->product->weight ?? 600;
                }
                $items[] = [
                    'name' => mb_substr($item->product_name, 0, 127),
                    'code' => $item->product_id ? "P{$item->product_id}" : '',
                    'quantity' => $item->quantity,
                    'price' => (int) $item->price,
                    'weight' => $weight * $item->quantity,
                ];
            }

            // Calculate COD amount: if COD payment, collect total; otherwise 0
            $codAmount = ($order->payment_method === 'cod') ? (int) $order->total_amount : 0;

            $payload = [
                'payment_type_id' => 2, // Người nhận trả phí vận chuyển
                'note' => $order->notes ?? '',
                'required_note' => 'CHOXEMHANGKHONGTHU',
                'client_order_code' => $order->order_code,
                'to_name' => $order->recipient_name,
                'to_phone' => $order->phone,
                'to_address' => $order->shipping_address,
                'to_ward_code' => (string) $order->to_ward_code,
                'to_district_id' => (int) $order->to_district_id,
                'cod_amount' => $codAmount,
                'content' => "Đơn hàng {$order->order_code} - Aurelia Luxury Bags",
                'weight' => max(100, $order->total_weight),
                'length' => 25,
                'width' => 20,
                'height' => 15,
                'service_type_id' => 2, // Standard delivery
                'insurance_value' => min((int) $order->subtotal, 5000000),
                'items' => $items,
            ];

            $response = Http::withHeaders([
                'Token' => $this->apiToken,
                'ShopId' => (string) $this->shopId,
                'Content-Type' => 'application/json',
            ])->timeout(10)->post("{$this->apiUrl}/v2/shipping-order/create", $payload);

            if ($response->successful()) {
                $json = $response->json();
                if (($json['code'] ?? 0) === 200 && !empty($json['data'])) {
                    $data = $json['data'];
                    return [
                        'success' => true,
                        'ghn_order_code' => $data['order_code'] ?? null,
                        'expected_delivery_time' => $data['expected_delivery_time'] ?? null,
                        'total_fee' => (float) ($data['total_fee'] ?? 0),
                        'message' => 'Tạo đơn GHN thành công.',
                    ];
                }
                return $failResult('GHN trả về lỗi: ' . ($json['message'] ?? 'Unknown'));
            }

            $errorBody = $response->json();
            $errorMsg = $errorBody['message'] ?? $response->body();
            Log::error("GHN createShippingOrder failed: {$errorMsg}");
            return $failResult("Lỗi từ GHN: {$errorMsg}");
        } catch (Exception $e) {
            Log::error("GHN createShippingOrder exception: {$e->getMessage()}");
            return $failResult('Lỗi kết nối đến GHN: ' . $e->getMessage());
        }
    }

    /**
     * Get order detail from GHN by GHN order code.
     */
    public function getOrderDetail(string $ghnOrderCode): ?array
    {
        try {
            if (empty($this->apiToken)) {
                return null;
            }

            $response = Http::withHeaders([
                'Token' => $this->apiToken,
                'Content-Type' => 'application/json',
            ])->timeout(5)->post("{$this->apiUrl}/v2/shipping-order/detail", [
                'order_code' => $ghnOrderCode,
            ]);

            if ($response->successful()) {
                $json = $response->json();
                if (($json['code'] ?? 0) === 200 && !empty($json['data'])) {
                    return $json['data'];
                }
            }

            return null;
        } catch (Exception $e) {
            Log::warning("GHN getOrderDetail error: {$e->getMessage()}");
            return null;
        }
    }

    /**
     * Cancel a shipping order on GHN.
     */
    public function cancelShippingOrder(string $ghnOrderCode): array
    {
        try {
            if (empty($this->apiToken)) {
                return ['success' => false, 'message' => 'GHN API Token chưa cấu hình.'];
            }

            $response = Http::withHeaders([
                'Token' => $this->apiToken,
                'ShopId' => (string) $this->shopId,
                'Content-Type' => 'application/json',
            ])->timeout(5)->post("{$this->apiUrl}/v2/shipping-order/cancel", [
                'order_codes' => [$ghnOrderCode],
            ]);

            if ($response->successful()) {
                $json = $response->json();
                if (($json['code'] ?? 0) === 200) {
                    return ['success' => true, 'message' => 'Đã hủy đơn GHN thành công.'];
                }
                return ['success' => false, 'message' => 'GHN: ' . ($json['message'] ?? 'Lỗi không xác định')];
            }

            return ['success' => false, 'message' => 'GHN trả về HTTP ' . $response->status()];
        } catch (Exception $e) {
            Log::error("GHN cancelShippingOrder error: {$e->getMessage()}");
            return ['success' => false, 'message' => 'Lỗi kết nối GHN: ' . $e->getMessage()];
        }
    }

    /**
     * Get print token for A5 shipping label.
     */
    public function getPrintToken(string $ghnOrderCode): ?string
    {
        try {
            if (empty($this->apiToken)) {
                return null;
            }

            $response = Http::withHeaders([
                'Token' => $this->apiToken,
                'Content-Type' => 'application/json',
            ])->timeout(5)->get("{$this->apiUrl}/v2/a5/gen-token", [
                'order_codes' => $ghnOrderCode,
            ]);

            if ($response->successful()) {
                $json = $response->json();
                if (($json['code'] ?? 0) === 200 && !empty($json['data']['token'])) {
                    return $json['data']['token'];
                }
            }

            return null;
        } catch (Exception $e) {
            Log::warning("GHN getPrintToken error: {$e->getMessage()}");
            return null;
        }
    }

    /**
     * Get the print URL for a GHN shipping label.
     */
    public function getPrintUrl(string $ghnOrderCode, string $size = 'A5'): ?string
    {
        $token = $this->getPrintToken($ghnOrderCode);
        if (!$token) {
            return null;
        }

        $baseUrl = str_replace('/shiip/public-api', '', $this->apiUrl);

        return match ($size) {
            '80x80' => "{$baseUrl}/a5/public-api/printA5?token={$token}",
            '52x70' => "{$baseUrl}/a5/public-api/printA5?token={$token}",
            default => "{$baseUrl}/a5/public-api/printA5?token={$token}",
        };
    }

    /**
     * Map GHN webhook status to internal shipping_status.
     */
    public static function mapGhnStatusToInternal(string $ghnStatus): string
    {
        return match ($ghnStatus) {
            'ready_to_pick'      => 'pending',
            'picking'            => 'processing',
            'cancel'             => 'cancelled',
            'money_collect_picking' => 'processing',
            'picked'             => 'processing',
            'storing'            => 'processing',
            'transporting'       => 'shipping',
            'sorting'            => 'shipping',
            'delivering'         => 'shipping',
            'money_collect_delivering' => 'shipping',
            'delivered'          => 'delivered',
            'delivery_fail'      => 'shipping',
            'waiting_to_return'  => 'returning',
            'return'             => 'returning',
            'return_transporting' => 'returning',
            'return_sorting'     => 'returning',
            'returning'          => 'returning',
            'return_fail'        => 'returning',
            'returned'           => 'cancelled',
            'exception'          => 'shipping',
            'damage'             => 'shipping',
            'lost'               => 'cancelled',
            default              => 'pending',
        };
    }

    /**
     * Map GHN status to Vietnamese name.
     */
    public static function mapGhnStatusToName(string $ghnStatus): string
    {
        return match ($ghnStatus) {
            'ready_to_pick'      => 'Đơn hàng mới, chờ lấy hàng',
            'picking'            => 'Shipper đang đi lấy hàng',
            'cancel'             => 'Đơn hàng đã bị hủy',
            'money_collect_picking' => 'Đang thu tiền người gửi',
            'picked'             => 'Shipper đã lấy hàng',
            'storing'            => 'Hàng đang ở bưu cục trung chuyển',
            'transporting'       => 'Đang vận chuyển đến bưu cục đích',
            'sorting'            => 'Đang phân loại tại bưu cục đích',
            'delivering'         => 'Shipper đang giao hàng',
            'money_collect_delivering' => 'Đang thu tiền người nhận',
            'delivered'          => 'Giao hàng thành công',
            'delivery_fail'      => 'Giao hàng thất bại',
            'waiting_to_return'  => 'Đang chờ xác nhận chuyển hoàn',
            'return'             => 'Đang chuyển hoàn',
            'return_transporting' => 'Đang vận chuyển hoàn',
            'return_sorting'     => 'Đang phân loại hoàn',
            'returning'          => 'Shipper đang trả hàng về kho',
            'return_fail'        => 'Chuyển hoàn thất bại',
            'returned'           => 'Đã hoàn hàng về kho',
            'exception'          => 'Đơn hàng ngoại lệ',
            'damage'             => 'Hàng hóa bị hư hại',
            'lost'               => 'Hàng hóa bị thất lạc',
            default              => $ghnStatus,
        };
    }

    /**
     * Default services fallback.
     */
    protected function defaultServices(): array
    {
        return [
            ['service_id' => 53320, 'short_name' => 'Giao hàng tiêu chuẩn', 'service_type_id' => 2],
        ];
    }

    // =========================================================================
    // STATIC FALLBACK DATA
    // =========================================================================

    /**
     * Fallback static provinces for when GHN API is unavailable.
     */
    protected function getStaticProvinces(): array
    {
        $staticPath = public_path('js/vn-locations.js');
        if (!file_exists($staticPath)) {
            return [];
        }

        // Parse basic province list from static file as fallback
        return [
            ['ProvinceID' => 201, 'ProvinceName' => 'Hà Nội'],
            ['ProvinceID' => 202, 'ProvinceName' => 'Hồ Chí Minh'],
            ['ProvinceID' => 203, 'ProvinceName' => 'Đà Nẵng'],
        ];
    }
}
