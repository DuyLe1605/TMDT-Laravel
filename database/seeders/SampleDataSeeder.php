<?php

namespace Database\Seeders;

use App\Models\Address;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;
use App\Models\Voucher;
use App\Models\VoucherUsage;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class SampleDataSeeder extends Seeder
{
    /**
     * Seed realistic sample customers, vouchers, and orders across all states.
     */
    public function run(): void
    {
        // ---------------------------------------------------------------------
        // 1. SAMPLE CUSTOMERS
        // ---------------------------------------------------------------------
        $customers = [
            [
                'name'     => 'Nguyễn Văn An',
                'email'    => 'khachhang@gmail.com',
                'password' => Hash::make('password'),
                'role'     => 'customer',
                'phone'    => '0912345678',
            ],
            [
                'name'     => 'Trần Thị Lan Anh',
                'email'    => 'lananh@gmail.com',
                'password' => Hash::make('password'),
                'role'     => 'customer',
                'phone'    => '0987654321',
            ],
            [
                'name'     => 'Lê Hoàng Nam',
                'email'    => 'hoangnam@gmail.com',
                'password' => Hash::make('password'),
                'role'     => 'customer',
                'phone'    => '0905123456',
            ],
            [
                'name'     => 'Phạm Thu Thảo',
                'email'    => 'thuthao@gmail.com',
                'password' => Hash::make('password'),
                'role'     => 'customer',
                'phone'    => '0933888999',
            ],
        ];

        $seededUsers = [];
        foreach ($customers as $c) {
            $user = User::updateOrCreate(
                ['email' => $c['email']],
                [
                    'name'     => $c['name'],
                    'password' => $c['password'],
                    'role'     => $c['role'],
                    'email_verified_at' => now(),
                ]
            );
            $user->phone_number = $c['phone'];
            $seededUsers[] = $user;

            // Seed addresses for customer
            Address::updateOrCreate(
                ['user_id' => $user->id, 'phone' => $c['phone']],
                [
                    'recipient_name'   => $c['name'],
                    'phone'            => $c['phone'],
                    'province'         => 'Hà Nội',
                    'district'         => 'Quận Hoàn Kiếm',
                    'ward'             => 'Phường Tràng Tiền',
                    'specific_address' => 'Số 15 Tràng Tiền',
                    'address_type'     => 'home',
                    'is_default'       => true,
                ]
            );

            Address::updateOrCreate(
                ['user_id' => $user->id, 'recipient_name' => $c['name'] . ' (Công ty)'],
                [
                    'recipient_name'   => $c['name'] . ' (Công ty)',
                    'phone'            => $c['phone'],
                    'province'         => 'Thành phố Hồ Chí Minh',
                    'district'         => 'Quận 1',
                    'ward'             => 'Phường Bến Nghé',
                    'specific_address' => 'Tầng 12, Tòa nhà Bitexco, số 2 Hải Triều',
                    'address_type'     => 'office',
                    'is_default'       => false,
                ]
            );
        }

        // ---------------------------------------------------------------------
        // 2. SAMPLE VOUCHERS (ACTIVE, EXPIRED, EXHAUSTED, RESTRICTED)
        // ---------------------------------------------------------------------
        $vouchers = [
            [
                'code'                       => 'AURELIA20',
                'name'                       => 'Giảm 20% Đơn Từ 200K (Tối đa 15K)',
                'description'                => 'Áp dụng cho mọi đơn hàng từ 200.000₫. Giảm tối đa 15.000₫, hỗ trợ mọi hình thức thanh toán.',
                'discount_type'              => Voucher::TYPE_PERCENTAGE,
                'discount_value'             => 20.00,
                'max_discount_amount'        => 15000,
                'min_order_amount'           => 200000,
                'usage_limit'                => 500,
                'used_count'                 => 12,
                'usage_limit_per_user'       => 2,
                'applicable_payment_methods' => ['all'],
                'is_active'                  => true,
                'starts_at'                  => now()->subDays(5),
                'expires_at'                 => now()->addMonths(3),
            ],
            [
                'code'                       => 'FREESHIP',
                'name'                       => 'Miễn Phí Vận Chuyển 30K',
                'description'                => 'Giảm ngay 30.000₫ phí giao hàng GHN cho đơn hàng từ 150.000₫.',
                'discount_type'              => Voucher::TYPE_SHIPPING,
                'discount_value'             => 30000.00,
                'max_discount_amount'        => 30000,
                'min_order_amount'           => 150000,
                'usage_limit'                => 1000,
                'used_count'                 => 25,
                'usage_limit_per_user'       => 5,
                'applicable_payment_methods' => ['all'],
                'is_active'                  => true,
                'starts_at'                  => now()->subDays(5),
                'expires_at'                 => now()->addMonths(6),
            ],
            [
                'code'                       => 'MOMO50K',
                'name'                       => 'Giảm 50K Khi Thanh Toán Ví MoMo',
                'description'                => 'Ưu đãi độc quyền giảm 50.000₫ cho đơn hàng từ 500.000₫ khi chọn thanh toán qua Ví MoMo.',
                'discount_type'              => Voucher::TYPE_FIXED,
                'discount_value'             => 50000.00,
                'max_discount_amount'        => null,
                'min_order_amount'           => 500000,
                'usage_limit'                => 200,
                'used_count'                 => 8,
                'usage_limit_per_user'       => 1,
                'applicable_payment_methods' => ['momo'],
                'is_active'                  => true,
                'starts_at'                  => now()->subDays(5),
                'expires_at'                 => now()->addMonths(2),
            ],
            [
                'code'                       => 'BANK30K',
                'name'                       => 'Giảm 30K Khi Chuyển Khoản Ngân Hàng',
                'description'                => 'Giảm trực tiếp 30.000₫ cho đơn từ 300.000₫ khi thanh toán qua Chuyển khoản VietQR.',
                'discount_type'              => Voucher::TYPE_FIXED,
                'discount_value'             => 30000.00,
                'max_discount_amount'        => null,
                'min_order_amount'           => 300000,
                'usage_limit'                => 300,
                'used_count'                 => 5,
                'usage_limit_per_user'       => 2,
                'applicable_payment_methods' => ['bank_transfer'],
                'is_active'                  => true,
                'starts_at'                  => now()->subDays(2),
                'expires_at'                 => now()->addMonths(3),
            ],
            [
                'code'                       => 'WELCOME10',
                'name'                       => 'Chào Mừng Bạn Mới - Giảm 10%',
                'description'                => 'Giảm 10% tối đa 100.000₫ cho đơn hàng từ 100.000₫ dành cho thành viên mới.',
                'discount_type'              => Voucher::TYPE_PERCENTAGE,
                'discount_value'             => 10.00,
                'max_discount_amount'        => 100000,
                'min_order_amount'           => 100000,
                'usage_limit'                => 500,
                'used_count'                 => 18,
                'usage_limit_per_user'       => 1,
                'applicable_payment_methods' => ['all'],
                'is_active'                  => true,
                'starts_at'                  => now()->subDays(10),
                'expires_at'                 => now()->addMonths(12),
            ],
            [
                'code'                       => 'VIP100K',
                'name'                       => 'Tri Ân Khách VIP - Giảm 100K',
                'description'                => 'Giảm 100.000₫ cho các đơn hàng cao cấp từ 1.000.000₫.',
                'discount_type'              => Voucher::TYPE_FIXED,
                'discount_value'             => 100000.00,
                'max_discount_amount'        => null,
                'min_order_amount'           => 1000000,
                'usage_limit'                => 100,
                'used_count'                 => 14,
                'usage_limit_per_user'       => 2,
                'applicable_payment_methods' => ['all'],
                'is_active'                  => true,
                'starts_at'                  => now()->subDays(10),
                'expires_at'                 => now()->addMonths(2),
            ],
            // Ineligible Test Vouchers:
            [
                'code'                       => 'DONLON2M',
                'name'                       => 'Ưu Đãi Đơn Khủng - Giảm 250K',
                'description'                => 'Giảm ngay 250.000₫ cho đơn hàng từ 2.000.000₫ (Dùng test gợi ý mua thêm).',
                'discount_type'              => Voucher::TYPE_FIXED,
                'discount_value'             => 250000.00,
                'max_discount_amount'        => null,
                'min_order_amount'           => 2000000,
                'usage_limit'                => 50,
                'used_count'                 => 2,
                'usage_limit_per_user'       => 1,
                'applicable_payment_methods' => ['all'],
                'is_active'                  => true,
                'starts_at'                  => now()->subDays(1),
                'expires_at'                 => now()->addMonths(1),
            ],
            [
                'code'                       => 'HETLUOT',
                'name'                       => 'Mã Giảm 50K Đã Hết Lượt',
                'description'                => 'Mã đã đạt tối đa số lượt dùng trên toàn hệ thống (Dùng test ineligible).',
                'discount_type'              => Voucher::TYPE_FIXED,
                'discount_value'             => 50000.00,
                'max_discount_amount'        => null,
                'min_order_amount'           => 100000,
                'usage_limit'                => 10,
                'used_count'                 => 10,
                'usage_limit_per_user'       => 1,
                'applicable_payment_methods' => ['all'],
                'is_active'                  => true,
                'starts_at'                  => now()->subDays(5),
                'expires_at'                 => now()->addMonths(1),
            ],
            [
                'code'                       => 'EXPIRED50',
                'name'                       => 'Mã Giảm 50% Đã Hết Hạn',
                'description'                => 'Mã ưu đãi đã hết hạn từ tuần trước (Dùng test ineligible).',
                'discount_type'              => Voucher::TYPE_PERCENTAGE,
                'discount_value'             => 50.00,
                'max_discount_amount'        => 50000,
                'min_order_amount'           => 100000,
                'usage_limit'                => 100,
                'used_count'                 => 15,
                'usage_limit_per_user'       => 1,
                'applicable_payment_methods' => ['all'],
                'is_active'                  => true,
                'starts_at'                  => now()->subDays(20),
                'expires_at'                 => now()->subDays(3),
            ],
        ];

        foreach ($vouchers as $vData) {
            Voucher::updateOrCreate(['code' => $vData['code']], $vData);
        }

        // ---------------------------------------------------------------------
        // 3. SAMPLE ORDERS ACROSS ALL STATUSES (PENDING, PROCESSING, SHIPPING, DELIVERED, CANCELLED)
        // ---------------------------------------------------------------------
        $allProducts = Product::with('variants')->take(6)->get();
        if ($allProducts->isEmpty()) {
            return;
        }

        $orderTemplates = [
            // 1. PENDING (Customer CAN cancel, Admin CAN cancel)
            [
                'shipping_status' => Order::STATUS_PENDING,
                'payment_status'  => Order::PAYMENT_PENDING,
                'payment_method'  => 'cod',
                'voucher_code'    => null,
                'discount_amount' => 0,
                'ghn_order_code'  => null,
                'notes'           => 'Giao trong giờ hành chính, gọi trước 15 phút.',
                'created_at'      => now()->subHours(2),
            ],
            // 2. PENDING with MOMO50K voucher (Customer CAN cancel, Admin CAN cancel)
            [
                'shipping_status' => Order::STATUS_PENDING,
                'payment_status'  => Order::PAYMENT_PAID,
                'payment_method'  => 'momo',
                'voucher_code'    => 'MOMO50K',
                'discount_amount' => 50000,
                'ghn_order_code'  => null,
                'notes'           => 'Đã thanh toán MoMo thành công.',
                'created_at'      => now()->subHours(4),
            ],
            // 3. PROCESSING (Customer CANNOT cancel, Admin CAN cancel)
            [
                'shipping_status' => Order::STATUS_PROCESSING,
                'payment_status'  => Order::PAYMENT_PAID,
                'payment_method'  => 'bank_transfer',
                'voucher_code'    => 'AURELIA20',
                'discount_amount' => 15000,
                'ghn_order_code'  => null,
                'notes'           => 'Shop kiểm tra kỹ đường may giúp mình nhé.',
                'created_at'      => now()->subHours(10),
            ],
            // 4. SHIPPING with GHN code (Customer CANNOT cancel, Admin CANNOT cancel)
            [
                'shipping_status' => Order::STATUS_SHIPPING,
                'payment_status'  => Order::PAYMENT_PENDING,
                'payment_method'  => 'cod',
                'voucher_code'    => 'FREESHIP',
                'discount_amount' => 30000,
                'ghn_order_code'  => 'GHN' . rand(10000000, 99999999),
                'notes'           => 'Hàng có bảo hiểm cao cấp.',
                'created_at'      => now()->subDays(1),
            ],
            // 5. SHIPPING with MoMo (Customer CANNOT cancel, Admin CANNOT cancel)
            [
                'shipping_status' => Order::STATUS_SHIPPING,
                'payment_status'  => Order::PAYMENT_PAID,
                'payment_method'  => 'momo',
                'voucher_code'    => null,
                'discount_amount' => 0,
                'ghn_order_code'  => 'GHN' . rand(10000000, 99999999),
                'notes'           => 'Bọc chống sốc cẩn thận.',
                'created_at'      => now()->subDays(1)->subHours(5),
            ],
            // 6. DELIVERED (Customer CAN re-order, CANNOT cancel)
            [
                'shipping_status' => Order::STATUS_DELIVERED,
                'payment_status'  => Order::PAYMENT_PAID,
                'payment_method'  => 'cod',
                'voucher_code'    => 'WELCOME10',
                'discount_amount' => 85000,
                'ghn_order_code'  => 'GHN' . rand(10000000, 99999999),
                'notes'           => null,
                'created_at'      => now()->subDays(3),
            ],
            // 7. DELIVERED VIP (Customer CAN re-order, CANNOT cancel)
            [
                'shipping_status' => Order::STATUS_DELIVERED,
                'payment_status'  => Order::PAYMENT_PAID,
                'payment_method'  => 'bank_transfer',
                'voucher_code'    => 'VIP100K',
                'discount_amount' => 100000,
                'ghn_order_code'  => 'GHN' . rand(10000000, 99999999),
                'notes'           => 'Tặng kèm túi chống bụi và thiệp mừng.',
                'created_at'      => now()->subDays(5),
            ],
            // 8. RETURNING (Delivery issue, returning to shop)
            [
                'shipping_status' => Order::STATUS_RETURNING,
                'payment_status'  => Order::PAYMENT_PENDING,
                'payment_method'  => 'cod',
                'voucher_code'    => null,
                'discount_amount' => 0,
                'ghn_order_code'  => 'GHN' . rand(10000000, 99999999),
                'notes'           => 'Khách đi công tác không thể nhận hàng, xin hoàn hàng.',
                'created_at'      => now()->subDays(6),
            ],
            // 9. CANCELLED by Customer (Customer CAN re-order)
            [
                'shipping_status' => Order::STATUS_CANCELLED,
                'payment_status'  => Order::PAYMENT_PENDING,
                'payment_method'  => 'cod',
                'voucher_code'    => null,
                'discount_amount' => 0,
                'ghn_order_code'  => null,
                'cancel_reason'   => 'Đổi ý muốn mua mẫu túi màu đen khác.',
                'cancelled_at'    => now()->subDays(2),
                'notes'           => null,
                'created_at'      => now()->subDays(2)->subHours(1),
            ],
            // 10. CANCELLED with Voucher Restored (Paid online -> refunding)
            [
                'shipping_status' => Order::STATUS_CANCELLED,
                'payment_status'  => Order::PAYMENT_REFUNDING,
                'payment_method'  => 'momo',
                'voucher_code'    => 'AURELIA20',
                'discount_amount' => 15000,
                'ghn_order_code'  => null,
                'cancel_reason'   => 'Khách yêu cầu hủy qua hotline do đặt trùng 2 đơn.',
                'cancelled_at'    => now()->subDays(1),
                'notes'           => 'Đang xử lý hoàn tiền MoMo.',
                'created_at'      => now()->subDays(1)->subHours(3),
            ],
        ];

        foreach ($orderTemplates as $index => $tmpl) {
            $customer = $seededUsers[$index % count($seededUsers)];
            $product1 = $allProducts[$index % count($allProducts)];
            $product2 = $allProducts[($index + 1) % count($allProducts)];

            $variant1 = $product1->variants->first();
            $variant2 = $product2->variants->first();

            $price1 = $variant1 ? (float)$variant1->effective_price : (float)$product1->effective_price;
            $price2 = $variant2 ? (float)$variant2->effective_price : (float)$product2->effective_price;

            $qty1 = 1;
            $qty2 = ($index % 2 === 0) ? 1 : 0;

            $subtotal = ($price1 * $qty1) + ($price2 * $qty2);
            $shippingFee = ($subtotal >= 500000 || $tmpl['voucher_code'] === 'FREESHIP') ? 0 : 30000;
            $discount = (float)($tmpl['discount_amount'] ?? 0);
            $totalAmount = max(0, $subtotal + $shippingFee - $discount);

            $voucherModel = !empty($tmpl['voucher_code'])
                ? Voucher::where('code', $tmpl['voucher_code'])->first()
                : null;

            $orderCode = 'AUR-' . now()->format('Ymd') . '-' . strtoupper(Str::random(5));

            $order = Order::create([
                'user_id'              => $customer->id,
                'order_code'           => $orderCode,
                'recipient_name'       => $customer->name,
                'phone'                => $customer->phone_number ?? '0912345678',
                'shipping_address'     => 'Số ' . ($index + 12) . ' Tràng Tiền, Phường Tràng Tiền, Quận Hoàn Kiếm, Hà Nội',
                'shipping_status'      => $tmpl['shipping_status'],
                'shipping_fee'         => $shippingFee,
                'to_district_id'       => 1482,
                'to_ward_code'         => '1A0101',
                'expected_delivery_at' => now()->addDays(2),
                'total_weight'         => 650,
                'ghn_order_code'       => $tmpl['ghn_order_code'] ?? null,
                'ghn_status'           => $tmpl['shipping_status'] === Order::STATUS_SHIPPING ? 'delivering' : null,
                'ghn_status_name'      => $tmpl['shipping_status'] === Order::STATUS_SHIPPING ? 'Đang giao hàng' : null,
                'payment_method'       => $tmpl['payment_method'],
                'payment_status'       => $tmpl['payment_status'],
                'voucher_id'           => $voucherModel?->id,
                'voucher_code'         => $tmpl['voucher_code'],
                'discount_amount'      => $discount,
                'subtotal'             => $subtotal,
                'total_amount'         => $totalAmount,
                'notes'                => $tmpl['notes'],
                'cancel_reason'        => $tmpl['cancel_reason'] ?? null,
                'cancelled_at'         => $tmpl['cancelled_at'] ?? null,
                'paid_at'              => $tmpl['payment_status'] === Order::PAYMENT_PAID ? now()->subHours(1) : null,
                'created_at'           => $tmpl['created_at'],
                'updated_at'           => $tmpl['created_at'],
            ]);

            // Create Order Item 1
            OrderItem::create([
                'order_id'           => $order->id,
                'product_id'         => $product1->id,
                'product_variant_id' => $variant1?->id,
                'product_name'       => $product1->name,
                'variant_title'      => $variant1?->variant_title,
                'product_image'      => $variant1?->image ?? $product1->image,
                'price'              => $price1,
                'quantity'           => $qty1,
                'subtotal'           => $price1 * $qty1,
            ]);

            // Create Order Item 2 if applicable
            if ($qty2 > 0) {
                OrderItem::create([
                    'order_id'           => $order->id,
                    'product_id'         => $product2->id,
                    'product_variant_id' => $variant2?->id,
                    'product_name'       => $product2->name,
                    'variant_title'      => $variant2?->variant_title,
                    'product_image'      => $variant2?->image ?? $product2->image,
                    'price'              => $price2,
                    'quantity'           => $qty2,
                    'subtotal'           => $price2 * $qty2,
                ]);
            }

            // If order has voucher and is not cancelled, record usage log
            if ($voucherModel && $discount > 0 && $tmpl['shipping_status'] !== Order::STATUS_CANCELLED) {
                VoucherUsage::create([
                    'voucher_id'      => $voucherModel->id,
                    'user_id'         => $customer->id,
                    'order_id'        => $order->id,
                    'discount_amount' => $discount,
                    'used_at'         => $tmpl['created_at'],
                ]);
            }
        }
    }
}
