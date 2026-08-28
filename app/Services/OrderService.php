<?php

namespace App\Services;

use App\Models\CartItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Exception;

class OrderService
{
    /**
     * Generate unique order code.
     */
    public function generateOrderCode(): string
    {
        do {
            $code = 'AUR-' . date('Ymd') . '-' . strtoupper(Str::random(5));
        } while (Order::where('order_code', $code)->exists());

        return $code;
    }

    /**
     * Create an order from selected cart items.
     *
     * @throws Exception
     */
    public function createOrder(array $orderData, Collection $cartItems, ?User $user = null): Order
    {
        if ($cartItems->isEmpty()) {
            throw new Exception("Không có sản phẩm nào được chọn để thanh toán.");
        }

        return DB::transaction(function () use ($orderData, $cartItems, $user) {
            $subtotal = 0.0;

            // 1. Validate stock and calculate subtotal
            foreach ($cartItems as $cartItem) {
                // Lock product row for update to prevent race conditions
                $product = Product::where('id', $cartItem->product_id)->lockForUpdate()->first();

                if (!$product || !$product->is_active) {
                    throw new Exception("Sản phẩm '{$cartItem->product->name}' không còn kinh doanh.");
                }

                if ($product->stock < $cartItem->quantity) {
                    throw new Exception("Sản phẩm '{$product->name}' chỉ còn {$product->stock} chiếc trong kho, không đủ đáp ứng {$cartItem->quantity} chiếc yêu cầu.");
                }

                $effectivePrice = $product->has_discount ? (float) $product->sale_price : (float) $product->price;
                $subtotal += $effectivePrice * $cartItem->quantity;
            }

            // 2. Shipping calculation: Free ship if subtotal >= 500,000
            $shippingMethod = $orderData['shipping_method'] ?? 'standard';
            $baseShippingFee = ($shippingMethod === 'express') ? 50000 : 30000;
            $shippingFee = ($subtotal >= 500000) ? 0 : $baseShippingFee;

            $discountAmount = 0.0;
            $totalAmount = $subtotal + $shippingFee - $discountAmount;

            // 3. Create Order
            $order = Order::create([
                'user_id' => $user?->id,
                'order_code' => $this->generateOrderCode(),
                'recipient_name' => $orderData['recipient_name'],
                'phone' => $orderData['phone'],
                'shipping_address' => $orderData['shipping_address'],
                'payment_method' => $orderData['payment_method'] ?? 'cod',
                'payment_status' => 'pending',
                'shipping_status' => 'pending',
                'subtotal' => $subtotal,
                'shipping_fee' => $shippingFee,
                'discount_amount' => $discountAmount,
                'total_amount' => $totalAmount,
                'notes' => $orderData['notes'] ?? null,
            ]);

            // 4. Create Order Items and decrement stock
            foreach ($cartItems as $cartItem) {
                $product = Product::find($cartItem->product_id);
                $effectivePrice = $product->has_discount ? (float) $product->sale_price : (float) $product->price;
                $itemSubtotal = $effectivePrice * $cartItem->quantity;

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'product_image' => $product->image,
                    'price' => $effectivePrice,
                    'quantity' => $cartItem->quantity,
                    'subtotal' => $itemSubtotal,
                ]);

                // Decrement inventory stock
                $product->decrement('stock', $cartItem->quantity);

                // Remove from cart
                $cartItem->delete();
            }

            return $order->load(['items.product', 'user']);
        });
    }

    /**
     * Cancel an order and restore stock if appropriate.
     *
     * @throws Exception
     */
    public function cancelOrder(Order $order, string $reason = ''): Order
    {
        if ($order->shipping_status === 'delivered' || $order->shipping_status === 'shipping') {
            throw new Exception("Đơn hàng đang giao hoặc đã giao thành công, không thể hủy.");
        }

        if ($order->shipping_status === 'cancelled') {
            throw new Exception("Đơn hàng này đã được hủy trước đó.");
        }

        return DB::transaction(function () use ($order, $reason) {
            // Restore inventory stock
            foreach ($order->items as $item) {
                if ($item->product_id) {
                    Product::where('id', $item->product_id)->increment('stock', $item->quantity);
                }
            }

            $order->update([
                'shipping_status' => 'cancelled',
                'notes' => $order->notes ? $order->notes . " | Lý do hủy: " . $reason : "Lý do hủy: " . $reason,
            ]);

            return $order->fresh();
        });
    }
}
