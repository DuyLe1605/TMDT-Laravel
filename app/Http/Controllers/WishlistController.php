<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Services\WishlistService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class WishlistController extends Controller
{
    public function __construct(
        protected WishlistService $wishlistService
    ) {}

    /**
     * Hiển thị trang danh sách yêu thích của user.
     */
    public function index(): View
    {
        $wishlistItems = $this->wishlistService->getUserWishlist(Auth::id());
        $wishlists = $wishlistItems;

        return view('account.wishlist', compact('wishlistItems', 'wishlists'));
    }

    /**
     * Toggle wishlist: thêm hoặc xóa sản phẩm (AJAX).
     */
    public function toggle(Product $product): JsonResponse
    {
        $result = $this->wishlistService->toggle(Auth::id(), $product->id);

        return response()->json([
            'success' => true,
            'added' => $result['added'],
            'count' => $result['count'],
            'message' => $result['added']
                ? "Đã thêm \"{$product->name}\" vào danh sách yêu thích!"
                : "Đã xóa \"{$product->name}\" khỏi danh sách yêu thích.",
        ]);
    }

    /**
     * Xóa sản phẩm khỏi wishlist (từ trang wishlist).
     */
    public function remove(Product $product): RedirectResponse
    {
        $this->wishlistService->remove(Auth::id(), $product->id);

        return redirect()->back()->with('success', "Đã xóa \"{$product->name}\" khỏi danh sách yêu thích.");
    }
}
