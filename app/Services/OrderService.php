<?php

namespace App\Services;

use App\Models\CartItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;
use App\Services\Shipping\GhnShippingService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Exception;

class OrderService
{
    public function __construct(
        protected GhnShippingService $ghnService
    ) {}

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
            $totalWeight = 0;

            // 1. Validate stock and calculate subtotal + weight
            foreach ($cartItems as $cartItem) {
                // Lock product row
                $product = Product::where('id', $cartItem->product_id)->lockForUpdate()->first();

                if (!$product || !$product->is_active) {
                    throw new Exception("Sản phẩm '{$cartItem->product->name}' không còn kinh doanh.");
                }

                $variant = null;
                $effectivePrice = 0.0;

                if ($cartItem->product_variant_id) {
                    $variant = ProductVariant::where('id', $cartItem->product_variant_id)
                        ->where('product_id', $product->id)
                        ->lockForUpdate()
                        ->first();

                    if (!$variant || !$variant->is_active) {
                        throw new Exception("Phân loại hàng của sản phẩm '{$product->name}' hiện không còn khả dụng.");
                    }

                    if ($variant->stock < $cartItem->quantity) {
                        throw new Exception("Phân loại '{$variant->variant_title}' của '{$product->name}' chỉ còn {$variant->stock} chiếc trong kho, không đủ đáp ứng {$cartItem->quantity} chiếc.");
                    }

                    $effectivePrice = $variant->effective_price;
                } else {
                    if ($product->stock < $cartItem->quantity) {
                        throw new Exception("Sản phẩm '{$product->name}' chỉ còn {$product->stock} chiếc trong kho, không đủ đáp ứng {$cartItem->quantity} chiếc yêu cầu.");
                    }

                    $effectivePrice = $product->effective_price;
                }

                $subtotal += $effectivePrice * $cartItem->quantity;
                $productWeight = $product->weight ?? 600;
                $totalWeight += $productWeight * $cartItem->quantity;
            }

            // 2. Shipping calculation: Use calculated GHN fee if provided, else fallback
            $shippingMethod = $orderData['shipping_method'] ?? 'standard';
            $baseShippingFee = ($shippingMethod === 'express') ? 50000 : 30000;

            if (isset($orderData['shipping_fee']) && is_numeric($orderData['shipping_fee'])) {
                $baseShippingFee = max(0, (float) $orderData['shipping_fee']);
            }

            $shippingFee = ($subtotal >= 500000 && $shippingMethod === 'standard') ? 0 : $baseShippingFee;

            $discountAmount = 0.0;
            $totalAmount = $subtotal + $shippingFee - $discountAmount;

            // 3. Determine payment status based on payment method
            $paymentMethod = $orderData['payment_method'] ?? 'cod';
            $paymentStatus = Order::PAYMENT_PENDING;
            $paidAt = null;

            // If payment was already verified through an online gateway/callback
            if (!empty($orderData['payment_status']) && $orderData['payment_status'] === Order::PAYMENT_PAID) {
                $paymentStatus = Order::PAYMENT_PAID;
                $paidAt = now();
            }

            // 4. Parse expected delivery from GHN if available
            $expectedDeliveryAt = null;
            if (!empty($orderData['expected_delivery_at'])) {
                try {
                    $expectedDeliveryAt = \Carbon\Carbon::parse($orderData['expected_delivery_at']);
                } catch (\Exception) {
                    // Ignore parse errors
                }
            }

            // 5. Create Order
            $order = Order::create([
                'user_id'              => $user?->id,
                'order_code'           => $this->generateOrderCode(),
                'recipient_name'       => $orderData['recipient_name'],
                'phone'                => $orderData['phone'],
                'shipping_address'     => $orderData['shipping_address'],
                'to_district_id'       => $orderData['to_district_id'] ?? null,
                'to_ward_code'         => $orderData['to_ward_code'] ?? null,
                'total_weight'         => max(100, $totalWeight),
                'expected_delivery_at' => $expectedDeliveryAt,
                'payment_method'       => $paymentMethod,
                'payment_status'       => $paymentStatus,
                'shipping_status'      => Order::STATUS_PENDING,
                'subtotal'             => $subtotal,
                'shipping_fee'         => $shippingFee,
                'discount_amount'      => $discountAmount,
                'total_amount'         => $totalAmount,
                'notes'                => $orderData['notes'] ?? null,
                'paid_at'              => $paidAt,
            ]);

