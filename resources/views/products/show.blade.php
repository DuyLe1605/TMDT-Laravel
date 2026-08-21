@extends('layouts.app')

@section('title', 'Chi tiết sản phẩm #' . $product->id)

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10 col-xl-9">
        <!-- Breadcrumbs -->
        <div class="breadcrumb-modern">
            <a href="{{ route('admin.products.index') }}">Sản phẩm</a>
            <i data-lucide="chevron-right" style="width: 14px; height: 14px;"></i>
            <span class="text-primary fw-medium">Chi tiết #{{ str_pad($product->id, 3, '0', STR_PAD_LEFT) }}</span>
        </div>

        <div class="card-modern">
            <!-- Card Header -->
            <div class="card-modern-header d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <div class="category-squircle" style="width: 44px; height: 44px; margin-right: 1rem !important; flex-shrink: 0;">
                        <i data-lucide="package" style="width: 22px; height: 22px;"></i>
                    </div>
                    <div>
                        <div class="d-flex align-items-center mb-1">
                            <h5 class="fw-bold text-dark mb-0">Chi Tiết Sản Phẩm Túi Xách</h5>
                            <span class="badge-mono-id" style="margin-left: 0.85rem !important;">#{{ str_pad($product->id, 3, '0', STR_PAD_LEFT) }}</span>
                        </div>
                        <div class="text-secondary small">Thuộc tính ngành hàng, định giá và tình trạng kho</div>
                    </div>
                </div>
                <a href="{{ route('admin.products.index') }}" class="btn-surface" style="padding: 0.55rem 1.1rem; font-size: 0.88rem;">
                    <i data-lucide="arrow-left" style="width: 15px; height: 15px; margin-right: 0.45rem;"></i>
                    <span>Quay lại</span>
                </a>
            </div>
            
            <!-- Card Body -->
            <div class="card-modern-body">
                <!-- Theme-Aware Product Hero Banner -->
                <div class="hero-banner-modern">
                    <div class="row align-items-center g-4">
                        <div class="col-md-3 text-center">
                            @if ($product->image)
                                <img src="{{ $product->image }}" alt="{{ $product->name }}" class="img-fluid rounded-3 border shadow-sm" style="max-height: 160px; object-fit: cover;">
                            @else
                                <div class="category-squircle mx-auto" style="width: 110px; height: 110px;">
                                    <i data-lucide="shopping-bag" style="width: 48px; height: 48px;"></i>
                                </div>
                            @endif
                        </div>
                        <div class="col-md-9">
                            <div class="d-flex flex-wrap justify-content-between align-items-start gap-2 mb-2">
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-1 rounded-pill" style="font-size: 0.78rem;">
                                    {{ $product->category?->name ?? 'Túi xách' }}
                                </span>
                                <div>
                                    @if ($product->is_featured)
                                        <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-2.5 py-1 rounded-pill me-1" style="font-size: 0.75rem;">
                                            ⭐ Nổi bật
                                        </span>
                                    @endif
                                    @if ($product->is_active)
                                        <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-1 rounded-pill" style="font-size: 0.75rem;">
                                            <i data-lucide="check" style="width: 12px; height: 12px; margin-right: 0.25rem; display: inline-block;"></i>
                                            <span>Đang mở bán</span>
                                        </span>
                                    @else
                                        <span class="badge bg-secondary-subtle text-secondary px-3 py-1 rounded-pill" style="font-size: 0.75rem;">
                                            Đang ẩn
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="hero-banner-title">
                                {{ $product->name }}
                            </div>

                            <div class="d-flex align-items-baseline gap-3 mt-2">
                                <span class="fs-3 fw-bold text-primary">
                                    {{ $product->has_discount ? $product->formatted_sale_price : $product->formatted_price }}
                                </span>
                                @if ($product->has_discount)
                                    <span class="text-muted text-decoration-line-through fs-5">
                                        {{ $product->formatted_price }}
                                    </span>
                                    <span class="badge bg-danger-subtle text-danger px-2.5 py-1 rounded-pill fw-bold" style="font-size: 0.78rem;">
                                        Tiết kiệm {{ number_format((float)$product->price - (float)$product->sale_price, 0, ',', '.') }} ₫
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Structured Specs Grid -->
                <div class="spec-grid-box mb-4">
                    <div class="row g-4">
                        <div class="col-sm-6 col-md-4">
                            <div class="spec-item">
                                <span class="spec-item-label">Mã định danh (ID)</span>
                                <span class="spec-item-value font-monospace text-primary">#{{ $product->id }}</span>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-4">
                            <div class="spec-item">
                                <span class="spec-item-label">Số lượng tồn kho</span>
                                <span class="spec-item-value {{ $product->stock > 0 ? 'text-success' : 'text-danger' }}">
                                    {{ $product->stock }} chiếc trong kho
                                </span>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-4">
                            <div class="spec-item">
                                <span class="spec-item-label">Chất liệu</span>
                                <span class="spec-item-value">{{ $product->material ?? 'Đang cập nhật' }}</span>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-4">
                            <div class="spec-item">
                                <span class="spec-item-label">Kích thước (D x R x C)</span>
                                <span class="spec-item-value font-monospace">{{ $product->dimensions ?? 'Tiêu chuẩn' }}</span>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-4">
                            <div class="spec-item">
                                <span class="spec-item-label">Màu sắc</span>
                                <span class="spec-item-value">{{ $product->color ?? 'Mặc định' }}</span>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-4">
                            <div class="spec-item">
                                <span class="spec-item-label">Thời điểm nhập kho</span>
                                <span class="spec-item-value d-inline-flex align-items-center text-secondary">
                                    <i data-lucide="calendar" class="text-tertiary" style="width: 15px; height: 15px; margin-right: 0.5rem;"></i>
                                    <span>{{ $product->created_at ? $product->created_at->format('d/m/Y H:i') : '---' }}</span>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Product Description Box -->
                @if ($product->description)
                    <div class="p-4 rounded-3 mb-4" style="background: var(--bg-surface-subtle); border: 1px solid var(--border-default);">
                        <h6 class="fw-bold text-dark mb-2">Mô tả sản phẩm & Đặc điểm nổi bật</h6>
                        <p class="text-secondary mb-0" style="font-size: 0.92rem; line-height: 1.7; white-space: pre-line;">
                            {{ $product->description }}
                        </p>
                    </div>
                @endif

                <!-- Action Toolbar -->
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 pt-3 border-top">
                    <button 
                        type="button" 
                        class="btn btn-outline-danger px-3.5 py-2 rounded-3 d-inline-flex align-items-center"
                        onclick="openDeleteProductModal('{{ $product->id }}', '{{ addslashes($product->name) }}')"
                    >
                        <i data-lucide="trash-2" style="width: 15px; height: 15px; margin-right: 0.45rem;"></i>
                        <span>Xóa sản phẩm</span>
                    </button>

                    <a href="{{ route('admin.products.edit', $product) }}" class="btn-brand-primary">
                        <i data-lucide="pencil" style="width: 16px; height: 16px; margin-right: 0.45rem;"></i>
                        <span>Chỉnh sửa sản phẩm</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modern Delete Confirmation Modal -->
