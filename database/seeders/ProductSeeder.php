<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductAttribute;
use App\Models\ProductAttributeValue;
use App\Models\ProductVariant;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $hermes = Brand::where('slug', 'hermes')->first();
        $chanel = Brand::where('slug', 'chanel')->first();
        $gucci = Brand::where('slug', 'gucci')->first();
        $dior = Brand::where('slug', 'dior')->first();
        $lv = Brand::where('slug', 'louis-vuitton')->first();
        $aurelia = Brand::where('slug', 'aurelia-atelier')->first();

        $crossbody = Category::where('slug', 'tui-deo-cheo')->first() ?? Category::first();
        $handbag = Category::where('slug', 'tui-xach-tay-cong-so')->first() ?? Category::first();
        $tote = Category::where('slug', 'tui-tote-da-nang')->first() ?? Category::first();
        $shoulder = Category::where('slug', 'tui-kep-nach-thoi-thuong')->first() ?? Category::first();
        $clutch = Category::where('slug', 'vi-cam-tay-clutch-da-tiec')->first() ?? Category::first();
        $backpack = Category::where('slug', 'balo-mini-nu-thoi-trang')->first() ?? Category::first();

        $productsData = [
            // 1. Túi Hermes Birkin Style có BIẾN THỂ THÔNG MINH ĐẦY ĐỦ
            [
                'brand_id' => $hermes?->id,
                'category_id' => $crossbody?->id,
                'name' => 'Túi Xách Nữ Hermès Constance Box Calfkin',
                'slug' => 'tui-xach-nu-hermes-constance-box-calfkin',
                'sku' => 'HER-CONSTANCE-BOX',
                'price' => 750000,
                'sale_price' => 590000,
                'has_variants' => true,
                'stock' => 145,
                'material' => 'Da bò tự nhiên / Da PU',
                'dimensions' => '20 x 7 x 14 cm',
                'color' => 'Cam Hermès, Đen Obsidian, Trắng Kem',
                'description' => "Mẫu túi Hermès Constance kinh điển với khóa chữ H mạ vàng 18K tinh tế. Khách hàng có thể lựa chọn chất liệu Da thật Epsom hoặc Da PU cao cấp tùy nhu cầu.\n- Ngăn chính rộng rãi, dây đeo chéo da có thể điều chỉnh độ dài.\n- Phù hợp đi làm, dạo phố và các buổi gặp mặt cao cấp.",
                'image' => 'https://images.unsplash.com/photo-1548036328-c9fa89d128fa?auto=format&fit=crop&w=800&q=80',
                'is_featured' => true,
                'is_active' => true,
                'attributes_config' => [
                    [
                        'name' => 'Chất liệu',
                        'position' => 1,
                        'values' => ['Da thật Epsom', 'Da PU cao cấp']
                    ],
                    [
                        'name' => 'Màu sắc',
                        'position' => 2,
                        'values' => ['Cam Hermès', 'Đen Obsidian', 'Trắng Kem']
                    ],
                ],
                'variants_config' => [
                    [
                        'variant_title' => 'Da thật Epsom / Cam Hermès',
                        'option1_value' => 'Da thật Epsom',
                        'option2_value' => 'Cam Hermès',
                        'sku' => 'HER-CONST-REAL-ORG',
                        'price' => 1450000,
                        'sale_price' => 1190000,
                        'stock' => 25,
                        'image' => 'https://images.unsplash.com/photo-1548036328-c9fa89d128fa?auto=format&fit=crop&w=800&q=80',
                    ],
                    [
                        'variant_title' => 'Da thật Epsom / Đen Obsidian',
                        'option1_value' => 'Da thật Epsom',
                        'option2_value' => 'Đen Obsidian',
                        'sku' => 'HER-CONST-REAL-BLK',
                        'price' => 1450000,
                        'sale_price' => 1190000,
                        'stock' => 30,
                        'image' => 'https://images.unsplash.com/photo-1584917865442-de89df76afd3?auto=format&fit=crop&w=800&q=80',
                    ],
                    [
                        'variant_title' => 'Da thật Epsom / Trắng Kem',
                        'option1_value' => 'Da thật Epsom',
                        'option2_value' => 'Trắng Kem',
                        'sku' => 'HER-CONST-REAL-WHT',
                        'price' => 1450000,
                        'sale_price' => null,
                        'stock' => 15,
                        'image' => 'https://images.unsplash.com/photo-1590874103328-eac38a683ce7?auto=format&fit=crop&w=800&q=80',
                    ],
                    [
                        'variant_title' => 'Da PU cao cấp / Cam Hermès',
                        'option1_value' => 'Da PU cao cấp',
                        'option2_value' => 'Cam Hermès',
                        'sku' => 'HER-CONST-PU-ORG',
                        'price' => 750000,
                        'sale_price' => 590000,
                        'stock' => 35,
                        'image' => 'https://images.unsplash.com/photo-1548036328-c9fa89d128fa?auto=format&fit=crop&w=800&q=80',
                    ],
                    [
                        'variant_title' => 'Da PU cao cấp / Đen Obsidian',
                        'option1_value' => 'Da PU cao cấp',
                        'option2_value' => 'Đen Obsidian',
                        'sku' => 'HER-CONST-PU-BLK',
                        'price' => 750000,
                        'sale_price' => 590000,
                        'stock' => 40,
                        'image' => 'https://images.unsplash.com/photo-1584917865442-de89df76afd3?auto=format&fit=crop&w=800&q=80',
                    ],
                    [
                        'variant_title' => 'Da PU cao cấp / Trắng Kem',
                        'option1_value' => 'Da PU cao cấp',
                        'option2_value' => 'Trắng Kem',
                        'sku' => 'HER-CONST-PU-WHT',
                        'price' => 750000,
                        'sale_price' => 620000,
                        'stock' => 0, // Test out of stock state!
                        'image' => 'https://images.unsplash.com/photo-1590874103328-eac38a683ce7?auto=format&fit=crop&w=800&q=80',
                    ],
                ]
            ],

            // 2. Túi Chanel Classic Flap
            [
                'brand_id' => $chanel?->id,
                'category_id' => $crossbody?->id,
                'name' => 'Túi Xách Nữ Chanel Classic Flap Quả Trám',
                'slug' => 'tui-xach-nu-chanel-classic-flap-qua-tram',
                'sku' => 'CHA-CLASSIC-FLAP',
                'price' => 890000,
                'sale_price' => 690000,
                'has_variants' => true,
                'stock' => 50,
                'material' => 'Da cừu Caviar chống trầy xước',
                'dimensions' => '25 x 8 x 16 cm',
                'color' => 'Đen Vàng Gold, Trắng Kem',
                'description' => "Đường may quả trám kinh điển kiểu Pháp, khóa vặn logo đôi lồng vào nhau sang trọng vượt thời gian.",
                'image' => 'https://images.unsplash.com/photo-1590874103328-eac38a683ce7?auto=format&fit=crop&w=800&q=80',
                'is_featured' => true,
                'is_active' => true,
                'attributes_config' => [
                    [
                        'name' => 'Màu sắc',
                        'position' => 1,
                        'values' => ['Đen Khóa Vàng (Gold)', 'Trắng Khóa Bạc (Silver)']
                    ],
                    [
                        'name' => 'Kích thước',
                        'position' => 2,
                        'values' => ['Size 20 Mini', 'Size 25 Medium']
                    ]
                ],
                'variants_config' => [
                    [
                        'variant_title' => 'Đen Khóa Vàng (Gold) / Size 20 Mini',
                        'option1_value' => 'Đen Khóa Vàng (Gold)',
                        'option2_value' => 'Size 20 Mini',
                        'sku' => 'CHA-CF-BLK-G-20',
                        'price' => 890000,
                        'sale_price' => 690000,
                        'stock' => 20,
                        'image' => 'https://images.unsplash.com/photo-1590874103328-eac38a683ce7?auto=format&fit=crop&w=800&q=80',
                    ],
                    [
                        'variant_title' => 'Đen Khóa Vàng (Gold) / Size 25 Medium',
                        'option1_value' => 'Đen Khóa Vàng (Gold)',
                        'option2_value' => 'Size 25 Medium',
                        'sku' => 'CHA-CF-BLK-G-25',
                        'price' => 1150000,
                        'sale_price' => 950000,
                        'stock' => 15,
                        'image' => 'https://images.unsplash.com/photo-1590874103328-eac38a683ce7?auto=format&fit=crop&w=800&q=80',
                    ],
                    [
                        'variant_title' => 'Trắng Khóa Bạc (Silver) / Size 20 Mini',
                        'option1_value' => 'Trắng Khóa Bạc (Silver)',
                        'option2_value' => 'Size 20 Mini',
                        'sku' => 'CHA-CF-WHT-S-20',
                        'price' => 890000,
                        'sale_price' => 720000,
                        'stock' => 15,
                        'image' => 'https://images.unsplash.com/photo-1590874103328-eac38a683ce7?auto=format&fit=crop&w=800&q=80',
                    ],
                ]
            ],

            // 3. Lady Dior
            [
                'brand_id' => $dior?->id,
                'category_id' => $handbag?->id,
                'name' => 'Túi Xách Tay Nữ Lady Dior My ABCDior Đính Charm',
                'slug' => 'tui-xach-tay-nu-lady-dior-my-abcdior-dinh-charm',
                'sku' => 'DIO-LADY-ABC',
                'price' => 1350000,
                'sale_price' => 1090000,
                'has_variants' => false,
                'stock' => 20,
                'material' => 'Da cừu Cannage dập nổi',
                'dimensions' => '20 x 8 x 17 cm',
                'color' => 'Hồng Lotus (Dusty Pink)',
                'description' => "Thiết kế hoàng gia gắn liền với Công nương Diana, quai cầm tròn và bộ chữ charm D-I-O-R mạ vàng sang trọng.",
                'image' => 'https://images.unsplash.com/photo-1566150905458-1bf1fc113f0d?auto=format&fit=crop&w=800&q=80',
                'is_featured' => true,
                'is_active' => true,
            ],

            // 4. Gucci Tote
            [
                'brand_id' => $gucci?->id,
                'category_id' => $tote?->id,
                'name' => 'Túi Tote Nữ Gucci Ophidia GG Supreme Laptop 14 Inch',
                'slug' => 'tui-tote-nu-gucci-ophidia-gg-supreme-laptop-14-inch',
                'sku' => 'GUC-OPH-TOTE',
                'price' => 790000,
                'sale_price' => 650000,
                'has_variants' => false,
                'stock' => 45,
                'material' => 'Canvas phủ da bò viền nâu',
                'dimensions' => '38 x 13 x 30 cm',
                'color' => 'Họa tiết GG Supreme Nâu Be',
                'description' => "Họa tiết monogram GG kết hợp dải ruy băng xanh đỏ trứ danh của nhà Gucci. Đựng thoải mái laptop 14-15 inch và hồ sơ.",
                'image' => 'https://images.unsplash.com/photo-1544816155-12df9643f363?auto=format&fit=crop&w=800&q=80',
                'is_featured' => true,
                'is_active' => true,
            ],

            // 5. Louis Vuitton Baguette
            [
                'brand_id' => $lv?->id,
                'category_id' => $shoulder?->id,
                'name' => 'Túi Kẹp Nách Louis Vuitton Pochette Accessoires',
                'slug' => 'tui-kep-nach-louis-vuitton-pochette-accessoires',
                'sku' => 'LV-POCHETTE-ACC',
                'price' => 650000,
                'sale_price' => 520000,
                'has_variants' => false,
                'stock' => 28,
                'material' => 'Canvas Monogram kinh điển',
                'dimensions' => '23 x 4 x 13 cm',
                'color' => 'Nâu Monogram',
                'description' => "Chiếc túi kẹp nách được săn đón nhiều nhất mọi thời đại, phong cách retro cổ điển phù hợp mọi outfit.",
                'image' => 'https://images.unsplash.com/photo-1575032617751-6ddec2089882?auto=format&fit=crop&w=800&q=80',
                'is_featured' => true,
                'is_active' => true,
            ],

            // 6. Aurelia Atelier Clutch
            [
                'brand_id' => $aurelia?->id,
                'category_id' => $clutch?->id,
                'name' => 'Ví Cầm Tay Dạ Tiệc Aurelia Sparkling Crystal Clutch',
                'slug' => 'vi-cam-tay-da-tiec-aurelia-sparkling-crystal-clutch',
                'sku' => 'AUR-CRYSTAL-CLUTCH',
                'price' => 1550000,
                'sale_price' => 1290000,
                'has_variants' => false,
                'stock' => 12,
                'material' => 'Khung kim loại đính pha lê Swarovski',
                'dimensions' => '19 x 5 x 11 cm',
                'color' => 'Gold Ánh Kim',
                'description' => "Chế tác thủ công đính kết từng viên pha lê cao cấp, tạo hiệu ứng tỏa sáng rực rỡ dưới ánh đèn dạ hội.",
                'image' => 'https://images.unsplash.com/photo-1566150902887-9679ec15d409?auto=format&fit=crop&w=800&q=80',
                'is_featured' => true,
                'is_active' => true,
            ],
        ];

        foreach ($productsData as $data) {
            $attrsConfig = $data['attributes_config'] ?? null;
            $variantsConfig = $data['variants_config'] ?? null;
            unset($data['attributes_config'], $data['variants_config']);

            $product = Product::updateOrCreate(
                ['slug' => $data['slug']],
                $data
            );

            // Xử lý tạo Attributes và Variants nếu có
            if ($attrsConfig && $variantsConfig) {
                // Xóa cũ để re-seed sạch
                $product->attributes()->delete();
                $product->variants()->delete();

                $createdValues = [];

                foreach ($attrsConfig as $attr) {
                    $attribute = ProductAttribute::create([
                        'product_id' => $product->id,
                        'name' => $attr['name'],
                        'position' => $attr['position'],
                    ]);

                    foreach ($attr['values'] as $val) {
                        $valueModel = ProductAttributeValue::create([
                            'product_attribute_id' => $attribute->id,
                            'value' => $val,
                        ]);
                        $createdValues[$attr['name']][$val] = $valueModel->id;
                    }
                }

                foreach ($variantsConfig as $variantItem) {
                    $variant = ProductVariant::create([
                        'product_id' => $product->id,
                        'variant_title' => $variantItem['variant_title'],
                        'sku' => $variantItem['sku'],
                        'price' => $variantItem['price'],
                        'sale_price' => $variantItem['sale_price'],
                        'stock' => $variantItem['stock'],
                        'image' => $variantItem['image'],
                        'option1_value' => $variantItem['option1_value'],
                        'option2_value' => $variantItem['option2_value'] ?? null,
                        'is_active' => true,
                    ]);

                    // Attach pivot values
                    $pivotIds = [];
                    if (!empty($variantItem['option1_value'])) {
                        // find matching attr value
                        foreach ($createdValues as $groupName => $valMap) {
                            if (isset($valMap[$variantItem['option1_value']])) {
                                $pivotIds[] = $valMap[$variantItem['option1_value']];
                            }
                        }
                    }
                    if (!empty($variantItem['option2_value'])) {
                        foreach ($createdValues as $groupName => $valMap) {
                            if (isset($valMap[$variantItem['option2_value']])) {
                                $pivotIds[] = $valMap[$variantItem['option2_value']];
                            }
                        }
                    }

                    if (!empty($pivotIds)) {
                        $variant->attributeValues()->sync($pivotIds);
                    }
                }
            }
        }
    }
}
