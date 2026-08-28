<?php

namespace App\Services;

use App\Models\Address;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Exception;

class AddressService
{
    /**
     * Get all addresses for a given user.
     */
    public function getUserAddresses(int $userId): Collection
    {
        return Address::where('user_id', $userId)
            ->orderBy('is_default', 'desc')
            ->latest()
            ->get();
    }

    /**
     * Create a new address for user.
     */
    public function createAddress(int $userId, array $data): Address
    {
        return DB::transaction(function () use ($userId, $data) {
            $hasExisting = Address::where('user_id', $userId)->exists();
            $isDefault = !empty($data['is_default']) || !$hasExisting;

            if ($isDefault) {
                Address::where('user_id', $userId)->update(['is_default' => false]);
            }

            $data['user_id'] = $userId;
            $data['is_default'] = $isDefault;

            return Address::create($data);
        });
    }

    /**
     * Update an existing address.
     */
    public function updateAddress(Address $address, array $data): Address
    {
        return DB::transaction(function () use ($address, $data) {
            if (!empty($data['is_default']) && !$address->is_default) {
                Address::where('user_id', $address->user_id)->update(['is_default' => false]);
                $data['is_default'] = true;
            }

            $address->update($data);
            return $address->fresh();
        });
    }

    /**
     * Set an address as default for the user.
     */
    public function setDefault(Address $address): Address
    {
        return DB::transaction(function () use ($address) {
            Address::where('user_id', $address->user_id)->update(['is_default' => false]);
            $address->update(['is_default' => true]);

            return $address->fresh();
        });
    }

    /**
     * Delete an address.
     *
     * @throws Exception
     */
    public function deleteAddress(Address $address): bool
    {
        return DB::transaction(function () use ($address) {
            $userId = $address->user_id;
            $wasDefault = $address->is_default;

            $deleted = $address->delete();

            // If deleted address was default, promote the latest remaining address to default
            if ($wasDefault) {
                $newDefault = Address::where('user_id', $userId)->latest()->first();
                if ($newDefault) {
                    $newDefault->update(['is_default' => true]);
                }
            }

            return $deleted;
        });
    }
}
