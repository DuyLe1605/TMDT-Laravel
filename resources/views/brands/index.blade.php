@extends('layouts.app')

@section('title', 'Quản lý Thương hiệu - TMDT Túi Xách Nữ')

@section('content')
<!-- Breadcrumbs & Header -->
<div class="mb-4">
    <div class="breadcrumb-modern">
        <a href="{{ route('admin.dashboard') }}">Tổng quan</a>
        <i data-lucide="chevron-right" style="width: 14px; height: 14px;"></i>
        <span class="text-primary fw-medium">Quản lý Thương hiệu</span>
    </div>
    
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mt-2">
        <div>
            <h2 class="fw-bold mb-1 text-dark" style="letter-spacing: -0.03em;">Quản lý Thương Hiệu Thời Trang</h2>
            <p class="text-secondary mb-0" style="font-size: 0.94rem;">
                Danh mục các nhà mốt xa xỉ và thương hiệu thời trang độc quyền (Hermès, Chanel, Gucci, Dior, Louis Vuitton...)
            </p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('admin.brands.create') }}" class="btn-brand-primary">
                <i data-lucide="plus" style="width: 18px; height: 18px; margin-right: 0.4rem;"></i>
                <span>Thêm thương hiệu mới</span>
            </a>
        </div>
    </div>
</div>

<!-- KPI Cards -->
<div class="row g-4 mb-4">
    <div class="col-sm-6 col-lg-4">
        <div class="metric-card metric-card-primary">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="metric-label mb-2">Tổng số thương hiệu</div>
                    <div class="metric-number">{{ $brands->total() }}</div>
                </div>
                <div class="metric-icon-box metric-icon-indigo">
                    <i data-lucide="award" style="width: 22px; height: 22px;"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-4">
        <div class="metric-card metric-card-success">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="metric-label mb-2">Thương hiệu hoạt động</div>
                    <div class="metric-number text-success">{{ $brands->where('is_active', true)->count() }}</div>
                </div>
                <div class="metric-icon-box metric-icon-emerald">
                    <i data-lucide="check-circle-2" style="width: 22px; height: 22px;"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-12 col-lg-4">
        <div class="metric-card metric-card-info">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="metric-label mb-2">Phân loại cao cấp</div>
                    <div class="metric-number text-primary">Haute Couture</div>
                </div>
                <div class="metric-icon-box" style="background: rgba(168, 85, 247, 0.12); color: #9333ea;">
                    <i data-lucide="sparkles" style="width: 22px; height: 22px;"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Brands Data Table Card -->
