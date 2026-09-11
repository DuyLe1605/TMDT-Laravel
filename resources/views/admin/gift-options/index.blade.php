@extends('layouts.app')

@section('title', 'Quản lý Gói Quà & Thiệp Mừng - Admin Portal')

@section('content')
<!-- Page Header -->
<div class="mb-4">
    <div class="breadcrumb-modern">
        <span>Admin Portal</span>
        <i data-lucide="chevron-right" style="width: 14px; height: 14px;"></i>
        <span>Quản lý E-Commerce</span>
        <i data-lucide="chevron-right" style="width: 14px; height: 14px;"></i>
        <span class="text-primary fw-medium">Gói Quà & Thiệp Mừng</span>
    </div>

    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mt-2">
        <div>
            <h2 class="fw-bold mb-1 text-dark" style="letter-spacing: -0.03em;">Quản Lý Gói Quà & Thiệp Mừng</h2>
            <p class="text-secondary mb-0" style="font-size: 0.94rem;">
                Cấu hình danh mục giấy gói hộp cứng Signature, nơ lụa và các mẫu thiệp chúc mừng viết tay cho khách hàng
            </p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('admin.gift-options.create') }}" class="btn btn-brand-primary d-flex align-items-center gap-2 px-3 py-2">
                <i data-lucide="plus-circle" style="width: 18px; height: 18px;"></i>
                <span>Thêm Mẫu Mới</span>
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
                    <div class="text-secondary small fw-medium">Tổng tùy chọn</div>
                    <div class="fs-4 fw-extrabold text-dark mt-1">{{ number_format($stats['total']) }}</div>
                </div>
                <div class="p-2 rounded-3 bg-light text-secondary">
                    <i data-lucide="gift" style="width: 22px; height: 22px;"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-6 col-md-3">
        <div class="metric-card p-3 h-100 shadow-sm border">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-secondary small fw-medium">Mẫu giấy gói</div>
                    <div class="fs-4 fw-extrabold text-primary mt-1">{{ number_format($stats['papers']) }}</div>
                </div>
                <div class="p-2 rounded-3 bg-primary-subtle text-primary">
                    <i data-lucide="package" style="width: 22px; height: 22px;"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-6 col-md-3">
        <div class="metric-card p-3 h-100 shadow-sm border">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-secondary small fw-medium">Mẫu thiệp mừng</div>
                    <div class="fs-4 fw-extrabold text-danger mt-1">{{ number_format($stats['cards']) }}</div>
                </div>
                <div class="p-2 rounded-3 bg-danger-subtle text-danger">
                    <i data-lucide="heart" style="width: 22px; height: 22px;"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-6 col-md-3">
        <div class="metric-card p-3 h-100 shadow-sm border">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-secondary small fw-medium">Đang kích hoạt</div>
                    <div class="fs-4 fw-extrabold text-success mt-1">{{ number_format($stats['active']) }}</div>
                </div>
                <div class="p-2 rounded-3 bg-success-subtle text-success">
                    <i data-lucide="check-circle" style="width: 22px; height: 22px;"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filters & Search Toolbar -->
<div class="card-modern p-3 mb-4 shadow-sm border">
    <form action="{{ route('admin.gift-options.index') }}" method="GET" class="row g-2 align-items-center">
        <div class="col-md-5">
            <div class="search-box-modern">
                <i data-lucide="search" class="search-icon"></i>
                <input type="text" name="search" class="form-control form-control-modern" placeholder="Tìm theo tên, mã code hoặc mô tả..." value="{{ request('search') }}">
            </div>
        </div>

        <div class="col-md-3">
            <select name="type" class="form-select form-select-modern" onchange="this.form.submit()">
                <option value="">-- Tất cả phân loại --</option>
                <option value="paper" {{ request('type') === 'paper' ? 'selected' : '' }}>Giấy gói quà</option>
                <option value="card" {{ request('type') === 'card' ? 'selected' : '' }}>Thiệp chúc mừng</option>
            </select>
        </div>

        <div class="col-md-2">
            <select name="status" class="form-select form-select-modern" onchange="this.form.submit()">
                <option value="">-- Tất cả trạng thái --</option>
                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Đang kích hoạt</option>
                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Tạm dừng</option>
            </select>
        </div>

        <div class="col-md-2 d-flex gap-2">
            <button type="submit" class="btn btn-brand-primary w-100">Lọc</button>
            @if(request()->hasAny(['search', 'type', 'status']))
                <a href="{{ route('admin.gift-options.index') }}" class="btn btn-surface px-2.5" title="Xóa lọc">
                    <i data-lucide="rotate-ccw" style="width: 16px; height: 16px;"></i>
                </a>
            @endif
        </div>
    </form>
