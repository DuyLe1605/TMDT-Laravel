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

                    <!-- Category Filter -->
                    <div class="mb-3.5">
                        <label class="form-label-modern mb-1.5 small fw-semibold">Dòng túi xách</label>
                        <select name="category_id" class="form-select form-select-modern w-100" onchange="this.form.submit()">
                            <option value="">Tất cả dòng túi</option>
                            @foreach ($categories as $cat)
                                <option value="{{ $cat->id }}" {{ (isset($categoryId) && $categoryId == $cat->id) ? 'selected' : '' }}>
                                    {{ $cat->name }} ({{ $cat->products_count ?? $cat->products()->count() }})
                                </option>
                            @endforeach
                        </select>
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
                        @if ($search || $categoryId || ($sort && $sort != 'created_desc'))
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
            <div class="row g-3 g-xl-4">
                @forelse ($products as $product)
                    <div class="col-6 col-md-4">
                        <div class="product-store-card h-100 d-flex flex-column">
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
                            <div class="p-3.5 d-flex flex-column flex-grow-1">
                                <div class="product-store-category mb-1">
                                    {{ $product->category?->name ?? 'Túi xách cao cấp' }}
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
                                <div class="mt-auto pt-3 border-top d-flex align-items-center justify-content-between gap-2">
                                    <div>
                                        <div class="price-current-luxury">
                                            {{ $product->has_discount ? $product->formatted_sale_price : $product->formatted_price }}
                                        </div>
                                        @if ($product->has_discount)
                                            <div class="price-original-luxury">
                                                {{ $product->formatted_price }}
                                            </div>
                                        @endif
                                    </div>
                                    <a href="{{ route('shop.show', $product) }}" class="btn-card-action flex-shrink-0" title="Xem chi tiết sản phẩm">
                                        <i data-lucide="eye" style="width: 17px; height: 17px;"></i>
                                    </a>
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
