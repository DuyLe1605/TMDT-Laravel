<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Root Category: Túi Xách Nữ
        $rootBags = Category::updateOrCreate(
            ['slug' => 'tui-xach-nu'],
            [
                'name' => 'Túi Xách Nữ',
                'parent_id' => null,
                'description' => 'Bộ sưu tập túi xách thời trang cao cấp từ các nhà mốt danh tiếng hàng đầu thế giới.',
                'image' => 'https://images.unsplash.com/photo-1584917865442-de89df76afd3?auto=format&fit=crop&w=400&q=80',
                'is_active' => true,
            ]
        );

        $bagsChildren = [
            ['name' => 'Túi Đeo Chéo', 'slug' => 'tui-deo-cheo', 'description' => 'Năng động, tiện dụng và thanh lịch cho mọi dịp ra ngoài.'],
            ['name' => 'Túi Xách Tay Công Sở', 'slug' => 'tui-xach-tay-cong-so', 'description' => 'Phom dáng chuẩn mực, đẳng cấp chuyên nghiệp cho quý cô công sở.'],
            ['name' => 'Túi Tote Đa Năng', 'slug' => 'tui-tote-da-nang', 'description' => 'Sức chứa rộng lớn, đựng vừa laptop và tài liệu.'],
            ['name' => 'Túi Kẹp Nách Thời Thượng', 'slug' => 'tui-kep-nach-thoi-thuong', 'description' => 'Xu hướng Baguette Y2K quyến rũ và phá cách.'],
        ];

        foreach ($bagsChildren as $child) {
            Category::updateOrCreate(
                ['slug' => $child['slug']],
                array_merge($child, ['parent_id' => $rootBags->id, 'is_active' => true])
            );
        }

        // 2. Root Category: Bóp & Ví
        $rootWallets = Category::updateOrCreate(
            ['slug' => 'bop-va-vi-da'],
            [
                'name' => 'Bóp & Ví Da Nữ',
                'parent_id' => null,
                'description' => 'Ví cầm tay, clutch dạ tiệc sang trọng chế tác từ da thượng hạng.',
                'image' => 'https://images.unsplash.com/photo-1566150902887-9679ec15d409?auto=format&fit=crop&w=400&q=80',
                'is_active' => true,
            ]
        );

        $walletsChildren = [
            ['name' => 'Ví Cầm Tay & Clutch Dạ Tiệc', 'slug' => 'vi-cam-tay-clutch-da-tiec', 'description' => 'Phụ kiện lung linh cho sự kiện dạ hội và tiệc cưới.'],
            ['name' => 'Ví Ngắn Gấp Gọn', 'slug' => 'vi-ngan-gap-gon', 'description' => 'Nhỏ gọn, tiện lợi bỏ vào túi xách hàng ngày.'],
        ];

        foreach ($walletsChildren as $child) {
            Category::updateOrCreate(
                ['slug' => $child['slug']],
                array_merge($child, ['parent_id' => $rootWallets->id, 'is_active' => true])
            );
        }

        // 3. Root Category: Balo Mini
        $rootBackpacks = Category::updateOrCreate(
            ['slug' => 'balo-mini-nu'],
            [
                'name' => 'Balo Mini Nữ',
                'parent_id' => null,
                'description' => 'Balo mini phong cách trẻ trung, năng động.',
                'image' => 'https://images.unsplash.com/photo-1553062407-98eeb64c6a62?auto=format&fit=crop&w=400&q=80',
                'is_active' => true,
            ]
        );

        Category::updateOrCreate(
            ['slug' => 'balo-mini-nu-thoi-trang'],
            [
                'name' => 'Balo Mini Nữ Thời Trang',
                'parent_id' => $rootBackpacks->id,
                'description' => 'Vừa đeo balo vừa đeo chéo phong cách Hàn Quốc.',
                'is_active' => true,
            ]
        );
    }
}
