<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'Điện thoại & Phụ kiện'],
            ['name' => 'Máy tính & Laptop'],
            ['name' => 'Thời trang Nam'],
            ['name' => 'Thời trang Nữ'],
            ['name' => 'Thiết bị điện tử'],
            ['name' => 'Nhà cửa & Đời sống'],
        ];

        foreach ($categories as $cat) {
            Category::firstOrCreate(['name' => $cat['name']]);
        }
    }
}
