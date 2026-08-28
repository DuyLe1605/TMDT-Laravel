<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Address extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'recipient_name',
        'phone',
        'province',
        'district',
        'ward',
        'specific_address',
        'address_type',
        'is_default',
    ];

    protected $casts = [
        'user_id' => 'integer',
        'is_default' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the user that owns the address.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get formatted full address line.
     */
    public function getFullAddressAttribute(): string
    {
        $parts = array_filter([
            $this->specific_address,
            $this->ward,
            $this->district,
            $this->province,
        ]);

        return implode(', ', $parts);
    }

    /**
     * Get label for address type.
     */
    public function getTypeLabelAttribute(): string
    {
        return match ($this->address_type) {
            'office' => 'Văn phòng',
            default => 'Nhà riêng',
        };
    }
}
