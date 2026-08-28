<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'price',
        'sale_price',
        'stock',
        'material',
        'dimensions',
        'color',
        'description',
        'image',
        'is_featured',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'category_id' => 'integer',
        'price' => 'decimal:2',
        'sale_price' => 'decimal:2',
        'stock' => 'integer',
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Boot the model to auto-generate slug if not provided.
     */
    protected static function boot(): void
    {
        parent::boot();

        static::saving(function (Product $product) {
            if (empty($product->slug) || $product->isDirty('name')) {
                $product->slug = Str::slug($product->name);
            }
        });
    }

    /**
     * Get the category that owns the product.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Formatted price accessor.
     */
    public function getFormattedPriceAttribute(): string
    {
        return number_format((float) $this->price, 0, ',', '.') . ' ₫';
    }

    /**
     * Formatted sale price accessor.
     */
    public function getFormattedSalePriceAttribute(): ?string
    {
        if ($this->sale_price === null) {
            return null;
        }

        return number_format((float) $this->sale_price, 0, ',', '.') . ' ₫';
    }

    /**
     * Check if product has active discount.
     */
    public function getHasDiscountAttribute(): bool
    {
        return $this->sale_price !== null && (float) $this->sale_price < (float) $this->price;
    }

    /**
     * Effective price accessor (sale price if discount active, else standard price).
     */
    public function getEffectivePriceAttribute(): float
    {
        return $this->has_discount ? (float) $this->sale_price : (float) $this->price;
    }

    /**
     * Formatted effective price string.
     */
    public function getFormattedEffectivePriceAttribute(): string
    {
        return number_format($this->effective_price, 0, ',', '.') . ' ₫';
    }

    /**
     * Cart items relationship.
     */
    public function cartItems()
    {
        return $this->hasMany(CartItem::class);
    }

    /**
     * Order items relationship.
     */
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
}
