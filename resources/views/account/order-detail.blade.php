@extends('layouts.storefront')

@section('title', 'Chi Tiết Đơn Hàng ' . $order->order_code)

@section('content')
<div class="container py-4 py-lg-5">
    <!-- Breadcrumb -->
    <div class="breadcrumb-modern mb-3">
        <a href="{{ route('home') }}">Trang chủ</a>
        <i data-lucide="chevron-right" style="width: 14px; height: 14px;"></i>
        <a href="{{ route('account.orders') }}">Đơn hàng của tôi</a>
        <i data-lucide="chevron-right" style="width: 14px; height: 14px;"></i>
        <span class="text-primary fw-medium font-monospace">{{ $order->order_code }}</span>
    </div>

    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4 pb-3 border-bottom">
        <div>
            <h1 class="fw-extrabold text-dark mb-1" style="font-size: 1.85rem; letter-spacing: -0.02em;">
                Đơn Hàng <span class="text-primary font-monospace">{{ $order->order_code }}</span>
            </h1>
            <p class="text-secondary small mb-0">
                Ngày đặt: {{ $order->created_at->format('d/m/Y H:i') }} &bull; Cập nhật lần cuối: {{ $order->updated_at->format('d/m/Y H:i') }}
            </p>
        </div>
        <div class="d-flex gap-2">
            <span class="badge {{ $order->shipping_status_badge['class'] }} fs-6 px-3 py-2">
                {{ $order->shipping_status_badge['label'] }}
            </span>
            <span class="badge {{ $order->payment_status_badge['class'] }} fs-6 px-3 py-2">
                {{ $order->payment_status_badge['label'] }}
            </span>
        </div>
    </div>

    <div class="row g-4">
        <!-- Left: Shipping & Items info -->
        <div class="col-lg-8">
            <!-- Delivery Info -->
            <div class="card-modern p-4 mb-4 shadow-sm border">
                <h6 class="fw-bold text-dark mb-3 pb-2 border-bottom d-flex align-items-center gap-2">
                    <i data-lucide="map-pin" class="text-primary" style="width: 18px; height: 18px;"></i>
                    <span>Thông Tin Nhận Hàng</span>
                </h6>
                <div class="row g-3 small">
                    <div class="col-sm-6">
                        <span class="text-secondary">Người nhận:</span>
                        <div class="fw-bold text-dark fs-6">{{ $order->recipient_name }}</div>
                    </div>
                    <div class="col-sm-6">
                        <span class="text-secondary">Số điện thoại:</span>
                        <div class="fw-bold text-dark fs-6">{{ $order->phone }}</div>
                    </div>
                    <div class="col-12">
                        <span class="text-secondary">Địa chỉ giao hàng:</span>
                        <div class="fw-medium text-dark">{{ $order->shipping_address }}</div>
                    </div>
                    <div class="col-sm-6">
                        <span class="text-secondary">Hình thức thanh toán:</span>
                        <div class="fw-medium text-dark">{{ $order->payment_method_label }}</div>
                    </div>
                    @if ($order->notes)
                        <div class="col-12">
                            <span class="text-secondary">Ghi chú:</span>
                            <div class="fst-italic text-dark">{{ $order->notes }}</div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Items table -->
            <div class="card-modern p-4 shadow-sm border">
                <h6 class="fw-bold text-dark mb-3 pb-2 border-bottom d-flex align-items-center gap-2">
                    <i data-lucide="package" class="text-primary" style="width: 18px; height: 18px;"></i>
                    <span>Danh Sách Sản Phẩm ({{ $order->items->count() }} món)</span>
                </h6>

                <div class="d-flex flex-column gap-3">
                    @foreach ($order->items as $item)
                        <div class="d-flex align-items-center justify-content-between gap-3 p-2.5 rounded-3 border" style="background: var(--bg-surface-subtle);">
                            <div class="d-flex align-items-center gap-3 min-w-0">
                                <div class="rounded-3 border overflow-hidden flex-shrink-0" style="width: 60px; height: 60px;">
                                    @if ($item->product_image)
                                        <img src="{{ $item->product_image }}" alt="{{ $item->product_name }}" class="w-100 h-100 object-fit-cover">
                                    @else
                                        <div class="w-100 h-100 d-flex align-items-center justify-content-center bg-light">
                                            <i data-lucide="shopping-bag" class="text-secondary" style="width: 20px; height: 20px;"></i>
                                        </div>
                                    @endif
                                </div>
                                <div class="min-w-0">
                                    <h6 class="fw-bold text-dark mb-1 text-truncate">{{ $item->product_name }}</h6>
                                    <div class="text-secondary small">
                                        Đơn giá: {{ $item->formatted_price }} &times; <span class="fw-bold text-dark">{{ $item->quantity }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="text-end flex-shrink-0">
                                <span class="fw-extrabold text-primary fs-6">{{ $item->formatted_subtotal }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Right: Summary -->
        <div class="col-lg-4">
            <div class="card-modern p-4 shadow-sm border sticky-top" style="top: 85px;">
                <h6 class="fw-bold text-dark mb-3 pb-2 border-bottom">Tóm Tắt Thanh Toán</h6>

                <div class="d-flex flex-column gap-2 text-secondary small mb-3">
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
                        <span class="fw-extrabold text-primary fs-4">{{ $order->formatted_total_amount }}</span>
                    </div>
                </div>

                <div class="pt-3 border-top">
                    <a href="{{ route('account.orders') }}" class="btn-surface w-100 py-2.5 text-decoration-none d-flex align-items-center justify-content-center gap-2">
                        <i data-lucide="arrow-left" style="width: 16px; height: 16px;"></i>
                        <span>Quay Lại Danh Sách</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
