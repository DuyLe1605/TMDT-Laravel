@extends('layouts.app')

@section('title', 'Quản lý Sản phẩm Túi Xách Nữ')

@section('content')
<!-- Breadcrumbs & Page Header -->
<div class="mb-4">
    <div class="breadcrumb-modern">
        <a href="{{ route('products.index') }}">Tổng quan</a>
        <i data-lucide="chevron-right" style="width: 14px; height: 14px;"></i>
        <span class="text-primary fw-medium">Sản phẩm Túi Xách Nữ</span>
    </div>
    
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mt-2">
        <div>
            <h2 class="fw-bold mb-1 text-dark" style="letter-spacing: -0.03em;">Quản lý Sản phẩm Túi Xách Nữ</h2>
            <p class="text-secondary mb-0" style="font-size: 0.94rem;">
                Danh mục kho hàng túi xách thời trang cao cấp, theo dõi giá bán, biến thể và số lượng tồn kho
            </p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('products.create') }}" class="btn-brand-primary">
                <i data-lucide="plus" style="width: 18px; height: 18px; margin-right: 0.45rem;"></i>
                <span>Thêm sản phẩm mới</span>
            </a>
        </div>
    </div>
</div>

<!-- KPI / Metric Cards Section -->
<div class="row g-4 mb-4">
    <div class="col-sm-6 col-lg-4">
        <div class="metric-card metric-card-primary">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="metric-label mb-2">Tổng sản phẩm</div>
                    <div class="metric-number">{{ method_exists($products, 'total') ? $products->total() : $products->count() }}</div>
                </div>
                <div class="metric-icon-box metric-icon-indigo">
                    <i data-lucide="package" style="width: 22px; height: 22px;"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-4">
        <div class="metric-card metric-card-success">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="metric-label mb-2">Dòng túi xách</div>
                    <div class="metric-number text-success">{{ $categories->count() }}</div>
                </div>
                <div class="metric-icon-box metric-icon-emerald">
                    <i data-lucide="folder-tree" style="width: 22px; height: 22px;"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-12 col-lg-4">
        <div class="metric-card metric-card-info">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="metric-label mb-2">Phân khúc kinh doanh</div>
                    <div class="metric-number" style="font-size: 1.3rem; font-weight: 700;">Túi Xách & Ví Nữ</div>
                </div>
                <div class="metric-icon-box metric-icon-sky">
                    <i data-lucide="sparkles" style="width: 22px; height: 22px;"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Data Table Card Container -->
