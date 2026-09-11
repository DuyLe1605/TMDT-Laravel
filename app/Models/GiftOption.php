<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GiftOption extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'name',
        'code',
        'image',
        'price',
        'description',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('sort_order', 'asc')->orderBy('id', 'asc');
    }

    public function scopePapers($query)
    {
        return $query->where('type', 'paper');
    }

    public function scopeCards($query)
    {
        return $query->where('type', 'card');
    }

    public function getFormattedPriceAttribute(): string
    {
        if ((float) $this->price <= 0) {
            return 'Miễn phí';
        }
        return number_format($this->price, 0, ',', '.') . ' ₫';
    }

    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            'paper' => 'Giấy gói quà',
            'card' => 'Thiệp chúc mừng',
            default => 'Tùy chọn quà tặng',
        };
    }
}
