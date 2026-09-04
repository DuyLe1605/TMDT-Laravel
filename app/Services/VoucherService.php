<?php

namespace App\Services;

use App\Models\Order;
use App\Models\User;
use App\Models\Voucher;
use App\Models\VoucherUsage;
use Illuminate\Support\Facades\DB;
use Exception;

class VoucherService
{
    /**
     * Validate and calculate discount for a voucher code.
     */
    public function validateVoucher(
        string $code,
        float $subtotal,
        float $shippingFee = 0.0,
        string $paymentMethod = 'cod',
        ?User $user = null
    ): array {
        $code = strtoupper(trim($code));

        if (empty($code)) {
            return [
                'success' => false,
                'valid' => false,
                'message' => 'Vui lòng nhập mã giảm giá.',
                'discount_amount' => 0.0,
                'voucher' => null,
            ];
        }

        $voucher = Voucher::where('code', $code)->first();

        if (!$voucher) {
            return [
                'success' => false,
                'valid' => false,
                'message' => "Mã giảm giá '{$code}' không tồn tại.",
                'discount_amount' => 0.0,
                'voucher' => null,
            ];
        }

        if (!$voucher->is_active) {
            return [
                'success' => false,
                'valid' => false,
                'message' => "Mã giảm giá '{$code}' hiện đang tạm ngừng áp dụng.",
                'discount_amount' => 0.0,
                'voucher' => $voucher,
            ];
        }

        if ($voucher->isUpcoming()) {
            $startDate = $voucher->starts_at->format('d/m/Y H:i');
            return [
                'success' => false,
                'valid' => false,
                'message' => "Mã giảm giá '{$code}' chưa bắt đầu (Có hiệu lực từ {$startDate}).",
                'discount_amount' => 0.0,
                'voucher' => $voucher,
            ];
        }

        if ($voucher->isExpired()) {
            $expiryDate = $voucher->expires_at->format('d/m/Y');
            return [
                'success' => false,
                'valid' => false,
                'message' => "Mã giảm giá '{$code}' đã hết hạn sử dụng vào ngày {$expiryDate}.",
                'discount_amount' => 0.0,
                'voucher' => $voucher,
            ];
        }

        if ($voucher->isUsageLimitReached()) {
            return [
                'success' => false,
                'valid' => false,
                'message' => "Mã giảm giá '{$code}' đã hết lượt sử dụng trên toàn hệ thống.",
                'discount_amount' => 0.0,
                'voucher' => $voucher,
            ];
        }

        // Check user redemption limit
        if ($user && !$voucher->hasRemainingUsesForUser($user->id)) {
            return [
                'success' => false,
                'valid' => false,
                'message' => "Bạn đã sử dụng hết {$voucher->usage_limit_per_user} lượt của mã '{$code}'.",
                'discount_amount' => 0.0,
                'voucher' => $voucher,
            ];
        }

        // Check minimum order amount requirement
        if ($subtotal < (float)$voucher->min_order_amount) {
            $diff = (float)$voucher->min_order_amount - $subtotal;
            $formattedDiff = number_format($diff, 0, ',', '.') . ' ₫';
            $formattedMin = number_format((float)$voucher->min_order_amount, 0, ',', '.') . ' ₫';

            return [
                'success' => false,
                'valid' => false,
                'message' => "Mã '{$code}' chỉ áp dụng cho đơn từ {$formattedMin}. Hãy mua thêm {$formattedDiff} để sử dụng.",
                'discount_amount' => 0.0,
                'voucher' => $voucher,
            ];
        }

        // Check payment method restriction
        if (!empty($paymentMethod) && !$voucher->appliesToPaymentMethod($paymentMethod)) {
            $methodName = match (strtolower($paymentMethod)) {
                'cod' => 'COD (Tiền mặt)',
                'bank_transfer' => 'Chuyển khoản VietQR',
                'momo' => 'Ví MoMo',
                default => $paymentMethod,
            };

            return [
                'success' => false,
                'valid' => false,
                'message' => "Mã '{$code}' không áp dụng cho thanh toán {$methodName}. ({$voucher->payment_method_restriction_label})",
                'discount_amount' => 0.0,
                'voucher' => $voucher,
                'restriction_type' => 'payment_method',
            ];
        }

        // Calculate discount amount
        $discountAmount = $this->calculateDiscount($voucher, $subtotal, $shippingFee);

        return [
            'success' => true,
            'valid' => true,
            'message' => "Áp dụng thành công mã '{$code}'! Giảm " . number_format($discountAmount, 0, ',', '.') . ' ₫.',
            'discount_amount' => $discountAmount,
            'formatted_discount' => number_format($discountAmount, 0, ',', '.') . ' ₫',
            'voucher' => $voucher,
        ];
    }

