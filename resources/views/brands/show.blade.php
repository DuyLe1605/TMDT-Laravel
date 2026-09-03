@extends('layouts.app')

@section('title', 'Thương Hiệu: ' . $brand->name)

@section('content')
<div class="breadcrumb-modern mb-3">
    <a href="{{ route('admin.brands.index') }}">Thương hiệu</a>
    <i data-lucide="chevron-right" style="width: 14px; height: 14px;"></i>
    <span class="text-primary fw-medium">{{ $brand->name }}</span>
</div>

<div class="row g-4">
    <!-- Left: Brand Info Card -->
    <div class="col-lg-4">
        <div class="card-modern p-4">
            <div class="text-center pb-3 border-bottom mb-3">
                <div class="rounded-4 border overflow-hidden d-inline-flex align-items-center justify-content-center mb-3 shadow-sm" style="width: 84px; height: 84px; background: var(--bg-surface-subtle);">
                    @if ($brand->logo)
                        <img src="{{ $brand->logo }}" alt="{{ $brand->name }}" class="w-100 h-100 object-fit-cover">
                    @else
                        <i data-lucide="award" class="text-primary" style="width: 42px; height: 42px;"></i>
                    @endif
                </div>
                <h4 class="fw-bold text-dark mb-1">{{ $brand->name }}</h4>
                <div class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-1 rounded-pill small">
                    {{ $brand->slug }}
                </div>
            </div>

            <div class="d-flex flex-column gap-3 text-secondary small">
                <div>
                    <span class="text-muted d-block mb-1">Mô tả:</span>
                    <p class="text-dark mb-0" style="line-height: 1.6;">
                        {{ $brand->description ?? 'Chưa có thông tin mô tả chi tiết.' }}
                    </p>
                </div>

                @if ($brand->website)
                    <div>
                        <span class="text-muted d-block mb-1">Website chính thức:</span>
                        <a href="{{ $brand->website }}" target="_blank" class="text-primary text-decoration-none d-inline-flex align-items-center gap-1">
                            <span>{{ $brand->website }}</span>
                            <i data-lucide="external-link" style="width: 12px; height: 12px;"></i>
                        </a>
                    </div>
                @endif

                <div>
                    <span class="text-muted d-block mb-1">Trạng thái:</span>
                    @if ($brand->is_active)
                        <span class="badge bg-success-subtle text-success border border-success-subtle px-2.5 py-1 rounded-pill">
                            ● Đang hoạt động
                        </span>
                    @else
                        <span class="badge bg-secondary-subtle text-secondary border px-2.5 py-1 rounded-pill">
                            Tạm ẩn
                        </span>
                    @endif
                </div>
            </div>

            <div class="d-flex gap-2 mt-4 pt-3 border-top">
                <a href="{{ route('admin.brands.edit', $brand) }}" class="btn-brand-primary w-100 justify-content-center">
                    <i data-lucide="edit-3" style="width: 15px; height: 15px; margin-right: 0.35rem;"></i>
                    <span>Chỉnh sửa</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Right: Products by Brand -->
    <div class="col-lg-8">
        <div class="card-modern">
            <div class="card-modern-header d-flex justify-content-between align-items-center">
                <h5 class="fw-bold text-dark mb-0">Sản phẩm thuộc thương hiệu {{ $brand->name }}</h5>
                <span class="badge bg-secondary-subtle text-secondary px-2.5 py-1 rounded-pill">
                    {{ $brand->products->count() }} sản phẩm
                </span>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th style="width: 60px;">Ảnh</th>
                            <th>Tên sản phẩm</th>
                            <th>Giá bán</th>
                            <th>Tồn kho</th>
                            <th class="text-end pe-4">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($brand->products as $product)
                            <tr>
                                <td>
                                    <div class="rounded-3 border overflow-hidden" style="width: 44px; height: 44px; background: var(--bg-surface-subtle);">
                                        @if ($product->image)
                                            <img src="{{ $product->image }}" alt="" class="w-100 h-100 object-fit-cover">
                                        @else
                                            <div class="w-100 h-100 d-flex align-items-center justify-content-center">
                                                <i data-lucide="shopping-bag" style="width: 18px; height: 18px;" class="text-secondary"></i>
                                            </div>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <a href="{{ route('admin.products.show', $product) }}" class="fw-bold text-dark text-decoration-none hover-primary">
                                        {{ $product->name }}
                                    </a>
                                    @if ($product->has_variants)
                                        <span class="badge bg-info-subtle text-info border border-info-subtle rounded-pill px-2 py-0.5 ms-1" style="font-size: 0.68rem;">
                                            Có biến thể
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <div class="fw-bold text-primary small">
                                        {{ $product->formatted_price_range }}
                                    </div>
                                </td>
                                <td>
                                    <span class="small fw-semibold {{ $product->total_stock > 0 ? 'text-success' : 'text-danger' }}">
                                        {{ $product->total_stock }} chiếc
                                    </span>
                                </td>
                                <td class="text-end pe-4">
                                    <a href="{{ route('admin.products.edit', $product) }}" class="btn-surface p-1.5 text-primary" title="Sửa sản phẩm">
                                        <i data-lucide="edit-3" style="width: 14px; height: 14px;"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted small">
                                    Chưa có sản phẩm nào được gán vào thương hiệu này.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
