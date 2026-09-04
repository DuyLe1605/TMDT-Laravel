<?php

namespace App\Http\Controllers;

use App\Services\CartService;
use App\Services\Shipping\GhnShippingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ShippingController extends Controller
{
    public function __construct(
        protected GhnShippingService $ghnService,
        protected CartService $cartService
    ) {}

    /**
     * Get list of provinces.
     */
    public function getProvinces(): JsonResponse
    {
        $provinces = $this->ghnService->getProvinces();

        return response()->json([
            'success' => true,
            'data' => $provinces,
        ]);
    }

    /**
     * Get list of districts by province ID.
     */
    public function getDistricts(Request $request): JsonResponse
    {
        $provinceId = (int) $request->query('province_id');

        if (!$provinceId) {
            return response()->json([
                'success' => false,
                'message' => 'Vui lòng cung cấp province_id.',
                'data' => [],
            ], 400);
        }

        $districts = $this->ghnService->getDistricts($provinceId);

        return response()->json([
            'success' => true,
            'data' => $districts,
        ]);
    }

    /**
     * Get list of wards by district ID.
     */
    public function getWards(Request $request): JsonResponse
    {
        $districtId = (int) $request->query('district_id');

        if (!$districtId) {
            return response()->json([
                'success' => false,
                'message' => 'Vui lòng cung cấp district_id.',
                'data' => [],
            ], 400);
        }

        $wards = $this->ghnService->getWards($districtId);

        return response()->json([
            'success' => true,
            'data' => $wards,
        ]);
    }

    /**
     * Calculate shipping fee dynamically.
     */
    public function calculateFee(Request $request): JsonResponse
    {
        $toDistrictId = (int) $request->input('district_id');
        $toWardCode = (string) $request->input('ward_code');
        $provinceName = $request->input('province');
        $districtName = $request->input('district');
        $wardName = $request->input('ward');

        // If district_id is not provided, try resolving from text names
        if (!$toDistrictId && !empty($provinceName) && !empty($districtName)) {
            $resolved = $this->ghnService->resolveLocationCodes($provinceName, $districtName, $wardName);
            if ($resolved) {
                $toDistrictId = $resolved['district_id'];
                $toWardCode = $resolved['ward_code'] ?: $toWardCode;
            }
        }

        if (!$toDistrictId) {
            return response()->json([
                'success' => false,
                'message' => 'Thiếu thông tin Quận/Huyện nhận hàng.',
            ], 422);
        }

        $itemIds = (array) $request->input('items', []);
        $serviceTypeId = (int) $request->input('service_type_id', 2); // 2: Standard, 1: Express

        // Calculate total weight and insurance value from selected items if provided
        $totalWeight = 500; // minimum 500g
        $subtotal = 0;

        if (!empty($itemIds)) {
            $selectedItems = $this->cartService->getSelectedItems($itemIds);
            if ($selectedItems->isNotEmpty()) {
                $summary = $this->cartService->calculateSummary($selectedItems);
                $subtotal = (float) $summary['total_amount'];
                $totalQty = (int) $summary['total_quantity'];
                // Estimate 600 grams per handbag
                $totalWeight = max(500, $totalQty * 600);
            }
        }

        // Express shipping surcharge: service_type_id = 1
        $result = $this->ghnService->calculateFee(
            $toDistrictId,
            $toWardCode,
            $totalWeight,
            $subtotal,
            $serviceTypeId
        );

        $fee = (float) ($result['fee'] ?? 30000);

        // Store promotion rule: If subtotal >= 500k, free standard shipping!
        $isFreeshipEligible = ($subtotal >= 500000 && $serviceTypeId === 2);
        $finalFee = $isFreeshipEligible ? 0.0 : $fee;

        return response()->json([
            'success' => true,
            'original_fee' => $fee,
            'formatted_original_fee' => number_format($fee, 0, ',', '.') . ' ₫',
            'shipping_fee' => $finalFee,
            'formatted_shipping_fee' => $finalFee > 0 ? number_format($finalFee, 0, ',', '.') . ' ₫' : 'Miễn phí',
            'is_freeship' => $isFreeshipEligible,
            'discount_freeship' => $isFreeshipEligible ? $fee : 0,
            'leadtime_text' => $result['leadtime_text'] ?? 'Giao hàng sau 2 - 3 ngày',
            'is_fallback' => $result['is_fallback'] ?? false,
        ]);
    }
}
