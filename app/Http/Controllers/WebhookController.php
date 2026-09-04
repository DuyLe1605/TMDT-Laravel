<?php

namespace App\Http\Controllers;

use App\Services\OrderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    public function __construct(
        protected OrderService $orderService
    ) {}

    /**
     * Handle GHN webhook callback.
     * GHN sends POST requests when order status changes.
     */
    public function ghnCallback(Request $request): JsonResponse
    {
        $payload = $request->all();

        Log::info('GHN Webhook received', $payload);

        // Validate required fields
        if (empty($payload['Status'])) {
            Log::warning('GHN Webhook: Missing Status field', $payload);
            return response()->json(['success' => false, 'message' => 'Missing Status'], 400);
        }

        try {
            $result = $this->orderService->processGhnWebhook($payload);

            if ($result) {
                Log::info('GHN Webhook processed successfully', [
                    'order_code' => $payload['ClientOrderCode'] ?? $payload['OrderCode'] ?? 'unknown',
                    'status' => $payload['Status'],
                ]);
                return response()->json(['success' => true]);
            }

            Log::warning('GHN Webhook: Order not found', $payload);
            return response()->json(['success' => false, 'message' => 'Order not found'], 404);
        } catch (\Exception $e) {
            Log::error('GHN Webhook processing error: ' . $e->getMessage(), $payload);
            return response()->json(['success' => false, 'message' => 'Internal error'], 500);
        }
    }
}
