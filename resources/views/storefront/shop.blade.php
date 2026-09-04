@extends('layouts.storefront')

@section('title', 'Bộ Sưu Tập Túi Xách Nữ - Aurelia Luxury Bags')

@section('content')
<div class="container py-4 py-lg-5">
    <!-- Breadcrumb & Header Hero -->
    <div class="mb-4">
        <div class="breadcrumb-modern mb-2">
            <a href="{{ route('home') }}">Trang chủ</a>
            <i data-lucide="chevron-right" style="width: 14px; height: 14px;"></i>
            <span class="text-primary fw-medium">Bộ sưu tập túi xách</span>
        </div>
        
        <div class="d-flex flex-wrap justify-content-between align-items-end gap-3 mt-3 pb-3 border-bottom">
            <div>
                <h1 class="fw-bold text-dark mb-1" style="font-size: 2rem; letter-spacing: -0.03em;">Bộ Sưu Tập Túi Xách Nữ</h1>
                <p class="text-secondary mb-0" style="font-size: 0.95rem;">
                    Tuyển chọn những thiết kế túi da cao cấp, thời thượng từ Aurelia Luxury
                </p>
            </div>
            <div class="d-flex align-items-center">
                <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-2 rounded-pill fw-semibold" style="font-size: 0.85rem;">
                    <i data-lucide="sparkles" style="width: 14px; height: 14px; margin-right: 0.35rem; display: inline-block;"></i>
                    <span>{{ $products->total() }} sản phẩm đang mở bán</span>
                </span>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Sidebar Filter (Left Column) -->
        <div class="col-lg-3">
            <div class="shop-filter-card sticky-top" style="top: 85px;">
                <div class="shop-filter-title">
                    <i data-lucide="sliders-horizontal" class="text-primary" style="width: 17px; height: 17px;"></i>
                    <span>Bộ lọc tìm kiếm</span>
                </div>

                <form method="GET" action="{{ route('shop.index') }}" id="shopFilterForm">
                    <!-- Search Input -->
                    <div class="mb-3.5">
                        <label class="form-label-modern mb-1.5 small fw-semibold">Từ khóa tìm kiếm</label>
                        <div class="search-box-modern w-100">
                            <i data-lucide="search" class="search-icon" style="width: 15px; height: 15px;"></i>
                            <input 
                                type="text" 
                                name="search" 
                                class="form-control form-control-modern" 
                                placeholder="Tên túi, da bò, đen..." 
                                value="{{ $search ?? '' }}"
                            >
                        </div>
                    </div>

                    <!-- Brand Filter -->
                    <div class="mb-3.5">
                        <label class="form-label-modern mb-1.5 small fw-semibold">Thương hiệu nhà mốt</label>
                        <select name="brand_id" class="form-select form-select-modern w-100" onchange="this.form.submit()">
                            <option value="">Tất cả thương hiệu</option>
                            @foreach ($brands as $b)
                                <option value="{{ $b->id }}" {{ (isset($brandId) && $brandId == $b->id) ? 'selected' : '' }}>
                                    👑 {{ $b->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Category Filter -->
                    <div class="mb-3.5">
                        <label class="form-label-modern mb-1.5 small fw-semibold">Dòng túi xách</label>
                        <select name="category_id" class="form-select form-select-modern w-100" onchange="this.form.submit()">
                            <option value="">Tất cả dòng túi</option>
                            @foreach ($categories as $cat)
                                <option value="{{ $cat->id }}" {{ (isset($categoryId) && $categoryId == $cat->id) ? 'selected' : '' }}>
                                    {{ $cat->parent_id ? '↳ ' . $cat->name : '📁 ' . $cat->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Price Range Filter -->
                    <div class="mb-3.5">
                        <label class="form-label-modern mb-1.5 small fw-semibold">Khoảng giá (VNĐ)</label>
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <input 
                                type="number" 
                                name="min_price" 
                                class="form-control form-control-sm form-control-modern" 
                                placeholder="Từ..." 
                                value="{{ $minPrice ?? '' }}"
                                step="50000"
                            >
                            <span class="text-secondary small">-</span>
                            <input 
                                type="number" 
                                name="max_price" 
                                class="form-control form-control-sm form-control-modern" 
                                placeholder="Đến..." 
                                value="{{ $maxPrice ?? '' }}"
                                step="50000"
                            >
                        </div>
                        <!-- Quick Price Presets -->
                        <div class="d-flex flex-wrap gap-1.5">
                            <button type="button" class="btn btn-xs btn-outline-secondary rounded-pill py-0.5 px-2" style="font-size: 0.72rem;" onclick="setPricePreset(0, 500000)">&lt; 500k</button>
                            <button type="button" class="btn btn-xs btn-outline-secondary rounded-pill py-0.5 px-2" style="font-size: 0.72rem;" onclick="setPricePreset(500000, 1000000)">500k-1tr</button>
                            <button type="button" class="btn btn-xs btn-outline-secondary rounded-pill py-0.5 px-2" style="font-size: 0.72rem;" onclick="setPricePreset(1000000, 2000000)">1tr-2tr</button>
                            <button type="button" class="btn btn-xs btn-outline-secondary rounded-pill py-0.5 px-2" style="font-size: 0.72rem;" onclick="setPricePreset(2000000, '')">&gt; 2tr</button>
                        </div>
                    </div>

                    <!-- In Stock Filter -->
                    <div class="mb-3.5 p-2.5 rounded-3" style="background: var(--bg-surface-subtle); border: 1px solid var(--border-default);">
                        <div class="form-check mb-0">
                            <input class="form-check-input" type="checkbox" name="in_stock" id="in_stock" value="1" {{ !empty($inStock) ? 'checked' : '' }} onchange="this.form.submit()">
                            <label class="form-check-label text-dark small fw-medium user-select-none" for="in_stock">
                                Chỉ hiện sản phẩm còn hàng
                            </label>
                        </div>
                    </div>

                    <!-- Sort Filter -->
                    <div class="mb-4">
                        <label class="form-label-modern mb-1.5 small fw-semibold">Sắp xếp theo</label>
                        <select name="sort" class="form-select form-select-modern w-100" onchange="this.form.submit()">
                            <option value="created_desc" {{ (isset($sort) && $sort == 'created_desc') ? 'selected' : '' }}>Mới nhất trước</option>
                            <option value="price_asc" {{ (isset($sort) && $sort == 'price_asc') ? 'selected' : '' }}>Giá: Thấp &rarr; Cao</option>
                            <option value="price_desc" {{ (isset($sort) && $sort == 'price_desc') ? 'selected' : '' }}>Giá: Cao &rarr; Thấp</option>
                            <option value="name_asc" {{ (isset($sort) && $sort == 'name_asc') ? 'selected' : '' }}>Tên: A &rarr; Z</option>
                        </select>
                    </div>

                    <div class="d-flex gap-2 pt-1">
                        <button type="submit" class="btn-brand-primary w-100 py-2.5 justify-content-center fw-semibold">
                            <i data-lucide="filter" style="width: 15px; height: 15px; margin-right: 0.45rem;"></i>
                            <span>Áp dụng lọc</span>
                        </button>
                        @if ($search || $categoryId || $brandId || $minPrice || $maxPrice || $inStock || ($sort && $sort != 'created_desc'))
                            <a href="{{ route('shop.index') }}" class="btn btn-surface py-2.5 px-3 d-inline-flex align-items-center justify-content-center" title="Đặt lại bộ lọc">
                                <i data-lucide="rotate-ccw" style="width: 16px; height: 16px;"></i>
                            </a>
                        @endif
                    </div>
                </form>
            </div>
        </div>

        <!-- Products Grid (Right Column) -->
        <div class="col-lg-9">
            <!-- Active Filters Summary Banner -->
            @if (!empty($search) || !empty($categoryId) || !empty($brandId) || !empty($minPrice) || !empty($maxPrice) || !empty($inStock))
                <div class="p-3 rounded-3 mb-4 d-flex flex-wrap align-items-center justify-content-between gap-2" style="background: var(--bg-surface-subtle); border: 1px solid var(--border-default);">
                    <div class="d-flex align-items-center flex-wrap gap-2">
                        <span class="text-secondary small">Tìm thấy <strong>{{ $products->total() }}</strong> sản phẩm:</span>
                        @if (!empty($search))
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-2.5 py-1 small">
                                Từ khóa: "{{ $search }}"
                            </span>
                        @endif
                        @if (!empty($brandId))
                            @php $activeBrand = $brands->firstWhere('id', $brandId); @endphp
                            @if ($activeBrand)
                                <span class="badge bg-dark-subtle text-dark border rounded-pill px-2.5 py-1 small">
                                    👑 {{ $activeBrand->name }}
                                </span>
                            @endif
                        @endif
                        @if (!empty($categoryId))
                            @php $activeCat = $categories->firstWhere('id', $categoryId); @endphp
                            @if ($activeCat)
                                <span class="badge bg-info-subtle text-info border border-info-subtle rounded-pill px-2.5 py-1 small">
                                    📁 {{ $activeCat->name }}
                                </span>
                            @endif
                        @endif
                        @if (!empty($minPrice) || !empty($maxPrice))
                            <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2.5 py-1 small">
                                Giá: {{ $minPrice ? number_format($minPrice, 0, ',', '.') . ' ₫' : '0 ₫' }} - {{ $maxPrice ? number_format($maxPrice, 0, ',', '.') . ' ₫' : 'Tối đa' }}
                            </span>
                        @endif
                        @if (!empty($inStock))
                            <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle rounded-pill px-2.5 py-1 small">
                                Còn hàng
                            </span>
                        @endif
                    </div>
                    <a href="{{ route('shop.index') }}" class="btn btn-sm btn-surface text-danger d-inline-flex align-items-center gap-1 text-decoration-none">
                        <i data-lucide="x" style="width: 14px; height: 14px;"></i>
                        <span>Xóa bộ lọc</span>
                    </a>
                </div>
            @endif

            <div class="row g-3 g-xl-4">
                @forelse ($products as $product)
                    <div class="col-6 col-md-4 d-flex">
                        <div class="product-store-card w-100 d-flex flex-column">
                            <!-- Thumbnail with Luxury Badges -->
                            <div class="product-store-img-box">
                                <a href="{{ route('shop.show', $product) }}" class="d-block w-100 h-100">
                                    @if ($product->image)
                                        <img src="{{ $product->image }}" alt="{{ $product->name }}" class="product-store-img" loading="lazy">
                                    @else
                                        <div class="product-store-placeholder d-flex align-items-center justify-content-center">
                                            <i data-lucide="shopping-bag" style="width: 48px; height: 48px;" class="text-tertiary"></i>
                                        </div>
                                    @endif
                                </a>

                                <!-- Badges -->
                                <div class="position-absolute top-0 start-0 p-2.5 d-flex flex-column gap-1.5" style="z-index: 2;">
                                    @if ($product->has_discount)
                                        <span class="badge-discount-luxury">
                                            -{{ round((($product->price - $product->sale_price) / $product->price) * 100) }}%
                                        </span>
                                    @endif
                                    @if ($product->is_featured)
                                        <span class="badge-hot-luxury d-inline-flex align-items-center gap-1">
                                            <i data-lucide="flame" style="width: 12px; height: 12px;"></i>
                                            <span>Nổi bật</span>
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <!-- Content Area -->
                            <div class="product-store-body">
                                <div class="d-flex align-items-center gap-2 mb-1.5">
                                    @if ($product->brand)
                                        <span class="brand-pill-luxury">
                                            👑 {{ $product->brand->name }}
                                        </span>
                                    @endif
                                    <div class="product-store-category">
                                        {{ $product->category?->name ?? 'Túi xách' }}
                                    </div>
                                </div>

                                <h6 class="mb-2">
                                    <a href="{{ route('shop.show', $product) }}" class="product-store-title text-decoration-none">
                                        {{ $product->name }}
                                    </a>
                                </h6>

                                @if ($product->color)
                                    <div class="mb-3">
                                        <span class="product-chip">
                                            <span style="width: 8px; height: 8px; border-radius: 50%; background: #6366f1; display: inline-block;"></span>
                                            <span>{{ $product->color }}</span>
                                        </span>
                                    </div>
                                @endif

                                <!-- Price & Quick Action Toolbar -->
                                <div class="product-store-footer">
                                    <div>
                                        <div class="price-current-luxury">
                                            {{ $product->has_variants ? $product->formatted_price_range : ($product->has_discount ? $product->formatted_sale_price : $product->formatted_price) }}
                                        </div>
                                        @if ($product->has_discount && !$product->has_variants)
                                            <div class="price-original-luxury">
                                                {{ $product->formatted_price }}
                                            </div>
                                        @endif
                                    </div>
                                    <div class="d-flex align-items-center gap-2 flex-shrink-0">
                                        <button 
                                            type="button" 
                                            class="btn-card-action" 
                                            title="Thêm vào giỏ hàng" 
                                            onclick="openQuickAddModal({
                                                id: {{ $product->id }},
                                                name: '{{ addslashes($product->name) }}',
                                                effective_price: {{ (float) ($product->sale_price ?? $product->price) }},
                                                original_price: {{ $product->has_discount ? (float) $product->price : 'null' }},
                                                image: '{{ $product->image ?? '' }}',
                                                category_name: '{{ addslashes($product->category?->name ?? 'Túi xách') }}',
                                                stock: {{ $product->stock }}
                                            })"
                                        >
                                            <i data-lucide="shopping-bag" style="width: 16px; height: 16px;"></i>
                                        </button>
                                        <a href="{{ route('shop.show', $product) }}" class="btn-card-action" title="Xem chi tiết sản phẩm">
                                            <i data-lucide="eye" style="width: 16px; height: 16px;"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center py-5">
                        <div class="category-squircle mx-auto mb-3" style="width: 64px; height: 64px;">
                            <i data-lucide="search-x" style="width: 32px; height: 32px;"></i>
                        </div>
                        <h5 class="fw-bold text-dark mb-1">Không tìm thấy mẫu túi xách phù hợp</h5>
                        <p class="text-secondary small mb-4">Hãy thử tìm kiếm với từ khóa khác hoặc bỏ các bộ lọc đang chọn.</p>
                        <a href="{{ route('shop.index') }}" class="btn-brand-primary">
                            <i data-lucide="rotate-ccw" style="width: 16px; height: 16px; margin-right: 0.45rem;"></i>
                            <span>Xem tất cả sản phẩm</span>
                        </a>
                    </div>
                @endforelse
            </div>

            <!-- Pagination -->
            @if ($products->hasPages())
                <div class="d-flex justify-content-center mt-5">
                    {{ $products->links('pagination::bootstrap-5') }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function setPricePreset(min, max) {
        const form = document.getElementById('shopFilterForm');
        const minInput = form.querySelector('input[name="min_price"]');
        const maxInput = form.querySelector('input[name="max_price"]');
        if (minInput) minInput.value = min || '';
        if (maxInput) maxInput.value = max || '';
        form.submit();
    }
</script>
@endsection
