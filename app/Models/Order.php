<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

class Order extends Model
{
    use HasFactory;

    // =========================================================================
    // SHIPPING STATUS CONSTANTS
    // =========================================================================
    public const STATUS_PENDING    = 'pending';
    public const STATUS_PROCESSING = 'processing';
    public const STATUS_SHIPPING   = 'shipping';
    public const STATUS_DELIVERED  = 'delivered';
    public const STATUS_RETURNING  = 'returning';
    public const STATUS_CANCELLED  = 'cancelled';

    // =========================================================================
    // PAYMENT STATUS CONSTANTS
    // =========================================================================
    public const PAYMENT_PENDING   = 'pending';
    public const PAYMENT_PAID      = 'paid';
    public const PAYMENT_FAILED    = 'failed';
    public const PAYMENT_REFUNDING = 'refunding';

    protected $fillable = [
        'user_id',
        'order_code',
        'ghn_order_code',
        'ghn_status',
        'ghn_status_name',
        'recipient_name',
        'phone',
        'shipping_address',
        'to_district_id',
        'to_ward_code',
        'total_weight',
        'expected_delivery_at',
        'payment_method',
        'payment_status',
        'shipping_status',
        'subtotal',
        'shipping_fee',
        'discount_amount',
        'total_amount',
        'notes',
        'cancel_reason',
        'cancelled_at',
        'paid_at',
    ];

    protected $casts = [
        'user_id'               => 'integer',
        'to_district_id'        => 'integer',
        'total_weight'          => 'integer',
        'subtotal'              => 'decimal:2',
        'shipping_fee'          => 'decimal:2',
        'discount_amount'       => 'decimal:2',
        'total_amount'          => 'decimal:2',
        'expected_delivery_at'  => 'datetime',
        'cancelled_at'          => 'datetime',
        'paid_at'               => 'datetime',
        'created_at'            => 'datetime',
        'updated_at'            => 'datetime',
    ];

    // =========================================================================
    // RELATIONSHIPS
    // =========================================================================

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

    // =========================================================================
    // SCOPE QUERIES (for filtering/searching)
    // =========================================================================

    /**
     * Scope: Filter by shipping status.
     */
    public function scopeByShippingStatus(Builder $query, ?string $status): Builder
    {
        if ($status && $status !== 'all') {
            return $query->where('shipping_status', $status);
        }
        return $query;
    }

    /**
     * Scope: Filter by payment status.
     */
    public function scopeByPaymentStatus(Builder $query, ?string $status): Builder
    {
        if ($status && $status !== 'all') {
            return $query->where('payment_status', $status);
        }
        return $query;
    }

    /**
     * Scope: Search by order code, recipient name, or phone.
     */
    public function scopeSearch(Builder $query, ?string $keyword): Builder
    {
        if (!empty($keyword)) {
            $keyword = trim($keyword);
            return $query->where(function (Builder $q) use ($keyword) {
                $q->where('order_code', 'LIKE', "%{$keyword}%")
                  ->orWhere('ghn_order_code', 'LIKE', "%{$keyword}%")
                  ->orWhere('recipient_name', 'LIKE', "%{$keyword}%")
                  ->orWhere('phone', 'LIKE', "%{$keyword}%");
            });
        }
        return $query;
    }

    /**
     * Scope: Filter by date range.
     */
    public function scopeDateRange(Builder $query, ?string $from, ?string $to): Builder
    {
        if (!empty($from)) {
            $query->whereDate('created_at', '>=', $from);
        }
        if (!empty($to)) {
            $query->whereDate('created_at', '<=', $to);
        }
        return $query;
    }

    // =========================================================================
    // HELPER METHODS (Business Logic)
    // =========================================================================

    /**
     * Check if order can be cancelled by the customer.
     * Customer can only cancel when status is 'pending'.
     */
    public function canBeCancelledByCustomer(): bool
    {
        return $this->shipping_status === self::STATUS_PENDING;
    }

    /**
     * Check if order can be cancelled by admin.
     * Admin can cancel when status is 'pending' or 'processing'.
     */
    public function canBeCancelledByAdmin(): bool
    {
        return in_array($this->shipping_status, [
            self::STATUS_PENDING,
            self::STATUS_PROCESSING,
        ]);
    }

    /**
     * Check if order can be confirmed by admin (pending → processing).
     */
    public function canBeConfirmed(): bool
    {
        return $this->shipping_status === self::STATUS_PENDING;
    }

    /**
     * Check if order can be sent to GHN for delivery.
     * Must be 'processing' and NOT already sent.
     */
    public function canBeSentToGhn(): bool
    {
        return $this->shipping_status === self::STATUS_PROCESSING
            && empty($this->ghn_order_code);
    }

