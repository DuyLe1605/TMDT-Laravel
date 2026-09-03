<?php

namespace Database\Seeders;

use App\Models\Brand;
use Illuminate\Database\Seeder;

class BrandSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $brands = [
            [
                'name' => 'Hermès',
                'slug' => 'hermes',
                'logo' => 'https://images.unsplash.com/photo-1548036328-c9fa89d128fa?auto=format&fit=crop&w=200&q=80',
                'description' => 'Thương hiệu thời trang xa xỉ hàng đầu từ Pháp với nghệ thuật chế tác đồ da thủ công tinh xảo bậc nhất thế giới.',
                'website' => 'https://www.hermes.com',
                'is_active' => true,
            ],
            [
                'name' => 'Chanel',
                'slug' => 'chanel',
                'logo' => 'https://images.unsplash.com/photo-1590874103328-eac38a683ce7?auto=format&fit=crop&w=200&q=80',
                'description' => 'Biểu tượng kinh điển của sự thanh lịch vượt thời gian, nổi tiếng với kỹ thuật chần chỉ quả trám và dây xích bện da.',
                'website' => 'https://www.chanel.com',
                'is_active' => true,
            ],
            [
                'name' => 'Gucci',
                'slug' => 'gucci',
                'logo' => 'https://images.unsplash.com/photo-1584917865442-de89df76afd3?auto=format&fit=crop&w=200&q=80',
                'description' => 'Nhà mốt sang trọng của Ý với phong cách quý phái, sáng tạo đột phá cùng họa tiết GG và khóa móng ngựa kinh điển.',
                'website' => 'https://www.gucci.com',
                'is_active' => true,
            ],
            [
                'name' => 'Dior',
                'slug' => 'dior',
                'logo' => 'https://images.unsplash.com/photo-1566150905458-1bf1fc113f0d?auto=format&fit=crop&w=200&q=80',
                'description' => 'Thương hiệu couture lừng danh với các thiết kế quý phái như Lady Dior, biểu tượng của sự nữ tính và vương giả.',
                'website' => 'https://www.dior.com',
                'is_active' => true,
            ],
            [
                'name' => 'Louis Vuitton',
                'slug' => 'louis-vuitton',
                'logo' => 'https://images.unsplash.com/photo-1544816155-12df9643f363?auto=format&fit=crop&w=200&q=80',
                'description' => 'Thương hiệu đồ da xa xỉ huyền thoại với họa tiết Monogram và Damier trứ danh trên toàn cầu.',
                'website' => 'https://www.louisvuitton.com',
                'is_active' => true,
            ],
            [
                'name' => 'Aurelia Atelier',
                'slug' => 'aurelia-atelier',
                'logo' => 'https://images.unsplash.com/photo-1575032617751-6ddec2089882?auto=format&fit=crop&w=200&q=80',
                'description' => 'Thương hiệu thiết kế túi da thủ công cao cấp độc quyền, tôn vinh nét đẹp thanh lịch và cá tính của phụ nữ hiện đại.',
                'website' => 'https://aurelia.vn',
                'is_active' => true,
            ],
        ];

        foreach ($brands as $brand) {
            Brand::updateOrCreate(['slug' => $brand['slug']], $brand);
        }
    }
}
