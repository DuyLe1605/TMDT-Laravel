@extends('layouts.app')

@section('title', 'Quản lý Voucher & Khuyến mãi - Admin Portal')

@section('content')
<!-- Page Header -->
<div class="mb-4">
    <div class="breadcrumb-modern">
        <span>Admin Portal</span>
        <i data-lucide="chevron-right" style="width: 14px; height: 14px;"></i>
        <span>Quản lý E-Commerce</span>
        <i data-lucide="chevron-right" style="width: 14px; height: 14px;"></i>
        <span class="text-primary fw-medium">Mã giảm giá (Vouchers)</span>
    </div>

    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mt-2">
        <div>
            <h2 class="fw-bold mb-1 text-dark" style="letter-spacing: -0.03em;">Quản Lý Voucher & Khuyến Mãi</h2>
            <p class="text-secondary mb-0" style="font-size: 0.94rem;">
                Thiết lập chiến dịch ưu đãi, giảm giá phần trăm, mã freeship và kiểm soát số lượt áp dụng
            </p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('admin.vouchers.create') }}" class="btn btn-primary d-flex align-items-center gap-2 px-3 py-2">
                <i data-lucide="plus-circle" style="width: 18px; height: 18px;"></i>
                <span>Tạo Voucher Mới</span>
            </a>
        </div>
    </div>
</div>

<!-- KPI Summary Cards -->
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="metric-card p-3 h-100 shadow-sm border">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-secondary small fw-medium">Tổng số voucher</div>
                    <div class="fs-4 fw-extrabold text-dark mt-1">{{ number_format($stats['total']) }}</div>
                </div>
                <div class="p-2 rounded-3 bg-light text-secondary">
                    <i data-lucide="ticket" style="width: 22px; height: 22px;"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-6 col-md-3">
        <div class="metric-card p-3 h-100 shadow-sm border">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-secondary small fw-medium">Đang hoạt động</div>
                    <div class="fs-4 fw-extrabold text-success mt-1">{{ number_format($stats['active']) }}</div>
                </div>
                <div class="p-2 rounded-3 bg-success-subtle text-success">
                    <i data-lucide="check-circle" style="width: 22px; height: 22px;"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-6 col-md-3">
        <div class="metric-card p-3 h-100 shadow-sm border">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-secondary small fw-medium">Lượt đã sử dụng</div>
                    <div class="fs-4 fw-extrabold text-primary mt-1">{{ number_format($stats['total_used']) }}</div>
                </div>
                <div class="p-2 rounded-3 bg-primary-subtle text-primary">
                    <i data-lucide="users" style="width: 22px; height: 22px;"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-6 col-md-3">
        <div class="metric-card p-3 h-100 shadow-sm border">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-secondary small fw-medium">Tổng tiền trợ giá</div>
                    <div class="fs-4 fw-extrabold text-warning-emphasis mt-1">{{ number_format($stats['total_discount'], 0, ',', '.') }} ₫</div>
                </div>
                <div class="p-2 rounded-3 bg-warning-subtle text-warning">
                    <i data-lucide="coins" style="width: 22px; height: 22px;"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filter Bar -->
