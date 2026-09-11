<?php

namespace Database\Seeders;

use App\Models\GiftOption;
use Illuminate\Database\Seeder;

class GiftOptionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $options = [
            // Wrapping Papers
            [
                'type' => 'paper',
                'name' => 'Nhung Đỏ Royal Velvet & Nơ Gold',
                'code' => 'paper_royal_burgundy',
                'image' => '#881337',
                'price' => 35000,
                'description' => 'Chất liệu giấy nhung dập vân chìm sang trọng kèm nơ lụa vàng Gold',
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'type' => 'paper',
                'name' => 'Xanh Midnight & Ép Kim Vàng',
                'code' => 'paper_midnight_gold',
                'image' => '#1e1b4b',
                'price' => 35000,
                'description' => 'Họa tiết ánh kim vàng đồng trên nền xanh bóng đêm quý phái',
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'type' => 'paper',
                'name' => 'Lụa Trắng Classic Ivory & Ruy Băng Đen',
                'code' => 'paper_classic_ivory',
                'image' => '#fef3c7',
                'price' => 35000,
                'description' => 'Phong cách tối giản thanh lịch chuẩn Pháp với ruy băng đen satin',
                'is_active' => true,
                'sort_order' => 3,
            ],
            [
                'type' => 'paper',
                'name' => 'Hồng Pastel Sweet Romance',
                'code' => 'paper_sweet_romance',
                'image' => '#fbcfe8',
                'price' => 35000,
                'description' => 'Gam màu ngọt ngào, dịu dàng dành tặng nàng',
                'is_active' => true,
                'sort_order' => 4,
            ],

            // Greeting Cards
            [
                'type' => 'card',
                'name' => 'Thiệp Chúc Mừng Sinh Nhật (Happy Birthday)',
                'code' => 'card_birthday',
                'image' => '🎂',
                'price' => 0,
                'description' => 'Thiệp chúc mừng tuổi mới luôn rạng ngời và hạnh phúc',
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'type' => 'card',
                'name' => 'Thiệp Tình Yêu & Kỷ Niệm (Sweet Love)',
                'code' => 'card_love',
                'image' => '💖',
                'price' => 0,
                'description' => 'Dành tặng ngày kỷ niệm, Valentine và những dịp hẹn hò đặc biệt',
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'type' => 'card',
                'name' => 'Thiệp Tri Ân & Cảm Ơn (Thank You)',
                'code' => 'card_thank_you',
                'image' => '🌸',
                'price' => 0,
                'description' => 'Lời cảm ơn sâu sắc gửi đến mẹ, đồng nghiệp, đối tác hay bạn tri kỷ',
                'is_active' => true,
                'sort_order' => 3,
            ],
            [
                'type' => 'card',
                'name' => 'Thiệp Aurelia Luxury Signature (Trang Nhã)',
                'code' => 'card_signature',
                'image' => '👑',
                'price' => 0,
                'description' => 'Thiệp dập nổi logo Aurelia Bags mạ vàng thanh lịch cho mọi dịp',
                'is_active' => true,
                'sort_order' => 4,
            ],
        ];

        foreach ($options as $item) {
            GiftOption::updateOrCreate(
                ['code' => $item['code']],
                $item
            );
        }
    }
}
