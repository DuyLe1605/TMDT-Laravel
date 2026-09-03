@extends('layouts.storefront')

@section('title', 'Đặt Hàng Thành Công - Mã Đơn: ' . $order->order_code)

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- Main Success Card -->
            <div class="card-modern p-4 p-md-5 shadow-sm border text-center mb-4">
                <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-3.5" style="width: 76px; height: 76px; background: var(--success-50, #ecfdf5); color: var(--success-600, #059669); border: 2px solid #a7f3d0;">
                    <i data-lucide="check" style="width: 40px; height: 40px; stroke-width: 2.5;"></i>
                </div>

                <h2 class="fw-extrabold text-dark mb-2" style="letter-spacing: -0.02em;">
                    Đặt Hàng Thành Công!
                </h2>
                <p class="text-secondary small mb-4" style="max-width: 500px; margin-left: auto; margin-right: auto; line-height: 1.6;">
                    Cảm ơn bạn đã tin tưởng và lựa chọn sản phẩm túi xách tại <strong>Aurelia Luxury Bags</strong>. Đơn hàng của bạn đã được ghi nhận và đang được chuẩn bị đóng gói.
                </p>

                <!-- Order Code Badge Box -->
                <div class="d-inline-flex flex-wrap align-items-center justify-content-center gap-2 p-2.5 px-4 rounded-pill border mb-4" style="background: var(--bg-surface-subtle);">
                    <span class="text-secondary small">Mã đơn hàng:</span>
                    <span class="fw-extrabold text-primary font-monospace fs-6" id="orderCodeText">{{ $order->order_code }}</span>
                    <button type="button" class="btn btn-sm btn-link text-primary p-0 text-decoration-none" onclick="copyOrderCode('{{ $order->order_code }}')" title="Sao chép mã đơn">
                        <i data-lucide="copy" style="width: 14px; height: 14px;"></i>
                    </button>
                </div>

                <!-- Order Details Receipt -->
                <div class="text-start p-4 rounded-3 mb-4" style="background: var(--bg-surface-subtle); border: 1px solid var(--border-default);">
                    <h6 class="fw-bold text-dark mb-3 pb-2 border-bottom d-flex align-items-center justify-content-between">
                        <span>Thông Tin Giao Nhận</span>
                        <span class="badge {{ $order->shipping_status_badge['class'] }} small">
                            {{ $order->shipping_status_badge['label'] }}
                        </span>
                    </h6>

                    <div class="row g-3 small mb-4">
                        <div class="col-sm-6">
                            <span class="text-secondary">Người nhận:</span>
                            <div class="fw-bold text-dark">{{ $order->recipient_name }}</div>
                        </div>
                        <div class="col-sm-6">
                            <span class="text-secondary">Số điện thoại:</span>
                            <div class="fw-bold text-dark">{{ $order->phone }}</div>
                        </div>
                        <div class="col-12">
                            <span class="text-secondary">Địa chỉ giao hàng:</span>
                            <div class="fw-semibold text-dark">{{ $order->shipping_address }}</div>
                        </div>
                        <div class="col-sm-6">
                            <span class="text-secondary">Phương thức thanh toán:</span>
                            <div class="fw-semibold text-dark">{{ $order->payment_method_label }}</div>
                        </div>
                        <div class="col-sm-6">
                            <span class="text-secondary">Trạng thái thanh toán:</span>
                            <div>
                                <span class="badge {{ $order->payment_status_badge['class'] }}">
                                    {{ $order->payment_status_badge['label'] }}
                                </span>
                            </div>
                        </div>
                        @if ($order->notes)
                            <div class="col-12">
                                <span class="text-secondary">Ghi chú:</span>
                                <div class="fst-italic text-dark">{{ $order->notes }}</div>
                            </div>
                        @endif
                    </div>

                    <!-- Products Table -->
                    <h6 class="fw-bold text-dark mb-2.5 pb-2 border-bottom">Sản Phẩm Đã Đặt</h6>
                    <div class="d-flex flex-column gap-2 mb-3">
                        @foreach ($order->items as $item)
                            <div class="d-flex align-items-center justify-content-between gap-3 py-1.5 border-bottom border-light">
                                <div class="d-flex align-items-center gap-2.5 min-w-0">
                                    <div class="rounded-2 border overflow-hidden flex-shrink-0" style="width: 44px; height: 44px;">
                                        @if ($item->product_image)
                                            <img src="{{ $item->product_image }}" alt="{{ $item->product_name }}" class="w-100 h-100 object-fit-cover">
                                        @else
                                            <div class="w-100 h-100 d-flex align-items-center justify-content-center bg-light">
                                                <i data-lucide="shopping-bag" style="width: 16px; height: 16px;"></i>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="min-w-0">
                                        <div class="fw-semibold text-dark small text-truncate">{{ $item->product_name }}</div>
                                        @if ($item->variant_title)
                                            <div class="text-primary" style="font-size: 0.72rem; font-weight: 500;">
                                                Phân loại: {{ $item->variant_title }}
                                            </div>
                                        @endif
                                        <div class="text-secondary" style="font-size: 0.75rem;">
                                            {{ $item->formatted_price }} &times; {{ $item->quantity }}
                                        </div>
                                    </div>
                                </div>
                                <span class="fw-bold text-dark small">{{ $item->formatted_subtotal }}</span>
                            </div>
                        @endforeach
                    </div>

                    <!-- Pricing breakdown -->
                    <div class="d-flex flex-column gap-1.5 pt-2 text-secondary small border-top">
                        <div class="d-flex justify-content-between">
                            <span>Tiền hàng:</span>
                            <span class="fw-bold text-dark">{{ $order->formatted_subtotal }}</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span>Phí vận chuyển:</span>
                            <span class="fw-bold text-dark">{{ $order->formatted_shipping_fee }}</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-baseline pt-2 border-top">
                            <span class="fw-bold text-dark fs-6">Tổng thanh toán:</span>
                            <span class="fw-extrabold text-primary fs-5">{{ $order->formatted_total_amount }}</span>
                        </div>
                    </div>
                </div>

                @if ($order->payment_method === 'momo' && $order->payment_status === 'pending')
                    <!-- MoMo QR Box -->
                    <div class="p-4 rounded-3 border mb-4 text-center" style="background: rgba(165, 0, 100, 0.05); border-color: rgba(165, 0, 100, 0.3) !important;">
                        <div class="d-inline-flex align-items-center gap-2 mb-2 px-3 py-1 rounded-pill" style="background: #a50064; color: #fff; font-size: 0.8rem; font-weight: 700;">
                            <span>MOMO E-WALLET</span>
                        </div>
                        <h6 class="fw-bold mb-2" style="color: #a50064;">Quét Mã MoMo Để Thanh Toán</h6>
                        <p class="text-secondary small mb-3">
                            Mở ứng dụng <strong>Ví MoMo</strong> trên điện thoại và quét mã QR bên dưới để thanh toán đơn hàng:
                        </p>
                        <div class="d-inline-block p-3 bg-white rounded-3 shadow-sm border mb-3">
                            <!-- MoMo QR Code (Using standardized VietQR MoMo NAPAS or quick pay QR) -->
                            <img 
                                src="https://api.vietqr.io/image/970422-999988886666-compact2.jpg?amount={{ (int)$order->total_amount }}&addInfo=MOMO_{{ $order->order_code }}&accountName=AURELIA%20MOMO" 
                                alt="MoMo Payment QR" 
                                class="img-fluid" 
                                style="max-width: 230px;"
                            >
                        </div>
                        <div class="small text-secondary">
                            Số Ví MoMo: <strong style="color: #a50064;">0988 889 999</strong> &bull; Chủ tài khoản: <strong>AURELIA STORE</strong> &bull; Lời nhắn: <strong class="text-dark">{{ $order->order_code }}</strong>
                        </div>
                    </div>
                @elseif ($order->payment_method === 'bank_transfer' && $order->payment_status === 'pending')
                    <!-- Bank QR Box -->
                    <div class="p-4 rounded-3 border mb-4 text-center" style="background: var(--brand-50, #fdf4f0); border-color: var(--brand-200) !important;">
                        <h6 class="fw-bold text-primary mb-2">Quét Mã QR Chuyển Khoản Ngay</h6>
                        <p class="text-secondary small mb-3">
                            Mở ứng dụng ngân hàng và quét mã QR bên dưới để thanh toán tự động đúng số tiền và cú pháp
                        </p>
                        <div class="d-inline-block p-3 bg-white rounded-3 shadow-sm border mb-3">
                            <img 
                                src="https://api.vietqr.io/image/970422-999988886666-compact2.jpg?amount={{ (int)$order->total_amount }}&addInfo={{ $order->order_code }}&accountName=AURELIA%20BAGS" 
                                alt="VietQR Payment Code" 
                                class="img-fluid" 
                                style="max-width: 240px;"
                            >
                        </div>
                        <div class="small text-secondary">
                            Số tài khoản: <strong>999988886666</strong> &bull; MB Bank &bull; Nội dung: <strong class="text-primary">{{ $order->order_code }}</strong>
                        </div>
                    </div>
                @endif

                <!-- Navigation CTAs -->
                <div class="d-flex flex-wrap justify-content-center gap-3">
                    <a href="{{ route('home') }}" class="btn-surface py-2.5 px-4 text-decoration-none">
                        <i data-lucide="home" style="width: 16px; height: 16px; margin-right: 0.4rem;"></i>
                        <span>Về Trang Chủ</span>
                    </a>

                    @auth
                        <a href="{{ route('account.orders') }}" class="btn-brand-primary py-2.5 px-4 text-decoration-none">
                            <i data-lucide="list-ordered" style="width: 16px; height: 16px; margin-right: 0.4rem;"></i>
                            <span>Xem Lịch Sử Đơn Hàng</span>
                        </a>
                    @else
                        <a href="{{ route('shop.index') }}" class="btn-brand-primary py-2.5 px-4 text-decoration-none">
                            <i data-lucide="shopping-bag" style="width: 16px; height: 16px; margin-right: 0.4rem;"></i>
                            <span>Tiếp Tục Mua Sắm</span>
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function copyOrderCode(code) {
        navigator.clipboard.writeText(code).then(() => {
            if (window.showToast) {
                window.showToast('Đã sao chép mã đơn hàng: ' + code, 'success');
            } else {
                alert('Đã sao chép mã đơn: ' + code);
            }
        });
    }
</script>
@endsection