</div>

<!-- Table Card -->
<div class="card-modern shadow-sm border overflow-hidden">
    <div class="table-responsive">
        <table class="table table-modern align-middle mb-0">
            <thead>
                <tr>
                    <th style="width: 60px;">Mẫu</th>
                    <th>Tên & Mã code</th>
                    <th style="width: 140px;">Phân loại</th>
                    <th style="width: 130px;">Giá phụ thu</th>
                    <th style="width: 90px;" class="text-center">Thứ tự</th>
                    <th style="width: 120px;" class="text-center">Trạng thái</th>
                    <th style="width: 110px;" class="text-end">Hành động</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($giftOptions as $option)
                    <tr>
                        <td>
                            @if(str_starts_with($option->image ?? '', '#'))
                                <span class="d-inline-block rounded-circle border shadow-sm" style="width: 34px; height: 34px; background-color: {{ $option->image }};"></span>
                            @elseif($option->type === 'card' && !empty($option->image) && mb_strlen($option->image) <= 4)
                                <span class="fs-4">{{ $option->image }}</span>
                            @elseif($option->image)
                                <img src="{{ $option->image }}" alt="" class="rounded-2 border object-fit-cover" style="width: 36px; height: 36px;">
                            @else
                                <div class="rounded-2 d-flex align-items-center justify-content-center bg-light text-secondary" style="width: 36px; height: 36px;">
                                    <i data-lucide="{{ $option->type === 'paper' ? 'package' : 'heart' }}" style="width: 18px; height: 18px;"></i>
                                </div>
                            @endif
                        </td>
                        <td>
                            <div class="fw-bold text-dark">{{ $option->name }}</div>
                            <div class="d-flex align-items-center gap-2 mt-0.5">
                                <span class="badge-mono-id">{{ $option->code }}</span>
                                @if($option->description)
                                    <span class="text-secondary small text-truncate" style="max-width: 320px;">{{ $option->description }}</span>
                                @endif
                            </div>
                        </td>
                        <td>
                            @if($option->type === 'paper')
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-2.5 py-1 small fw-semibold">
                                    Giấy gói quà
                                </span>
                            @else
                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-2.5 py-1 small fw-semibold">
                                    Thiệp mừng
                                </span>
                            @endif
                        </td>
                        <td>
                            <span class="fw-bold {{ (float) $option->price > 0 ? 'text-dark' : 'text-success' }}">
                                {{ $option->formatted_price }}
                            </span>
                        </td>
                        <td class="text-center font-monospace text-secondary">
                            {{ $option->sort_order }}
                        </td>
                        <td class="text-center">
                            <form action="{{ route('admin.gift-options.toggle', $option) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-sm {{ $option->is_active ? 'btn-success-subtle text-success' : 'btn-secondary-subtle text-secondary' }} rounded-pill px-2.5 py-0.5 border" style="font-size: 0.75rem;">
                                    {{ $option->is_active ? 'Kích hoạt' : 'Tạm dừng' }}
                                </button>
                            </form>
                        </td>
                        <td class="text-end">
                            <div class="d-inline-flex align-items-center gap-1">
                                <a href="{{ route('admin.gift-options.edit', $option) }}" class="btn btn-sm btn-surface p-1.5" title="Chỉnh sửa">
                                    <i data-lucide="edit-3" style="width: 15px; height: 15px;"></i>
                                </a>
                                <form action="{{ route('admin.gift-options.destroy', $option) }}" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc muốn xóa tùy chọn quà tặng này không?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-surface text-danger p-1.5" title="Xóa">
                                        <i data-lucide="trash-2" style="width: 15px; height: 15px;"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-secondary">
                            <i data-lucide="gift" class="mb-2" style="width: 36px; height: 36px; opacity: 0.4;"></i>
                            <div class="fw-bold">Chưa có tùy chọn quà tặng nào</div>
                            <p class="small mb-3">Hãy tạo mẫu giấy gói hoặc mẫu thiệp mừng đầu tiên</p>
                            <a href="{{ route('admin.gift-options.create') }}" class="btn btn-sm btn-brand-primary">
                                + Thêm Tùy Chọn Mới
                            </a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($giftOptions->hasPages())
        <div class="card-modern-footer p-3 border-top">
            {{ $giftOptions->links() }}
        </div>
    @endif
</div>
@endsection
