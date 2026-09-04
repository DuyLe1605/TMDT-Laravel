<?php

namespace App\Services;

use App\Models\CoinTransaction;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class CoinService
{
    /**
     * Tỉ lệ xu tối đa được dùng trên tổng tiền hàng sau voucher: 10%
     */
    public const MAX_SPEND_PERCENT = 0.10;

    /**
     * Trần xu tối đa cho mỗi đơn hàng: 30.000 Xu (tương đương 30.000 VNĐ)
     */
    public const MAX_SPEND_PER_ORDER = 30000;

    /**
     * Tỉ giá quy đổi: 1 Xu = 1 VNĐ
     */
    public const COIN_EXCHANGE_RATE = 1;

    /**
     * Calculate maximum redeemable coins for a purchase.
     *
     * @param User $user
     * @param float $netGoodsTotal Giá trị hàng sau voucher (không tính phí ship)
     * @return int
     */
    public function calculateMaxRedeemableCoins(User $user, float $netGoodsTotal): int
    {
        if ($user->coins_balance <= 0 || $netGoodsTotal <= 0) {
            return 0;
        }

        // Tối đa 10% giá trị tiền hàng sau voucher
        $percentMax = (int) floor($netGoodsTotal * self::MAX_SPEND_PERCENT);

        // Trần tuyệt đối 30.000 Xu
        $ceilingMax = min($percentMax, self::MAX_SPEND_PER_ORDER);

        // Không vượt quá số dư thực có của khách
        return min((int) $user->coins_balance, $ceilingMax);
    }

    /**
     * Cộng Xu vào ví người dùng (Earn)
     *
     * @throws InvalidArgumentException
     */
    public function addCoins(
        User $user,
        int $amount,
        string $referenceType,
        int $referenceId,
        string $description
    ): CoinTransaction {
        if ($amount <= 0) {
            throw new InvalidArgumentException('Số xu cộng thêm phải lớn hơn 0.');
        }

        return DB::transaction(function () use ($user, $amount, $referenceType, $referenceId, $description) {
            /** @var User $lockedUser */
            $lockedUser = User::where('id', $user->id)->lockForUpdate()->firstOrFail();

            $newBalance = $lockedUser->coins_balance + $amount;
            $lockedUser->coins_balance = $newBalance;
            $lockedUser->save();

            $transaction = CoinTransaction::create([
                'user_id'        => $lockedUser->id,
                'type'           => CoinTransaction::TYPE_EARN,
                'amount'         => $amount,
                'balance_after'  => $newBalance,
                'reference_type' => $referenceType,
                'reference_id'   => $referenceId,
                'description'    => $description,
            ]);

            $user->coins_balance = $newBalance;

            return $transaction;
        });
    }

    /**
     * Trừ Xu khi thanh toán đơn hàng (Spend)
     *
     * @throws InvalidArgumentException
     */
    public function deductCoins(
        User $user,
        int $amount,
        string $referenceType,
        int $referenceId,
        string $description
    ): CoinTransaction {
        if ($amount <= 0) {
            throw new InvalidArgumentException('Số xu sử dụng phải lớn hơn 0.');
        }

        return DB::transaction(function () use ($user, $amount, $referenceType, $referenceId, $description) {
            /** @var User $lockedUser */
            $lockedUser = User::where('id', $user->id)->lockForUpdate()->firstOrFail();

            if ($lockedUser->coins_balance < $amount) {
                throw new InvalidArgumentException("Số dư Xu không đủ (Hiện có: {$lockedUser->coins_balance} Xu).");
            }

            $newBalance = $lockedUser->coins_balance - $amount;
            $lockedUser->coins_balance = $newBalance;
            $lockedUser->save();

            $transaction = CoinTransaction::create([
                'user_id'        => $lockedUser->id,
                'type'           => CoinTransaction::TYPE_SPEND,
                'amount'         => -$amount,
                'balance_after'  => $newBalance,
                'reference_type' => $referenceType,
                'reference_id'   => $referenceId,
                'description'    => $description,
            ]);

            $user->coins_balance = $newBalance;

            return $transaction;
        });
    }

    /**
     * Hoàn lại Xu khi hủy đơn hàng (Refund)
     *
     * @throws InvalidArgumentException
     */
    public function refundCoins(
        User $user,
        int $amount,
        string $referenceType,
        int $referenceId,
        string $description
    ): ?CoinTransaction {
        if ($amount <= 0) {
            return null;
        }

        return DB::transaction(function () use ($user, $amount, $referenceType, $referenceId, $description) {
            /** @var User $lockedUser */
            $lockedUser = User::where('id', $user->id)->lockForUpdate()->firstOrFail();

            $newBalance = $lockedUser->coins_balance + $amount;
            $lockedUser->coins_balance = $newBalance;
            $lockedUser->save();

            $transaction = CoinTransaction::create([
                'user_id'        => $lockedUser->id,
                'type'           => CoinTransaction::TYPE_REFUND,
                'amount'         => $amount,
                'balance_after'  => $newBalance,
                'reference_type' => $referenceType,
                'reference_id'   => $referenceId,
                'description'    => $description,
            ]);

            $user->coins_balance = $newBalance;

            return $transaction;
        });
    }
}
