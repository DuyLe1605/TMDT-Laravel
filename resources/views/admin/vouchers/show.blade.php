@extends('layouts.app')

@section('title', 'Chi tiết Voucher ' . $voucher->code . ' - Admin Portal')

@section('content')
<!-- Page Header -->
<div class="mb-4">
    <div class="breadcrumb-modern">
        <span>Admin Portal</span>
        <i data-lucide="chevron-right" style="width: 14px; height: 14px;"></i>
        <span>Quản lý E-Commerce</span>
        <i data-lucide="chevron-right" style="width: 14px; height: 14px;"></i>
        <a href="{{ route('admin.vouchers.index') }}" class="text-decoration-none text-secondary">Vouchers</a>
        <i data-lucide="chevron-right" style="width: 14px; height: 14px;"></i>
        <span class="text-primary fw-medium">{{ $voucher->code }}</span>
    </div>

    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mt-2">
        <div class="d-flex align-items-center gap-3">
            <div>
                <div class="d-flex align-items-center gap-2">
                    <h2 class="fw-bold mb-0 text-dark font-monospace" style="letter-spacing: -0.02em;">{{ $voucher->code }}</h2>
                    <span class="badge {{ $voucher->status_badge['class'] }} fs-7 py-1 px-2.5">
                        {{ $voucher->status_badge['label'] }}
                    </span>
                </div>
                <p class="text-secondary mb-0 mt-1" style="font-size: 0.94rem;">{{ $voucher->name }}</p>
            </div>
        </div>

        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('admin.vouchers.edit', $voucher) }}" class="btn btn-primary d-flex align-items-center gap-1">
                <i data-lucide="edit-3" style="width: 16px; height: 16px;"></i>
                <span>Chỉnh sửa</span>
            </a>
            <a href="{{ route('admin.vouchers.index') }}" class="btn btn-outline-secondary d-flex align-items-center gap-1">
                <i data-lucide="arrow-left" style="width: 16px; height: 16px;"></i>
                <span>Quay lại danh sách</span>
            </a>
        </div>
    </div>
</div>

