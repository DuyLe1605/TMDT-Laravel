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
            ['name' => 'Túi Đeo Chéo'],
            ['name' => 'Túi Xách Tay Công Sở'],
            ['name' => 'Túi Tote Đa Năng'],
            ['name' => 'Túi Kẹp Nách Thời Thượng'],
            ['name' => 'Ví Cầm Tay & Clutch Dạ Tiệc'],
            ['name' => 'Balo Mini Nữ Thời Trang'],
        ];

        foreach ($categories as $category) {
            Category::firstOrCreate(['name' => $category['name']], $category);
        }
    }
}
