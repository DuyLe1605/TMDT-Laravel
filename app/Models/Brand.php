<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Brand extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'logo',
        'description',
        'website',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Boot auto-generate slug.
     */
    protected static function boot(): void
    {
        parent::boot();

        static::saving(function (Brand $brand) {
            if (empty($brand->slug) || $brand->isDirty('name')) {
                $brand->slug = Str::slug($brand->name);
            }
        });
    }

    /**
     * Get products belonging to this brand.
     */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    /**
     * Active scope.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
