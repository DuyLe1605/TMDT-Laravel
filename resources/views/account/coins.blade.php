@extends('layouts.storefront')

@section('title', 'Ví Xu Aurelia - Tài Khoản Của Tôi')

@section('content')
<div class="container py-4 py-lg-5">
    <!-- Breadcrumb -->
    <div class="breadcrumb-modern mb-3">
        <a href="{{ route('home') }}">Trang chủ</a>
        <i data-lucide="chevron-right" style="width: 14px; height: 14px;"></i>
        <span>Tài khoản</span>
        <i data-lucide="chevron-right" style="width: 14px; height: 14px;"></i>
        <span class="text-primary fw-medium">Ví Xu Aurelia</span>
    </div>

    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4 pb-3 border-bottom">
        <div>
            <h1 class="fw-extrabold text-dark mb-1" style="font-size: 1.85rem; letter-spacing: -0.02em;">
                Ví Xu Thưởng Aurelia
            </h1>
            <p class="text-secondary small mb-0">
                Tích lũy Xu từ đánh giá trải nghiệm sản phẩm và sử dụng để giảm giá trực tiếp khi thanh toán
            </p>
        </div>
        <a href="{{ route('shop.index') }}" class="btn-brand-primary py-2 px-3.5 text-decoration-none">
            <i data-lucide="shopping-bag" style="width: 16px; height: 16px; margin-right: 0.35rem;"></i>
            <span>Dùng Xu Mua Sắm Ngay</span>
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
                    <a href="{{ route('account.orders') }}" class="btn-surface w-100 text-start py-2 px-3 text-decoration-none d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center gap-2">
                            <i data-lucide="package" style="width: 16px; height: 16px;"></i>
                            <span>Đơn hàng của tôi</span>
                        </div>
                    </a>
                    <a href="{{ route('account.addresses') }}" class="btn-surface w-100 text-start py-2 px-3 text-decoration-none d-flex align-items-center gap-2">
                        <i data-lucide="map-pin" style="width: 16px; height: 16px;"></i>
                        <span>Sổ địa chỉ nhận hàng</span>
                    </a>
                    <a href="{{ route('account.coins') }}" class="btn-brand-primary w-100 text-start py-2 px-3 text-decoration-none d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-coin fs-6"></i>
                            <span>Ví Xu Aurelia</span>
                        </div>
                        <span class="badge bg-white text-dark fw-bold rounded-pill px-2" style="font-size: 0.72rem;">
                            {{ number_format($user->coins_balance) }} Xu
                        </span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="col-lg-9">
            <!-- 3 Overview Cards -->
            <div class="row g-3 mb-4">
                <!-- Current Balance Card -->
                <div class="col-md-4">
                    <div class="card-modern p-4 text-white position-relative overflow-hidden shadow-sm h-100" 
                         style="background: linear-gradient(135deg, #b45309 0%, #d97706 50%, #f59e0b 100%); border: none;">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="small fw-semibold text-white text-opacity-75 text-uppercase" style="letter-spacing: 0.5px;">Số dư khả dụng</span>
                            <div class="rounded-circle d-flex align-items-center justify-content-center bg-white bg-opacity-25" style="width: 32px; height: 32px;">
                                <i class="bi bi-coin text-white fs-5"></i>
                            </div>
                        </div>
                        <div class="display-6 fw-extrabold mb-1 font-monospace">
                            {{ number_format($user->coins_balance) }}
                            <span class="fs-5 fw-normal">Xu</span>
                        </div>
                        <small class="text-white text-opacity-90">
                            &approx; {{ number_format($user->coins_balance) }} ₫ (Tỉ giá 1:1)
                        </small>
                    </div>
                </div>

                <!-- Total Earned Card -->
                <div class="col-md-4">
                    <div class="card-modern p-4 border bg-white shadow-sm h-100">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="small fw-semibold text-secondary text-uppercase" style="letter-spacing: 0.5px;">Tổng Xu đã tích lũy</span>
                            <div class="rounded-circle d-flex align-items-center justify-content-center bg-success-subtle text-success" style="width: 32px; height: 32px;">
                                <i class="bi bi-arrow-up-circle fs-5"></i>
                            </div>
                        </div>
                        <div class="display-6 fw-bold text-success mb-1 font-monospace">
                            +{{ number_format($totalEarned) }}
                        </div>
                        <small class="text-secondary">Từ đánh giá & khuyến mãi</small>
                    </div>
                </div>

                <!-- Total Spent Card -->
                <div class="col-md-4">
                    <div class="card-modern p-4 border bg-white shadow-sm h-100">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="small fw-semibold text-secondary text-uppercase" style="letter-spacing: 0.5px;">Tổng Xu đã tiêu dùng</span>
                            <div class="rounded-circle d-flex align-items-center justify-content-center bg-warning-subtle text-warning-emphasis" style="width: 32px; height: 32px;">
                                <i class="bi bi-bag-check fs-5"></i>
                            </div>
                        </div>
                        <div class="display-6 fw-bold text-warning-emphasis mb-1 font-monospace">
                            -{{ number_format($totalSpent) }}
                        </div>
                        <small class="text-secondary">Tiết kiệm khi mua sắm</small>
                    </div>
                </div>
            </div>

            <!-- How it works banner -->
            <div class="card-modern p-4 mb-4 border" style="background: #fffbf0; border-color: #fde68a !important;">
                <h6 class="fw-bold text-dark mb-3 d-flex align-items-center gap-2">
                    <i class="bi bi-info-circle-fill text-warning"></i>
                    <span>Chính Sách & Quy Định Sử Dụng Xu Aurelia</span>
                </h6>
                <div class="row g-3 small text-secondary">
                    <div class="col-md-6">
                        <div class="fw-bold text-dark mb-1">
                            <i class="bi bi-gift-fill text-danger me-1"></i> Làm thế nào để nhận Xu?
                        </div>
                        <ul class="ps-3 mb-0">
                            <li><strong>+1.000 Xu</strong>: Đánh giá sản phẩm đã mua kèm hình ảnh thực tế và tối thiểu 50 ký tự.</li>
                            <li><strong>+300 Xu</strong>: Đánh giá chi tiết từ 50 ký tự không kèm ảnh.</li>
                            <li><strong>+100 Xu</strong>: Đánh giá nhanh hoặc chấm sao.</li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <div class="fw-bold text-dark mb-1">
                            <i class="bi bi-shield-check text-primary me-1"></i> Sử dụng & Hoàn Xu:
                        </div>
                        <ul class="ps-3 mb-0">
                            <li>Quy đổi cố định: <strong>1 Xu = 1 VNĐ</strong> trừ thẳng vào tổng tiền thanh toán.</li>
                            <li>Tối đa <strong>10%</strong> giá trị đơn hàng, trần <strong>30.000 Xu</strong> / đơn.</li>
                            <li>Nếu đơn hàng bị hủy, <strong>100% số Xu đã tiêu sẽ được hoàn trả tự động</strong> vào ví của bạn.</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Transaction Ledger Table -->
            <div class="card-modern p-4 shadow-sm border">
                <h6 class="fw-bold text-dark mb-3 pb-2 border-bottom d-flex align-items-center gap-2">
                    <i class="bi bi-clock-history text-primary"></i>
                    <span>Lịch Sử Biến Động Số Dư</span>
                </h6>

                @if ($transactions->isEmpty())
                    <div class="text-center py-5 text-secondary">
                        <div class="rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 56px; height: 56px; background: #fef3c7; color: #d97706;">
                            <i class="bi bi-coin fs-3"></i>
                        </div>
                        <h6 class="fw-bold text-dark mb-1">Chưa có biến động số dư nào</h6>
                        <p class="small mb-3 text-muted">
                            Hãy hoàn thành các đơn hàng và gửi đánh giá sản phẩm để tích lũy những đồng Xu đầu tiên!
                        </p>
                        <a href="{{ route('account.orders') }}" class="btn btn-sm btn-brand-primary">
                            Xem đơn hàng cần đánh giá
                        </a>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr class="text-secondary small">
                                    <th style="width: 170px;">Thời Gian</th>
                                    <th style="width: 130px;">Loại Giao Dịch</th>
                                    <th>Nội Dung Mô Tả</th>
                                    <th class="text-end" style="width: 130px;">Biến Động</th>
                                    <th class="text-end" style="width: 140px;">Số Dư Sau Đó</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($transactions as $tx)
                                    <tr>
                                        <td class="small text-muted font-monospace">
                                            {{ $tx->created_at->format('d/m/Y H:i') }}
                                        </td>
                                        <td>
                                            <span class="badge {{ $tx->type_badge['class'] }} font-monospace" style="font-size: 0.72rem;">
                                                <i class="bi bi-{{ $tx->type_badge['icon'] }} me-1"></i>{{ $tx->type_badge['label'] }}
                                            </span>
                                        </td>
                                        <td class="small">
                                            <div class="fw-semibold text-dark">{{ $tx->description }}</div>
                                        </td>
                                        <td class="text-end font-monospace fw-bold {{ $tx->amount > 0 ? 'text-success' : 'text-danger' }}">
                                            {{ $tx->formatted_amount }}
                                        </td>
                                        <td class="text-end font-monospace text-secondary small">
                                            {{ number_format($tx->balance_after) }} Xu
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if ($transactions->hasPages())
                        <div class="pt-4 border-top d-flex justify-content-center">
                            {{ $transactions->links() }}
                        </div>
                    @endif
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
