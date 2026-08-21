@extends('layouts.storefront')

@section('title', 'Bộ Sưu Tập Túi Xách Nữ - Cửa Hàng')

@section('content')
<div class="container py-4">
    <!-- Breadcrumb -->
    <div class="breadcrumb-modern mb-3">
        <a href="{{ route('home') }}">Trang chủ</a>
        <i data-lucide="chevron-right" style="width: 14px; height: 14px;"></i>
        <span class="text-primary fw-medium">Bộ sưu tập túi xách</span>
    </div>

    <!-- Page Title & Header -->
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h2 class="fw-bold text-dark mb-1" style="letter-spacing: -0.02em;">Bộ Sưu Tập Túi Xách Nữ</h2>
            <p class="text-secondary small mb-0">
                Tìm thấy <strong>{{ $products->total() }}</strong> mẫu túi xách cao cấp đang mở bán
            </p>
        </div>
    </div>

    <div class="row g-4">
        <!-- Sidebar Filter (Left Column) -->
        <div class="col-lg-3">
            <div class="card-modern p-3.5 sticky-top" style="top: 80px;">
                <h6 class="fw-bold text-dark mb-3 d-flex align-items-center gap-2">
                    <i data-lucide="sliders-horizontal" class="text-primary" style="width: 16px; height: 16px;"></i>
                    <span>Bộ lọc tìm kiếm</span>
                </h6>

                <form method="GET" action="{{ route('shop.index') }}" id="shopFilterForm">
                    <!-- Search Input -->
                    <div class="mb-3.5">
                        <label class="form-label-modern mb-1.5 small fw-semibold">Từ khóa</label>
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
                        <select name="category_id" class="form-select form-select-modern" onchange="this.form.submit()">
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
                        <select name="sort" class="form-select form-select-modern" onchange="this.form.submit()">
                            <option value="created_desc" {{ (isset($sort) && $sort == 'created_desc') ? 'selected' : '' }}>Mới nhất trước</option>
                            <option value="price_asc" {{ (isset($sort) && $sort == 'price_asc') ? 'selected' : '' }}>Giá: Thấp &rarr; Cao</option>
                            <option value="price_desc" {{ (isset($sort) && $sort == 'price_desc') ? 'selected' : '' }}>Giá: Cao &rarr; Thấp</option>
                            <option value="name_asc" {{ (isset($sort) && $sort == 'name_asc') ? 'selected' : '' }}>Tên: A &rarr; Z</option>
                        </select>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn-brand-primary w-100 py-2 justify-content-center">
                            <span>Áp dụng</span>
                        </button>
                        @if ($search || $categoryId || ($sort && $sort != 'created_desc'))
                            <a href="{{ route('shop.index') }}" class="btn btn-surface py-2 px-3" title="Đặt lại bộ lọc">
                                <i data-lucide="rotate-ccw" style="width: 15px; height: 15px;"></i>
                            </a>
                        @endif
                    </div>
                </form>
            </div>
        </div>

        <!-- Products Grid (Right Column) -->
        <div class="col-lg-9">
            <div class="row g-3">
                @forelse ($products as $product)
                    <div class="col-6 col-md-4">
                        <div class="product-store-card h-100 d-flex flex-column">
                            <!-- Thumbnail -->
                            <div class="product-store-img-box position-relative">
                                <a href="{{ route('shop.show', $product) }}">
                                    @if ($product->image)
                                        <img src="{{ $product->image }}" alt="{{ $product->name }}" class="product-store-img w-100 object-fit-cover" loading="lazy">
                                    @else
                                        <div class="product-store-placeholder d-flex align-items-center justify-content-center">
                                            <i data-lucide="shopping-bag" style="width: 48px; height: 48px;" class="text-tertiary"></i>
                                        </div>
                                    @endif
                                </a>

                                <!-- Badges -->
                                <div class="position-absolute top-0 start-0 p-2.5 d-flex flex-column gap-1">
                                    @if ($product->has_discount)
                                        <span class="badge bg-danger text-white fw-bold px-2 py-0.5 rounded-pill" style="font-size: 0.68rem;">
                                            -{{ round((($product->price - $product->sale_price) / $product->price) * 100) }}%
                                        </span>
                                    @endif
                                    @if ($product->is_featured)
                                        <span class="badge bg-warning text-dark fw-bold px-2 py-0.5 rounded-pill" style="font-size: 0.68rem;">
                                            ⭐ Hot
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <!-- Content -->
                            <div class="product-store-content p-3 d-flex flex-column flex-grow-1">
                                <div class="text-tertiary small mb-1" style="font-size: 0.75rem;">
                                    {{ $product->category?->name ?? 'Túi xách' }}
                                </div>
                                <h6 class="fw-bold mb-2">
                                    <a href="{{ route('shop.show', $product) }}" class="product-store-title text-decoration-none">
                                        {{ $product->name }}
                                    </a>
                                </h6>

                                @if ($product->color || $product->material)
                                    <div class="text-secondary small mb-2 d-flex flex-wrap gap-1" style="font-size: 0.75rem;">
                                        @if ($product->color)
                                            <span>Màu: <strong>{{ $product->color }}</strong></span>
                                        @endif
                                    </div>
                                @endif

                                <div class="mt-auto pt-2 border-top d-flex align-items-baseline justify-content-between">
                                    <div>
                                        <div class="fw-bold text-primary" style="font-size: 1.05rem;">
                                            {{ $product->has_discount ? $product->formatted_sale_price : $product->formatted_price }}
                                        </div>
                                        @if ($product->has_discount)
                                            <div class="text-muted text-decoration-line-through small" style="font-size: 0.78rem;">
                                                {{ $product->formatted_price }}
                                            </div>
                                        @endif
                                    </div>
                                    <a href="{{ route('shop.show', $product) }}" class="btn btn-sm btn-surface p-1.5" title="Xem chi tiết">
                                        <i data-lucide="eye" style="width: 16px; height: 16px;"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center py-5">
                        <div class="category-squircle mx-auto mb-3" style="width: 60px; height: 60px;">
                            <i data-lucide="search-x" style="width: 30px; height: 30px;"></i>
                        </div>
                        <h5 class="fw-bold text-dark mb-1">Không tìm thấy túi xách phù hợp</h5>
                        <p class="text-secondary small mb-4">Hãy thử tìm kiếm với từ khóa khác hoặc xóa bộ lọc hiện tại.</p>
                        <a href="{{ route('shop.index') }}" class="btn-surface">
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