<div class="card-modern p-3 mb-4 shadow-sm border">
    <form action="{{ route('admin.vouchers.index') }}" method="GET" class="row g-2 align-items-end">
        <div class="col-md-4">
            <label class="form-label text-secondary small fw-semibold mb-1">Tìm kiếm</label>
            <div class="input-group">
                <span class="input-group-text bg-white border-end-0 text-secondary">
                    <i data-lucide="search" style="width: 16px; height: 16px;"></i>
                </span>
                <input type="text" name="search" class="form-control border-start-0" placeholder="Mã voucher hoặc tên chương trình..." value="{{ request('search') }}">
            </div>
        </div>

        <div class="col-md-3">
            <label class="form-label text-secondary small fw-semibold mb-1">Loại ưu đãi</label>
            <select name="type" class="form-select">
                <option value="">Tất cả loại chiết khấu</option>
                <option value="percentage" {{ request('type') === 'percentage' ? 'selected' : '' }}>Giảm theo %</option>
                <option value="fixed_amount" {{ request('type') === 'fixed_amount' ? 'selected' : '' }}>Giảm số tiền cố định</option>
                <option value="shipping_discount" {{ request('type') === 'shipping_discount' ? 'selected' : '' }}>Giảm phí vận chuyển (Freeship)</option>
            </select>
        </div>

        <div class="col-md-3">
            <label class="form-label text-secondary small fw-semibold mb-1">Trạng thái</label>
            <select name="status" class="form-select">
                <option value="">Tất cả trạng thái</option>
                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Đang chạy (Active)</option>
                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Tạm ngưng (Inactive)</option>
                <option value="expired" {{ request('status') === 'expired' ? 'selected' : '' }}>Hết hạn (Expired)</option>
            </select>
        </div>

        <div class="col-md-2 d-flex gap-2">
            <button type="submit" class="btn btn-dark w-100 d-flex align-items-center justify-content-center gap-1">
                <i data-lucide="filter" style="width: 15px; height: 15px;"></i>
                <span>Lọc</span>
            </button>
            @if(request()->hasAny(['search', 'type', 'status']))
                <a href="{{ route('admin.vouchers.index') }}" class="btn btn-outline-secondary" title="Đặt lại bộ lọc">
                    <i data-lucide="x" style="width: 16px; height: 16px;"></i>
                </a>
            @endif
        </div>
    </form>
</div>