<!-- KPI Cards -->
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="metric-card p-3 shadow-sm border">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-secondary small fw-medium">Lượt sử dụng</div>
                    <div class="fs-4 fw-extrabold text-dark mt-1">
                        {{ number_format($stats['total_redemptions']) }} / {{ $voucher->usage_limit ? number_format($voucher->usage_limit) : '∞' }}
                    </div>
                </div>
                <div class="p-2 rounded-3 bg-primary-subtle text-primary">
                    <i data-lucide="ticket" style="width: 22px; height: 22px;"></i>
                </div>
            </div>
            @if($voucher->usage_limit)
                @php
                    $pct = min(100, round(($stats['total_redemptions'] / $voucher->usage_limit) * 100));
                @endphp
                <div class="progress mt-3" style="height: 6px;">
                    <div class="progress-bar {{ $pct >= 90 ? 'bg-danger' : 'bg-primary' }}" style="width: {{ $pct }}%"></div>
                </div>
                <div class="text-secondary small mt-1" style="font-size: 0.72rem;">Đã đạt {{ $pct }}% tổng hạn ngạch</div>
            @endif
        </div>
    </div>

    <div class="col-md-4">
        <div class="metric-card p-3 shadow-sm border">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-secondary small fw-medium">Khách hàng hưởng lợi</div>
                    <div class="fs-4 fw-extrabold text-success mt-1">{{ number_format($stats['unique_users']) }}</div>
                </div>
                <div class="p-2 rounded-3 bg-success-subtle text-success">
                    <i data-lucide="users" style="width: 22px; height: 22px;"></i>
                </div>
            </div>
            <div class="text-secondary small mt-3" style="font-size: 0.75rem;">
                Tối đa <strong>{{ $voucher->usage_limit_per_user }}</strong> lượt / mỗi tài khoản
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="metric-card p-3 shadow-sm border">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-secondary small fw-medium">Tổng tiền đã trợ giá</div>
                    <div class="fs-4 fw-extrabold text-warning-emphasis mt-1">
                        {{ number_format($stats['total_saved'], 0, ',', '.') }} ₫
                    </div>
                </div>
                <div class="p-2 rounded-3 bg-warning-subtle text-warning">
                    <i data-lucide="coins" style="width: 22px; height: 22px;"></i>
                </div>
            </div>
            <div class="text-secondary small mt-3" style="font-size: 0.75rem;">
                Tổng giá trị chiết khấu đã hỗ trợ cho khách mua hàng
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Left Column: Voucher Specification Card -->
    <div class="col-lg-5">
        <div class="card-modern p-4 shadow-sm border h-100">
            <h5 class="fw-bold text-dark mb-3 pb-2 border-bottom d-flex align-items-center gap-2">
                <i data-lucide="sliders" class="text-primary" style="width: 18px; height: 18px;"></i>
                <span>Quy Tắc & Thông Số Voucher</span>
            </h5>

            <div class="d-flex flex-column gap-3 small">
                <div class="d-flex justify-content-between border-bottom pb-2">
                    <span class="text-secondary">Loại hình chiết khấu:</span>
                    <span class="fw-bold text-dark">{{ $voucher->discount_type_label }}</span>
                </div>

                <div class="d-flex justify-content-between border-bottom pb-2">
                    <span class="text-secondary">Mức giảm:</span>
                    <span class="fw-bold text-success fs-6">{{ $voucher->formatted_discount }}</span>
                </div>

                @if($voucher->discount_type === 'percentage' && $voucher->max_discount_amount)
                    <div class="d-flex justify-content-between border-bottom pb-2">
                        <span class="text-secondary">Mức giảm tối đa:</span>
                        <span class="fw-bold text-dark">{{ number_format($voucher->max_discount_amount, 0, ',', '.') }} ₫</span>
                    </div>
                @endif

                <div class="d-flex justify-content-between border-bottom pb-2">
                    <span class="text-secondary">Đơn hàng tối thiểu:</span>
                    <span class="fw-bold text-dark">{{ $voucher->formatted_min_order }}</span>
                </div>

                <div class="d-flex justify-content-between border-bottom pb-2">
                    <span class="text-secondary">Phương thức thanh toán:</span>
                    <div>
                        @if(in_array('all', $voucher->applicable_payment_methods ?? ['all']))
                            <span class="badge bg-light text-dark border">Tất cả phương thức</span>
                        @else
                            @foreach($voucher->applicable_payment_methods as $pm)
                                <span class="badge bg-info-subtle text-info border border-info-subtle text-uppercase">{{ $pm }}</span>
                            @endforeach
                        @endif
                    </div>
                </div>

                <div class="d-flex justify-content-between border-bottom pb-2">
                    <span class="text-secondary">Thời gian bắt đầu:</span>
                    <span class="text-dark">{{ $voucher->starts_at ? $voucher->starts_at->format('d/m/Y H:i') : 'Ngay khi tạo' }}</span>
                </div>

                <div class="d-flex justify-content-between border-bottom pb-2">
                    <span class="text-secondary">Thời gian kết thúc:</span>
                    <span class="text-dark">{{ $voucher->expires_at ? $voucher->expires_at->format('d/m/Y H:i') : 'Không giới hạn' }}</span>
                </div>

                @if($voucher->description)
                    <div>
                        <span class="text-secondary d-block mb-1">Mô tả chương trình:</span>
                        <div class="p-2.5 rounded-2 bg-light border text-dark">
                            {{ $voucher->description }}
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Right Column: Usage History Table -->
    <div class="col-lg-7">
        <div class="card-modern shadow-sm border overflow-hidden">
            <div class="p-3 border-bottom d-flex align-items-center justify-content-between">
                <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                    <i data-lucide="history" class="text-primary" style="width: 18px; height: 18px;"></i>
                    <span>Lịch Sử Đổi Mã & Áp Dụng ({{ $usages->total() }} lượt)</span>
                </h6>
            </div>

            <div class="table-responsive">
                <table class="table align-middle mb-0 small">
                    <thead class="bg-light text-secondary">
                        <tr>
                            <th class="ps-3 py-2.5">Đơn Hàng</th>
                            <th class="py-2.5">Khách Hàng</th>
                            <th class="py-2.5 text-center">Tiền Giảm</th>
                            <th class="pe-3 py-2.5 text-end">Thời Gian</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($usages as $usage)
                            <tr>
                                <td class="ps-3 py-3">
                                    @if($usage->order)
                                        <a href="{{ route('admin.orders.show', $usage->order) }}" class="fw-bold font-monospace text-decoration-none text-primary">
                                            {{ $usage->order->order_code }}
                                        </a>
                                        <div class="text-secondary" style="font-size: 0.72rem;">
                                            Tổng đơn: {{ number_format($usage->order->final_total, 0, ',', '.') }} ₫
                                        </div>
                                    @else
                                        <span class="text-muted">Đơn #{{ $usage->order_id }}</span>
                                    @endif
                                </td>

                                <td class="py-3">
                                    @if($usage->user)
                                        <div class="fw-semibold text-dark">{{ $usage->user->name }}</div>
                                        <div class="text-secondary" style="font-size: 0.72rem;">{{ $usage->user->email }}</div>
                                    @else
                                        <span class="text-muted fst-italic">Khách vãng lai</span>
                                    @endif
                                </td>

                                <td class="py-3 text-center">
                                    <span class="fw-bold text-success font-monospace">
                                        -{{ number_format($usage->discount_amount, 0, ',', '.') }} ₫
                                    </span>
                                </td>

                                <td class="pe-3 py-3 text-end text-secondary">
                                    {{ $usage->created_at->format('d/m/Y H:i') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-4 text-muted">
                                    <i data-lucide="inbox" style="width: 32px; height: 32px;" class="mb-2"></i>
                                    <div>Chưa có khách hàng nào sử dụng mã này</div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($usages->hasPages())
                <div class="p-3 border-top d-flex justify-content-center">
                    {{ $usages->links('pagination::bootstrap-5') }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
