<?php

namespace App\Http\Controllers;

use App\Http\Requests\Checkout\ProcessCheckoutRequest;
use App\Models\Address;
use App\Models\Order;
use App\Services\AddressService;
use App\Services\CartService;
use App\Services\OrderService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Exception;

class CheckoutController extends Controller
{
    public function __construct(
        protected CartService $cartService,
        protected AddressService $addressService,
        protected OrderService $orderService
    ) {}

    /**
     * Display checkout page with selected cart items.
     */
    public function index(Request $request): View|RedirectResponse
    {
        // Get selected item IDs from request query / form / session
        $itemIds = $request->input('items', []);

        if (is_string($itemIds)) {
            $itemIds = explode(',', $itemIds);
        }

        $itemIds = array_filter(array_map('intval', (array) $itemIds));

        if (empty($itemIds)) {
            return redirect()->route('cart.index')->with('error', 'Vui lòng chọn ít nhất một sản phẩm trong giỏ hàng để tiến hành thanh toán.');
        }

        $selectedItems = $this->cartService->getSelectedItems($itemIds);

        if ($selectedItems->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Không tìm thấy các sản phẩm đã chọn trong giỏ hàng của bạn.');
        }

        $summary = $this->cartService->calculateSummary($selectedItems);

        // Fetch user addresses if authenticated
        $addresses = Auth::check() ? $this->addressService->getUserAddresses(Auth::id()) : collect();
        $defaultAddress = Auth::check() ? (Auth::user()->defaultAddress() ?: $addresses->first()) : null;

        // Shipping fee rule: Free if subtotal >= 500,000, else standard 30,000
        $shippingFee = ($summary['total_amount'] >= 500000) ? 0 : 30000;
        $grandTotal = $summary['total_amount'] + $shippingFee;

        return view('checkout.index', compact(
            'selectedItems',
            'summary',
            'addresses',
            'defaultAddress',
            'shippingFee',
            'grandTotal'
        ));
    }

    /**
     * Process checkout and create order.
     */
    public function process(ProcessCheckoutRequest $request): RedirectResponse
    {
        try {
            $selectedItemIds = array_map('intval', $request->input('selected_items', []));
            $selectedItems = $this->cartService->getSelectedItems($selectedItemIds);

            if ($selectedItems->isEmpty()) {
                return redirect()->route('cart.index')->with('error', 'Không có sản phẩm nào được chọn hoặc giỏ hàng đã thay đổi.');
            }

            $order = $this->orderService->createOrder(
                $request->validated(),
                $selectedItems,
                Auth::user()
            );

            return redirect()->route('checkout.success', $order->order_code)
                ->with('success', "Đặt hàng thành công! Mã đơn hàng của bạn là {$order->order_code}.");
        } catch (Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Đã xảy ra lỗi khi tạo đơn hàng: ' . $e->getMessage());
        }
    }

    /**
     * Display order success confirmation page.
     */
    public function success(string $orderCode): View|RedirectResponse
    {
        $order = Order::where('order_code', $orderCode)
            ->with(['items.product', 'user'])
            ->firstOrFail();

        // Optional permission check: If order has user_id and current user is logged in, ensure matching or admin
        if ($order->user_id && Auth::check() && $order->user_id !== Auth::id() && !Auth::user()->isAdmin()) {
            abort(403);
        }

        return view('checkout.success', compact('order'));
    }
}