<!-- Vouchers Table Card -->
<div class="card-modern shadow-sm border overflow-hidden">
    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead class="bg-light text-secondary small">
                <tr>
                    <th class="ps-3 py-3" style="width: 180px;">Mã Voucher</th>
                    <th class="py-3">Chương trình & Điều kiện</th>
                    <th class="py-3 text-center" style="width: 170px;">Mức chiết khấu</th>
                    <th class="py-3 text-center" style="width: 140px;">Đơn tối thiểu</th>
                    <th class="py-3 text-center" style="width: 150px;">Lượt dùng</th>
                    <th class="py-3 text-center" style="width: 150px;">Thời hạn</th>
                    <th class="py-3 text-center" style="width: 120px;">Trạng thái</th>
                    <th class="pe-3 py-3 text-end" style="width: 130px;">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                @forelse($vouchers as $voucher)
                    <tr>
                        <td class="ps-3 py-3">
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2.5 py-1.5 font-monospace fw-bold fs-6">
                                    {{ $voucher->code }}
                                </span>
                                <button type="button" class="btn btn-sm btn-link text-secondary p-0 copy-btn" onclick="navigator.clipboard.writeText('{{ $voucher->code }}'); alert('Đã copy mã: {{ $voucher->code }}');" title="Sao chép mã">
                                    <i data-lucide="copy" style="width: 14px; height: 14px;"></i>
                                </button>
                            </div>
                        </td>

                        <td class="py-3">
                            <div class="fw-bold text-dark">{{ $voucher->name }}</div>
                            @if($voucher->description)
                                <div class="text-secondary small text-truncate" style="max-width: 320px;">{{ $voucher->description }}</div>
                            @endif
                            <div class="d-flex flex-wrap gap-1 mt-1">
                                <span class="badge bg-light text-secondary border small">
                                    Loại: {{ $voucher->discount_type_label }}
                                </span>
                                @if(in_array('all', $voucher->applicable_payment_methods ?? ['all']))
                                    <span class="badge bg-light text-dark border small">Mọi hình thức TT</span>
                                @else
                                    @foreach($voucher->applicable_payment_methods as $pm)
                                        <span class="badge bg-info-subtle text-info border border-info-subtle small text-uppercase">{{ $pm }}</span>
                                    @endforeach
                                @endif
                                <span class="badge bg-light text-secondary border small">
                                    Tối đa: {{ $voucher->usage_limit_per_user }} lần/khách
                                </span>
                            </div>
                        </td>

                        <td class="py-3 text-center">
                            <div class="fw-bold text-success fs-6">{{ $voucher->formatted_discount }}</div>
                            @if($voucher->discount_type === 'percentage' && $voucher->max_discount_amount)
                                <div class="text-secondary" style="font-size: 0.75rem;">
                                    Tối đa {{ number_format($voucher->max_discount_amount, 0, ',', '.') }} ₫
                                </div>
                            @endif
                        </td>

                        <td class="py-3 text-center">
                            <span class="fw-medium text-dark">{{ $voucher->formatted_min_order }}</span>
                        </td>

                        <td class="py-3 text-center">
                            <div class="small fw-semibold text-dark mb-1">
                                {{ $voucher->used_count }} / {{ $voucher->usage_limit ? number_format($voucher->usage_limit) : '∞' }}
                            </div>
                            @if($voucher->usage_limit)
                                @php
                                    $pct = min(100, round(($voucher->used_count / $voucher->usage_limit) * 100));
                                @endphp
                                <div class="progress" style="height: 5px; width: 100px; margin: 0 auto;">
                                    <div class="progress-bar {{ $pct >= 90 ? 'bg-danger' : 'bg-primary' }}" style="width: {{ $pct }}%"></div>
                                </div>
                            @endif
                        </td>

                        <td class="py-3 text-center small text-secondary">
                            @if($voucher->expires_at)
                                <div>Hạn: <strong class="text-dark">{{ $voucher->expires_at->format('d/m/Y') }}</strong></div>
                                @if($voucher->isExpired())
                                    <span class="badge bg-danger-subtle text-danger mt-1">Đã hết hạn</span>
                                @endif
                            @else
                                <span class="text-muted">Không giới hạn</span>
                            @endif
                        </td>

                        <td class="py-3 text-center">
                            <form action="{{ route('admin.vouchers.toggle', $voucher) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-sm p-0 border-0 bg-transparent" title="Bấm để bật/tắt">
                                    <span class="badge {{ $voucher->status_badge['class'] }} d-flex align-items-center gap-1 justify-content-center py-1 px-2">
                                        <span class="rounded-circle d-inline-block" style="width: 6px; height: 6px; background-color: currentColor;"></span>
                                        <span>{{ $voucher->status_badge['label'] }}</span>
                                    </span>
                                </button>
                            </form>
                        </td>

                        <td class="pe-3 py-3 text-end">
                            <div class="d-flex align-items-center justify-content-end gap-1">
                                <a href="{{ route('admin.vouchers.show', $voucher) }}" class="btn btn-sm btn-light border text-secondary" title="Xem chi tiết & Lịch sử dùng">
                                    <i data-lucide="eye" style="width: 15px; height: 15px;"></i>
                                </a>
                                <a href="{{ route('admin.vouchers.edit', $voucher) }}" class="btn btn-sm btn-light border text-secondary" title="Chỉnh sửa voucher">
                                    <i data-lucide="edit-3" style="width: 15px; height: 15px;"></i>
                                </a>
                                <form action="{{ route('admin.vouchers.destroy', $voucher) }}" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc chắn muốn xóa/vô hiệu hóa voucher {{ $voucher->code }}?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-light border text-danger" title="Xóa voucher">
                                        <i data-lucide="trash-2" style="width: 15px; height: 15px;"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center py-5">
                            <div class="p-4 text-secondary">
                                <i data-lucide="ticket-slash" class="mb-3" style="width: 48px; height: 48px; stroke-width: 1.2;"></i>
                                <h5 class="fw-bold text-dark">Chưa có mã giảm giá nào</h5>
                                <p class="small text-secondary mb-3">Tạo các voucher khuyến mãi hấp dẫn để thu hút khách hàng và tăng tỷ lệ chuyển đổi.</p>
                                <a href="{{ route('admin.vouchers.create') }}" class="btn btn-primary btn-sm">
                                    <i data-lucide="plus" style="width: 16px; height: 16px; margin-right: 4px;"></i>
                                    Tạo voucher đầu tiên
                                </a>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($vouchers->hasPages())
        <div class="p-3 border-top d-flex justify-content-center">
            {{ $vouchers->links('pagination::bootstrap-5') }}
        </div>
    @endif
</div>
@endsection
