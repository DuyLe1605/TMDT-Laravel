<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreReviewRequest;
use App\Models\OrderItem;
use App\Models\Product;
use App\Services\ReviewService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function __construct(
        protected ReviewService $reviewService
    ) {}

    /**
     * Handle review submission from customer.
     */
    public function store(StoreReviewRequest $request): RedirectResponse
    {
        $orderItem = OrderItem::with('order')->findOrFail($request->order_item_id);

        try {
            $uploadedImages = $request->file('images', []);
            if (!is_array($uploadedImages)) {
                $uploadedImages = [$uploadedImages];
            }

            $review = $this->reviewService->createReview(
                $request->user(),
                $orderItem,
                $request->validated(),
                $uploadedImages
            );

            $coinMsg = $review->coins_rewarded > 0 
                ? " Bạn nhận được +" . number_format($review->coins_rewarded, 0, ',', '.') . " Xu thưởng!" 
                : "";

            return back()->with('success', "Đánh giá của bạn đã được đăng thành công.{$coinMsg}");
        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    /**
     * Fetch filtered reviews for product page via AJAX.
     */
    public function getReviewsJson(Product $product, Request $request): JsonResponse
    {
        $reviews = $this->reviewService->getFilteredReviews(
            $product,
            $request->only(['rating', 'has_images']),
            5
        );

        $html = view('storefront.partials.review-items', ['reviews' => $reviews])->render();
        $paginationHtml = $reviews->links()->render();

        return response()->json([
            'success'         => true,
            'html'            => $html,
            'pagination'      => $paginationHtml,
            'total'           => $reviews->total(),
            'current_page'    => $reviews->currentPage(),
            'last_page'       => $reviews->lastPage(),
        ]);
    }
}
