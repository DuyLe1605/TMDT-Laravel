<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CartItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'session_id',
        'product_id',
        'product_variant_id',
        'quantity',
    ];

    protected $casts = [
        'user_id' => 'integer',
        'product_id' => 'integer',
        'product_variant_id' => 'integer',
        'quantity' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the user that owns the cart item.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the product for the cart item.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the variant for the cart item (if selected).
     */
    public function variant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }

    /**
     * Get unit effective price (variant price if set, otherwise product price).
     */
    public function getUnitPriceAttribute(): float
    {
        if ($this->variant) {
            return (float) $this->variant->effective_price;
        }

        if (!$this->product) {
            return 0.0;
        }

        return (float) $this->product->effective_price;
    }

    /**
     * Get current available stock.
     */
    public function getAvailableStockAttribute(): int
    {
        if ($this->variant) {
            return (int) $this->variant->stock;
        }

        return $this->product ? (int) $this->product->stock : 0;
    }

    /**
     * Get subtotal for this item row.
     */
    public function getSubtotalAttribute(): float
    {
        return $this->unit_price * $this->quantity;
    }

    /**
     * Formatted subtotal string.
     */
    public function getFormattedSubtotalAttribute(): string
    {
        return number_format($this->subtotal, 0, ',', '.') . ' ₫';
    }

    /**
     * Formatted unit price string.
     */
    public function getFormattedUnitPriceAttribute(): string
    {
        return number_format($this->unit_price, 0, ',', '.') . ' ₫';
    }
}
