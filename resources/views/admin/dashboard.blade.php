@extends('layouts.app')

@section('title', 'Bảng điều khiển Quản trị')

@section('content')
<!-- Page Header -->
<div class="mb-4">
    <div class="breadcrumb-modern">
        <span>Admin Portal</span>
        <i data-lucide="chevron-right" style="width: 14px; height: 14px;"></i>
        <span class="text-primary fw-medium">Bảng điều khiển</span>
    </div>
    
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mt-2">
        <div>
            <h2 class="fw-bold mb-1 text-dark" style="letter-spacing: -0.03em;">Bảng Điều Khiển Quản Trị</h2>
            <p class="text-secondary mb-0" style="font-size: 0.94rem;">
                Tổng quan hiệu suất kinh doanh, tồn kho và danh mục túi xách nữ Aurelia
            </p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('admin.products.create') }}" class="btn-brand-primary">
                <i data-lucide="plus" style="width: 18px; height: 18px; margin-right: 0.45rem;"></i>
                <span>Thêm túi xách mới</span>
            </a>
            <a href="{{ route('home') }}" class="btn-surface" target="_blank" title="Xem trang bán hàng">
                <i data-lucide="external-link" style="width: 16px; height: 16px; margin-right: 0.35rem;"></i>
                <span>Xem Storefront</span>
            </a>
        </div>
    </div>
</div>

<!-- KPI Metric Cards Grid -->
<div class="row g-4 mb-4">
    <!-- Total Products -->
    <div class="col-sm-6 col-xl-3">
        <div class="metric-card metric-card-primary">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="metric-label mb-2">Tổng sản phẩm</div>
                    <div class="metric-number">{{ $totalProducts }}</div>
                    <div class="text-secondary small mt-1" style="font-size: 0.78rem;">
                        <span class="text-success fw-semibold">{{ $activeProducts }}</span> đang mở bán
                    </div>
                </div>
                <div class="metric-icon-box metric-icon-indigo">
                    <i data-lucide="package" style="width: 22px; height: 22px;"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Categories -->
    <div class="col-sm-6 col-xl-3">
        <div class="metric-card metric-card-success">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="metric-label mb-2">Dòng túi xách</div>
                    <div class="metric-number text-success">{{ $totalCategories }}</div>
                    <div class="text-secondary small mt-1" style="font-size: 0.78rem;">
                        Phân loại thương hiệu
                    </div>
                </div>
                <div class="metric-icon-box metric-icon-emerald">
                    <i data-lucide="folder-tree" style="width: 22px; height: 22px;"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Featured Items -->
    <div class="col-sm-6 col-xl-3">
        <div class="metric-card metric-card-info">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="metric-label mb-2">Sản phẩm nổi bật</div>
                    <div class="metric-number text-info">{{ $featuredCount }}</div>
                    <div class="text-secondary small mt-1" style="font-size: 0.78rem;">
                        Hiển thị trang chủ
                    </div>
                </div>
                <div class="metric-icon-box metric-icon-sky">
                    <i data-lucide="star" style="width: 22px; height: 22px;"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Out of Stock Warning -->
    <div class="col-sm-6 col-xl-3">
        <div class="metric-card {{ $outOfStock > 0 ? 'metric-card-danger' : 'metric-card-primary' }}">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="metric-label mb-2">Tạm hết hàng</div>
                    <div class="metric-number {{ $outOfStock > 0 ? 'text-danger' : 'text-dark' }}">{{ $outOfStock }}</div>
                    <div class="text-secondary small mt-1" style="font-size: 0.78rem;">
                        Cần nhập thêm kho
                    </div>
                </div>
                <div class="metric-icon-box {{ $outOfStock > 0 ? 'metric-icon-rose' : 'metric-icon-indigo' }}">
                    <i data-lucide="alert-circle" style="width: 22px; height: 22px;"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Main Table: Latest Products -->
<div class="card-modern">
    <div class="card-modern-header d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center gap-2">
            <span class="fw-bold text-dark" style="font-size: 1.05rem;">Sản phẩm túi xách mới nhập gần đây</span>
            <span class="badge-count-pill">5 sản phẩm</span>
        </div>
        <a href="{{ route('admin.products.index') }}" class="btn-surface small py-1.5 px-3">
            <span>Quản lý tất cả</span>
            <i data-lucide="chevron-right" style="width: 14px; height: 14px; margin-left: 0.25rem;"></i>
        </a>
    </div>

    <div class="table-responsive">
        <table class="table-modern">
            <thead>
                <tr>
                    <th style="width: 80px;" class="text-center">ID</th>
                    <th>Tên sản phẩm túi xách</th>
                    <th style="width: 160px;">Dòng túi</th>
                    <th style="width: 150px;">Giá bán</th>
                    <th style="width: 120px;" class="text-center">Tồn kho</th>
                    <th style="width: 100px;" class="text-center">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($latestProducts as $prod)
                    <tr>
                        <td class="text-center">
                            <span class="badge-mono-id">#{{ str_pad($prod->id, 3, '0', STR_PAD_LEFT) }}</span>
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-3">
                                @if ($prod->image)
                                    <img src="{{ $prod->image }}" alt="{{ $prod->name }}" class="rounded-3 border object-fit-cover flex-shrink-0" style="width: 42px; height: 42px;">
                                @else
                                    <div class="category-squircle flex-shrink-0" style="width: 42px; height: 42px;">
                                        <i data-lucide="shopping-bag" style="width: 18px; height: 18px;"></i>
                                    </div>
                                @endif
                                <div>
                                    <a href="{{ route('admin.products.show', $prod) }}" class="category-name-text d-block text-decoration-none">
                                        {{ $prod->name }}
                                    </a>
                                    <div class="text-tertiary small" style="font-size: 0.78rem;">
                                        Màu: {{ $prod->color ?? 'Mặc định' }}
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2.5 py-1 rounded-pill" style="font-size: 0.78rem;">
                                {{ $prod->category?->name ?? 'Chưa phân loại' }}
                            </span>
                        </td>
                        <td>
                            <span class="fw-bold text-dark">
                                {{ $prod->has_discount ? $prod->formatted_sale_price : $prod->formatted_price }}
                            </span>
                        </td>
                        <td class="text-center">
                            @if ($prod->stock > 0)
                                <span class="badge bg-success-subtle text-success px-2.5 py-1 rounded-pill fw-semibold" style="font-size: 0.76rem;">
                                    {{ $prod->stock }} chiếc
                                </span>
                            @else
                                <span class="badge bg-danger-subtle text-danger px-2.5 py-1 rounded-pill fw-semibold" style="font-size: 0.76rem;">
                                    Hết hàng
                                </span>
                            @endif
                        </td>
                        <td class="text-center">
                            <a href="{{ route('admin.products.edit', $prod) }}" class="btn btn-sm btn-surface p-1.5" title="Chỉnh sửa">
                                <i data-lucide="pencil" style="width: 15px; height: 15px;"></i>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted">Chưa có sản phẩm nào.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