    /**
     * Check if order has been sent to GHN.
     */
    public function isGhnOrder(): bool
    {
        return !empty($this->ghn_order_code);
    }

    /**
     * Check if customer can reorder (buy again) from this cancelled/delivered order.
     */
    public function canReorder(): bool
    {
        return in_array($this->shipping_status, [
            self::STATUS_CANCELLED,
            self::STATUS_DELIVERED,
        ]);
    }

    /**
     * Get all available shipping status labels for tabs/filters.
     */
    public static function getShippingStatusOptions(): array
    {
        return [
            'all'        => 'Tất cả',
            'pending'    => 'Chờ xử lý',
            'processing' => 'Đang chuẩn bị',
            'shipping'   => 'Đang giao',
            'delivered'  => 'Đã giao',
            'returning'  => 'Đang hoàn',
            'cancelled'  => 'Đã hủy',
        ];
    }

    // =========================================================================
    // FORMATTED ACCESSORS
    // =========================================================================

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
     * Formatted discount amount string.
     */
    public function getFormattedDiscountAmountAttribute(): string
    {
        return (float) $this->discount_amount > 0
            ? '-' . number_format((float) $this->discount_amount, 0, ',', '.') . ' ₫'
            : '0 ₫';
    }

    /**
     * Formatted expected delivery date.
     */
    public function getFormattedExpectedDeliveryAttribute(): ?string
    {
        if (!$this->expected_delivery_at) {
            return null;
        }

        $days = ['Chủ nhật', 'Thứ 2', 'Thứ 3', 'Thứ 4', 'Thứ 5', 'Thứ 6', 'Thứ 7'];
        $dayName = $days[(int) $this->expected_delivery_at->format('w')];
        return "Dự kiến giao vào {$dayName}, {$this->expected_delivery_at->format('d/m/Y')}";
    }

    /**
     * Get status label and color badge for shipping status.
     */
    public function getShippingStatusBadgeAttribute(): array
    {
        return match ($this->shipping_status) {
            'pending'    => ['label' => 'Chờ xử lý',      'class' => 'bg-warning text-dark',          'icon' => 'clock'],
            'processing' => ['label' => 'Đang chuẩn bị',  'class' => 'bg-info text-dark',             'icon' => 'package'],
            'shipping'   => ['label' => 'Đang giao hàng',  'class' => 'bg-primary text-white',         'icon' => 'truck'],
            'delivered'  => ['label' => 'Đã giao hàng',    'class' => 'bg-success text-white',         'icon' => 'check-circle'],
            'returning'  => ['label' => 'Đang hoàn hàng',  'class' => 'bg-orange-subtle text-orange',  'icon' => 'rotate-ccw'],
            'cancelled'  => ['label' => 'Đã hủy',          'class' => 'bg-danger text-white',          'icon' => 'x-circle'],
            default      => ['label' => $this->shipping_status, 'class' => 'bg-secondary text-white',  'icon' => 'help-circle'],
        };
    }

    /**
     * Get status label and color badge for payment status.
     */
    public function getPaymentStatusBadgeAttribute(): array
    {
        return match ($this->payment_status) {
            'pending'   => ['label' => 'Chưa thanh toán',  'class' => 'bg-warning-subtle text-warning-emphasis border border-warning-subtle'],
            'paid'      => ['label' => 'Đã thanh toán',    'class' => 'bg-success-subtle text-success border border-success-subtle'],
            'failed'    => ['label' => 'Thất bại',         'class' => 'bg-danger-subtle text-danger border border-danger-subtle'],
            'refunding' => ['label' => 'Chờ hoàn tiền',    'class' => 'bg-info-subtle text-info border border-info-subtle'],
            default     => ['label' => $this->payment_status, 'class' => 'bg-secondary-subtle text-secondary'],
        };
    }

    /**
     * Get label for payment method.
     */
    public function getPaymentMethodLabelAttribute(): string
    {
        return match ($this->payment_method) {
            'momo'          => 'Ví Điện Tử MoMo (QR Code / App)',
            'bank_transfer' => 'Chuyển khoản Ngân hàng (VietQR)',
            default         => 'Thanh toán khi nhận hàng (COD)',
        };
    }

    /**
     * Get short payment method label (for tables).
     */
    public function getPaymentMethodShortAttribute(): string
    {
        return match ($this->payment_method) {
            'momo'          => 'MoMo',
            'bank_transfer' => 'Chuyển khoản',
            default         => 'COD',
        };
    }

    /**
     * Get total item count in order.
     */
    public function getTotalItemsCountAttribute(): int
    {
        return $this->items->sum('quantity');
    }
}
