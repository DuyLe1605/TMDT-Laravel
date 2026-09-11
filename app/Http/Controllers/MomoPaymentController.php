<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\Payment\MomoPaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class MomoPaymentController extends Controller
{
    public function __construct(
        protected MomoPaymentService $momoService
    ) {}

    /**
     * Handle MoMo redirect callback (user returns from MoMo payment page).
     * GET /payment/momo/callback
     */
    public function callback(Request $request): RedirectResponse
    {
        $data = $request->all();

        $result = $this->momoService->handleRedirectCallback($data);

        if ($result['success'] && $result['order_code']) {
            $order = Order::where('order_code', $result['order_code'])->first();
            $ghnNotice = '';

            // Tự động tạo vận đơn GHN nếu chưa có
            if ($order && empty($order->ghn_order_code)) {
                try {
                    $order->load(['items.product', 'items.variant']);
                    $orderService = app(\App\Services\OrderService::class);
                    $orderService->sendToGhn($order);
                    $ghnNotice = " Vận đơn GHN đã được khởi tạo tự động (#{$order->ghn_order_code}).";
                } catch (\Exception $e) {
                    Log::warning("MoMo callback: Chưa thể tạo vận đơn GHN tự động: " . $e->getMessage());
                }
            }

            return redirect()
                ->route('checkout.success', $result['order_code'])
                ->with('success', 'Thanh toán MoMo thành công!' . $ghnNotice);
        }

        // Payment failed or cancelled
        if ($result['order_code']) {
            return redirect()
                ->route('checkout.success', $result['order_code'])
                ->with('warning', $result['message'] ?? 'Thanh toán MoMo chưa hoàn tất. Bạn có thể nhấn nút Thanh toán lại.');
        }

        // Fallback: redirect to home if no order found
        return redirect()
            ->route('home')
            ->with('error', 'Không tìm thấy thông tin đơn hàng. Vui lòng kiểm tra lại.');
    }

    /**
     * Start / Retry MoMo payment for an existing order (Pay Again).
     * GET /orders/{order}/pay/momo
     */
    public function payAgain(Order $order, Request $request): RedirectResponse
    {
        if (Auth::check() && $order->user_id && $order->user_id !== Auth::id()) {
            abort(403, 'Bạn không có quyền thanh toán đơn hàng này.');
        }

        if ($order->payment_status === Order::PAYMENT_PAID) {
            return redirect()
                ->route('checkout.success', $order->order_code)
                ->with('info', 'Đơn hàng này đã được thanh toán thành công trước đó.');
        }

        if ($order->shipping_status === Order::STATUS_CANCELLED) {
            return redirect()
                ->route('account.orders')
                ->with('error', 'Đơn hàng này đã bị hủy, không thể tiếp tục thanh toán.');
        }

        $requestType = $request->query('method', $request->input('method')); // captureWallet, payWithATM, payWithCC
        $order->load('items');
        $momoResult = $this->momoService->createPayment($order, $requestType);

        if ($momoResult['success'] && !empty($momoResult['payUrl'])) {
            return redirect()->away($momoResult['payUrl']);
        }

        return redirect()
            ->back()
            ->with('error', $momoResult['message'] ?? 'Không thể kết nối đến MoMo lúc này. Vui lòng thử lại sau.');
    }

    /**
     * Start MoMo payment directly for an order.
     * GET /orders/{order}/start-momo
     */
    public function start(Order $order, Request $request): RedirectResponse
    {
        return $this->payAgain($order, $request);
    }

    /**
     * Handle MoMo IPN (Instant Payment Notification) callback.
     * POST /payment/momo/ipn & POST /webhook/momo (no CSRF, server-to-server)
     */
    public function ipn(Request $request): JsonResponse
    {
        $data = $request->all();

        $result = $this->momoService->handleIpnCallback($data);

        if ($result['success']) {
            // Check if we can auto create GHN order
            $extraData = !empty($data['extraData']) ? json_decode(base64_decode($data['extraData']), true) : null;
            $code = $extraData['order_code'] ?? null;
            if ($code) {
                $order = Order::where('order_code', $code)->first();
                if ($order && empty($order->ghn_order_code)) {
                    try {
                        $order->load(['items.product', 'items.variant']);
                        app(\App\Services\OrderService::class)->sendToGhn($order);
                        Log::info("Auto GHN created via MoMo IPN for order {$code}");
                    } catch (\Exception $e) {
                        Log::warning("IPN GHN auto-create error: " . $e->getMessage());
                    }
                }
            }

            return response()->json(['message' => 'Received'], 200);
        }

        Log::warning('MoMo IPN processing failed', [
            'message' => $result['message'],
            'data'    => $data,
        ]);

        return response()->json(['message' => $result['message']], 400);
    }

    /**
     * Query MoMo transaction status for an order.
     * POST /payment/momo/query/{order}
     */
    public function queryStatus(Order $order): JsonResponse
    {
        // Authorization: only order owner or admin can query
        if (Auth::check()) {
            $user = Auth::user();
            if ($order->user_id && $order->user_id !== $user->id && !$user->isAdmin()) {
                return response()->json(['message' => 'Không có quyền truy cập.'], 403);
            }
        }

        $result = $this->momoService->queryTransactionStatus($order);

        return response()->json([
            'success'    => $result['success'],
            'resultCode' => $result['resultCode'],
            'message'    => $result['message'],
            'data'       => $result['data'],
        ]);
    }
}
