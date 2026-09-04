<?php

namespace App\Http\Controllers;

use App\Services\VoucherService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VoucherController extends Controller
{
    public function __construct(
        protected VoucherService $voucherService
    ) {}

    /**
     * Apply voucher to cart/checkout via AJAX.
     */
    public function apply(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'code'           => ['required', 'string', 'max:50'],
            'subtotal'       => ['required', 'numeric', 'min:0'],
            'shipping_fee'   => ['nullable', 'numeric', 'min:0'],
            'payment_method' => ['nullable', 'string', 'in:cod,bank_transfer,momo,all'],
        ], [
            'code.required'     => 'Vui lòng nhập mã giảm giá.',
            'subtotal.required' => 'Thông tin tổng tiền đơn hàng không hợp lệ.',
        ]);

        $subtotal = (float) $validated['subtotal'];
        $shippingFee = (float) ($validated['shipping_fee'] ?? 0);
        $paymentMethod = $validated['payment_method'] ?? 'all';
        $user = Auth::user();

        $validation = $this->voucherService->validateVoucher(
            $validated['code'],
            $subtotal,
            $shippingFee,
            $paymentMethod,
            $user
        );

        if (!$validation['valid']) {
            return response()->json([
                'success' => false,
                'message' => $validation['message'],
            ], 422);
        }

        $voucher = $validation['voucher'];
        $discountAmount = $validation['discount_amount'];
        $finalTotal = max(0, $subtotal + $shippingFee - $discountAmount);

        return response()->json([
            'success' => true,
            'message' => "Áp dụng mã {$voucher->code} thành công! Tiết kiệm " . number_format($discountAmount, 0, ',', '.') . " ₫",
            'voucher' => [
                'id'                 => $voucher->id,
                'code'               => $voucher->code,
                'name'               => $voucher->name,
                'discount_type'      => $voucher->discount_type,
                'discount_value'     => (float) $voucher->discount_value,
                'discount_amount'    => $discountAmount,
                'formatted_discount' => '- ' . number_format($discountAmount, 0, ',', '.') . ' ₫',
            ],
            'subtotal'              => $subtotal,
            'shipping_fee'          => $shippingFee,
            'discount_amount'       => $discountAmount,
            'final_total'           => $finalTotal,
            'formatted_final_total' => number_format($finalTotal, 0, ',', '.') . ' ₫',
        ]);
    }

    /**
     * Get available vouchers classified into eligible and ineligible for the current cart.
     */
    public function available(Request $request): JsonResponse
    {
        $subtotal = (float) ($request->input('subtotal', 0));
        $shippingFee = (float) ($request->input('shipping_fee', 0));
        $paymentMethod = $request->input('payment_method', 'all');
        $user = Auth::user();

        $data = $this->voucherService->getAvailableVouchersForCart(
            $subtotal,
            $shippingFee,
            $paymentMethod,
            $user
        );

        // Format items for clean frontend consumption
        $formatVoucherItem = function ($item) {
            $v = $item['voucher'];
            return [
                'id'                         => $v->id,
                'code'                       => $v->code,
                'name'                       => $v->name,
                'description'                => $v->description,
                'discount_type'              => $v->discount_type,
                'discount_value'             => (float) $v->discount_value,
                'max_discount_amount'        => $v->max_discount_amount ? (float) $v->max_discount_amount : null,
                'min_order_amount'           => (float) $v->min_order_amount,
                'formatted_discount'         => $v->formatted_discount,
                'formatted_min_order'        => $v->formatted_min_order,
                'applicable_payment_methods' => $v->applicable_payment_methods ?? ['all'],
                'expires_at'                 => $v->expires_at ? $v->expires_at->format('d/m/Y') : null,
                'discount_amount'            => $item['discount_amount'] ?? 0,
                'formatted_discount_amount'  => isset($item['discount_amount']) ? number_format($item['discount_amount'], 0, ',', '.') . ' ₫' : null,
                'reason'                     => $item['reason'] ?? null,
            ];
        };

        return response()->json([
            'success'    => true,
            'eligible'   => array_map($formatVoucherItem, $data['eligible']),
            'ineligible' => array_map($formatVoucherItem, $data['ineligible']),
        ]);
    }
}