<div class="card-modern">
    <div class="card-modern-header d-flex flex-wrap align-items-center justify-content-between gap-3">
        <div class="d-flex align-items-center gap-2">
            <h5 class="fw-bold text-dark mb-0">Danh sách Thương hiệu</h5>
            <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-2.5 py-1" style="font-size: 0.75rem;">
                {{ $brands->total() }} thương hiệu
            </span>
        </div>

        <!-- Search Bar -->
        <form action="{{ route('admin.brands.index') }}" method="GET" class="d-flex align-items-center gap-2">
            <div class="position-relative" style="width: 280px;">
                <input 
                    type="text" 
                    name="search" 
                    value="{{ $search ?? '' }}" 
                    class="form-control form-control-modern pe-4" 
                    placeholder="Tìm tên thương hiệu..."
                >
                <i data-lucide="search" class="position-absolute end-0 top-50 translate-middle-y me-2.5 text-secondary" style="width: 15px; height: 15px;"></i>
            </div>
            @if (!empty($search))
                <a href="{{ route('admin.brands.index') }}" class="btn-surface px-2.5 py-2 text-secondary" title="Xóa tìm kiếm">
                    <i data-lucide="x" style="width: 16px; height: 16px;"></i>
                </a>
            @endif
        </form>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th style="width: 70px;" class="text-center">#</th>
                    <th style="width: 260px;">Thương hiệu</th>
                    <th>Slug định danh</th>
                    <th>Website chính thức</th>
                    <th class="text-center">Sản phẩm</th>
                    <th class="text-center" style="width: 130px;">Trạng thái</th>
                    <th class="text-end pe-4" style="width: 140px;">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($brands as $brand)
                    <tr>
                        <td class="text-center text-secondary small fw-semibold">
                            {{ $loop->iteration + ($brands->currentPage() - 1) * $brands->perPage() }}
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-3">
                                <div class="rounded-3 border overflow-hidden flex-shrink-0 d-flex align-items-center justify-content-center" style="width: 44px; height: 44px; background: var(--bg-surface-subtle);">
                                    @if ($brand->logo)
                                        <img src="{{ $brand->logo }}" alt="{{ $brand->name }}" class="w-100 h-100 object-fit-cover">
                                    @else
                                        <i data-lucide="award" class="text-primary" style="width: 20px; height: 20px;"></i>
                                    @endif
                                </div>
                                <div class="min-w-0">
                                    <a href="{{ route('admin.brands.show', $brand) }}" class="fw-bold text-dark text-decoration-none hover-primary text-truncate d-block">
                                        {{ $brand->name }}
                                    </a>
                                    <div class="text-secondary small text-truncate" style="max-width: 220px; font-size: 0.76rem;">
                                        {{ $brand->description ?? 'Chưa có mô tả' }}
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <code class="px-2 py-1 bg-light rounded text-primary small font-monospace">{{ $brand->slug }}</code>
                        </td>
                        <td>
                            @if ($brand->website)
                                <a href="{{ $brand->website }}" target="_blank" class="text-secondary small text-decoration-none hover-primary d-inline-flex align-items-center gap-1">
                                    <span>{{ parse_url($brand->website, PHP_URL_HOST) ?? $brand->website }}</span>
                                    <i data-lucide="external-link" style="width: 12px; height: 12px;"></i>
                                </a>
                            @else
                                <span class="text-muted small">--</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <span class="badge bg-secondary-subtle text-secondary px-2.5 py-1 rounded-pill fw-bold">
                                {{ $brand->products_count ?? $brand->products()->count() }} sản phẩm
                            </span>
                        </td>
                        <td class="text-center">
                            @if ($brand->is_active)
                                <span class="badge bg-success-subtle text-success border border-success-subtle px-2.5 py-1 rounded-pill small">
                                    ● Hoạt động
                                </span>
                            @else
                                <span class="badge bg-secondary-subtle text-secondary border px-2.5 py-1 rounded-pill small">
                                    Tạm ẩn
                                </span>
                            @endif
                        </td>
                        <td class="text-end pe-4">
                            <div class="d-inline-flex align-items-center gap-1">
                                <a href="{{ route('admin.brands.edit', $brand) }}" class="btn-surface p-2 text-primary" title="Chỉnh sửa thương hiệu">
                                    <i data-lucide="edit-3" style="width: 15px; height: 15px;"></i>
                                </a>
                                <button type="button" class="btn-surface p-2 text-danger border-0" onclick="confirmDeleteBrand({{ $brand->id }}, '{{ addslashes($brand->name) }}')" title="Xóa thương hiệu">
                                    <i data-lucide="trash-2" style="width: 15px; height: 15px;"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-5">
                            <div class="py-4">
                                <i data-lucide="award" class="text-muted mb-2" style="width: 44px; height: 44px;"></i>
                                <h6 class="fw-bold text-secondary">Chưa có thương hiệu nào</h6>
                                <p class="text-muted small mb-3">Hãy thêm thương hiệu xa xỉ đầu tiên như Hermès, Chanel, Gucci...</p>
                                <a href="{{ route('admin.brands.create') }}" class="btn-brand-primary">
                                    <i data-lucide="plus" style="width: 16px; height: 16px; margin-right: 0.35rem;"></i>
                                    <span>Thêm thương hiệu mới</span>
                                </a>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($brands->hasPages())
        <div class="p-3 border-top d-flex justify-content-end">
            {{ $brands->links() }}
        </div>
    @endif
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteBrandModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 420px;">
        <div class="modal-content modal-content-modern border-0">
            <div class="p-4 text-center">
                <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 54px; height: 54px; background: var(--danger-50); color: var(--danger-600);">
                    <i data-lucide="alert-triangle" style="width: 26px; height: 26px;"></i>
                </div>
                <h5 class="fw-bold text-dark mb-2">Xác nhận xóa thương hiệu?</h5>
                <p class="text-secondary small mb-4">
                    Bạn có chắc chắn muốn xóa thương hiệu <strong id="brandNameToDelete" class="text-dark"></strong> không? Các sản phẩm thuộc thương hiệu này sẽ được chuyển về trạng thái không có thương hiệu.
                </p>
                <form id="deleteBrandForm" method="POST" action="">
                    @csrf
                    @method('DELETE')
                    <div class="d-flex justify-content-center gap-3">
                        <button type="button" class="btn-surface px-4 py-2" data-bs-dismiss="modal">Hủy bỏ</button>
                        <button type="submit" class="btn btn-danger px-4 py-2 rounded-3 fw-semibold">Xác nhận xóa</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    let deleteBrandModalInstance = null;

    function confirmDeleteBrand(brandId, brandName) {
        const modalEl = document.getElementById('deleteBrandModal');
        document.getElementById('brandNameToDelete').textContent = brandName;
        document.getElementById('deleteBrandForm').action = `/admin/brands/${brandId}`;

        if (!deleteBrandModalInstance) {
            deleteBrandModalInstance = new bootstrap.Modal(modalEl);
        }
        deleteBrandModalInstance.show();
        setTimeout(() => { if (typeof lucide !== 'undefined') lucide.createIcons(); }, 100);
    }
</script>
@endsection
