<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductCrudTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Seed categories
        $this->seed(\Database\Seeders\CategorySeeder::class);
    }

    public function test_can_view_products_index_page(): void
    {
        $category = Category::first();
        Product::create([
            'category_id' => $category->id,
            'name' => 'Túi Đeo Chéo Nữ Mini',
            'price' => 500000,
            'stock' => 20,
        ]);

        $response = $this->get(route('products.index'));

        $response->assertStatus(200);
        $response->assertSee('Túi Đeo Chéo Nữ Mini');
        $response->assertSee('500.000 ₫');
    }

    public function test_can_view_create_product_page(): void
    {
        $response = $this->get(route('products.create'));

        $response->assertStatus(200);
        $response->assertSee('Thêm Sản Phẩm Túi Xách Mới');
    }

    public function test_can_create_new_product(): void
    {
        $category = Category::first();

        $response = $this->post(route('products.store'), [
            'category_id' => $category->id,
            'name' => 'Túi Xách Nữ Công Sở Aurelia',
            'price' => 850000,
            'sale_price' => 690000,
            'stock' => 15,
            'material' => 'Da bò tự nhiên',
            'dimensions' => '24 x 16 x 8 cm',
            'color' => 'Nâu Caramel',
            'description' => 'Túi xách cao cấp cho nàng công sở',
            'is_featured' => 1,
            'is_active' => 1,
        ]);

        $response->assertRedirect(route('products.index'));
        $this->assertDatabaseHas('products', [
            'name' => 'Túi Xách Nữ Công Sở Aurelia',
            'price' => 850000,
            'sale_price' => 690000,
            'stock' => 15,
        ]);
    }

    public function test_cannot_create_product_with_invalid_data(): void
    {
        $response = $this->post(route('products.store'), [
            'name' => '',
            'price' => -100,
            'stock' => -5,
        ]);

        $response->assertSessionHasErrors(['name', 'category_id', 'price', 'stock']);
    }

    public function test_can_view_product_detail(): void
    {
        $category = Category::first();
        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'Túi Kẹp Nách Baguette',
            'price' => 450000,
            'stock' => 10,
            'material' => 'Da PU',
        ]);

        $response = $this->get(route('products.show', $product));

        $response->assertStatus(200);
        $response->assertSee('Túi Kẹp Nách Baguette');
        $response->assertSee('450.000 ₫');
    }

    public function test_can_update_product(): void
    {
        $category = Category::first();
        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'Túi Xách Cũ',
            'price' => 300000,
            'stock' => 5,
        ]);

        $response = $this->put(route('products.update', $product), [
            'category_id' => $category->id,
            'name' => 'Túi Xách Mới Cập Nhật',
            'price' => 350000,
            'stock' => 12,
        ]);

        $response->assertRedirect(route('products.index'));
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'name' => 'Túi Xách Mới Cập Nhật',
            'price' => 350000,
            'stock' => 12,
        ]);
    }

    public function test_can_delete_product(): void
    {
        $category = Category::first();
        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'Túi Cần Xóa',
            'price' => 200000,
            'stock' => 1,
        ]);

        $response = $this->delete(route('products.destroy', $product));

        $response->assertRedirect(route('products.index'));
        $this->assertDatabaseMissing('products', [
            'id' => $product->id,
        ]);
    }
}
