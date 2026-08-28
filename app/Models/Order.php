<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'order_code',
        'recipient_name',
        'phone',
        'shipping_address',
        'payment_method',
        'payment_status',
        'shipping_status',
        'subtotal',
        'shipping_fee',
        'discount_amount',
        'total_amount',
        'notes',
    ];

    protected $casts = [
        'user_id' => 'integer',
        'subtotal' => 'decimal:2',
        'shipping_fee' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the user that owns the order.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the order items for the order.
     */
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Formatted total amount string.
     */
    public function getFormattedTotalAmountAttribute(): string
    {
        return number_format((float) $this->total_amount, 0, ',', '.') . ' ₫';
    }

    /**
     * Formatted subtotal string.
     */
    public function getFormattedSubtotalAttribute(): string
    {
        return number_format((float) $this->subtotal, 0, ',', '.') . ' ₫';
    }

    /**
     * Formatted shipping fee string.
     */
    public function getFormattedShippingFeeAttribute(): string
    {
        return (float) $this->shipping_fee > 0
            ? number_format((float) $this->shipping_fee, 0, ',', '.') . ' ₫'
            : 'Miễn phí';
    }

    /**
     * Get status label and color badge for shipping status.
     */
    public function getShippingStatusBadgeAttribute(): array
    {
        return match ($this->shipping_status) {
            'pending' => ['label' => 'Chờ xử lý', 'class' => 'bg-warning text-dark'],
            'processing' => ['label' => 'Đang chuẩn bị', 'class' => 'bg-info text-dark'],
            'shipping' => ['label' => 'Đang giao hàng', 'class' => 'bg-primary text-white'],
            'delivered' => ['label' => 'Đã giao hàng', 'class' => 'bg-success text-white'],
            'cancelled' => ['label' => 'Đã hủy', 'class' => 'bg-danger text-white'],
            default => ['label' => $this->shipping_status, 'class' => 'bg-secondary text-white'],
        };
    }

    /**
     * Get status label and color badge for payment status.
     */
    public function getPaymentStatusBadgeAttribute(): array
    {
        return match ($this->payment_status) {
            'pending' => ['label' => 'Chưa thanh toán', 'class' => 'bg-warning-subtle text-warning-emphasis border border-warning-subtle'],
            'paid' => ['label' => 'Đã thanh toán', 'class' => 'bg-success-subtle text-success border border-success-subtle'],
            'failed' => ['label' => 'Thất bại', 'class' => 'bg-danger-subtle text-danger border border-danger-subtle'],
            default => ['label' => $this->payment_status, 'class' => 'bg-secondary-subtle text-secondary'],
        };
    }

    /**
     * Get label for payment method.
     */
    public function getPaymentMethodLabelAttribute(): string
    {
        return match ($this->payment_method) {
            'momo' => 'Ví Điện Tử MoMo (QR Code / App)',
            'bank_transfer' => 'Chuyển khoản Ngân hàng (VietQR)',
            default => 'Thanh toán khi nhận hàng (COD)',
        };
    }
}
