<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\OrderService;
use App\Services\Shipping\GhnShippingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Exception;

class AdminOrderController extends Controller
{
    public function __construct(
        protected OrderService $orderService,
        protected GhnShippingService $ghnService
    ) {}

    /**
     * Admin: List all orders with filters.
     */
    public function index(Request $request): View
    {
        $query = Order::with(['user', 'items'])
            ->byShippingStatus($request->query('shipping_status'))
            ->byPaymentStatus($request->query('payment_status'))
            ->search($request->query('search'))
            ->dateRange($request->query('date_from'), $request->query('date_to'));

        // Sorting
        $sortBy = $request->query('sort', 'created_at');
        $sortDir = $request->query('dir', 'desc');
        $allowedSorts = ['created_at', 'total_amount', 'order_code'];
        if (in_array($sortBy, $allowedSorts)) {
            $query->orderBy($sortBy, $sortDir === 'asc' ? 'asc' : 'desc');
        } else {
            $query->latest();
        }

        $orders = $query->paginate(15)->withQueryString();

        // Status counts for summary cards
        $statusCounts = [
            'all'        => Order::count(),
            'pending'    => Order::where('shipping_status', 'pending')->count(),
            'processing' => Order::where('shipping_status', 'processing')->count(),
            'shipping'   => Order::where('shipping_status', 'shipping')->count(),
            'delivered'  => Order::where('shipping_status', 'delivered')->count(),
            'returning'  => Order::where('shipping_status', 'returning')->count(),
            'cancelled'  => Order::where('shipping_status', 'cancelled')->count(),
        ];

        $shippingStatusOptions = Order::getShippingStatusOptions();

        return view('admin.orders.index', compact(
            'orders',
            'statusCounts',
            'shippingStatusOptions'
        ));
    }

    /**
     * Admin: View order details.
     */
    public function show(Order $order): View
    {
        $order->load(['items.product', 'items.variant', 'user']);

        // Fetch GHN detail if order has been sent
        $ghnDetail = null;
        if ($order->isGhnOrder()) {
            $ghnDetail = $this->ghnService->getOrderDetail($order->ghn_order_code);
        }

        return view('admin.orders.show', compact('order', 'ghnDetail'));
    }

    /**
     * Admin: Update order status (confirm: pending → processing).
     */
    public function updateStatus(Request $request, Order $order): RedirectResponse
    {
        $action = $request->input('action');

        try {
            switch ($action) {
                case 'confirm':
                    $this->orderService->confirmOrder($order);
                    return redirect()->back()->with('success', "Đã xác nhận đơn hàng {$order->order_code}. Trạng thái: Đang chuẩn bị.");

                case 'mark_delivered':
                    $this->orderService->markDelivered($order);
                    return redirect()->back()->with('success', "Đã đánh dấu giao thành công đơn hàng {$order->order_code}.");

                case 'mark_paid':
                    $this->orderService->markPaid($order);
                    return redirect()->back()->with('success', "Đã xác nhận thanh toán đơn hàng {$order->order_code}.");

                default:
                    return redirect()->back()->with('error', 'Hành động không hợp lệ.');
            }
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Admin: Send order to GHN for delivery.
     */
    public function sendToGhn(Order $order): RedirectResponse
    {
        try {
            $this->orderService->sendToGhn($order);
            return redirect()->back()->with('success',
                "Đã gửi đơn hàng {$order->order_code} sang GHN thành công! Mã vận đơn: {$order->fresh()->ghn_order_code}");
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Gửi GHN thất bại: ' . $e->getMessage());
        }
    }

    /**
     * Admin: Cancel an order.
     */
    public function cancelOrder(Request $request, Order $order): RedirectResponse
    {
        $reason = $request->input('cancel_reason', 'Admin hủy đơn');

        try {
            $this->orderService->cancelOrder($order, $reason, true);
            return redirect()->back()->with('success', "Đã hủy đơn hàng {$order->order_code} thành công.");
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Admin: Print GHN shipping label.
     */
    public function printLabel(Order $order): RedirectResponse
    {
        if (!$order->isGhnOrder()) {
            return redirect()->back()->with('error', 'Đơn hàng chưa được gửi lên GHN, không thể in vận đơn.');
        }

        $printUrl = $this->ghnService->getPrintUrl($order->ghn_order_code);

        if (!$printUrl) {
            return redirect()->back()->with('error', 'Không thể lấy mã in vận đơn từ GHN. Vui lòng thử lại.');
        }

        return redirect()->away($printUrl);
    }
}
