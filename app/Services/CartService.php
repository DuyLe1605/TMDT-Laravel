<?php

namespace App\Services;

use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Exception;

class CartService
{
    /**
     * Get the current session identifier for guest cart.
     */
    protected function getSessionId(): string
    {
        if (!Session::isStarted()) {
            Session::start();
        }

        return Session::getId();
    }

    /**
     * Base query for current user / guest session cart items.
     */
    public function getCartQuery()
    {
        if (Auth::check()) {
            return CartItem::where('user_id', Auth::id());
        }

        return CartItem::where('session_id', $this->getSessionId())->whereNull('user_id');
    }

    /**
     * Get all cart items with products loaded.
     */
    public function getCartItems(): Collection
    {
        return $this->getCartQuery()
            ->with(['product.category'])
            ->latest()
            ->get();
    }

    /**
     * Get cart items by specific IDs (e.g. selected items for checkout).
     */
    public function getSelectedItems(array $ids): Collection
    {
        if (empty($ids)) {
            return new Collection();
        }

        return $this->getCartQuery()
            ->whereIn('id', $ids)
            ->with(['product.category'])
            ->get();
    }

    /**
     * Get count of distinct items in current cart (Shopee style line items count).
     */
    public function getCartCount(): int
    {
        return (int) $this->getCartQuery()->count();
    }

    /**
     * Add product to cart with stock validation.
     *
     * @throws Exception
     */
    public function addToCart(int $productId, int $quantity = 1): CartItem
    {
        $product = Product::where('is_active', true)->findOrFail($productId);

        if ($product->stock <= 0) {
            throw new Exception("Sản phẩm '{$product->name}' hiện đã hết hàng.");
        }

        $query = $this->getCartQuery()->where('product_id', $productId);
        $cartItem = $query->first();

        $newQuantity = $cartItem ? $cartItem->quantity + $quantity : $quantity;

        if ($newQuantity > $product->stock) {
            $available = $product->stock - ($cartItem ? $cartItem->quantity : 0);
            if ($available <= 0) {
                throw new Exception("Bạn đã có {$cartItem->quantity} sản phẩm trong giỏ, không thể thêm vượt quá tồn kho ({$product->stock}).");
            }
            throw new Exception("Chỉ có thể thêm tối đa {$available} sản phẩm nữa vào giỏ hàng (tồn kho: {$product->stock}).");
        }

        if ($cartItem) {
            $cartItem->update(['quantity' => $newQuantity]);
            return $cartItem->fresh(['product']);
        }

        $attributes = [
            'product_id' => $productId,
            'quantity' => $newQuantity,
        ];

        if (Auth::check()) {
            $attributes['user_id'] = Auth::id();
        } else {
            $attributes['session_id'] = $this->getSessionId();
        }

        return CartItem::create($attributes)->load('product');
    }

    /**
     * Update item quantity in cart.
     *
     * @throws Exception
     */
    public function updateQuantity(int $cartItemId, int $quantity): CartItem
    {
        $cartItem = $this->getCartQuery()->with('product')->findOrFail($cartItemId);

        if ($quantity <= 0) {
            $cartItem->delete();
            throw new Exception("Sản phẩm đã được xóa khỏi giỏ hàng.");
        }

        if ($quantity > $cartItem->product->stock) {
            throw new Exception("Số lượng yêu cầu ({$quantity}) vượt quá số lượng còn lại trong kho ({$cartItem->product->stock}).");
        }

        $cartItem->update(['quantity' => $quantity]);

        return $cartItem->fresh(['product']);
    }

    /**
     * Remove single item from cart.
     */
    public function removeItem(int $cartItemId): bool
    {
        $cartItem = $this->getCartQuery()->find($cartItemId);
        if ($cartItem) {
            return (bool) $cartItem->delete();
        }

        return false;
    }

    /**
     * Bulk remove selected items from cart.
     */
    public function removeBulk(array $cartItemIds): int
    {
        if (empty($cartItemIds)) {
            return 0;
        }

        return $this->getCartQuery()->whereIn('id', $cartItemIds)->delete();
    }

    /**
     * Calculate summary for cart or selected items.
     */
    public function calculateSummary(Collection $items): array
    {
        $totalQuantity = 0;
        $totalAmount = 0.0;

        foreach ($items as $item) {
            $totalQuantity += $item->quantity;
            $totalAmount += $item->subtotal;
        }

        return [
            'total_items' => $items->count(),
            'total_quantity' => $totalQuantity,
            'total_amount' => $totalAmount,
            'formatted_total_amount' => number_format($totalAmount, 0, ',', '.') . ' ₫',
        ];
    }

    /**
     * Merge guest cart items into authenticated user account upon login.
     */
    public function mergeGuestCart(int $userId, string $sessionId): void
    {
        $guestItems = CartItem::where('session_id', $sessionId)
            ->whereNull('user_id')
            ->get();

        foreach ($guestItems as $guestItem) {
            $userItem = CartItem::where('user_id', $userId)
                ->where('product_id', $guestItem->product_id)
                ->first();

            if ($userItem) {
                $userItem->increment('quantity', $guestItem->quantity);
                $guestItem->delete();
            } else {
                $guestItem->update([
                    'user_id' => $userId,
                    'session_id' => null,
                ]);
            }
        }
    }
}
