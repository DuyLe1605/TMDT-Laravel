<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Review;
use App\Services\ReviewService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminReviewController extends Controller
{
    public function __construct(
        protected ReviewService $reviewService
    ) {}

    /**
     * Display listing of reviews with filters.
     */
    public function index(Request $request): View
    {
        $query = Review::with(['user', 'product', 'images', 'order'])->latest();

        // Search by keyword
        if ($keyword = trim($request->input('search', ''))) {
            $query->where(function ($q) use ($keyword) {
                $q->where('comment', 'LIKE', "%{$keyword}%")
                  ->orWhereHas('user', fn($u) => $u->where('name', 'LIKE', "%{$keyword}%")->orWhere('email', 'LIKE', "%{$keyword}%"))
                  ->orWhereHas('product', fn($p) => $p->where('name', 'LIKE', "%{$keyword}%"));
            });
        }

        // Filter by rating
        if ($rating = $request->input('rating')) {
            $query->where('rating', (int) $rating);
        }

        // Filter by visibility
        if ($request->has('is_visible') && $request->input('is_visible') !== 'all') {
            $query->where('is_visible', $request->input('is_visible') == '1');
        }

        // Filter by reply status
        if ($replyStatus = $request->input('reply_status')) {
            if ($replyStatus === 'replied') {
                $query->whereNotNull('admin_reply');
            } elseif ($replyStatus === 'pending') {
                $query->whereNull('admin_reply');
            }
        }

        $reviews = $query->paginate(15)->withQueryString();

        // Stats summary
        $totalReviews = Review::count();
        $avgRating = round((float) (Review::avg('rating') ?? 5.0), 1);
        $pendingReplies = Review::whereNull('admin_reply')->count();

        return view('admin.reviews.index', [
            'reviews'        => $reviews,
            'totalReviews'   => $totalReviews,
            'avgRating'      => $avgRating,
            'pendingReplies' => $pendingReplies,
            'products'       => Product::where('is_active', true)->select('id', 'name')->get(),
        ]);
    }

    /**
     * Store admin reply for a review.
     */
    public function reply(Review $review, Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'admin_reply' => ['required', 'string', 'max:1500'],
        ], [
            'admin_reply.required' => 'Vui lòng nhập nội dung phản hồi từ Shop.',
            'admin_reply.max'      => 'Nội dung phản hồi không được quá 1.500 ký tự.',
        ]);

        $this->reviewService->replyReview($review, $validated['admin_reply']);

        return back()->with('success', 'Đã lưu phản hồi cho đánh giá thành công.');
    }

    /**
     * Toggle visibility of review.
     */
    public function toggleVisibility(Review $review): RedirectResponse
    {
        $this->reviewService->toggleVisibility($review);

        $statusStr = $review->is_visible ? 'hiển thị' : 'ẩn';
        return back()->with('success', "Đã chuyển trạng thái đánh giá thành: {$statusStr}.");
    }
}