<div class="modal fade" id="deleteProductConfirmModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 450px;">
        <div class="modal-content modal-content-modern border-0">
            <div class="p-4 text-center">
                <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 58px; height: 58px; background: var(--danger-50); color: var(--danger-600);">
                    <i data-lucide="alert-triangle" style="width: 28px; height: 28px;"></i>
                </div>
                <h5 class="fw-bold text-dark mb-2">Xác nhận xóa sản phẩm</h5>
                <p class="text-secondary small mb-4">
                    Bạn có chắc chắn muốn xóa túi xách <strong id="deleteProductName" class="text-dark"></strong> (#<span id="deleteProductId"></span>)? Thao tác này không thể hoàn tác.
                </p>
                <div class="d-flex justify-content-center gap-3">
                    <button type="button" class="btn-surface px-4 py-2" data-bs-dismiss="modal">
                        <span>Hủy bỏ</span>
                    </button>
                    <button type="button" class="btn btn-danger px-4 py-2 rounded-3 fw-semibold d-inline-flex align-items-center" id="confirmDeleteProductBtn" onclick="submitDeleteProductForm()">
                        <i data-lucide="trash-2" style="width: 16px; height: 16px; margin-right: 0.4rem;"></i>
                        <span>Xóa vĩnh viễn</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<form id="delete-product-form-{{ $product->id }}" action="{{ route('admin.products.destroy', $product) }}" method="POST" class="d-none">
    @csrf
    @method('DELETE')
</form>
@endsection

@section('scripts')
<script>
    let deleteProductModalInstance = null;

    function openDeleteProductModal(id, name) {
        document.getElementById('deleteProductId').innerText = id;
        document.getElementById('deleteProductName').innerText = name;
        
        const modalEl = document.getElementById('deleteProductConfirmModal');
        if (!deleteProductModalInstance) deleteProductModalInstance = new bootstrap.Modal(modalEl);
        deleteProductModalInstance.show();
        setTimeout(() => {
            if (typeof lucide !== 'undefined') lucide.createIcons();
        }, 200);
    }

    function submitDeleteProductForm() {
        const form = document.getElementById('delete-product-form-{{ $product->id }}');
        if (form) {
            form.submit();
        }
    }
</script>
@endsection
