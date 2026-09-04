<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
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
        'brand_id',
        'name',
        'slug',
        'sku',
        'price',
        'sale_price',
        'has_variants',
        'stock',
        'material',
        'dimensions',
        'color',
        'description',
        'image',
        'search_index',
        'is_featured',
        'is_active',
        'avg_rating',
        'reviews_count',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'category_id' => 'integer',
        'brand_id' => 'integer',
        'price' => 'decimal:2',
        'sale_price' => 'decimal:2',
        'has_variants' => 'boolean',
        'stock' => 'integer',
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
        'avg_rating' => 'float',
        'reviews_count' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Boot the model to auto-generate slug, sku and search_index.
     */
    protected static function boot(): void
    {
        parent::boot();

        static::saving(function (Product $product) {
            if (empty($product->slug) || $product->isDirty('name')) {
                $product->slug = Str::slug($product->name);
            }

            if (empty($product->sku)) {
                $product->sku = 'BAG-' . strtoupper(Str::random(6));
            }

            // Tự động sinh chuỗi tìm kiếm không dấu tổng hợp
            $brandName = $product->brand?->name ?? ($product->brand_id ? Brand::find($product->brand_id)?->name : '');
            $categoryName = $product->category?->name ?? ($product->category_id ? Category::find($product->category_id)?->name : '');
            
            $product->search_index = \App\Helpers\VietnameseHelper::buildSearchIndex(
                $product->name,
                $brandName,
                $categoryName,
                $product->material,
                $product->color,
                $product->sku,
                $product->description
            );
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
     * Get the brand that owns the product.
     */
    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    /**
     * Get product attributes (e.g. Chất liệu, Màu sắc).
     */
    public function attributes(): HasMany
    {
        return $this->hasMany(ProductAttribute::class)->orderBy('position');
    }

    /**
     * Get all product variants / SKUs.
     */
    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class);
    }

    /**
     * Get only active product variants.
     */
    public function activeVariants(): HasMany
    {
        return $this->hasMany(ProductVariant::class)->where('is_active', true);
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
     * Dynamic Price range string if has variants.
     */
    public function getFormattedPriceRangeAttribute(): string
    {
        if (!$this->has_variants || $this->activeVariants->isEmpty()) {
            return $this->formatted_effective_price;
        }

        $min = $this->activeVariants->min(fn($v) => $v->effective_price);
        $max = $this->activeVariants->max(fn($v) => $v->effective_price);

        if ($min == $max) {
            return number_format($min, 0, ',', '.') . ' ₫';
        }

        return number_format($min, 0, ',', '.') . ' ₫ - ' . number_format($max, 0, ',', '.') . ' ₫';
    }

    /**
     * Dynamic total stock (taking from variants if has_variants).
     */
    public function getTotalStockAttribute(): int
    {
        if ($this->has_variants && $this->relationLoaded('variants')) {
            return (int) $this->variants->sum('stock');
        }

        return (int) $this->stock;
    }

    /**
     * Cart items relationship.
     */
    public function cartItems(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    /**
     * Order items relationship.
     */
    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Product reviews relationship.
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class)->latest();
    }

    /**
     * Approved/Visible reviews relationship.
     */
    public function visibleReviews(): HasMany
    {
        return $this->hasMany(Review::class)->where('is_visible', true)->latest();
    }

    /**
     * Recalculate and update cached rating stats on product.
     */
    public function recalculateRatingStats(): void
    {
        $stats = $this->visibleReviews()
            ->selectRaw('COUNT(*) as total_reviews, AVG(rating) as average_rating')
            ->first();

        $total = (int) ($stats->total_reviews ?? 0);
        $avg = $total > 0 ? round((float) $stats->average_rating, 1) : 5.0;

        $this->updateQuietly([
            'reviews_count' => $total,
            'avg_rating' => $avg,
        ]);
    }
}