            // 6. Create Order Items and decrement stock
            foreach ($cartItems as $cartItem) {
                $product = Product::find($cartItem->product_id);
                $variant = $cartItem->product_variant_id ? ProductVariant::find($cartItem->product_variant_id) : null;

                $effectivePrice = $variant ? $variant->effective_price : $product->effective_price;
                $itemSubtotal = $effectivePrice * $cartItem->quantity;
                $itemImage = $variant && !empty($variant->image) ? $variant->image : $product->image;
                $variantTitle = $variant ? $variant->variant_title : null;

                OrderItem::create([
                    'order_id'           => $order->id,
                    'product_id'         => $product->id,
                    'product_variant_id' => $variant?->id,
                    'product_name'       => $product->name,
                    'variant_title'      => $variantTitle,
                    'product_image'      => $itemImage,
                    'price'              => $effectivePrice,
                    'quantity'           => $cartItem->quantity,
                    'subtotal'           => $itemSubtotal,
                ]);

                // Decrement inventory stock
                if ($variant) {
                    $variant->decrement('stock', $cartItem->quantity);
                    $product->decrement('stock', $cartItem->quantity);
                } else {
                    $product->decrement('stock', $cartItem->quantity);
                }

                // Remove from cart
                $cartItem->delete();
            }

            return $order->load(['items.product', 'items.variant', 'user']);
        });
    }

    // =========================================================================
    // ADMIN: ORDER STATUS MANAGEMENT
    // =========================================================================

    /**
     * Confirm an order (pending → processing).
     * Admin verifies the order is valid and starts preparing it.
     *
     * @throws Exception
     */
    public function confirmOrder(Order $order): Order
    {
        if (!$order->canBeConfirmed()) {
            throw new Exception("Đơn hàng này không ở trạng thái chờ xử lý, không thể xác nhận.");
        }

        $order->update([
            'shipping_status' => Order::STATUS_PROCESSING,
        ]);

        return $order->fresh();
    }

    /**
     * Send order to GHN for delivery (processing → shipping).
     * Admin manually triggers this when the package is packed and ready.
     *
     * @throws Exception
     */
    public function sendToGhn(Order $order): Order
    {
        if (!$order->canBeSentToGhn()) {
            throw new Exception("Đơn hàng không thể gửi GHN. Trạng thái hiện tại: {$order->shipping_status_badge['label']}" .
                ($order->ghn_order_code ? " (Đã có mã GHN: {$order->ghn_order_code})" : ''));
        }

        // Load items with products for weight calculation
        $order->load('items.product');

        $result = $this->ghnService->createShippingOrder($order);

        if (!$result['success']) {
            throw new Exception("Không thể tạo đơn GHN: {$result['message']}");
        }

        $updateData = [
            'shipping_status' => Order::STATUS_SHIPPING,
            'ghn_order_code'  => $result['ghn_order_code'],
            'ghn_status'      => 'ready_to_pick',
            'ghn_status_name' => 'Đơn hàng mới, chờ lấy hàng',
        ];

        // Parse expected delivery time from GHN
        if (!empty($result['expected_delivery_time'])) {
            try {
                $updateData['expected_delivery_at'] = \Carbon\Carbon::parse($result['expected_delivery_time']);
            } catch (\Exception) {
                // Ignore
            }
        }

        $order->update($updateData);

        return $order->fresh();
    }

    /**
     * Mark order as delivered (admin manual action).
     *
     * @throws Exception
     */
    public function markDelivered(Order $order): Order
    {
        if ($order->shipping_status !== Order::STATUS_SHIPPING) {
            throw new Exception("Chỉ có thể đánh dấu giao thành công khi đơn hàng đang trong trạng thái 'Đang giao'.");
        }

        $updateData = [
            'shipping_status' => Order::STATUS_DELIVERED,
            'ghn_status'      => 'delivered',
            'ghn_status_name' => 'Giao hàng thành công',
        ];

        // If COD, mark as paid when delivered
        if ($order->payment_method === 'cod' && $order->payment_status === Order::PAYMENT_PENDING) {
            $updateData['payment_status'] = Order::PAYMENT_PAID;
            $updateData['paid_at'] = now();
        }

        $order->update($updateData);

        return $order->fresh();
    }

    /**
     * Mark order payment as paid (admin manual confirmation for bank transfer, momo, or cash).
     *
     * @throws Exception
     */
    public function markPaid(Order $order): Order
    {
        if ($order->payment_status === Order::PAYMENT_PAID) {
            throw new Exception("Đơn hàng này đã được xác nhận thanh toán trước đó.");
        }

        $order->update([
            'payment_status' => Order::PAYMENT_PAID,
            'paid_at'        => now(),
        ]);

        return $order->fresh();
    }

    /**
     * Cancel an order and restore stock if appropriate.
     *
     * @throws Exception
     */
    public function cancelOrder(Order $order, string $reason = '', bool $isAdmin = false): Order
    {
        // Permission check
        if ($isAdmin) {
            if (!$order->canBeCancelledByAdmin()) {
                throw new Exception("Đơn hàng đang giao hoặc đã giao thành công, không thể hủy.");
            }
        } else {
            if (!$order->canBeCancelledByCustomer()) {
                throw new Exception("Bạn chỉ có thể hủy đơn hàng khi đơn đang ở trạng thái 'Chờ xử lý'.");
            }
        }

        if ($order->shipping_status === Order::STATUS_CANCELLED) {
            throw new Exception("Đơn hàng này đã được hủy trước đó.");
        }

        return DB::transaction(function () use ($order, $reason, $isAdmin) {
            // Cancel on GHN if already sent
            if ($order->isGhnOrder()) {
                $ghnResult = $this->ghnService->cancelShippingOrder($order->ghn_order_code);
                if (!$ghnResult['success'] && $isAdmin) {
                    // Log warning but don't block admin cancel
                    \Illuminate\Support\Facades\Log::warning(
                        "GHN cancel failed for {$order->ghn_order_code}: {$ghnResult['message']}"
                    );
                }
            }

            // Restore inventory stock
            foreach ($order->items as $item) {
                if ($item->product_variant_id) {
                    ProductVariant::where('id', $item->product_variant_id)->increment('stock', $item->quantity);
                }
                if ($item->product_id) {
                    Product::where('id', $item->product_id)->increment('stock', $item->quantity);
                }
            }

            // Determine payment status after cancellation
            $paymentStatus = $order->payment_status;
            if ($order->payment_status === Order::PAYMENT_PAID) {
                // Online payments that were paid → mark as refunding
                $paymentStatus = Order::PAYMENT_REFUNDING;
            }

            $order->update([
                'shipping_status' => Order::STATUS_CANCELLED,
                'payment_status'  => $paymentStatus,
                'cancel_reason'   => $reason ?: null,
                'cancelled_at'    => now(),
                'ghn_status'      => $order->isGhnOrder() ? 'cancel' : $order->ghn_status,
                'ghn_status_name' => $order->isGhnOrder() ? 'Đơn hàng đã bị hủy' : $order->ghn_status_name,
            ]);

            return $order->fresh();
        });
    }

    // =========================================================================
    // CUSTOMER: REORDER (MUA LẠI)
    // =========================================================================

    /**
     * Re-order: Add items from a cancelled/delivered order back to cart.
     * Returns array of results for each item (success/fail with reason).
     */
    public function reorder(Order $order, ?User $user): array
    {
        if (!$order->canReorder()) {
            return [
                'success' => false,
                'message' => 'Đơn hàng chưa hoàn tất hoặc chưa bị hủy, không thể mua lại.',
                'items' => [],
            ];
        }

        $order->load(['items.product', 'items.variant']);

        $addedItems = [];
        $failedItems = [];
        $sessionId = session()->getId();

        foreach ($order->items as $item) {
            $product = $item->product;

            // Product no longer exists or is inactive
            if (!$product || !$product->is_active) {
                $failedItems[] = [
                    'product_name' => $item->product_name,
                    'reason' => 'Sản phẩm không còn kinh doanh',
                ];
                continue;
            }

            $variantId = null;
            $availableStock = $product->stock;

            // Check variant availability
            if ($item->product_variant_id) {
                $variant = $item->variant;
                if (!$variant || !$variant->is_active) {
                    $failedItems[] = [
                        'product_name' => $item->product_name,
                        'variant_title' => $item->variant_title,
                        'reason' => 'Phân loại hàng không còn khả dụng',
                    ];
                    continue;
                }
                $availableStock = $variant->stock;
                $variantId = $variant->id;
            }

            // Check stock
            $quantity = min($item->quantity, $availableStock);
            if ($quantity <= 0) {
                $failedItems[] = [
                    'product_name' => $item->product_name,
                    'variant_title' => $item->variant_title,
                    'reason' => 'Hết hàng',
                ];
                continue;
            }

            // Add to cart (merge if already exists)
            $existingCartItem = CartItem::where('product_id', $product->id)
                ->where('product_variant_id', $variantId)
                ->when($user, fn($q) => $q->where('user_id', $user->id))
                ->when(!$user, fn($q) => $q->where('session_id', $sessionId)->whereNull('user_id'))
                ->first();

            if ($existingCartItem) {
                $newQty = min($existingCartItem->quantity + $quantity, $availableStock);
                $existingCartItem->update(['quantity' => $newQty]);
            } else {
                CartItem::create([
                    'user_id'            => $user?->id,
                    'session_id'         => $user ? null : $sessionId,
                    'product_id'         => $product->id,
                    'product_variant_id' => $variantId,
                    'quantity'           => $quantity,
                ]);
            }

            $addedItems[] = [
                'product_name' => $item->product_name,
                'variant_title' => $item->variant_title,
                'quantity' => $quantity,
                'original_quantity' => $item->quantity,
                'reduced' => $quantity < $item->quantity,
            ];
        }

        $totalAdded = count($addedItems);
        $totalFailed = count($failedItems);

        return [
            'success' => $totalAdded > 0,
            'message' => $totalAdded > 0
                ? "Đã thêm {$totalAdded} sản phẩm vào giỏ hàng." . ($totalFailed > 0 ? " ({$totalFailed} sản phẩm không khả dụng)" : '')
                : 'Không có sản phẩm nào còn khả dụng để mua lại.',
            'added_items' => $addedItems,
            'failed_items' => $failedItems,
            'added_count' => $totalAdded,
            'failed_count' => $totalFailed,
        ];
    }

    // =========================================================================
    // GHN WEBHOOK: STATUS UPDATE
    // =========================================================================

    /**
     * Process GHN webhook callback to update order status.
     */
    public function processGhnWebhook(array $payload): bool
    {
        $ghnOrderCode = $payload['OrderCode'] ?? null;
        $clientOrderCode = $payload['ClientOrderCode'] ?? null;
        $ghnStatus = $payload['Status'] ?? null;

        if (!$ghnStatus) {
            return false;
        }

        // Find order by GHN order code or our internal order code
        $order = null;
        if ($ghnOrderCode) {
            $order = Order::where('ghn_order_code', $ghnOrderCode)->first();
        }
        if (!$order && $clientOrderCode) {
            $order = Order::where('order_code', $clientOrderCode)->first();
        }

        if (!$order) {
            return false;
        }

        $internalStatus = GhnShippingService::mapGhnStatusToInternal($ghnStatus);
        $statusName = GhnShippingService::mapGhnStatusToName($ghnStatus);

        $updateData = [
            'ghn_status'      => $ghnStatus,
            'ghn_status_name' => $statusName,
            'shipping_status' => $internalStatus,
        ];

        // If delivered and COD: mark payment as paid
        if ($ghnStatus === 'delivered' && $order->payment_method === 'cod') {
            $updateData['payment_status'] = Order::PAYMENT_PAID;
            $updateData['paid_at'] = now();
        }

        // If returned/cancelled by GHN: restore stock
        if (in_array($ghnStatus, ['returned', 'cancel', 'lost'])) {
            DB::transaction(function () use ($order) {
                foreach ($order->items as $item) {
                    if ($item->product_variant_id) {
                        ProductVariant::where('id', $item->product_variant_id)
                            ->increment('stock', $item->quantity);
                    }
                    if ($item->product_id) {
                        Product::where('id', $item->product_id)
                            ->increment('stock', $item->quantity);
                    }
                }
            });

            $updateData['cancelled_at'] = now();

            // If was paid online → refunding
            if ($order->payment_status === Order::PAYMENT_PAID && $order->payment_method !== 'cod') {
                $updateData['payment_status'] = Order::PAYMENT_REFUNDING;
            }
        }

        $order->update($updateData);

        return true;
    }

    // =========================================================================
    // STATISTICS (for Admin Dashboard)
    // =========================================================================

    /**
     * Get order statistics for admin dashboard.
     */
    public function getOrderStatistics(): array
    {
        $total = Order::count();
        $pending = Order::where('shipping_status', Order::STATUS_PENDING)->count();
        $processing = Order::where('shipping_status', Order::STATUS_PROCESSING)->count();
        $shipping = Order::where('shipping_status', Order::STATUS_SHIPPING)->count();
        $delivered = Order::where('shipping_status', Order::STATUS_DELIVERED)->count();
        $cancelled = Order::where('shipping_status', Order::STATUS_CANCELLED)->count();

        $totalRevenue = Order::where('shipping_status', Order::STATUS_DELIVERED)
            ->sum('total_amount');

        $todayOrders = Order::whereDate('created_at', today())->count();
        $todayRevenue = Order::where('shipping_status', Order::STATUS_DELIVERED)
            ->whereDate('created_at', today())
            ->sum('total_amount');

        return [
            'total'         => $total,
            'pending'       => $pending,
            'processing'    => $processing,
            'shipping'      => $shipping,
            'delivered'     => $delivered,
            'cancelled'     => $cancelled,
            'total_revenue' => $totalRevenue,
            'today_orders'  => $todayOrders,
            'today_revenue' => $todayRevenue,
        ];
    }
}
