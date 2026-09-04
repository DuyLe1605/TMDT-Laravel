<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Order;
use App\Models\Product;

$u2 = User::find(2);
if ($u2) {
    // Check if user 2 has a delivered order
    $hasDelivered = Order::where('user_id', $u2->id)->where('shipping_status', 'delivered')->exists();
    if (!$hasDelivered) {
        $p = Product::with('variants')->first();
        $v = $p->variants->first();
        $order = Order::create([
            'order_code' => 'AUR-' . strtoupper(bin2hex(random_bytes(3))) . '-DLV',
            'user_id' => $u2->id,
            'recipient_name' => $u2->name,
            'phone' => '0987654321',
            'shipping_address' => 'Số 123 Lê Lợi, Phường Bến Thành, Quận 1, TP. Hồ Chí Minh',
            'payment_method' => 'cod',
            'payment_status' => 'paid',
            'paid_at' => now(),
            'shipping_method' => 'ghn_standard',
            'shipping_fee' => 30000,
            'shipping_status' => 'delivered',
            'delivered_at' => now()->subDay(),
            'subtotal' => $p->price,
            'total_amount' => $p->price + 30000,
        ]);
        $order->items()->create([
            'product_id' => $p->id,
            'product_variant_id' => $v?->id,
            'product_name' => $p->name,
            'variant_title' => $v?->title ?? 'Tiêu chuẩn',
            'product_image' => $p->featured_image,
            'price' => $p->price,
            'quantity' => 1,
            'subtotal' => $p->price,
        ]);
        echo "Successfully created delivered test order {$order->order_code} for user 2 ({$u2->email})\n";
    } else {
        echo "User 2 already has delivered order\n";
    }
}
