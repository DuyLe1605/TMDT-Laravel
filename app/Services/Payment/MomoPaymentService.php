<?php

namespace App\Services\Payment;

use App\Models\Order;
use App\Models\PaymentTransaction;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class MomoPaymentService
{
    protected string $partnerCode;
    protected string $accessKey;
    protected string $secretKey;
    protected string $apiEndpoint;
    protected string $redirectUrl;
    protected string $ipnUrl;

    public function __construct()
    {
        $this->partnerCode = config('services.momo.partner_code', '');
        $this->accessKey   = config('services.momo.access_key', '');
        $this->secretKey   = config('services.momo.secret_key', '');
        $this->apiEndpoint = config('services.momo.api_endpoint', 'https://test-payment.momo.vn');
        $this->redirectUrl = config('services.momo.redirect_url', '');
        $this->ipnUrl      = config('services.momo.ipn_url', '');
    }

    // =========================================================================
    // CREATE PAYMENT (captureWallet)
    // =========================================================================

    /**
     * Create a MoMo payment request and return the payment URL.
     *
     * @param Order $order
     * @param string|null $requestType 'captureWallet' (QR/App), 'payWithATM' (ATM nội địa), or 'payWithCC' (Thẻ quốc tế)
     * @return array{success: bool, payUrl: ?string, deeplink: ?string, qrCodeUrl: ?string, message: string, transaction: ?PaymentTransaction}
     */
    public function createPayment(Order $order, ?string $requestType = null): array
    {
        $requestId  = $this->generateRequestId();
        $momoOrderId = $this->buildMomoOrderId($order);
        $amount     = (int) $order->total_amount;
        $orderInfo  = "Thanh toán đơn hàng {$order->order_code} - Aurelia Bags";
        $extraData  = base64_encode(json_encode([
            'order_code' => $order->order_code,
        ]));

        // Build items list for MoMo display
        $items = [];
        foreach ($order->items as $item) {
            $items[] = [
                'id'         => (string) ($item->product_id ?? $item->id),
                'name'       => Str::limit($item->product_name, 100),
                'price'      => (int) $item->price,
                'currency'   => 'VND',
                'quantity'   => (int) $item->quantity,
                'totalPrice' => (int) $item->subtotal,
            ];
        }

        // Validate or fallback requestType: captureWallet, payWithATM, payWithCC
        $allowedTypes = ['captureWallet', 'payWithATM', 'payWithCC'];
        if (!$requestType || !in_array($requestType, $allowedTypes, true)) {
            $requestType = config('services.momo.request_type', 'payWithCC');
        }

        // Build signature raw string (alphabetically sorted keys)
        $rawSignature = "accessKey={$this->accessKey}"
            . "&amount={$amount}"
            . "&extraData={$extraData}"
            . "&ipnUrl={$this->ipnUrl}"
            . "&orderId={$momoOrderId}"
            . "&orderInfo={$orderInfo}"
            . "&partnerCode={$this->partnerCode}"
            . "&redirectUrl={$this->redirectUrl}"
            . "&requestId={$requestId}"
            . "&requestType={$requestType}";

        $signature = $this->generateSignature($rawSignature);

        $requestPayload = [
            'partnerCode' => $this->partnerCode,
            'partnerName' => 'Aurelia Luxury Bags',
            'storeId'     => 'AureliaBagsStore',
            'requestId'   => $requestId,
            'amount'      => $amount,
            'orderId'     => $momoOrderId,
            'orderInfo'   => $orderInfo,
            'redirectUrl' => $this->redirectUrl,
            'ipnUrl'      => $this->ipnUrl,
            'requestType' => $requestType,
            'extraData'   => $extraData,
            'items'       => $items,
            'lang'        => 'vi',
            'signature'   => $signature,
        ];

        // Create a pending PaymentTransaction record BEFORE calling MoMo
        $transaction = PaymentTransaction::create([
            'order_id'          => $order->id,
            'gateway'           => PaymentTransaction::GATEWAY_MOMO,
            'transaction_type'  => PaymentTransaction::TYPE_PAYMENT,
            'partner_code'      => $this->partnerCode,
            'request_id'        => $requestId,
            'momo_order_id'     => $momoOrderId,
            'amount'            => $amount,
            'pay_type'          => match ($requestType) {
                'captureWallet' => 'qr',
                'payWithATM'    => 'napas',
                'payWithCC'     => 'creditcard',
                default         => null,
            },
            'status'            => PaymentTransaction::STATUS_PENDING,
            'signature_request' => $signature,
            'request_payload'   => $requestPayload,
        ]);

        try {
            $endpoint = config('services.momo.endpoint') ?: "{$this->apiEndpoint}/v2/gateway/api/create";
            $verifySsl = filter_var(config('services.momo.verify_ssl', false), FILTER_VALIDATE_BOOLEAN);

            $response = Http::timeout(30)
                ->withOptions(['verify' => $verifySsl])
                ->post($endpoint, $requestPayload);

            $responseData = $response->json();

            Log::info('MoMo createPayment response', [
                'order_code'   => $order->order_code,
                'momo_order_id' => $momoOrderId,
                'result_code'  => $responseData['resultCode'] ?? null,
                'message'      => $responseData['message'] ?? null,
            ]);

            // Update transaction with response data
            $transaction->update([
                'result_code'       => $responseData['resultCode'] ?? null,
                'message'           => $responseData['message'] ?? null,
                'pay_url'           => $responseData['payUrl'] ?? null,
                'deeplink'          => $responseData['deeplink'] ?? null,
                'qr_code_url'       => $responseData['qrCodeUrl'] ?? null,
                'signature_response' => $responseData['signature'] ?? null,
                'response_payload'  => $responseData,
            ]);

            // resultCode 0 = success (payment request created)
            if (isset($responseData['resultCode']) && $responseData['resultCode'] == 0) {
                return [
                    'success'     => true,
                    'payUrl'      => $responseData['payUrl'] ?? null,
                    'deeplink'    => $responseData['deeplink'] ?? null,
                    'qrCodeUrl'   => $responseData['qrCodeUrl'] ?? null,
                    'message'     => $responseData['message'] ?? 'Tạo thanh toán MoMo thành công.',
                    'transaction' => $transaction,
                ];
            }

            // Payment request failed
            $transaction->update(['status' => PaymentTransaction::STATUS_FAILED]);

            return [
                'success'     => false,
                'payUrl'      => null,
                'deeplink'    => null,
                'qrCodeUrl'   => null,
                'message'     => $responseData['message'] ?? 'Không thể tạo thanh toán MoMo. Vui lòng thử lại.',
                'transaction' => $transaction,
            ];

        } catch (\Exception $e) {
            Log::error('MoMo createPayment exception', [
                'order_code' => $order->order_code,
                'error'      => $e->getMessage(),
            ]);

            $transaction->update([
                'status'  => PaymentTransaction::STATUS_FAILED,
                'message' => 'Lỗi kết nối MoMo: ' . $e->getMessage(),
            ]);

            return [
                'success'     => false,
                'payUrl'      => null,
                'deeplink'    => null,
                'qrCodeUrl'   => null,
                'message'     => 'Không thể kết nối đến MoMo. Vui lòng thử lại sau.',
                'transaction' => $transaction,
            ];
        }
    }

    // =========================================================================
    // HANDLE IPN CALLBACK (server-to-server from MoMo)
    // =========================================================================

    /**
     * Process MoMo IPN (Instant Payment Notification) callback.
     *
     * @return array{success: bool, message: string}
     */
    public function handleIpnCallback(array $data): array
    {
        Log::info('MoMo IPN received', $data);

        // Verify signature
        $rawSignature = "accessKey={$this->accessKey}"
            . "&amount={$data['amount']}"
            . "&extraData={$data['extraData']}"
            . "&message={$data['message']}"
            . "&orderId={$data['orderId']}"
            . "&orderInfo={$data['orderInfo']}"
            . "&orderType={$data['orderType']}"
            . "&partnerCode={$data['partnerCode']}"
            . "&payType={$data['payType']}"
            . "&requestId={$data['requestId']}"
            . "&responseTime={$data['responseTime']}"
            . "&resultCode={$data['resultCode']}"
            . "&transId={$data['transId']}";

        $expectedSignature = $this->generateSignature($rawSignature);

        if (!$this->verifySignature($rawSignature, $data['signature'] ?? '')) {
            Log::warning('MoMo IPN signature mismatch', [
                'orderId'   => $data['orderId'] ?? null,
                'expected'  => $expectedSignature,
                'received'  => $data['signature'] ?? null,
            ]);

            return ['success' => false, 'message' => 'Invalid signature'];
        }

        // Find the transaction
        $momoOrderId = $data['orderId'] ?? null;
        $requestId   = $data['requestId'] ?? null;

        $transaction = PaymentTransaction::where('momo_order_id', $momoOrderId)
            ->orWhere('request_id', $requestId)
            ->first();

        if (!$transaction) {
            Log::warning('MoMo IPN: Transaction not found', ['orderId' => $momoOrderId]);
            return ['success' => false, 'message' => 'Transaction not found'];
        }

        $resultCode = (int) ($data['resultCode'] ?? -1);

        // Update transaction
        $transaction->update([
            'result_code'       => $resultCode,
            'message'           => $data['message'] ?? null,
            'momo_trans_id'     => $data['transId'] ?? null,
            'pay_type'          => $data['payType'] ?? null,
            'signature_response' => $data['signature'] ?? null,
            'ipn_payload'       => $data,
            'status'            => $resultCode === 0
                ? PaymentTransaction::STATUS_PAID
                : PaymentTransaction::STATUS_FAILED,
            'paid_at'           => $resultCode === 0 ? now() : null,
        ]);

        // Update order payment status
        $order = $transaction->order;
        if ($order) {
            if ($resultCode === 0) {
                $order->update([
                    'payment_status' => Order::PAYMENT_PAID,
                    'paid_at'        => now(),
                ]);
                Log::info("MoMo payment SUCCESS for order {$order->order_code}", [
                    'transId' => $data['transId'] ?? null,
                ]);
            } else {
                // Non-zero resultCode means payment failed/cancelled by user
                Log::info("MoMo payment FAILED for order {$order->order_code}", [
                    'resultCode' => $resultCode,
                    'message'    => $data['message'] ?? null,
                ]);
            }
        }

        return ['success' => true, 'message' => 'IPN processed'];
    }

    // =========================================================================
    // HANDLE REDIRECT CALLBACK (user browser redirect back from MoMo)
    // =========================================================================

    /**
     * Process the redirect callback when user returns from MoMo payment page.
     *
     * @return array{success: bool, order_code: ?string, message: string}
     */
    public function handleRedirectCallback(array $data): array
    {
        Log::info('MoMo redirect callback received', $data);

        $momoOrderId = $data['orderId'] ?? null;
        $resultCode  = (int) ($data['resultCode'] ?? -1);

        // Verify signature
        $rawSignature = "accessKey={$this->accessKey}"
            . "&amount={$data['amount']}"
            . "&extraData={$data['extraData']}"
            . "&message={$data['message']}"
            . "&orderId={$data['orderId']}"
            . "&orderInfo={$data['orderInfo']}"
            . "&orderType={$data['orderType']}"
            . "&partnerCode={$data['partnerCode']}"
            . "&payType={$data['payType']}"
            . "&requestId={$data['requestId']}"
            . "&responseTime={$data['responseTime']}"
            . "&resultCode={$data['resultCode']}"
            . "&transId={$data['transId']}";

        if (!$this->verifySignature($rawSignature, $data['signature'] ?? '')) {
            Log::warning('MoMo redirect signature mismatch', ['orderId' => $momoOrderId]);
            return [
                'success'    => false,
                'order_code' => null,
                'message'    => 'Chữ ký xác thực không hợp lệ.',
            ];
        }

        // Find transaction and associated order
        $transaction = PaymentTransaction::where('momo_order_id', $momoOrderId)->first();

        if (!$transaction) {
            return [
                'success'    => false,
                'order_code' => null,
                'message'    => 'Không tìm thấy giao dịch.',
            ];
        }

        $order = $transaction->order;

        // Update transaction if IPN hasn't already handled it
        if ($transaction->isPending()) {
            $transaction->update([
                'result_code'   => $resultCode,
                'message'       => $data['message'] ?? null,
                'momo_trans_id' => $data['transId'] ?? null,
                'pay_type'      => $data['payType'] ?? null,
                'status'        => $resultCode === 0
                    ? PaymentTransaction::STATUS_PAID
                    : PaymentTransaction::STATUS_FAILED,
                'paid_at'       => $resultCode === 0 ? now() : null,
            ]);

            // Update order if payment was successful
            if ($resultCode === 0 && $order && $order->payment_status !== Order::PAYMENT_PAID) {
                $order->update([
                    'payment_status' => Order::PAYMENT_PAID,
                    'paid_at'        => now(),
                ]);
            }
        }

        return [
            'success'    => $resultCode === 0,
            'order_code' => $order?->order_code,
            'message'    => $resultCode === 0
                ? 'Thanh toán MoMo thành công!'
                : ($data['message'] ?? 'Thanh toán MoMo không thành công.'),
        ];
    }

    // =========================================================================
    // QUERY TRANSACTION STATUS
    // =========================================================================

    /**
     * Query the status of a MoMo transaction.
     *
     * @return array{success: bool, resultCode: ?int, message: string, data: ?array}
     */
    public function queryTransactionStatus(Order $order): array
    {
        $transaction = $order->paymentTransactions()
            ->where('gateway', PaymentTransaction::GATEWAY_MOMO)
            ->latest()
            ->first();

        if (!$transaction) {
            return [
                'success'    => false,
                'resultCode' => null,
                'message'    => 'Không tìm thấy giao dịch MoMo cho đơn hàng này.',
                'data'       => null,
            ];
        }

        $requestId = $this->generateRequestId();

        $rawSignature = "accessKey={$this->accessKey}"
            . "&orderId={$transaction->momo_order_id}"
            . "&partnerCode={$this->partnerCode}"
            . "&requestId={$requestId}";

        $signature = $this->generateSignature($rawSignature);

        $payload = [
            'partnerCode' => $this->partnerCode,
            'requestId'   => $requestId,
            'orderId'     => $transaction->momo_order_id,
            'lang'        => 'vi',
            'signature'   => $signature,
        ];

        try {
            $response = Http::timeout(30)
                ->post("{$this->apiEndpoint}/v2/gateway/api/query", $payload);

            $responseData = $response->json();

            Log::info('MoMo queryStatus response', [
                'order_code'  => $order->order_code,
                'result_code' => $responseData['resultCode'] ?? null,
            ]);

            $resultCode = (int) ($responseData['resultCode'] ?? -1);

            // Update transaction with latest status from MoMo
            if ($resultCode === 0 && $transaction->status !== PaymentTransaction::STATUS_PAID) {
                $transaction->update([
                    'status'        => PaymentTransaction::STATUS_PAID,
                    'result_code'   => 0,
                    'momo_trans_id' => $responseData['transId'] ?? $transaction->momo_trans_id,
                    'pay_type'      => $responseData['payType'] ?? $transaction->pay_type,
                    'message'       => $responseData['message'] ?? null,
                    'paid_at'       => now(),
                ]);

                // Also update order if not yet paid
                if ($order->payment_status !== Order::PAYMENT_PAID) {
                    $order->update([
                        'payment_status' => Order::PAYMENT_PAID,
                        'paid_at'        => now(),
                    ]);
                }
            }

            return [
                'success'    => true,
                'resultCode' => $resultCode,
                'message'    => $responseData['message'] ?? 'Query completed.',
                'data'       => $responseData,
            ];

        } catch (\Exception $e) {
            Log::error('MoMo queryStatus exception', [
                'order_code' => $order->order_code,
                'error'      => $e->getMessage(),
            ]);

            return [
                'success'    => false,
                'resultCode' => null,
                'message'    => 'Không thể kết nối MoMo để kiểm tra trạng thái.',
                'data'       => null,
            ];
        }
    }

    // =========================================================================
    // SIGNATURE HELPERS
    // =========================================================================

    /**
     * Generate HMAC-SHA256 signature.
     */
    public function generateSignature(string $rawData): string
    {
        return hash_hmac('sha256', $rawData, $this->secretKey);
    }

    /**
     * Verify HMAC-SHA256 signature from MoMo.
     */
    public function verifySignature(string $rawData, string $signature): bool
    {
        $expected = $this->generateSignature($rawData);
        return hash_equals($expected, $signature);
    }

    // =========================================================================
    // ID GENERATORS
    // =========================================================================

    /**
     * Build a unique orderId for MoMo.
     * Format: {order_code}-{unix_timestamp} to ensure uniqueness.
     */
    public function buildMomoOrderId(Order $order): string
    {
        return $order->order_code . '-' . time();
    }

    /**
     * Generate a unique requestId for MoMo API idempotency.
     */
    public function generateRequestId(): string
    {
        return $this->partnerCode . '-' . Str::uuid()->toString();
    }
}
