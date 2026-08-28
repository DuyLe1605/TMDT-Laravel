@extends('layouts.storefront')

@section('title', 'Trang Chủ - Cửa Hàng Túi Xách Nữ Thời Trang')

@section('content')
<!-- Hero Section -->
<section class="storefront-hero py-5">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-lg-6">
                <div class="d-inline-flex align-items-center gap-2 px-3 py-1.5 rounded-pill mb-3" style="background: var(--brand-50); border: 1px solid var(--brand-200);">
                    <i data-lucide="sparkles" class="text-primary" style="width: 16px; height: 16px;"></i>
                    <span class="fw-semibold text-primary small">Bộ Sưu Tập Mới 2026</span>
                </div>
                <h1 class="display-5 fw-extrabold text-dark mb-3" style="letter-spacing: -0.03em; line-height: 1.15;">
                    Tôn Vinh Đẳng Cấp & Vẻ Đẹp Phái Nữ Với <span class="text-primary">Aurelia Bags</span>
                </h1>
                <p class="text-secondary fs-6 mb-4" style="line-height: 1.7; max-width: 520px;">
                    Khám phá những mẫu túi xách nữ thiết kế tinh xảo, chất liệu da cao cấp dập vân sang trọng, mang lại sự tự tin và thời thượng cho mọi sự kiện.
                </p>
                <div class="d-flex flex-wrap align-items-center gap-3">
                    <a href="{{ route('shop.index') }}" class="btn-brand-primary py-2.5 px-4 fs-6">
                        <i data-lucide="shopping-bag" style="width: 18px; height: 18px; margin-right: 0.5rem;"></i>
                        <span>Xem Bộ Sưu Tập Ngay</span>
                    </a>
                    <a href="#categoriesSection" class="btn-surface py-2.5 px-4 fs-6 text-decoration-none">
                        <span>Dòng Túi Hot</span>
                    </a>
                </div>

                <!-- Trust Metrics -->
                <div class="row g-3 mt-4 pt-3 border-top">
                    <div class="col-4">
                        <div class="fw-bold text-dark fs-4">100%</div>
                        <div class="text-secondary small" style="font-size: 0.78rem;">Da Tuyển Chọn</div>
                    </div>
                    <div class="col-4">
                        <div class="fw-bold text-dark fs-4">500+</div>
                        <div class="text-secondary small" style="font-size: 0.78rem;">Khách Hài Lòng</div>
                    </div>
                    <div class="col-4">
                        <div class="fw-bold text-dark fs-4">30 Ngày</div>
                        <div class="text-secondary small" style="font-size: 0.78rem;">Đổi Trả Dễ Dàng</div>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="hero-visual-card position-relative">
                    <div class="hero-img-wrapper rounded-4 overflow-hidden shadow-lg border">
                        <img 
                            src="https://images.unsplash.com/photo-1584917865442-de89df76afd3?auto=format&fit=crop&w=900&q=80" 
                            alt="Aurelia Women Luxury Handbags" 
                            class="img-fluid w-100 object-fit-cover" 
                            style="min-height: 380px; max-height: 460px;"
                        >
                    </div>
                    <div class="hero-floating-badge p-3 rounded-3 shadow-sm border">
                        <div class="d-flex align-items-center gap-2.5">
                            <div class="badge-icon-circle bg-success-subtle text-success">
                                <i data-lucide="shield-check" style="width: 20px; height: 20px;"></i>
                            </div>
                            <div>
                                <div class="fw-bold text-dark small">Bảo Hành Chính Hãng</div>
                                <div class="text-secondary" style="font-size: 0.72rem;">Miễn phí bảo dưỡng da 12 tháng</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Categories Showcase Section -->
