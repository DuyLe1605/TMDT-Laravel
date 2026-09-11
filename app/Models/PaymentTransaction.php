<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentTransaction extends Model
{
    use HasFactory;

    // =========================================================================
    // GATEWAY CONSTANTS
    // =========================================================================
    public const GATEWAY_MOMO    = 'momo';
    public const GATEWAY_VNPAY   = 'vnpay';
    public const GATEWAY_ZALOPAY = 'zalopay';
    public const GATEWAY_COD     = 'cod';

    // =========================================================================
    // TRANSACTION TYPE CONSTANTS
    // =========================================================================
    public const TYPE_PAYMENT = 'payment';
    public const TYPE_REFUND  = 'refund';
    public const TYPE_QUERY   = 'query';

    // =========================================================================
    // STATUS CONSTANTS
    // =========================================================================
    public const STATUS_PENDING  = 'pending';
    public const STATUS_PAID     = 'paid';
    public const STATUS_FAILED   = 'failed';
    public const STATUS_REFUNDED = 'refunded';

    protected $fillable = [
        'order_id',
        'gateway',
        'transaction_type',
        'partner_code',
        'request_id',
        'momo_order_id',
        'gateway_order_id',
        'amount',
        'status',
        'result_code',
        'message',
        'momo_trans_id',
        'transaction_id',
        'pay_type',
        'pay_url',
        'deeplink',
        'qr_code_url',
        'signature_request',
        'signature_response',
        'request_payload',
        'response_payload',
        'ipn_payload',
        'paid_at',
    ];

    protected $casts = [
        'order_id'         => 'integer',
        'amount'           => 'integer',
        'result_code'      => 'integer',
        'request_payload'  => 'array',
        'response_payload' => 'array',
        'ipn_payload'      => 'array',
        'paid_at'          => 'datetime',
        'created_at'       => 'datetime',
        'updated_at'       => 'datetime',
    ];

    // =========================================================================
    // RELATIONSHIPS
    // =========================================================================

    /**
     * Get the order that this payment transaction belongs to.
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    // =========================================================================
    // HELPER METHODS
    // =========================================================================

    /**
     * Check if this transaction was successful.
     */
    public function isSuccessful(): bool
    {
        return $this->status === self::STATUS_PAID && $this->result_code === 0;
    }

    /**
     * Check if this transaction is still pending.
     */
    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    /**
     * Check if this transaction has failed.
     */
    public function isFailed(): bool
    {
        return $this->status === self::STATUS_FAILED;
    }

    // =========================================================================
    // FORMATTED ACCESSORS
    // =========================================================================

    /**
     * Get formatted amount string.
     */
    public function getFormattedAmountAttribute(): string
    {
        return number_format($this->amount, 0, ',', '.') . ' ₫';
    }

    /**
     * Get human-readable gateway label.
     */
    public function getGatewayLabelAttribute(): string
    {
        return match ($this->gateway) {
            self::GATEWAY_MOMO    => 'Ví MoMo',
            self::GATEWAY_VNPAY   => 'VNPay',
            self::GATEWAY_ZALOPAY => 'ZaloPay',
            self::GATEWAY_COD     => 'Thanh toán COD',
            default               => ucfirst($this->gateway),
        };
    }

    /**
     * Get status badge for display.
     */
    public function getStatusBadgeAttribute(): array
    {
        return match ($this->status) {
            self::STATUS_PENDING  => ['label' => 'Đang chờ',     'class' => 'bg-warning-subtle text-warning-emphasis border border-warning-subtle'],
            self::STATUS_PAID     => ['label' => 'Thành công',   'class' => 'bg-success-subtle text-success border border-success-subtle'],
            self::STATUS_FAILED   => ['label' => 'Thất bại',     'class' => 'bg-danger-subtle text-danger border border-danger-subtle'],
            self::STATUS_REFUNDED => ['label' => 'Đã hoàn tiền', 'class' => 'bg-info-subtle text-info border border-info-subtle'],
            default               => ['label' => $this->status,  'class' => 'bg-secondary-subtle text-secondary'],
        };
    }

    /**
     * Get human-readable pay type label.
     */
    public function getPayTypeLabelAttribute(): string
    {
        return match ($this->pay_type) {
            'qr'         => 'QR Code',
            'webApp'     => 'Web App',
            'creditcard' => 'Thẻ Tín dụng',
            'napas'      => 'Thẻ ATM (NAPAS)',
            default      => $this->pay_type ?? 'N/A',
        };
    }

    /**
     * Accessor & Mutator for compatibility with teacher's gateway_order_id
     */
    public function getGatewayOrderIdAttribute(): ?string
    {
        return $this->attributes['gateway_order_id'] ?? $this->attributes['momo_order_id'] ?? null;
    }

    public function setGatewayOrderIdAttribute($value): void
    {
        $this->attributes['momo_order_id'] = $value;
    }

    /**
     * Accessor & Mutator for compatibility with teacher's transaction_id
     */
    public function getTransactionIdAttribute(): ?string
    {
        return $this->attributes['transaction_id'] ?? $this->attributes['momo_trans_id'] ?? null;
    }

    public function setTransactionIdAttribute($value): void
    {
        $this->attributes['momo_trans_id'] = $value;
    }
}
