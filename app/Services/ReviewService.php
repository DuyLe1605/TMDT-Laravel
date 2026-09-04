<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Review;
use App\Models\ReviewImage;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use InvalidArgumentException;

class ReviewService
{
    public function __construct(
        protected CoinService $coinService
    ) {}

    /**
     * Check if user is eligible to review an order item.
     */
    public function canReviewOrderItem(User $user, OrderItem $orderItem): bool
    {
        $order = $orderItem->order;
        if (!$order || $order->user_id !== $user->id) {
            return false;
        }

        if ($order->shipping_status !== Order::STATUS_DELIVERED) {
            return false;
        }

        return !$orderItem->review()->exists();
    }

    /**
     * Create review for an order item and reward coins.
     *
     * @param User $user
     * @param OrderItem $orderItem
     * @param array $data ['rating' => int, 'comment' => string|null]
     * @param UploadedFile[] $uploadedImages
     * @return Review
     * @throws InvalidArgumentException
     */
    public function createReview(
        User $user,
        OrderItem $orderItem,
        array $data,
        array $uploadedImages = []
    ): Review {
        if (!$this->canReviewOrderItem($user, $orderItem)) {
            throw new InvalidArgumentException('Bạn chỉ có thể đánh giá sản phẩm sau khi đơn hàng đã giao thành công và chưa từng được đánh giá.');
        }

        $rating = (int) ($data['rating'] ?? 5);
        if ($rating < 1 || $rating > 5) {
            throw new InvalidArgumentException('Số sao đánh giá phải từ 1 đến 5.');
        }

        $comment = isset($data['comment']) ? trim($data['comment']) : null;
        $hasImages = !empty($uploadedImages);

        // Tính xu thưởng
        $coinsReward = Review::calculateCoinReward($hasImages, $comment);

        return DB::transaction(function () use ($user, $orderItem, $rating, $comment, $coinsReward, $uploadedImages) {
            // Tạo bản ghi Review
            $review = Review::create([
                'user_id'               => $user->id,
                'product_id'            => $orderItem->product_id,
                'order_id'              => $orderItem->order_id,
                'order_item_id'         => $orderItem->id,
                'rating'                => $rating,
                'comment'               => $comment,
                'product_variant_title' => $orderItem->variant_title,
                'is_verified_purchase'  => true,
                'coins_rewarded'        => $coinsReward,
                'is_visible'            => true,
            ]);

            // Lưu các hình ảnh đánh giá (tối đa 5 ảnh)
            $sort = 0;
            foreach ($uploadedImages as $image) {
                if ($image instanceof UploadedFile) {
                    $path = $image->store('reviews', 'public');
                    ReviewImage::create([
                        'review_id'  => $review->id,
                        'image_path' => $path,
                        'sort_order' => $sort++,
                    ]);
                }
            }

            // Tặng xu cho khách hàng
            if ($coinsReward > 0) {
                $productName = $orderItem->product_name;
                $this->coinService->addCoins(
                    $user,
                    $coinsReward,
                    'review',
                    $review->id,
                    "Thưởng {$coinsReward} Xu cho đánh giá sản phẩm: {$productName}"
                );
            }

            // Cập nhật thống kê rating cho sản phẩm
            $orderItem->product?->recalculateRatingStats();

            return $review->fresh(['images', 'user']);
        });
    }

    /**
     * Get review summary stats for a product.
     */
    public function getProductReviewsSummary(Product $product): array
    {
        $visibleReviews = $product->visibleReviews();

        $totalReviews = (clone $visibleReviews)->count();
        $avgRating = $totalReviews > 0 ? round((clone $visibleReviews)->avg('rating'), 1) : 5.0;

        $starCounts = [
            5 => (clone $visibleReviews)->where('rating', 5)->count(),
            4 => (clone $visibleReviews)->where('rating', 4)->count(),
            3 => (clone $visibleReviews)->where('rating', 3)->count(),
            2 => (clone $visibleReviews)->where('rating', 2)->count(),
            1 => (clone $visibleReviews)->where('rating', 1)->count(),
        ];

        $withImagesCount = (clone $visibleReviews)->has('images')->count();

        return [
            'total'             => $totalReviews,
            'avg_rating'        => $avgRating,
            'star_counts'       => $starCounts,
            'with_images_count' => $withImagesCount,
        ];
    }

    /**
     * Get paginated & filtered reviews for storefront.
     */
    public function getFilteredReviews(Product $product, array $filters = [], int $perPage = 6): LengthAwarePaginator
    {
        $query = $product->visibleReviews()->with(['user', 'images']);

        if (!empty($filters['rating']) && in_array((int) $filters['rating'], [1, 2, 3, 4, 5])) {
            $query->where('rating', (int) $filters['rating']);
        }

        if (!empty($filters['has_images']) && filter_var($filters['has_images'], FILTER_VALIDATE_BOOLEAN)) {
            $query->has('images');
        }

        return $query->paginate($perPage);
    }

    /**
     * Admin reply to a customer review.
     */
    public function replyReview(Review $review, string $replyText): Review
    {
        $review->update([
            'admin_reply'      => trim($replyText),
            'admin_replied_at' => now(),
        ]);

        return $review;
    }

    /**
     * Toggle visibility (Admin moderation).
     */
    public function toggleVisibility(Review $review): Review
    {
        $review->update([
            'is_visible' => !$review->is_visible,
        ]);

        $review->product?->recalculateRatingStats();

        return $review;
    }
}
