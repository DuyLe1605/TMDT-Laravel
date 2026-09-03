<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductVariant extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'sku',
        'variant_title',
        'price',
        'sale_price',
        'stock',
        'image',
        'option1_value',
        'option2_value',
        'option3_value',
        'is_active',
    ];

    protected $casts = [
        'product_id' => 'integer',
        'price' => 'decimal:2',
        'sale_price' => 'decimal:2',
        'stock' => 'integer',
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get parent product.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get attribute values that define this variant.
     */
    public function attributeValues(): BelongsToMany
    {
        return $this->belongsToMany(
            ProductAttributeValue::class,
            'product_variant_attribute_values',
            'product_variant_id',
            'product_attribute_value_id'
        );
    }

    /**
     * Cart items relationship.
     */
    public function cartItems(): HasMany
    {
        return $this->hasMany(CartItem::class, 'product_variant_id');
    }

    /**
     * Order items relationship.
     */
    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class, 'product_variant_id');
    }

    /**
     * Formatted standard price.
     */
    public function getFormattedPriceAttribute(): string
    {
        return number_format((float) $this->price, 0, ',', '.') . ' ₫';
    }

    /**
     * Formatted sale price.
     */
    public function getFormattedSalePriceAttribute(): ?string
    {
        if ($this->sale_price === null) {
            return null;
        }

        return number_format((float) $this->sale_price, 0, ',', '.') . ' ₫';
    }

    /**
     * Check if variant has active discount.
     */
    public function getHasDiscountAttribute(): bool
    {
        return $this->sale_price !== null && (float) $this->sale_price < (float) $this->price;
    }

    /**
     * Effective price.
     */
    public function getEffectivePriceAttribute(): float
    {
        return $this->has_discount ? (float) $this->sale_price : (float) $this->price;
    }

    /**
     * Formatted effective price.
     */
    public function getFormattedEffectivePriceAttribute(): string
    {
        return number_format($this->effective_price, 0, ',', '.') . ' ₫';
    }
}
