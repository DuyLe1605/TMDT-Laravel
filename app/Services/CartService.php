<?php

namespace App\Services;

use App\Models\CartItem;
use App\Models\Product;
use App\Models\ProductVariant;
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
     * Get all cart items with products and variants loaded.
     */
    public function getCartItems(): Collection
    {
        return $this->getCartQuery()
            ->with(['product.category', 'product.brand', 'variant'])
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
            ->with(['product.category', 'product.brand', 'variant'])
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
     * Add product (and optional variant) to cart with stock validation.
     *
     * @throws Exception
     */
    public function addToCart(int $productId, int $quantity = 1, ?int $variantId = null): CartItem
    {
        $product = Product::where('is_active', true)->findOrFail($productId);

        $variant = null;
        $maxStock = $product->stock;
        $itemName = $product->name;

        if ($variantId) {
            $variant = ProductVariant::where('product_id', $productId)
                ->where('is_active', true)
                ->findOrFail($variantId);
            $maxStock = $variant->stock;
            $itemName = "{$product->name} ({$variant->variant_title})";
        } elseif ($product->has_variants) {
            // Product has variants, but no variant chosen: pick the first active available variant
            $variant = ProductVariant::where('product_id', $productId)
                ->where('is_active', true)
                ->where('stock', '>', 0)
                ->first();

            if (!$variant) {
                // If all variants out of stock, grab the first active
                $variant = ProductVariant::where('product_id', $productId)
                    ->where('is_active', true)
                    ->first();
            }

            if ($variant) {
                $variantId = $variant->id;
                $maxStock = $variant->stock;
                $itemName = "{$product->name} ({$variant->variant_title})";
            }
        }

        if ($maxStock <= 0) {
            throw new Exception("Sản phẩm/phân loại '{$itemName}' hiện đã hết hàng.");
        }

        $query = $this->getCartQuery()->where('product_id', $productId);
        if ($variantId) {
            $query->where('product_variant_id', $variantId);
        } else {
            $query->whereNull('product_variant_id');
        }

        $cartItem = $query->first();
        $newQuantity = $cartItem ? $cartItem->quantity + $quantity : $quantity;

        if ($newQuantity > $maxStock) {
            $available = $maxStock - ($cartItem ? $cartItem->quantity : 0);
            if ($available <= 0) {
                throw new Exception("Bạn đã có {$cartItem->quantity} sản phẩm trong giỏ, không thể thêm vượt quá tồn kho ({$maxStock}).");
            }
            throw new Exception("Chỉ có thể thêm tối đa {$available} sản phẩm nữa vào giỏ hàng (tồn kho: {$maxStock}).");
        }

        if ($cartItem) {
            $cartItem->update(['quantity' => $newQuantity]);
            return $cartItem->fresh(['product', 'variant']);
        }

        $attributes = [
            'product_id' => $productId,
            'product_variant_id' => $variantId,
            'quantity' => $newQuantity,
        ];

        if (Auth::check()) {
            $attributes['user_id'] = Auth::id();
        } else {
            $attributes['session_id'] = $this->getSessionId();
        }

        return CartItem::create($attributes)->load(['product', 'variant']);
    }

    /**
     * Update item quantity in cart.
     *
     * @throws Exception
     */
    public function updateQuantity(int $cartItemId, int $quantity): CartItem
    {
        $cartItem = $this->getCartQuery()->with(['product', 'variant'])->findOrFail($cartItemId);

        if ($quantity <= 0) {
            $cartItem->delete();
            throw new Exception("Sản phẩm đã được xóa khỏi giỏ hàng.");
        }

        $stockLimit = $cartItem->variant ? $cartItem->variant->stock : $cartItem->product->stock;

        if ($quantity > $stockLimit) {
            throw new Exception("Số lượng yêu cầu ({$quantity}) vượt quá số lượng còn lại trong kho ({$stockLimit}).");
        }

        $cartItem->update(['quantity' => $quantity]);

        return $cartItem->fresh(['product', 'variant']);
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
                ->where('product_variant_id', $guestItem->product_variant_id)
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
