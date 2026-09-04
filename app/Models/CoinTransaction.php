<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CoinTransaction extends Model
{
    use HasFactory;

    public const TYPE_EARN   = 'earn';
    public const TYPE_SPEND  = 'spend';
    public const TYPE_REFUND = 'refund';
    public const TYPE_ADJUST = 'adjust';

    protected $fillable = [
        'user_id',
        'type',
        'amount',
        'balance_after',
        'reference_type',
        'reference_id',
        'description',
    ];

    protected $casts = [
        'user_id'       => 'integer',
        'amount'        => 'integer',
        'balance_after' => 'integer',
        'reference_id'  => 'integer',
        'created_at'    => 'datetime',
        'updated_at'    => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Formatted amount with +/- sign.
     */
    public function getFormattedAmountAttribute(): string
    {
        $prefix = $this->amount > 0 ? '+' : '';
        return $prefix . number_format($this->amount, 0, ',', '.') . ' Xu';
    }

    /**
     * Badge information for transaction type.
     */
    public function getTypeBadgeAttribute(): array
    {
        return match ($this->type) {
            self::TYPE_EARN   => ['label' => 'Nhận thưởng', 'class' => 'bg-success text-white', 'icon' => 'plus-circle'],
            self::TYPE_SPEND  => ['label' => 'Đã sử dụng', 'class' => 'bg-warning text-dark',  'icon' => 'shopping-bag'],
            self::TYPE_REFUND => ['label' => 'Hoàn Xu',      'class' => 'bg-info text-dark',     'icon' => 'rotate-ccw'],
            default           => ['label' => 'Điều chỉnh',   'class' => 'bg-secondary text-white', 'icon' => 'sliders'],
        };
    }
}