<div class="card-modern">
    <!-- Card Header with Real-time Filters (Strictly Same Row) -->
    <div class="card-modern-header d-flex flex-wrap justify-content-between align-items-center gap-3">
        <div class="d-flex align-items-center">
            <span class="fw-bold text-dark" style="font-size: 1.1rem;">Kho hàng túi xách</span>
            <span class="badge-count-pill">
                {{ method_exists($products, 'total') ? $products->total() : $products->count() }} sản phẩm
            </span>
        </div>
        
        <div class="d-flex align-items-center gap-2.5 flex-nowrap">
            <!-- Filter by Category -->
            <form method="GET" action="{{ route('products.index') }}" class="d-flex align-items-center gap-2.5 flex-nowrap mb-0">
                <div class="select-box-modern" style="width: 170px;">
                    <i data-lucide="folder-tree" class="select-icon" style="width: 15px; height: 15px;"></i>
                    <select name="category_id" class="form-select form-select-modern" onchange="this.form.submit()">
                        <option value="">Tất cả dòng túi</option>
                        @foreach ($categories as $cat)
                            <option value="{{ $cat->id }}" {{ (isset($categoryId) && $categoryId == $cat->id) ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="select-box-modern" style="width: 165px;">
                    <i data-lucide="arrow-up-down" class="select-icon" style="width: 15px; height: 15px;"></i>
                    <select name="sort" class="form-select form-select-modern" onchange="this.form.submit()">
                        <option value="created_desc" {{ (isset($sort) && $sort == 'created_desc') ? 'selected' : '' }}>Mới nhất trước</option>
                        <option value="created_asc" {{ (isset($sort) && $sort == 'created_asc') ? 'selected' : '' }}>Cũ nhất trước</option>
                        <option value="price_asc" {{ (isset($sort) && $sort == 'price_asc') ? 'selected' : '' }}>Giá: Thấp &rarr; Cao</option>
                        <option value="price_desc" {{ (isset($sort) && $sort == 'price_desc') ? 'selected' : '' }}>Giá: Cao &rarr; Thấp</option>
                        <option value="stock_desc" {{ (isset($sort) && $sort == 'stock_desc') ? 'selected' : '' }}>Tồn kho nhiều</option>
                        <option value="name_asc" {{ (isset($sort) && $sort == 'name_asc') ? 'selected' : '' }}>Tên A &rarr; Z</option>
                    </select>
                </div>
            </form>

            <!-- Accent-Insensitive Instant Search Box -->
            <div class="search-box-modern">
                <i data-lucide="search" class="search-icon" style="width: 16px; height: 16px;"></i>
                <input 
                    type="text" 
                    id="productSearchInput" 
                    class="form-control form-control-modern" 
                    placeholder="Tìm túi xách (ví dụ: da bo, kẹp nách)..."
                    onkeyup="filterProductRows()"
                >
            </div>
        </div>
    </div>

    <!-- Table Content -->
    <div class="table-responsive">
        <table class="table-modern" id="productTable">
            <thead>
                <tr>
                    <th style="width: 90px;" class="text-center">Mã ID</th>
                    <th>Sản phẩm túi xách</th>
                    <th style="width: 160px;">Dòng túi</th>
                    <th style="width: 160px;">Giá bán</th>
                    <th style="width: 120px;" class="text-center">Tồn kho</th>
                    <th style="width: 130px;" class="text-center">Trạng thái</th>
                    <th style="width: 90px;" class="text-center">Thao tác</th>
                </tr>
            </thead>
            <tbody id="productTableBody">
                @forelse ($products as $product)
                    @php
                        $createdAt = $product->created_at ? $product->created_at->format('d/m/Y') : '---';
                    @endphp
                    <tr class="product-data-row" 
                        data-id="{{ $product->id }}" 
                        data-name="{{ $product->name }}" 
                        data-material="{{ $product->material }}" 
                        data-color="{{ $product->color }}" 
                        data-category="{{ $product->category?->name }}">
                        <td class="text-center">
                            <span class="badge-mono-id">#{{ str_pad($product->id, 3, '0', STR_PAD_LEFT) }}</span>
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-3">
                                @if ($product->image)
                                    <img src="{{ $product->image }}" alt="{{ $product->name }}" class="rounded-3 border object-fit-cover flex-shrink-0" style="width: 48px; height: 48px;">
                                @else
                                    <div class="category-squircle flex-shrink-0" style="width: 48px; height: 48px;">
                                        <i data-lucide="shopping-bag" style="width: 22px; height: 22px;"></i>
                                    </div>
                                @endif
                                <div>
                                    <a href="{{ route('products.show', $product) }}" class="category-name-text d-block text-decoration-none">
                                        {{ $product->name }}
                                    </a>
                                    <div class="d-flex align-items-center gap-2 text-tertiary mt-0.5" style="font-size: 0.8rem;">
                                        @if ($product->color)
                                            <span>Màu: <strong class="text-secondary">{{ $product->color }}</strong></span>
                                            <span>&bull;</span>
                                        @endif
                                        @if ($product->material)
                                            <span>Chất liệu: <strong class="text-secondary">{{ $product->material }}</strong></span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2.5 py-1 rounded-pill" style="font-size: 0.78rem;">
                                {{ $product->category?->name ?? 'Chưa phân loại' }}
                            </span>
                        </td>
                        <td>
                            <div>
                                <span class="fw-bold text-dark d-block" style="font-size: 0.95rem;">
                                    {{ $product->has_discount ? $product->formatted_sale_price : $product->formatted_price }}
                                </span>
                                @if ($product->has_discount)
                                    <span class="text-muted text-decoration-line-through small" style="font-size: 0.8rem;">
                                        {{ $product->formatted_price }}
                                    </span>
                                @endif
                            </div>
                        </td>
                        <td class="text-center">
                            @if ($product->stock > 10)
                                <span class="badge bg-success-subtle text-success px-2.5 py-1 rounded-pill fw-semibold" style="font-size: 0.76rem;">
                                    {{ $product->stock }} chiếc
                                </span>
                            @elseif ($product->stock > 0)
                                <span class="badge bg-warning-subtle text-warning-emphasis px-2.5 py-1 rounded-pill fw-semibold" style="font-size: 0.76rem;">
                                    Còn {{ $product->stock }}
                                </span>
                            @else
                                <span class="badge bg-danger-subtle text-danger px-2.5 py-1 rounded-pill fw-semibold" style="font-size: 0.76rem;">
                                    Hết hàng
                                </span>
                            @endif
                        </td>
                        <td class="text-center">
                            @if ($product->is_active)
                                <span class="d-inline-flex align-items-center gap-1.5 text-success small fw-medium">
                                    <span class="status-pulse-dot" style="width: 7px; height: 7px;"></span>
                                    <span>Đang bán</span>
                                </span>
                            @else
                                <span class="text-secondary small fw-medium">
                                    &bull; Đang ẩn
                                </span>
                            @endif
                        </td>
                        <td class="text-center">
                            <!-- 3-Dots Action Dropdown -->
                            <div class="dropdown">
                                <button 
                                    class="btn-action-dropdown" 
                                    type="button" 
                                    data-bs-toggle="dropdown" 
                                    data-bs-display="static"
                                    aria-expanded="false"
                                    title="Tùy chọn thao tác"
                                >
                                    <i data-lucide="more-horizontal" style="width: 16px; height: 16px;"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-modern dropdown-menu-end shadow">
                                    <li>
                                        <a href="{{ route('products.show', $product) }}" class="dropdown-item-modern">
                                            <i data-lucide="eye" style="width: 16px; height: 16px; margin-right: 0.5rem;"></i>
                                            <span>Xem chi tiết</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ route('products.edit', $product) }}" class="dropdown-item-modern">
                                            <i data-lucide="pencil" style="width: 16px; height: 16px; margin-right: 0.5rem;"></i>
                                            <span>Chỉnh sửa</span>
                                        </a>
                                    </li>
                                    <li><hr class="dropdown-divider-modern"></li>
                                    <li>
                                        <button 
                                            type="button" 
                                            class="dropdown-item-modern item-danger" 
                                            onclick="openDeleteProductModal('{{ $product->id }}', '{{ addslashes($product->name) }}')"
                                        >
                                            <i data-lucide="trash-2" style="width: 16px; height: 16px; margin-right: 0.5rem;"></i>
                                            <span>Xóa sản phẩm</span>
                                        </button>
                                    </li>
                                </ul>
                            </div>

                            <form id="delete-product-form-{{ $product->id }}" action="{{ route('products.destroy', $product) }}" method="POST" class="d-none">
                                @csrf
                                @method('DELETE')
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-5">
                            <div class="py-4">
                                <div class="category-squircle mx-auto mb-3" style="width: 56px; height: 56px;">
                                    <i data-lucide="package-open" style="width: 28px; height: 28px;"></i>
                                </div>
                                <h5 class="fw-bold text-dark mb-1">Chưa có sản phẩm túi xách nào</h5>
                                <p class="text-secondary small mb-4">Bắt đầu bằng cách thêm mẫu túi xách nữ đầu tiên vào kho hàng.</p>
                                <a href="{{ route('products.create') }}" class="btn-brand-primary">
                                    <i data-lucide="plus" style="width: 16px; height: 16px; margin-right: 0.45rem;"></i>
                                    <span>Thêm sản phẩm ngay</span>
                                </a>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination Footer -->
    @if ($products->hasPages())
        <div class="card-modern-footer d-flex justify-content-end">
            {{ $products->links('pagination::bootstrap-5') }}
        </div>
    @endif
