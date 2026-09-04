<?php

namespace App\Http\Controllers;

use App\Models\CoinTransaction;
use App\Services\CoinService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CoinController extends Controller
{
    public function __construct(
        protected CoinService $coinService
    ) {}

    /**
     * Display user's coins wallet and transaction ledger.
     */
    public function index(Request $request): View
    {
        $user = $request->user();

        $transactions = CoinTransaction::where('user_id', $user->id)
            ->latest()
            ->paginate(15);

        $totalEarned = CoinTransaction::where('user_id', $user->id)
            ->where('type', CoinTransaction::TYPE_EARN)
            ->sum('amount');

        $totalSpent = abs(CoinTransaction::where('user_id', $user->id)
            ->where('type', CoinTransaction::TYPE_SPEND)
            ->sum('amount'));

        return view('account.coins', [
            'user'         => $user,
            'transactions' => $transactions,
            'totalEarned'  => $totalEarned,
            'totalSpent'   => $totalSpent,
        ]);
    }

    /**
     * Preview maximum redeemable coins for checkout.
     */
    public function calculateRedeemable(Request $request): JsonResponse
    {
        $user = $request->user();
        if (!$user) {
            return response()->json([
                'success'   => false,
                'message'   => 'Vui lòng đăng nhập để sử dụng Xu.',
                'max_coins' => 0,
            ], 401);
        }

        $netGoodsTotal = max(0, (float) $request->input('net_goods_total', 0));
        $maxCoins = $this->coinService->calculateMaxRedeemableCoins($user, $netGoodsTotal);
        $discountAmount = $maxCoins * CoinService::COIN_EXCHANGE_RATE;

        return response()->json([
            'success'                  => true,
            'user_balance'             => (int) $user->coins_balance,
            'formatted_user_balance'   => number_format((int) $user->coins_balance, 0, ',', '.') . ' Xu',
            'max_coins'                => $maxCoins,
            'discount_amount'          => $discountAmount,
            'formatted_discount'       => number_format($discountAmount, 0, ',', '.') . ' ₫',
            'max_spend_percent'        => (CoinService::MAX_SPEND_PERCENT * 100) . '%',
            'max_spend_limit'          => number_format(CoinService::MAX_SPEND_PER_ORDER, 0, ',', '.') . ' Xu',
        ]);
    }
}