<section class="py-5" id="categoriesSection" style="background: var(--bg-surface-subtle);">
    <div class="container">
        <div class="d-flex flex-wrap justify-content-between align-items-end mb-4">
            <div>
                <span class="text-primary fw-semibold small text-uppercase tracking-wider">Danh Mục Sản Phẩm</span>
                <h2 class="fw-bold text-dark mb-0 mt-1" style="letter-spacing: -0.02em;">Dòng Túi Xách Nổi Bật</h2>
            </div>
            <a href="{{ route('shop.index') }}" class="text-primary fw-semibold small text-decoration-none d-inline-flex align-items-center gap-1">
                <span>Xem tất cả</span>
                <i data-lucide="arrow-right" style="width: 15px; height: 15px;"></i>
            </a>
        </div>

        <div class="row g-3">
            @forelse ($categories as $category)
                <div class="col-6 col-md-4 col-lg-3">
                    <a href="{{ route('shop.index', ['category_id' => $category->id]) }}" class="category-showcase-card text-decoration-none d-block">
                        <div class="d-flex align-items-center gap-3">
                            <div class="category-squircle flex-shrink-0" style="width: 46px; height: 46px;">
                                <i data-lucide="folder-tree" style="width: 20px; height: 20px;"></i>
                            </div>
                            <div class="overflow-hidden">
                                <h6 class="fw-bold text-dark mb-0 text-truncate">{{ $category->name }}</h6>
                                <span class="text-secondary small">{{ $category->products_count ?? $category->products()->count() }} sản phẩm</span>
                            </div>
                        </div>
                    </a>
                </div>
            @empty
                <div class="col-12 text-center text-muted py-4">Đang cập nhật danh mục...</div>
            @endforelse
        </div>
    </div>
</section>

<!-- Featured Products Section -->
<section class="py-5">
    <div class="container">
        <div class="d-flex flex-wrap justify-content-between align-items-end mb-4">
            <div>
                <span class="text-primary fw-semibold small text-uppercase tracking-wider">Tuyển Chọn Đặc Biệt</span>
                <h2 class="fw-bold text-dark mb-0 mt-1" style="letter-spacing: -0.02em;">Sản Phẩm Bán Chạy & Nổi Bật</h2>
            </div>
            <a href="{{ route('shop.index') }}" class="btn-surface text-decoration-none small py-1.5 px-3">
                <span>Khám phá thêm</span>
                <i data-lucide="chevron-right" style="width: 15px; height: 15px; margin-left: 0.25rem;"></i>
            </a>
        </div>

        <div class="row g-4">
            @forelse ($featuredProducts as $product)
                <div class="col-6 col-md-4 col-lg-3">
                    <div class="product-store-card h-100 d-flex flex-column">
                        <!-- Thumbnail & Badges -->
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

                            <!-- Floating Badges -->
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

                        <!-- Card Body -->
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
                                <div class="d-flex align-items-center gap-1.5 flex-shrink-0">
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
                                    <a href="{{ route('shop.show', $product) }}" class="btn-card-action flex-shrink-0" title="Xem chi tiết sản phẩm">
                                        <i data-lucide="eye" style="width: 16px; height: 16px;"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12 text-center text-muted py-5">
                    <p>Hiện chưa có sản phẩm nổi bật nào.</p>
                </div>
            @endforelse
        </div>
    </div>
</section>

<!-- Why Choose Us Banner -->
<section class="py-5" style="background: var(--bg-surface-subtle);">
    <div class="container">
        <div class="row g-4 text-center">
            <div class="col-md-3 col-6">
                <div class="p-3">
                    <div class="brand-logo-badge mx-auto mb-3" style="width: 50px; height: 50px;">
                        <i data-lucide="truck" style="width: 24px; height: 24px;"></i>
                    </div>
                    <h6 class="fw-bold text-dark mb-1">Giao Hàng Miễn Phí</h6>
                    <p class="text-secondary small mb-0">Đơn từ 500k toàn quốc</p>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="p-3">
                    <div class="brand-logo-badge mx-auto mb-3" style="width: 50px; height: 50px;">
                        <i data-lucide="refresh-cw" style="width: 24px; height: 24px;"></i>
                    </div>
                    <h6 class="fw-bold text-dark mb-1">Đổi Hàng 30 Ngày</h6>
                    <p class="text-secondary small mb-0">Đổi trả linh hoạt tận nơi</p>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="p-3">
                    <div class="brand-logo-badge mx-auto mb-3" style="width: 50px; height: 50px;">
                        <i data-lucide="shield-check" style="width: 24px; height: 24px;"></i>
                    </div>
                    <h6 class="fw-bold text-dark mb-1">100% Chất Lượng</h6>
                    <p class="text-secondary small mb-0">Chất liệu da tiêu chuẩn</p>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="p-3">
                    <div class="brand-logo-badge mx-auto mb-3" style="width: 50px; height: 50px;">
                        <i data-lucide="headphones" style="width: 24px; height: 24px;"></i>
                    </div>
                    <h6 class="fw-bold text-dark mb-1">Hỗ Trợ 24/7</h6>
                    <p class="text-secondary small mb-0">Tư vấn chọn mẫu nhiệt tình</p>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
