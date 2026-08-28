<?php

namespace Tests\Feature;

use App\Models\Address;
use App\Models\CartItem;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CartAndCheckoutTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    public function test_can_add_product_to_cart_and_update_quantity(): void
    {
        $category = Category::create([
            'name' => 'Túi Đeo Chéo',
            'slug' => 'tui-deo-cheo',
        ]);

        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'Túi Da Thật Aurelia Classic',
            'slug' => 'tui-da-that-aurelia-classic',
            'price' => 650000,
            'sale_price' => 550000,
            'stock' => 15,
            'is_active' => true,
        ]);

        $user = User::factory()->create();

        // 1. Add to cart as authenticated user
        $response = $this->actingAs($user)->postJson(route('cart.add'), [
            'product_id' => $product->id,
            'quantity' => 2,
        ]);

        $response->assertOk()
            ->assertJson([
                'success' => true,
                'cart_count' => 2,
            ]);

        $this->assertDatabaseHas('cart_items', [
            'user_id' => $user->id,
            'product_id' => $product->id,
            'quantity' => 2,
        ]);

        $cartItem = CartItem::where('user_id', $user->id)->first();

        // 2. Update quantity in cart
        $updateResponse = $this->actingAs($user)->putJson(route('cart.update', $cartItem->id), [
            'quantity' => 3,
        ]);

        $updateResponse->assertOk()
            ->assertJson([
                'success' => true,
                'cart_count' => 3,
            ]);

        $this->assertDatabaseHas('cart_items', [
            'id' => $cartItem->id,
            'quantity' => 3,
        ]);
    }

    public function test_can_manage_user_addresses_and_complete_checkout(): void
    {
        $category = Category::create([
            'name' => 'Túi Xách Tay',
            'slug' => 'tui-xach-tay',
        ]);

        $product1 = Product::create([
            'category_id' => $category->id,
            'name' => 'Túi Da Cá Sấu Aurelia Elegance',
            'slug' => 'tui-da-ca-sau-aurelia-elegance',
            'price' => 1200000,
            'sale_price' => 990000,
            'stock' => 10,
            'is_active' => true,
        ]);

        $product2 = Product::create([
            'category_id' => $category->id,
            'name' => 'Ví Nữ Da Bò Cao Cấp',
            'slug' => 'vi-nu-da-bo-cao-cap',
            'price' => 350000,
            'stock' => 20,
            'is_active' => true,
        ]);

        $user = User::factory()->create();

        // 1. Add addresses for user
        $addrResponse = $this->actingAs($user)->postJson(route('addresses.store'), [
            'recipient_name' => 'Nguyễn Thị Hương',
            'phone' => '0987654321',
            'province' => 'TP. Hồ Chí Minh',
            'district' => 'Quận 1',
            'ward' => 'Phường Bến Nghé',
            'specific_address' => 'Số 50 Đường Đồng Khởi',
            'address_type' => 'home',
            'is_default' => 1,
        ]);

        $addrResponse->assertOk()->assertJson(['success' => true]);

        $this->assertDatabaseHas('addresses', [
            'user_id' => $user->id,
            'recipient_name' => 'Nguyễn Thị Hương',
            'phone' => '0987654321',
            'is_default' => 1,
        ]);

        // 2. Add both products to cart
        $cartItem1 = CartItem::create([
            'user_id' => $user->id,
            'product_id' => $product1->id,
            'quantity' => 1,
        ]);

        $cartItem2 = CartItem::create([
            'user_id' => $user->id,
            'product_id' => $product2->id,
            'quantity' => 2,
        ]);

        // 3. Checkout selecting only product 1 (subtotal = 990,000 >= 500,000 -> Free shipping)
        $checkoutResponse = $this->actingAs($user)->post(route('checkout.process'), [
            'selected_items' => [$cartItem1->id],
            'recipient_name' => 'Nguyễn Thị Hương',
            'phone' => '0987654321',
            'shipping_address' => 'Số 50 Đường Đồng Khởi, Phường Bến Nghé, Quận 1, TP. Hồ Chí Minh',
            'shipping_method' => 'standard',
            'payment_method' => 'cod',
            'notes' => 'Giao trong giờ hành chính',
        ]);

        $checkoutResponse->assertRedirect();

        // 4. Assert Order was created with correct totals
        $order = Order::where('user_id', $user->id)->first();
        $this->assertNotNull($order);
        $this->assertEquals(990000, (float) $order->subtotal);
        $this->assertEquals(0, (float) $order->shipping_fee); // Freeship for >= 500k
        $this->assertEquals(990000, (float) $order->total_amount);
        $this->assertEquals('cod', $order->payment_method);
        $this->assertEquals('pending', $order->shipping_status);

        // 5. Assert Inventory Stock was decremented (10 -> 9)
        $this->assertEquals(9, $product1->fresh()->stock);

        // 6. Assert cartItem1 was deleted from cart, but unselected cartItem2 remains in cart
        $this->assertDatabaseMissing('cart_items', ['id' => $cartItem1->id]);
        $this->assertDatabaseHas('cart_items', ['id' => $cartItem2->id]);
    }
}
