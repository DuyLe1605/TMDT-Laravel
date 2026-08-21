<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $crossbody = Category::where('name', 'Túi Đeo Chéo')->first();
        $handbag = Category::where('name', 'Túi Xách Tay Công Sở')->first();
        $tote = Category::where('name', 'Túi Tote Đa Năng')->first();
        $shoulder = Category::where('name', 'Túi Kẹp Nách Thời Thượng')->first();
        $clutch = Category::where('name', 'Ví Cầm Tay & Clutch Dạ Tiệc')->first();
        $backpack = Category::where('name', 'Balo Mini Nữ Thời Trang')->first();

        $products = [
            // 1. Túi Đeo Chéo
            [
                'category_id' => $crossbody?->id ?? 1,
                'name' => 'Túi Đeo Chéo Nữ Da Bò Cao Cấp Aurelia Box',
                'slug' => 'tui-deo-cheo-nu-da-bo-cao-cap-aurelia-box',
                'price' => 750000,
                'sale_price' => 590000,
                'stock' => 35,
                'material' => 'Da bò tự nhiên dập vân hạt',
                'dimensions' => '20 x 7 x 14 cm',
                'color' => 'Đen Obsidian',
                'description' => "Thiết kế phom hộp cứng cáp sang trọng, khóa xoay mạ vàng tĩnh điện 18k chống trầy xước. Thích hợp đi làm, dạo phố và dự tiệc nhẹ.\n- Ngăn chính rộng rãi đựng vừa iPhone Pro Max, son, phấn, ví tiền mini.\n- Dây đeo da phối xích kim loại có thể điều chỉnh độ dài linh hoạt.",
                'image' => 'https://images.unsplash.com/photo-1548036328-c9fa89d128fa?auto=format&fit=crop&w=800&q=80',
                'is_featured' => true,
                'is_active' => true,
            ],
            [
                'category_id' => $crossbody?->id ?? 1,
                'name' => 'Túi Đeo Chéo Nữ Quả Trám Classic Chain',
                'slug' => 'tui-deo-cheo-nu-qua-tram-classic-chain',
                'price' => 620000,
                'sale_price' => 480000,
                'stock' => 48,
                'material' => 'Da PU tráng gương chống thấm nước',
                'dimensions' => '22 x 6 x 15 cm',
                'color' => 'Trắng Kem (Ivory)',
                'description' => "Đường may chần chỉ quả trám kinh điển phong cách Pháp, mang đến vẻ đẹp thanh lịch quý phái cho mọi quý cô.",
                'image' => 'https://images.unsplash.com/photo-1590874103328-eac38a683ce7?auto=format&fit=crop&w=800&q=80',
                'is_featured' => true,
                'is_active' => true,
            ],

            // 2. Túi Xách Tay Công Sở
            [
                'category_id' => $handbag?->id ?? 2,
                'name' => 'Túi Xách Tay Nữ Công Sở Vân Da Cá Sấu Eleanor Satchel',
                'slug' => 'tui-xach-tay-nu-cong-so-van-da-ca-sau-eleanor-satchel',
                'price' => 1250000,
                'sale_price' => 990000,
                'stock' => 20,
                'material' => 'Da tổng hợp dập vân cá sấu cao cấp',
                'dimensions' => '28 x 11 x 20 cm',
                'color' => 'Nâu Caramel Vintage',
                'description' => "Phom dáng Satchel đứng chuẩn công sở, đựng vừa iPad 11 inch, sổ tay và đồ cá nhân cần thiết. Đáy túi có 4 chân đế kim loại bảo vệ.",
                'image' => 'https://images.unsplash.com/photo-1584917865442-de89df76afd3?auto=format&fit=crop&w=800&q=80',
                'is_featured' => true,
                'is_active' => true,
            ],
            [
                'category_id' => $handbag?->id ?? 2,
                'name' => 'Túi Xách Nữ Quai Tròn Mini Kelly Style',
                'slug' => 'tui-xach-nu-quai-tron-mini-kelly-style',
                'price' => 890000,
                'sale_price' => null,
                'stock' => 15,
                'material' => 'Da bò Epsom cao cấp',
                'dimensions' => '21 x 9 x 16 cm',
                'color' => 'Xanh Emerald (Ngọc lục bảo)',
                'description' => "Thiết kế quý phái, quai xách tròn êm ái kèm khăn lụa nơ thời trang. Thể hiện đẳng cấp và gu thẩm mỹ tinh tế.",
                'image' => 'https://images.unsplash.com/photo-1566150905458-1bf1fc113f0d?auto=format&fit=crop&w=800&q=80',
                'is_featured' => false,
                'is_active' => true,
            ],

            // 3. Túi Tote Đa Năng
            [
                'category_id' => $tote?->id ?? 3,
                'name' => 'Túi Tote Da Mềm Đựng Laptop 14 Inch Minimalist',
                'slug' => 'tui-tote-da-mem-dung-laptop-14-inch-minimalist',
                'price' => 690000,
                'sale_price' => 550000,
                'stock' => 60,
                'material' => 'Da Microfiber siêu nhẹ kháng nước',
                'dimensions' => '36 x 12 x 29 cm',
                'color' => 'Đen Midnight',
                'description' => "Trọng lượng chỉ 480g, sức chứa cực lớn vừa laptop 14 inch, tài liệu A4, bình nước và mỹ phẩm. Lựa chọn số 1 cho nàng công sở và sinh viên.",
                'image' => 'https://images.unsplash.com/photo-1544816155-12df9643f363?auto=format&fit=crop&w=800&q=80',
                'is_featured' => true,
                'is_active' => true,
            ],
            [
                'category_id' => $tote?->id ?? 3,
                'name' => 'Túi Tote Vải Canvas Phối Da Canvas Chic',
                'slug' => 'tui-tote-vai-canvas-phoi-da-canvas-chic',
                'price' => 450000,
                'sale_price' => 380000,
                'stock' => 42,
                'material' => 'Vải Canvas dệt dày phối quai da bò',
                'dimensions' => '32 x 10 x 26 cm',
                'color' => 'Beige Phối Nâu',
                'description' => "Phong cách thanh lịch Hàn Quốc, nhẹ nhàng nhưng cực kỳ tiện dụng cho các buổi cafe, dã ngoại cuối tuần.",
                'image' => 'https://images.unsplash.com/photo-1591561954557-26941169b49e?auto=format&fit=crop&w=800&q=80',
                'is_featured' => false,
                'is_active' => true,
            ],

            // 4. Túi Kẹp Nách Thời Thượng
            [
                'category_id' => $shoulder?->id ?? 4,
                'name' => 'Túi Kẹp Nách Baguette Y2K Da Bóng Trendy',
                'slug' => 'tui-kep-nach-baguette-y2k-da-bong-trendy',
                'price' => 520000,
                'sale_price' => 420000,
                'stock' => 28,
                'material' => 'Da bóng phủ Nano chống xước',
                'dimensions' => '24 x 6 x 13 cm',
                'color' => 'Bạc Metalic (Ánh kim)',
                'description' => "Item 'must-have' của mọi cô nàng thời thượng, bắt trọn xu hướng thời trang Y2K đang làm mưa làm gió.",
                'image' => 'https://images.unsplash.com/photo-1575032617751-6ddec2089882?auto=format&fit=crop&w=800&q=80',
                'is_featured' => true,
                'is_active' => true,
            ],

            // 5. Ví Cầm Tay & Clutch Dạ Tiệc
            [
                'category_id' => $clutch?->id ?? 5,
                'name' => 'Clutch Dạ Tiệc Đính Đá Pha Lê Sparkling Luxe',
                'slug' => 'clutch-da-tiec-dinh-da-pha-le-sparkling-luxe',
                'price' => 1450000,
                'sale_price' => 1190000,
                'stock' => 12,
                'material' => 'Khung kim loại mạ vàng đính đá Swarovski nhân tạo',
                'dimensions' => '19 x 5 x 11 cm',
                'color' => 'Gold Ánh Kim',
                'description' => "Phụ kiện lộng lẫy dành riêng cho các buổi dạ tiệc cưới, sự kiện thảm đỏ và dạ hội sang trọng.",
                'image' => 'https://images.unsplash.com/photo-1566150902887-9679ec15d409?auto=format&fit=crop&w=800&q=80',
                'is_featured' => true,
                'is_active' => true,
            ],

            // 6. Balo Mini Nữ Thời Trang
            [
                'category_id' => $backpack?->id ?? 6,
                'name' => 'Balo Mini Nữ Da Chần Bông Năng Động City Girl',
                'slug' => 'balo-mini-nu-da-chan-bong-nang-dong-city-girl',
                'price' => 680000,
                'sale_price' => 540000,
                'stock' => 30,
                'material' => 'Da PU mềm mại chần bông',
                'dimensions' => '22 x 10 x 25 cm',
                'color' => 'Hồng Pastel (Dusty Rose)',
                'description' => "Nhỏ gọn, năng động, quai đeo 2 kiểu: vừa làm balo vừa biến hóa thành túi đeo chéo cá tính.",
                'image' => 'https://images.unsplash.com/photo-1553062407-98eeb64c6a62?auto=format&fit=crop&w=800&q=80',
                'is_featured' => false,
                'is_active' => true,
            ],
        ];

        foreach ($products as $item) {
            Product::updateOrCreate(
                ['slug' => $item['slug']],
                $item
            );
        }
    }
}
