<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'role', 'coins_balance'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'coins_balance' => 'integer',
        ];
    }

    /**
     * Check if user has admin role.
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Check if user has customer role.
     */
    public function isCustomer(): bool
    {
        return $this->role === 'customer';
    }

    /**
     * Get cart items for the user.
     */
    public function cartItems()
    {
        return $this->hasMany(CartItem::class);
    }

    /**
     * Get addresses for the user.
     */
    public function addresses()
    {
        return $this->hasMany(Address::class)->orderBy('is_default', 'desc')->latest();
    }

    /**
     * Get default address for user.
     */
    public function defaultAddress(): ?Address
    {
        return $this->addresses()->where('is_default', true)->first() ?: $this->addresses()->first();
    }

    /**
     * Get orders for the user.
     */
    public function orders()
    {
        return $this->hasMany(Order::class)->latest();
    }

    /**
     * Get voucher redemption logs for user.
     */
    public function voucherUsages()
    {
        return $this->hasMany(VoucherUsage::class);
    }

    /**
     * Get product reviews by user.
     */
    public function reviews()
    {
        return $this->hasMany(Review::class)->latest();
    }

    /**
     * Get coin transactions history for user.
     */
    public function coinTransactions()
    {
        return $this->hasMany(CoinTransaction::class)->latest();
    }

    /**
     * Formatted coins balance string.
     */
    public function getFormattedCoinsBalanceAttribute(): string
    {
        return number_format((int) $this->coins_balance, 0, ',', '.') . ' Xu';
    }
}
