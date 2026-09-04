<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\AddressService;
use App\Services\OrderService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Exception;

class AccountController extends Controller
{
    public function __construct(
        protected AddressService $addressService,
        protected OrderService $orderService
    ) {}

    /**
     * Display order history list for current user, with status tabs.
     */
    public function orders(Request $request): View
    {
        $currentStatus = $request->query('status', 'all');

        $query = Order::where('user_id', Auth::id())
            ->with(['items.review', 'items.product'])
            ->byShippingStatus($currentStatus)
            ->latest();

        $orders = $query->paginate(10)->withQueryString();

        // Get counts for each status tab
        $statusCounts = [
            'all'        => Order::where('user_id', Auth::id())->count(),
            'pending'    => Order::where('user_id', Auth::id())->where('shipping_status', 'pending')->count(),
            'processing' => Order::where('user_id', Auth::id())->where('shipping_status', 'processing')->count(),
            'shipping'   => Order::where('user_id', Auth::id())->where('shipping_status', 'shipping')->count(),
            'delivered'  => Order::where('user_id', Auth::id())->where('shipping_status', 'delivered')->count(),
            'cancelled'  => Order::where('user_id', Auth::id())->where('shipping_status', 'cancelled')->count(),
        ];

        $statusTabs = [
            'all'        => 'Tất cả',
            'pending'    => 'Chờ xử lý',
            'processing' => 'Đang chuẩn bị',
            'shipping'   => 'Đang giao',
            'delivered'  => 'Đã giao',
            'cancelled'  => 'Đã hủy',
        ];

        return view('account.orders', compact('orders', 'statusCounts', 'statusTabs', 'currentStatus'));
    }

    /**
     * Display order details.
     */
    public function orderDetail(Order $order): View
    {
        if ($order->user_id !== Auth::id() && !Auth::user()->isAdmin()) {
            abort(403);
        }

        $order->load(['items.product', 'items.review']);

        return view('account.order-detail', compact('order'));
    }

    /**
     * Customer cancels their own order (only when status is 'pending').
     */
    public function cancelOrder(Request $request, Order $order): RedirectResponse
    {
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        $reason = $request->input('cancel_reason', 'Khách hàng tự hủy');

        try {
            $this->orderService->cancelOrder($order, $reason, false);
            return redirect()->back()->with('success', "Đã hủy đơn hàng {$order->order_code} thành công. Tồn kho đã được hoàn trả.");
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Customer re-orders: Add items from a previous order back to cart.
     */
    public function reorder(Order $order): RedirectResponse
    {
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        $result = $this->orderService->reorder($order, Auth::user());

        if ($result['success']) {
            return redirect()->route('cart.index')->with('success', $result['message']);
        }

        return redirect()->back()->with('error', $result['message']);
    }

    /**
     * Customer confirms delivery received ("Đã nhận được hàng").
     */
    public function confirmDelivery(Order $order): RedirectResponse
    {
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        try {
            $this->orderService->markDelivered($order);
            return redirect()->back()->with('success', "Cảm ơn bạn đã xác nhận nhận hàng thành công cho đơn hàng {$order->order_code}!");
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Display address book in account settings.
     */
    public function addresses(): View
    {
        $addresses = $this->addressService->getUserAddresses(Auth::id());

        return view('account.addresses', compact('addresses'));
    }
}
