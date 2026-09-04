<?php

namespace Database\Seeders;

use App\Models\CoinTransaction;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Review;
use App\Models\ReviewImage;
use App\Models\User;
use App\Services\CoinService;
use Illuminate\Database\Seeder;

class ReviewAndCoinSeeder extends Seeder
{
    public function run(): void
    {
        $coinService = app(CoinService::class);

        // Lấy danh sách khách hàng mẫu
        $users = User::where('role', 'customer')->get();
        if ($users->isEmpty()) {
            $this->command->info('Không tìm thấy khách hàng nào. Vui lòng chạy SampleDataSeeder trước.');
            return;
        }

        // Lấy danh sách các đơn hàng đã giao thành công (delivered)
        $deliveredOrders = Order::where('shipping_status', Order::STATUS_DELIVERED)->with('items.product')->get();

        if ($deliveredOrders->isEmpty()) {
            $this->command->info('Chưa có đơn hàng delivered nào.');
            return;
        }

        // Mẫu ảnh feedback thực tế túi xách
        $sampleImages = [
            'https://images.unsplash.com/photo-1584917865442-de89df76afd3?auto=format&fit=crop&w=600&q=80',
            'https://images.unsplash.com/photo-1590874103328-eac38a683ce7?auto=format&fit=crop&w=600&q=80',
            'https://images.unsplash.com/photo-1548036328-c9fa89d128fa?auto=format&fit=crop&w=600&q=80',
            'https://images.unsplash.com/photo-1591561954557-26941169b49e?auto=format&fit=crop&w=600&q=80',
            'https://images.unsplash.com/photo-1566150905458-1bf1fc113f0d?auto=format&fit=crop&w=600&q=80',
            'https://images.unsplash.com/photo-1594223274512-ad4803739b7c?auto=format&fit=crop&w=600&q=80',
        ];

        // Mẫu nhận xét đa dạng các mức sao
        $feedbackTemplates = [
            5 => [
                [
                    'comment' => 'Túi xách đẹp xuất sắc ngoài mong đợi của mình luôn! Chất da sờ rất mềm mại, thơm mùi da thuộc cao cấp. Đường kim mũi chỉ tỉ mỉ, khóa mạ vàng sáng bóng không tì vết. Đóng gói hộp rất sang trọng có cả thiệp cảm ơn. Rất đáng đồng tiền bát gạo!',
                    'has_images' => true,
                    'admin_reply' => 'Aurelia Luxury xin chân thành cảm ơn bạn đã yêu thích sản phẩm và gửi gắm những lời khen tuyệt vời! Chúc bạn luôn rạng rỡ và tự tin khi diện em túi này nha!'
                ],
                [
                    'comment' => 'Giao hàng siêu nhanh, đặt hôm trước hôm sau đã nhận được qua GHN. Túi form rất đứng dáng, đựng vừa cả điện thoại Promax lẫn ví dài và mỹ phẩm. Bạn bè ai nhìn cũng khen tấm tắc. Sẽ tiếp tục ủng hộ shop!',
                    'has_images' => true,
                    'admin_reply' => 'Dạ Aurelia cảm ơn bạn nhiều ạ! Sự hài lòng của bạn là động lực to lớn nhất để shop không ngừng nâng cao chất lượng dịch vụ ạ!'
                ],
                [
                    'comment' => 'Sản phẩm quá đỉnh, chất da mềm mịn và phom túi giữ chuẩn chỉnh. Đúng chất lượng thương hiệu cao cấp.',
                    'has_images' => false,
                    'admin_reply' => 'Aurelia cảm ơn bạn đã tin tưởng và đồng hành cùng thương hiệu ạ!'
                ]
            ],
            4 => [
                [
                    'comment' => 'Túi rất đẹp, màu sắc y hình chụp trên website. Da mềm và phom dáng thanh lịch. Trừ 1 sao nhỏ vì hộp bên ngoài bị móp nhẹ do bên vận chuyển, nhưng may mắn túi bên trong bọc túi chống sốc kỹ nên không ảnh hưởng gì.',
                    'has_images' => true,
                    'admin_reply' => 'Dạ Aurelia chân thành xin lỗi bạn về sự cố móp hộp do quá trình vận chuyển. Shop sẽ làm việc lại với bên đối tác giao hàng để đảm bảo kiện hàng nguyên vẹn hơn trong các lần tới ạ!'
                ],
                [
                    'comment' => 'Chất lượng da tốt, đường may chắc chắn. Kích thước hơi nhỏ hơn mình tưởng tượng một chút nhưng vẫn đựng đủ đồ dùng cá nhân cần thiết.',
                    'has_images' => false,
                    'admin_reply' => 'Cảm ơn phản hồi đóng góp quý báu của bạn! Aurelia sẽ cập nhật thêm hình ảnh chụp thực tế trên người mẫu để khách hàng dễ hình dung kích thước hơn ạ.'
                ]
            ],
            3 => [
                [
                    'comment' => 'Chất da ổn, form túi bình thường. Dây đeo hơi dài so với chiều cao của mình (1m55) nên phải đi đục thêm lỗ. Tạm hài lòng với mức giá sale.',
                    'has_images' => false,
                    'admin_reply' => 'Aurelia rất tiếc vì dây đeo chưa thực sự vừa vặn với chiều cao của bạn. Lần tới bạn có thể nhắn tin để bên mình hỗ trợ điều chỉnh độ dài dây trước khi gửi hàng nhé ạ!'
                ]
            ],
            2 => [
                [
                    'comment' => 'Màu sắc thực tế hơi tối hơn so với ảnh chụp trên web một tone. Khóa kéo ban đầu hơi rít, phải bôi một chút sáp nến mới trơn tru hơn.',
                    'has_images' => true,
                    'admin_reply' => 'Dạ Aurelia thành thật xin lỗi bạn về trải nghiệm chưa trọn vẹn này. Về màu sắc do ánh sáng studio có thể chênh lệch nhẹ. Bên mình có chính sách đổi màu miễn phí trong 30 ngày, bạn liên hệ Hotline để shop hỗ trợ đổi chiếc ưng ý nhất nhé ạ!'
                ]
            ],
            1 => [
                [
                    'comment' => 'Túi giao bị thiếu tag thẻ bảo hành đi kèm. Mong shop kiểm tra lại khâu đóng gói.',
                    'has_images' => false,
                    'admin_reply' => 'Aurelia vô cùng xin lỗi bạn về sơ suất thiếu thẻ bảo hành trong khâu đóng gói. Bên mình đã liên hệ qua số điện thoại để gửi bù thẻ bảo hành điện tử chính hãng tới bạn ngay trong hôm nay ạ!'
                ]
            ]
        ];

        $reviewCount = 0;

        foreach ($deliveredOrders as $order) {
            $user = $order->user;
            if (!$user) continue;

            foreach ($order->items as $item) {
                // Tránh review trùng
                if ($item->review()->exists()) continue;

                // Chọn ngẫu nhiên mức sao (ưu tiên 5 sao và 4 sao như thực tế)
                $rand = rand(1, 100);
                if ($rand <= 65) {
                    $rating = 5;
                } elseif ($rand <= 85) {
                    $rating = 4;
                } elseif ($rand <= 93) {
                    $rating = 3;
                } elseif ($rand <= 97) {
                    $rating = 2;
                } else {
                    $rating = 1;
                }

                $pool = $feedbackTemplates[$rating];
                $template = $pool[array_rand($pool)];

                $hasImages = $template['has_images'];
                $comment = $template['comment'];
                $adminReply = $template['admin_reply'] ?? null;

                // Tính xu thưởng chuẩn chính sách
                $coinsReward = Review::calculateCoinReward($hasImages, $comment);

                $review = Review::create([
                    'user_id'               => $user->id,
                    'product_id'            => $item->product_id,
                    'order_id'              => $order->id,
                    'order_item_id'         => $item->id,
                    'rating'                => $rating,
                    'comment'               => $comment,
                    'product_variant_title' => $item->variant_title,
                    'is_verified_purchase'  => true,
                    'coins_rewarded'        => $coinsReward,
                    'is_visible'            => true,
                    'admin_reply'           => $adminReply,
                    'admin_replied_at'      => $adminReply ? now()->subHours(rand(1, 48)) : null,
                    'created_at'            => $order->created_at->addDays(rand(1, 3)),
                    'updated_at'            => now(),
                ]);

                // Thêm hình ảnh đính kèm nếu có
                if ($hasImages) {
                    $numImages = rand(1, 3);
                    $shuffled = $sampleImages;
                    shuffle($shuffled);
                    for ($k = 0; $k < $numImages; $k++) {
                        ReviewImage::create([
                            'review_id'  => $review->id,
                            'image_path' => $shuffled[$k],
                            'sort_order' => $k,
                        ]);
                    }
                }

                // Tặng xu cho user và ghi sổ Ledger
                if ($coinsReward > 0) {
                    $coinService->addCoins(
                        $user,
                        $coinsReward,
                        'review',
                        $review->id,
                        "Thưởng {$coinsReward} Xu cho đánh giá sản phẩm: {$item->product_name}"
                    );
                }

                $reviewCount++;
            }
        }

        // Cập nhật rating stats cho tất cả sản phẩm
        foreach (Product::all() as $product) {
            $product->recalculateRatingStats();
        }

        $this->command->info("Đã tạo thành công {$reviewCount} đánh giá mẫu phong phú và cộng thưởng Xu tương ứng cho khách hàng!");
    }
}
