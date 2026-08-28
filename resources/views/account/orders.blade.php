@extends('layouts.storefront')

@section('title', 'Lịch Sử Đơn Hàng - Tài Khoản Của Tôi')

@section('content')
<div class="container py-4 py-lg-5">
    <!-- Breadcrumb -->
    <div class="breadcrumb-modern mb-3">
        <a href="{{ route('home') }}">Trang chủ</a>
        <i data-lucide="chevron-right" style="width: 14px; height: 14px;"></i>
        <span>Tài khoản</span>
        <i data-lucide="chevron-right" style="width: 14px; height: 14px;"></i>
        <span class="text-primary fw-medium">Lịch sử đơn hàng</span>
    </div>

    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4 pb-3 border-bottom">
        <div>
            <h1 class="fw-extrabold text-dark mb-1" style="font-size: 1.85rem; letter-spacing: -0.02em;">
                Đơn Hàng Của Tôi
            </h1>
            <p class="text-secondary small mb-0">
                Theo dõi tiến trình và xem lại lịch sử các đơn hàng bạn đã mua tại Aurelia Luxury
            </p>
        </div>
        <a href="{{ route('shop.index') }}" class="btn-brand-primary py-2 px-3.5 text-decoration-none">
            <i data-lucide="shopping-bag" style="width: 16px; height: 16px; margin-right: 0.35rem;"></i>
            <span>Tiếp tục mua sắm</span>
        </a>
    </div>

    <div class="row g-4">
        <!-- Sidebar Navigation -->
        <div class="col-lg-3">
            <div class="card-modern p-3 shadow-sm border sticky-top" style="top: 85px;">
                <div class="d-flex align-items-center gap-3 p-2 mb-3 border-bottom">
                    <div class="sidebar-user-avatar" style="width: 44px; height: 44px; font-size: 1rem;">
                        {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                    </div>
                    <div class="min-w-0">
                        <div class="fw-bold text-dark text-truncate">{{ Auth::user()->name }}</div>
                        <div class="text-secondary small text-truncate">{{ Auth::user()->email }}</div>
                    </div>
                </div>

                <div class="d-flex flex-column gap-1">
                    <a href="{{ route('account.orders') }}" class="btn-brand-primary w-100 text-start py-2 px-3 text-decoration-none d-flex align-items-center gap-2">
                        <i data-lucide="package" style="width: 16px; height: 16px;"></i>
                        <span>Đơn hàng của tôi</span>
                    </a>
                    <a href="{{ route('account.addresses') }}" class="btn-surface w-100 text-start py-2 px-3 text-decoration-none d-flex align-items-center gap-2">
                        <i data-lucide="map-pin" style="width: 16px; height: 16px;"></i>
                        <span>Sổ địa chỉ nhận hàng</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Main Orders List -->
        <div class="col-lg-9">
            @if ($orders->isEmpty())
                <div class="card-modern text-center py-5 px-4 shadow-sm border">
                    <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 68px; height: 68px; background: var(--brand-50); color: var(--brand-600);">
                        <i data-lucide="shopping-bag" style="width: 34px; height: 34px;"></i>
                    </div>
                    <h5 class="fw-bold text-dark mb-2">Bạn chưa có đơn hàng nào</h5>
                    <p class="text-secondary small mb-4">
                        Khám phá ngay bộ sưu tập túi xách thời thượng và đặt đơn hàng đầu tiên của bạn!
                    </p>
                    <a href="{{ route('shop.index') }}" class="btn-brand-primary py-2 px-4 text-decoration-none">
                        <span>Xem Bộ Sưu Tập Ngay</span>
                    </a>
                </div>
            @else
                <div class="d-flex flex-column gap-3.5">
                    @foreach ($orders as $order)
                        <div class="card-modern p-4 shadow-sm border">
                            <!-- Order Header -->
                            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 pb-3 mb-3 border-bottom">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="text-secondary small">Mã đơn:</span>
                                    <span class="fw-extrabold text-dark font-monospace">{{ $order->order_code }}</span>
                                    <span class="text-secondary small">&bull; {{ $order->created_at->format('d/m/Y H:i') }}</span>
                                </div>
                                <div class="d-flex align-items-center gap-2">
                                    <span class="badge {{ $order->shipping_status_badge['class'] }} small">
                                        {{ $order->shipping_status_badge['label'] }}
                                    </span>
                                    <span class="badge {{ $order->payment_status_badge['class'] }} small">
                                        {{ $order->payment_status_badge['label'] }}
                                    </span>
                                </div>
                            </div>

                            <!-- Items preview -->
                            <div class="d-flex flex-column gap-2 mb-3">
                                @foreach ($order->items as $item)
                                    <div class="d-flex align-items-center justify-content-between gap-3">
                                        <div class="d-flex align-items-center gap-2.5 min-w-0">
                                            <div class="rounded-2 border overflow-hidden flex-shrink-0" style="width: 42px; height: 42px;">
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
                                                <div class="text-secondary" style="font-size: 0.75rem;">
                                                    {{ $item->formatted_price }} &times; {{ $item->quantity }}
                                                </div>
                                            </div>
                                        </div>
                                        <span class="fw-bold text-dark small">{{ $item->formatted_subtotal }}</span>
                                    </div>
                                @endforeach
                            </div>

                            <!-- Footer summary & action -->
                            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 pt-3 border-top bg-light-subtle rounded-bottom p-2 px-3">
                                <div class="small">
                                    <span class="text-secondary">Tổng thanh toán:</span>
                                    <span class="fw-extrabold text-primary fs-6 ms-1">{{ $order->formatted_total_amount }}</span>
                                </div>
                                <div>
                                    <a href="{{ route('account.orders.show', $order) }}" class="btn btn-sm btn-surface text-decoration-none fw-semibold">
                                        <span>Xem chi tiết</span>
                                        <i data-lucide="chevron-right" style="width: 14px; height: 14px; margin-left: 0.25rem;"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach

                    <div class="mt-3">
                        {{ $orders->links() }}
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
