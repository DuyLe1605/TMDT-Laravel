<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Voucher extends Model
{
    use HasFactory;

    public const TYPE_PERCENTAGE = 'percentage';
    public const TYPE_FIXED = 'fixed_amount';
    public const TYPE_FIXED_AMOUNT = 'fixed_amount';
    public const TYPE_SHIPPING = 'shipping_discount';
    public const TYPE_SHIPPING_DISCOUNT = 'shipping_discount';

    protected $fillable = [
        'code',
        'name',
        'description',
        'discount_type',
        'discount_value',
        'max_discount_amount',
        'min_order_amount',
        'applicable_payment_methods',
        'usage_limit',
        'used_count',
        'usage_limit_per_user',
        'starts_at',
        'expires_at',
        'is_active',
    ];

    protected $casts = [
        'discount_value' => 'decimal:2',
        'max_discount_amount' => 'decimal:2',
        'min_order_amount' => 'decimal:2',
        'usage_limit' => 'integer',
        'used_count' => 'integer',
        'usage_limit_per_user' => 'integer',
        'applicable_payment_methods' => 'array',
        'starts_at' => 'datetime',
        'expires_at' => 'datetime',
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relationship: usages log.
     */
    public function usages(): HasMany
    {
        return $this->hasMany(VoucherUsage::class);
    }

    /**
     * Relationship: orders that used this voucher.
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    // =========================================================================
    // STATUS & TIME HELPERS
    // =========================================================================

    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    public function isUpcoming(): bool
    {
        return $this->starts_at && $this->starts_at->isFuture();
    }

    public function isUsageLimitReached(): bool
    {
        return $this->usage_limit !== null && $this->used_count >= $this->usage_limit;
    }

    public function isValidNow(): bool
    {
        if (!$this->is_active) {
            return false;
        }

        if ($this->isUpcoming() || $this->isExpired()) {
            return false;
        }

        if ($this->isUsageLimitReached()) {
            return false;
        }

        return true;
    }

    /**
     * Check if voucher applies to given payment method.
     */
    public function appliesToPaymentMethod(string $method): bool
    {
        $raw = $this->applicable_payment_methods;
        if (is_string($raw)) {
            $allowed = array_map('trim', explode(',', strtolower($raw)));
        } elseif (is_array($raw)) {
            $allowed = array_map(fn($item) => trim(strtolower((string)$item)), $raw);
        } else {
            $allowed = ['all'];
        }

        if (in_array('all', $allowed)) {
            return true;
        }

        return in_array(strtolower($method), $allowed);
    }

    /**
     * Check if a specific user has remaining uses for this voucher.
     */
    public function hasRemainingUsesForUser(?int $userId): bool
    {
        if (!$userId) {
            return true;
        }

        $usedTimes = $this->usages()->where('user_id', $userId)->count();

        return $usedTimes < $this->usage_limit_per_user;
    }

    // =========================================================================
    // ACCESSORS
    // =========================================================================

    /**
     * Formatted discount description.
     * e.g., "Giảm 20% (Tối đa 15.000 ₫)" or "Giảm 50.000 ₫"
     */
    public function getFormattedDiscountAttribute(): string
    {
        return match ($this->discount_type) {
            self::TYPE_PERCENTAGE => (int)$this->discount_value . '%' .
                ($this->max_discount_amount ? ' (Tối đa ' . number_format((float)$this->max_discount_amount, 0, ',', '.') . ' ₫)' : ''),
            self::TYPE_FIXED => number_format((float)$this->discount_value, 0, ',', '.') . ' ₫',
            self::TYPE_SHIPPING => 'Freeship ' . number_format((float)$this->discount_value, 0, ',', '.') . ' ₫',
            default => number_format((float)$this->discount_value, 0, ',', '.') . ' ₫',
        };
    }

    /**
     * Formatted min order requirement.
     */
    public function getFormattedMinOrderAttribute(): string
    {
        if ($this->min_order_amount <= 0) {
            return 'Không giới hạn';
        }

        return 'Đơn từ ' . number_format((float)$this->min_order_amount, 0, ',', '.') . ' ₫';
    }

    /**
     * Status badge for admin & storefront.
     */
    public function getStatusBadgeAttribute(): array
    {
        if (!$this->is_active) {
            return ['label' => 'Tạm dừng', 'class' => 'bg-secondary-subtle text-secondary border border-secondary-subtle'];
        }

        if ($this->isExpired()) {
            return ['label' => 'Hết hạn', 'class' => 'bg-danger-subtle text-danger border border-danger-subtle'];
        }

        if ($this->isUpcoming()) {
            return ['label' => 'Sắp diễn ra', 'class' => 'bg-info-subtle text-info border border-info-subtle'];
        }

        if ($this->isUsageLimitReached()) {
            return ['label' => 'Hết lượt', 'class' => 'bg-warning-subtle text-warning-emphasis border border-warning-subtle'];
        }

        return ['label' => 'Đang hoạt động', 'class' => 'bg-success-subtle text-success border border-success-subtle'];
    }

    /**
     * Human-readable payment method label.
     */
    public function getPaymentMethodRestrictionLabelAttribute(): string
    {
        $raw = $this->applicable_payment_methods;
        if (is_string($raw)) {
            $allowed = array_map('trim', explode(',', strtolower($raw)));
        } elseif (is_array($raw)) {
            $allowed = array_map(fn($item) => trim(strtolower((string)$item)), $raw);
        } else {
            $allowed = ['all'];
        }

        if (in_array('all', $allowed)) {
            return 'Mọi phương thức thanh toán';
        }

        $labels = [];
        foreach ($allowed as $m) {
            $labels[] = match ($m) {
                'cod' => 'COD (Tiền mặt)',
                'bank_transfer' => 'Chuyển khoản VietQR',
                'momo' => 'Ví MoMo',
                default => $m,
            };
        }

        return 'Chỉ áp dụng: ' . implode(', ', $labels);
    }

    // =========================================================================
    // SCOPES
    // =========================================================================

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeValidNow(Builder $query): Builder
    {
        $now = Carbon::now();

        return $query->where('is_active', true)
            ->where(function ($q) use ($now) {
                $q->whereNull('starts_at')->orWhere('starts_at', '<=', $now);
            })
            ->where(function ($q) use ($now) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>=', $now);
            })
            ->where(function ($q) {
                $q->whereNull('usage_limit')
                  ->orWhereRaw('used_count < usage_limit');
            });
    }

    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        if (empty($term)) {
            return $query;
        }

        return $query->where(function ($q) use ($term) {
            $q->where('code', 'like', "%{$term}%")
              ->orWhere('name', 'like', "%{$term}%")
              ->orWhere('description', 'like', "%{$term}%");
        });
    }
}
