<?php

namespace Database\Seeders;

use App\Models\Voucher;
use Illuminate\Database\Seeder;

class VoucherSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $vouchers = [
            [
                'code'                       => 'AURELIA20',
                'name'                       => 'Giảm 20% Đơn Từ 200K (Tối đa 15K)',
                'description'                => 'Áp dụng cho mọi đơn hàng từ 200.000₫. Giảm tối đa 15.000₫, hỗ trợ mọi phương thức thanh toán.',
                'discount_type'              => Voucher::TYPE_PERCENTAGE,
                'discount_value'             => 20.00,
                'max_discount_amount'        => 15000,
                'min_order_amount'           => 200000,
                'usage_limit'                => 500,
                'used_count'                 => 0,
                'usage_limit_per_user'       => 2,
                'applicable_payment_methods' => ['all'],
                'is_active'                  => true,
                'starts_at'                  => now()->subDays(1),
                'expires_at'                 => now()->addMonths(3),
            ],
            [
                'code'                       => 'FREESHIP',
                'name'                       => 'Miễn Phí Vận Chuyển 30K',
                'description'                => 'Giảm ngay 30.000₫ phí giao hàng GHN cho đơn hàng từ 150.000₫.',
                'discount_type'              => Voucher::TYPE_SHIPPING_DISCOUNT,
                'discount_value'             => 30000.00,
                'max_discount_amount'        => 30000,
                'min_order_amount'           => 150000,
                'usage_limit'                => 1000,
                'used_count'                 => 0,
                'usage_limit_per_user'       => 5,
                'applicable_payment_methods' => ['all'],
                'is_active'                  => true,
                'starts_at'                  => now()->subDays(1),
                'expires_at'                 => now()->addMonths(6),
            ],
            [
                'code'                       => 'MOMO50K',
                'name'                       => 'Giảm 50K Khi Thanh Toán Ví MoMo',
                'description'                => 'Ưu đãi độc quyền giảm 50.000₫ cho đơn hàng từ 500.000₫ khi chọn thanh toán qua Ví MoMo.',
                'discount_type'              => Voucher::TYPE_FIXED_AMOUNT,
                'discount_value'             => 50000.00,
                'max_discount_amount'        => null,
                'min_order_amount'           => 500000,
                'usage_limit'                => 200,
                'used_count'                 => 0,
                'usage_limit_per_user'       => 1,
                'applicable_payment_methods' => ['momo'],
                'is_active'                  => true,
                'starts_at'                  => now()->subDays(1),
                'expires_at'                 => now()->addMonths(2),
            ],
            [
                'code'                       => 'WELCOME10',
                'name'                       => 'Chào Mừng Khách Hàng Mới - Giảm 10%',
                'description'                => 'Giảm ngay 10% tối đa 100.000₫ cho đơn hàng từ 100.000₫.',
                'discount_type'              => Voucher::TYPE_PERCENTAGE,
                'discount_value'             => 10.00,
                'max_discount_amount'        => 100000,
                'min_order_amount'           => 100000,
                'usage_limit'                => 300,
                'used_count'                 => 0,
                'usage_limit_per_user'       => 1,
                'applicable_payment_methods' => ['all'],
                'is_active'                  => true,
                'starts_at'                  => now()->subDays(1),
                'expires_at'                 => now()->addMonths(12),
            ],
            [
                'code'                       => 'VIP100K',
                'name'                       => 'Tri Ân Đơn Lớn - Giảm 100K',
                'description'                => 'Giảm trực tiếp 100.000₫ cho các đơn hàng cao cấp từ 1.000.000₫.',
                'discount_type'              => Voucher::TYPE_FIXED_AMOUNT,
                'discount_value'             => 100000.00,
                'max_discount_amount'        => null,
                'min_order_amount'           => 1000000,
                'usage_limit'                => 50,
                'used_count'                 => 0,
                'usage_limit_per_user'       => 1,
                'applicable_payment_methods' => ['all'],
                'is_active'                  => true,
                'starts_at'                  => now()->subDays(1),
                'expires_at'                 => now()->addMonths(1),
            ],
        ];

        foreach ($vouchers as $voucherData) {
            Voucher::updateOrCreate(
                ['code' => $voucherData['code']],
                $voucherData
            );
        }
    }
}
