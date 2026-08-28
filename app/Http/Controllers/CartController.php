<?php

namespace App\Http\Controllers;

use App\Http\Requests\Cart\AddToCartRequest;
use App\Services\CartService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Exception;

class CartController extends Controller
{
    public function __construct(
        protected CartService $cartService
    ) {}

    /**
     * Display the shopping cart page.
     */
    public function index(): View
    {
        $cartItems = $this->cartService->getCartItems();
        $summary = $this->cartService->calculateSummary($cartItems);

        return view('cart.index', compact('cartItems', 'summary'));
    }

    /**
     * Add product to cart.
     */
    public function add(AddToCartRequest $request): JsonResponse|RedirectResponse
    {
        try {
            $cartItem = $this->cartService->addToCart(
                (int) $request->input('product_id'),
                (int) $request->input('quantity', 1)
            );

            $cartCount = $this->cartService->getCartCount();

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => "Đã thêm '{$cartItem->product->name}' vào giỏ hàng thành công!",
                    'cart_count' => $cartCount,
                    'item' => [
                        'id' => $cartItem->id,
                        'product_id' => $cartItem->product_id,
                        'name' => $cartItem->product->name,
                        'quantity' => $cartItem->quantity,
                        'unit_price' => $cartItem->unit_price,
                        'subtotal' => $cartItem->subtotal,
                        'formatted_subtotal' => $cartItem->formatted_subtotal,
                    ],
                ]);
            }

            return redirect()->back()->with('success', "Đã thêm '{$cartItem->product->name}' vào giỏ hàng!");
        } catch (Exception $e) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                ], 422);
            }

            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Update item quantity in cart.
     */
    public function update(Request $request, int $id): JsonResponse|RedirectResponse
    {
        $quantity = (int) $request->input('quantity', 1);

        try {
            $cartItem = $this->cartService->updateQuantity($id, $quantity);
            $cartItems = $this->cartService->getCartItems();
            $summary = $this->cartService->calculateSummary($cartItems);
            $cartCount = $this->cartService->getCartCount();

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Đã cập nhật số lượng thành công.',
                    'cart_count' => $cartCount,
                    'item' => [
                        'id' => $cartItem->id,
                        'quantity' => $cartItem->quantity,
                        'unit_price' => $cartItem->unit_price,
                        'formatted_unit_price' => $cartItem->formatted_unit_price,
                        'subtotal' => $cartItem->subtotal,
                        'formatted_subtotal' => $cartItem->formatted_subtotal,
                    ],
                    'summary' => $summary,
                ]);
            }

            return redirect()->route('cart.index')->with('success', 'Đã cập nhật giỏ hàng.');
        } catch (Exception $e) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                ], 422);
            }

            return redirect()->route('cart.index')->with('error', $e->getMessage());
        }
    }

    /**
     * Remove an item from the cart.
     */
    public function remove(Request $request, int $id): JsonResponse|RedirectResponse
    {
        $deleted = $this->cartService->removeItem($id);
        $cartCount = $this->cartService->getCartCount();
        $cartItems = $this->cartService->getCartItems();
        $summary = $this->cartService->calculateSummary($cartItems);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => $deleted,
                'message' => $deleted ? 'Đã xóa sản phẩm khỏi giỏ hàng.' : 'Không tìm thấy sản phẩm.',
                'cart_count' => $cartCount,
                'summary' => $summary,
            ]);
        }

        return redirect()->route('cart.index')->with('success', 'Đã xóa sản phẩm khỏi giỏ hàng.');
    }

    /**
     * Bulk remove selected items from the cart.
     */
    public function bulkRemove(Request $request): JsonResponse|RedirectResponse
    {
        $itemIds = $request->input('item_ids', []);

        if (!is_array($itemIds) || empty($itemIds)) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Vui lòng chọn ít nhất 1 sản phẩm để xóa.',
                ], 422);
            }

            return redirect()->route('cart.index')->with('error', 'Vui lòng chọn ít nhất 1 sản phẩm để xóa.');
        }

        $deletedCount = $this->cartService->removeBulk($itemIds);
        $cartCount = $this->cartService->getCartCount();
        $cartItems = $this->cartService->getCartItems();
        $summary = $this->cartService->calculateSummary($cartItems);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Đã xóa {$deletedCount} sản phẩm khỏi giỏ hàng.",
                'cart_count' => $cartCount,
                'summary' => $summary,
            ]);
        }

        return redirect()->route('cart.index')->with('success', "Đã xóa {$deletedCount} sản phẩm được chọn khỏi giỏ hàng.");
    }

    /**
     * Get live cart count for navbar badge.
     */
    public function count(): JsonResponse
    {
        return response()->json([
            'count' => $this->cartService->getCartCount(),
        ]);
    }
}
