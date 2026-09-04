<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'product_id',
        'order_id',
        'order_item_id',
        'rating',
        'comment',
        'product_variant_title',
        'is_verified_purchase',
        'coins_rewarded',
        'is_visible',
        'admin_reply',
        'admin_replied_at',
    ];

    protected $casts = [
        'user_id'              => 'integer',
        'product_id'           => 'integer',
        'order_id'             => 'integer',
        'order_item_id'        => 'integer',
        'rating'               => 'integer',
        'is_verified_purchase' => 'boolean',
        'coins_rewarded'       => 'integer',
        'is_visible'           => 'boolean',
        'admin_replied_at'     => 'datetime',
        'created_at'           => 'datetime',
        'updated_at'           => 'datetime',
    ];

    // =========================================================================
    // RELATIONSHIPS
    // =========================================================================

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function orderItem(): BelongsTo
    {
        return $this->belongsTo(OrderItem::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ReviewImage::class)->orderBy('sort_order');
    }

    // =========================================================================
    // SCOPES
    // =========================================================================

    public function scopeVisible(Builder $query): Builder
    {
        return $query->where('is_visible', true);
    }

    public function scopeWithRating(Builder $query, ?int $rating): Builder
    {
        if ($rating && $rating >= 1 && $rating <= 5) {
            return $query->where('rating', $rating);
        }
        return $query;
    }

    public function scopeHasImages(Builder $query): Builder
    {
        return $query->has('images');
    }

    // =========================================================================
    // ACCESSORS & HELPERS
    // =========================================================================

    /**
     * Shopee-style masked username (e.g., "n*****n" or "a***z").
     */
    public function getMaskedUserNameAttribute(): string
    {
        $name = trim($this->user?->name ?? 'Khách hàng');
        $length = mb_strlen($name, 'UTF-8');
        if ($length <= 2) {
            return mb_substr($name, 0, 1, 'UTF-8') . '***';
        }

        $first = mb_substr($name, 0, 1, 'UTF-8');
        $last = mb_substr($name, -1, 1, 'UTF-8');
        return $first . '*****' . $last;
    }

    /**
     * User avatar with graceful fallback.
     */
    public function getUserAvatarUrlAttribute(): string
    {
        $name = urlencode($this->user?->name ?? 'User');
        return "https://ui-avatars.com/api/?name={$name}&background=1a1a1a&color=d4af37&size=80&bold=true";
    }

    /**
     * Calculate coin reward based on content and media.
     * - Media + text >= 50 chars: 1.000 Xu
     * - Text >= 50 chars (no media): 300 Xu
     * - Short text / stars only: 100 Xu
     */
    public static function calculateCoinReward(bool $hasMedia, ?string $comment): int
    {
        $commentLength = mb_strlen(trim($comment ?? ''), 'UTF-8');

        if ($hasMedia && $commentLength >= 50) {
            return 1000;
        }

        if ($commentLength >= 50) {
            return 300;
        }

        return 100;
    }
}