    /**
     * Calculate discount amount according to voucher rules.
     */
    public function calculateDiscount(Voucher $voucher, float $subtotal, float $shippingFee = 0.0): float
    {
        $discount = 0.0;

        switch ($voucher->discount_type) {
            case Voucher::TYPE_PERCENTAGE:
                $raw = $subtotal * ((float)$voucher->discount_value / 100);
                if ($voucher->max_discount_amount !== null && $voucher->max_discount_amount > 0) {
                    $discount = min($raw, (float)$voucher->max_discount_amount);
                } else {
                    $discount = $raw;
                }
                $discount = min($discount, $subtotal);
                break;

            case Voucher::TYPE_FIXED:
                $discount = min((float)$voucher->discount_value, $subtotal);
                break;

            case Voucher::TYPE_SHIPPING:
                $discount = min((float)$voucher->discount_value, max(0, $shippingFee));
                break;
        }

        return max(0.0, round($discount, 2));
    }

    /**
     * Record voucher usage when an order is created.
     */
    public function recordUsage(Voucher $voucher, Order $order, ?User $user, float $discountAmount): VoucherUsage
    {
        return DB::transaction(function () use ($voucher, $order, $user, $discountAmount) {
            $usage = VoucherUsage::create([
                'voucher_id' => $voucher->id,
                'user_id' => $user?->id,
                'order_id' => $order->id,
                'discount_amount' => $discountAmount,
                'used_at' => now(),
            ]);

            $voucher->increment('used_count');

            return $usage;
        });
    }

    /**
     * Restore voucher usage when an order is cancelled (Shopee style).
     */
    public function restoreUsage(Order $order): bool
    {
        if (!$order->voucher_id) {
            return false;
        }

        return DB::transaction(function () use ($order) {
            $voucher = Voucher::find($order->voucher_id);
            if ($voucher) {
                if ($voucher->used_count > 0) {
                    $voucher->decrement('used_count');
                }
            }

            VoucherUsage::where('order_id', $order->id)->delete();

            return true;
        });
    }

    /**
     * Get list of vouchers categorized for checkout modal.
     * Divides into 'eligible' (can apply now) and 'ineligible' (not yet met conditions).
     */
    public function getAvailableVouchersForCart(
        float $subtotal,
        float $shippingFee = 0.0,
        string $paymentMethod = 'cod',
        ?User $user = null
    ): array {
        $vouchers = Voucher::active()
            ->validNow()
            ->orderBy('min_order_amount', 'asc')
            ->get();

        $eligible = [];
        $ineligible = [];

        foreach ($vouchers as $voucher) {
            // Check user limit
            if ($user && !$voucher->hasRemainingUsesForUser($user->id)) {
                $ineligible[] = [
                    'voucher' => $voucher,
                    'reason' => 'Bạn đã sử dụng hết lượt của mã này',
                    'can_apply' => false,
                ];
                continue;
            }

            // Check min order
            if ($subtotal < (float)$voucher->min_order_amount) {
                $diff = (float)$voucher->min_order_amount - $subtotal;
                $ineligible[] = [
                    'voucher' => $voucher,
                    'reason' => 'Mua thêm ' . number_format($diff, 0, ',', '.') . ' ₫ để dùng',
                    'can_apply' => false,
                ];
                continue;
            }

            // Check payment method
            $paymentOk = $voucher->appliesToPaymentMethod($paymentMethod);
            if (!$paymentOk) {
                $ineligible[] = [
                    'voucher' => $voucher,
                    'reason' => $voucher->payment_method_restriction_label,
                    'can_apply' => false,
                    'reason_type' => 'payment_method',
                ];
                continue;
            }

            // Calculate potential discount
            $potentialDiscount = $this->calculateDiscount($voucher, $subtotal, $shippingFee);

            $eligible[] = [
                'voucher' => $voucher,
                'discount_amount' => $potentialDiscount,
                'formatted_discount_amount' => number_format($potentialDiscount, 0, ',', '.') . ' ₫',
                'can_apply' => true,
            ];
        }

        return [
            'eligible' => $eligible,
            'ineligible' => $ineligible,
            'total_count' => count($eligible) + count($ineligible),
        ];
    }
}