</div>

<!-- =======================================================================
     DELETE CONFIRMATION MODAL
     ======================================================================= -->
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
@endsection

@section('scripts')
<script>
    let activeDeleteProductId = null;
    let deleteProductModalInstance = null;

    function removeVietnameseTones(str) {
        if (!str) return '';
        return str.normalize('NFD')
            .replace(/[\u0300-\u036f]/g, '')
            .replace(/[đĐ]/g, 'd')
            .replace(/[^a-z0-9\s-]/gi, '')
            .toLowerCase()
            .trim();
    }

    function openDeleteProductModal(id, name) {
        activeDeleteProductId = id;
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
        if (activeDeleteProductId) {
            const form = document.getElementById('delete-product-form-' + activeDeleteProductId);
            if (form) form.submit();
        }
    }

    function filterProductRows() {
        const query = removeVietnameseTones(document.getElementById('productSearchInput').value);
        const rows = document.querySelectorAll('.product-data-row');
        
        rows.forEach(row => {
            const name = removeVietnameseTones(row.getAttribute('data-name') || '');
            const material = removeVietnameseTones(row.getAttribute('data-material') || '');
            const color = removeVietnameseTones(row.getAttribute('data-color') || '');
            const category = removeVietnameseTones(row.getAttribute('data-category') || '');
            const id = row.getAttribute('data-id') || '';
            
            const match = name.includes(query) || material.includes(query) || color.includes(query) || category.includes(query) || id.includes(query);
            row.style.display = match ? '' : 'none';
        });
    }
</script>
@endsection
