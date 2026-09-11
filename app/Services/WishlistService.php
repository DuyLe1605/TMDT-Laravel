<?php

namespace App\Services;

use App\Models\Wishlist;
use App\Models\Product;
use Illuminate\Support\Collection;

class WishlistService
{
    /**
     * Toggle wishlist: thêm nếu chưa có, xóa nếu đã có.
     *
     * @return array{added: bool, count: int}
     */
    public function toggle(int $userId, int $productId): array
    {
        $existing = Wishlist::where('user_id', $userId)
            ->where('product_id', $productId)
            ->first();

        if ($existing) {
            $existing->delete();
            return [
                'added' => false,
                'count' => $this->getWishlistCount($userId),
            ];
        }

        Wishlist::create([
            'user_id' => $userId,
            'product_id' => $productId,
        ]);

        return [
            'added' => true,
            'count' => $this->getWishlistCount($userId),
        ];
    }

    /**
     * Lấy danh sách wishlist của user kèm eager load product.
     */
    public function getUserWishlist(int $userId): Collection
    {
        return Wishlist::where('user_id', $userId)
            ->with(['product' => function ($q) {
                $q->with(['category', 'brand', 'variants']);
            }])
            ->latest()
            ->get();
    }

    /**
     * Kiểm tra sản phẩm đã được user thả tim chưa.
     */
    public function isWishlisted(int $userId, int $productId): bool
    {
        return Wishlist::where('user_id', $userId)
            ->where('product_id', $productId)
            ->exists();
    }

    /**
     * Đếm số lượng sản phẩm trong wishlist của user.
     */
    public function getWishlistCount(int $userId): int
    {
        return Wishlist::where('user_id', $userId)->count();
    }

    /**
     * Lấy danh sách product_id đã wishlist (để check icon trái tim hàng loạt).
     */
    public function getWishlistedProductIds(int $userId): array
    {
        return Wishlist::where('user_id', $userId)
            ->pluck('product_id')
            ->toArray();
    }

    /**
     * Xóa sản phẩm khỏi wishlist.
     */
    public function remove(int $userId, int $productId): bool
    {
        return (bool) Wishlist::where('user_id', $userId)
            ->where('product_id', $productId)
            ->delete();
    }
}
