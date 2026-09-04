@extends('layouts.storefront')

@section('title', $product->name . ' - Chi Tiết Sản Phẩm')

@section('content')
<div class="container py-4">
    <!-- Breadcrumbs -->
    <div class="breadcrumb-modern mb-4">
        <a href="{{ route('home') }}">Trang chủ</a>
        <i data-lucide="chevron-right" style="width: 14px; height: 14px;"></i>
        <a href="{{ route('shop.index') }}">Bộ sưu tập</a>
        @if ($product->category)
            <i data-lucide="chevron-right" style="width: 14px; height: 14px;"></i>
            <a href="{{ route('shop.index', ['category_id' => $product->category_id]) }}">{{ $product->category->name }}</a>
        @endif
        @if ($product->brand)
            <i data-lucide="chevron-right" style="width: 14px; height: 14px;"></i>
            <a href="{{ route('shop.index', ['brand_id' => $product->brand_id]) }}">{{ $product->brand->name }}</a>
        @endif
        <i data-lucide="chevron-right" style="width: 14px; height: 14px;"></i>
        <span class="text-primary fw-medium text-truncate" style="max-width: 250px;">{{ $product->name }}</span>
    </div>

    <!-- Product Main Details Grid -->
    <div class="card-modern p-4 p-lg-5 mb-5">
        <div class="row g-5">
            <!-- Left: Product Image & Gallery -->
            <div class="col-lg-5">
                <div class="product-gallery-main rounded-4 overflow-hidden border shadow-sm position-relative">
                    @if ($product->image)
                        <img id="mainProductImg" src="{{ $product->image }}" alt="{{ $product->name }}" class="img-fluid w-100 object-fit-cover" style="max-height: 440px; min-height: 320px; transition: opacity 0.2s ease;">
                    @else
                        <div class="d-flex align-items-center justify-content-center" style="height: 360px; background: var(--bg-surface-subtle);" id="mainProductPlaceholder">
                            <i data-lucide="shopping-bag" style="width: 64px; height: 64px;" class="text-tertiary"></i>
                        </div>
                    @endif

                    <span id="savingsBadge" class="badge bg-danger text-white fw-bold px-3 py-1.5 rounded-pill position-absolute top-0 start-0 m-3 {{ $product->has_discount ? '' : 'd-none' }}" style="font-size: 0.8rem;">
                        Tiết kiệm {{ number_format((float)$product->price - (float)$product->sale_price, 0, ',', '.') }} ₫
                    </span>
                </div>

                <!-- Variant Thumbnails (if has variants with custom images) -->
                @if ($product->has_variants && $product->variants->whereNotNull('image')->count() > 1)
                    <div class="d-flex align-items-center gap-2 mt-3 overflow-x-auto pb-1">
                        @foreach ($product->variants->whereNotNull('image')->unique('image') as $imgVar)
                            <div class="rounded-3 border p-0.5 cursor-pointer variant-thumb {{ $loop->first ? 'active-thumb' : '' }}" 
                                 onclick="switchMainImage('{{ $imgVar->image }}', this)" 
                                 style="width: 54px; height: 54px; flex-shrink: 0;">
                                <img src="{{ $imgVar->image }}" alt="" class="w-100 h-100 object-fit-cover rounded-2">
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Right: Product Information & Purchase CTAs -->
            <div class="col-lg-7">
                <!-- Badges Header -->
                <div class="d-flex flex-wrap align-items-center gap-2 mb-2.5">
                    @if ($product->brand)
                        <a href="{{ route('shop.index', ['brand_id' => $product->brand_id]) }}" class="badge bg-dark text-white border-0 px-3 py-1.5 rounded-pill text-decoration-none hover-scale" style="font-size: 0.78rem;">
                            👑 Thương hiệu: {{ $product->brand->name }}
                        </a>
                    @endif
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-1.5 rounded-pill" style="font-size: 0.78rem;">
                        {{ $product->category?->name ?? 'Túi xách' }}
                    </span>
                    @if ($product->is_featured)
                        <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-2.5 py-1.5 rounded-pill" style="font-size: 0.75rem;">
                            ⭐ Nổi bật
                        </span>
                    @endif
                    <span class="badge bg-success-subtle text-success border border-success-subtle px-2.5 py-1.5 rounded-pill" style="font-size: 0.75rem;">
                        Chính hãng 100%
                    </span>
                </div>

                <h1 class="fw-extrabold text-dark mb-2" style="letter-spacing: -0.02em; font-size: 1.75rem;">
                    {{ $product->name }}
                </h1>

                <div class="d-flex align-items-center gap-3 mb-3 text-secondary small">
                    <span>Mã SKU: <strong class="text-dark font-monospace" id="displaySku">{{ $product->sku }}</strong></span>
                    <span>&bull;</span>
                    <span>Bảo hành da 12 tháng</span>
                </div>

                <!-- Price Box with Savings Tag -->
                <div class="p-3.5 rounded-3 mb-4 d-flex flex-wrap align-items-center justify-content-between gap-3" style="background: var(--bg-surface-subtle); border: 1px solid var(--border-default);">
                    <div>
                        <div class="text-secondary small mb-0.5" style="font-size: 0.75rem;">Giá niêm yết chính hãng</div>
                        <div class="d-flex align-items-baseline gap-3">
                            <span class="fs-2 fw-extrabold text-primary" id="displayPrice" style="letter-spacing: -0.02em;">
                                {{ $product->has_variants ? $product->formatted_price_range : ($product->has_discount ? $product->formatted_sale_price : $product->formatted_price) }}
                            </span>
                            <span class="text-muted text-decoration-line-through fs-6 {{ $product->has_discount ? '' : 'd-none' }}" id="displayOriginalPrice">
                                {{ $product->formatted_price }}
                            </span>
                        </div>
                    </div>

                    <div id="priceDiscountTag" class="badge bg-danger-subtle text-danger border border-danger-subtle px-3 py-1.5 rounded-pill fw-bold {{ $product->has_discount ? '' : 'd-none' }}" style="font-size: 0.8rem;">
                        Giá ưu đãi đặc biệt
                    </div>
                </div>

                <!-- SMART VARIANT SELECTORS (Shopee / Lazada Style) -->
                @if ($product->has_variants && $product->attributes->isNotEmpty())
                    <div class="variants-selection-wrapper p-3.5 rounded-3 mb-4 border bg-white shadow-sm">
                        <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom">
                            <span class="fw-bold text-dark small text-uppercase d-flex align-items-center gap-1.5">
                                <i data-lucide="layers" class="text-primary" style="width: 16px; height: 16px;"></i>
                                <span>Lựa chọn phân loại sản phẩm:</span>
                            </span>
                            <span class="text-secondary small" id="selectedVariantSummary">
                                Đang chọn: <span class="fw-bold text-primary" id="selectedVariantTitleText">---</span>
                            </span>
                        </div>

                        <div class="d-flex flex-column gap-3">
                            @foreach ($product->attributes as $attrGroup)
                                <div class="variant-attribute-group" data-group-name="{{ $attrGroup->name }}">
                                    <div class="d-flex align-items-center gap-2 mb-2">
                                        <span class="text-secondary small fw-semibold" style="min-width: 75px;">{{ $attrGroup->name }}:</span>
                                        <span class="fw-bold text-dark small current-group-val" id="group-val-{{ $loop->index }}"></span>
                                    </div>
                                    <div class="d-flex flex-wrap align-items-center gap-2">
                                        @foreach ($attrGroup->values as $val)
                                            <button 
                                                type="button" 
                                                class="btn variant-option-pill {{ $loop->first ? 'active' : '' }}" 
                                                data-group-index="{{ $loop->parent->index }}"
                                                data-group-name="{{ $attrGroup->name }}"
                                                data-value="{{ $val->value }}"
                                                onclick="selectVariantOption({{ $loop->parent->index }}, '{{ addslashes($val->value) }}', this)"
                                            >
                                                <i data-lucide="check" class="check-icon" style="width: 13px; height: 13px;"></i>
                                                <span>{{ $val->value }}</span>
                                            </button>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Specs Breakdown (Luxury Metric Cards) -->
                <div class="row g-2.5 mb-4">
                    <div class="col-4">
                        <div class="p-2.5 rounded-3 h-100" style="background: var(--bg-surface-subtle); border: 1px solid var(--border-default);">
                            <div class="d-flex align-items-center gap-1.5 text-secondary mb-1" style="font-size: 0.72rem;">
                                <i data-lucide="layers" class="text-primary" style="width: 14px; height: 14px;"></i>
                                <span>Chất liệu</span>
                            </div>
                            <div class="fw-bold text-dark small text-truncate" id="specsMaterial" title="{{ $product->material ?? 'Da cao cấp' }}">{{ $product->material ?? 'Da cao cấp' }}</div>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="p-2.5 rounded-3 h-100" style="background: var(--bg-surface-subtle); border: 1px solid var(--border-default);">
                            <div class="d-flex align-items-center gap-1.5 text-secondary mb-1" style="font-size: 0.72rem;">
                                <i data-lucide="palette" class="text-primary" style="width: 14px; height: 14px;"></i>
                                <span>Màu sắc</span>
                            </div>
                            <div class="fw-bold text-dark small text-truncate" id="specsColor" title="{{ $product->color ?? 'Tiêu chuẩn' }}">{{ $product->color ?? 'Tiêu chuẩn' }}</div>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="p-2.5 rounded-3 h-100" style="background: var(--bg-surface-subtle); border: 1px solid var(--border-default);">
                            <div class="d-flex align-items-center gap-1.5 text-secondary mb-1" style="font-size: 0.72rem;">
                                <i data-lucide="ruler" class="text-primary" style="width: 14px; height: 14px;"></i>
                                <span>Kích thước</span>
                            </div>
                            <div class="fw-bold text-dark small text-truncate" title="{{ $product->dimensions ?? 'Chuẩn thời trang' }}">{{ $product->dimensions ?? 'Chuẩn thời trang' }}</div>
                        </div>
                    </div>
                </div>

                <!-- Stock Status -->
                <div class="d-flex align-items-center gap-2 mb-3.5">
                    <span class="status-pulse-dot" id="stockStatusDot"></span>
                    <span class="small fw-semibold" id="stockStatusText">
                        Đang kiểm tra tồn kho...
                    </span>
                </div>

                <!-- Quantity Stepper & CTAs Container -->
                <div id="purchaseActionContainer">
                    <div class="d-flex align-items-center gap-3 mb-4">
                        <span class="fw-semibold text-dark small">Số lượng mua:</span>
                        <div class="qty-stepper d-inline-flex align-items-center rounded-3 border">
                            <button type="button" class="btn btn-stepper px-3 py-2" onclick="adjustDetailQty(-1)" aria-label="Giảm">
                                <i data-lucide="minus" style="width: 14px; height: 14px;"></i>
                            </button>
                            <input 
                                type="number" 
                                id="detailQuantityInput" 
                                value="1" 
                                min="1" 
                                max="{{ $product->stock }}"
                                class="form-control text-center border-0 qty-input"
                                style="width: 55px; font-weight: 700; background: transparent;"
                                onchange="validateDetailQty(this)"
                            >
                            <button type="button" class="btn btn-stepper px-3 py-2" onclick="adjustDetailQty(1)" aria-label="Tăng">
                                <i data-lucide="plus" style="width: 14px; height: 14px;"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Action CTAs: Add to Cart & Buy Now -->
                    <div class="d-flex flex-wrap gap-3 mb-4">
                        <button 
                            type="button" 
                            id="detailAddToCartBtn" 
                            class="btn-surface py-2.5 px-4 fs-6 fw-semibold d-inline-flex align-items-center justify-content-center text-primary"
                            style="border-color: var(--brand-400) !important;"
                            onclick="handleDetailAddToCart(false)"
                        >
                            <i data-lucide="shopping-bag" style="width: 18px; height: 18px; margin-right: 0.5rem;"></i>
                            <span>Thêm Vào Giỏ Hàng</span>
                        </button>

                        <button 
                            type="button" 
                            id="detailBuyNowBtn" 
                            class="btn-brand-primary py-2.5 px-4 fs-6 fw-semibold d-inline-flex align-items-center justify-content-center shadow-sm"
                            onclick="handleDetailAddToCart(true)"
                        >
                            <i data-lucide="zap" style="width: 18px; height: 18px; margin-right: 0.5rem;"></i>
                            <span>Mua Ngay</span>
                        </button>
                    </div>
                </div>

                <div id="outOfStockNotice" class="alert alert-danger py-2.5 px-3.5 mb-4 small d-none">
                    Phân loại sản phẩm này tạm thời đã hết hàng. Quý khách vui lòng chọn phân loại khác hoặc liên hệ Hotline để đặt trước.
                </div>

                <!-- Guarantees List -->
                <div class="pt-3 border-top d-flex flex-wrap gap-3 text-secondary small">
                    <div class="d-inline-flex align-items-center gap-2 p-2 rounded-2" style="background: var(--bg-surface-subtle);">
                        <i data-lucide="truck" class="text-primary flex-shrink-0" style="width: 16px; height: 16px;"></i>
                        <span>Giao hàng toàn quốc</span>
                    </div>
                    <div class="d-inline-flex align-items-center gap-2 p-2 rounded-2" style="background: var(--bg-surface-subtle);">
                        <i data-lucide="refresh-cw" class="text-primary flex-shrink-0" style="width: 16px; height: 16px;"></i>
                        <span>Đổi trả 30 ngày</span>
                    </div>
                    <div class="d-inline-flex align-items-center gap-2 p-2 rounded-2" style="background: var(--bg-surface-subtle);">
                        <i data-lucide="shield-check" class="text-primary flex-shrink-0" style="width: 16px; height: 16px;"></i>
                        <span>Bảo hành da 12 tháng</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Description Box -->
        @if ($product->description)
            <div class="mt-5 pt-4 border-top">
                <h5 class="fw-bold text-dark mb-3">Mô tả chi tiết & Đặc tính sản phẩm</h5>
                <div class="text-secondary" style="line-height: 1.8; white-space: pre-line;">
                    {{ $product->description }}
                </div>
            </div>
        @endif

        <!-- Customer Reviews Section (Shopee & Luxury Style) -->
        <div class="mt-5 pt-4 border-top" id="reviews-section">
            <div class="d-flex align-items-center justify-content-between mb-4">
                <h5 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                    <i class="bi bi-star-fill text-warning"></i>
                    ĐÁNH GIÁ SẢN PHẨM
                </h5>
                <span class="badge bg-warning-subtle text-dark border border-warning-subtle px-3 py-1.5 rounded-pill font-monospace">
                    {{ $reviewSummary['total'] ?? 0 }} đánh giá
                </span>
            </div>

            <!-- Shopee Rating Overview Board -->
            <div class="p-4 rounded-4 mb-4" style="background: #fffbf8; border: 1px solid #fbe5d8;">
                <div class="row align-items-center g-4">
                    <!-- Left Score Column -->
                    <div class="col-12 col-md-3 text-center border-md-end border-warning-subtle pe-md-4">
                        <div class="display-4 fw-bold text-danger mb-1">
                            {{ number_format($reviewSummary['avg_rating'] ?? 5.0, 1) }}
                            <span class="fs-4 text-muted fw-normal">/ 5</span>
                        </div>
                        <div class="d-flex justify-content-center gap-1 text-warning fs-5 mb-1">
                            @for($i = 1; $i <= 5; $i++)
                                @if($i <= round($reviewSummary['avg_rating'] ?? 5.0))
                                    <i class="bi bi-star-fill"></i>
                                @else
                                    <i class="bi bi-star text-muted opacity-25"></i>
                                @endif
                            @endfor
                        </div>
                        <small class="text-muted">Dựa trên {{ $reviewSummary['total'] ?? 0 }} lượt đánh giá</small>
                    </div>

                    <!-- Right Filter Pills Column (Shopee Style) -->
                    <div class="col-12 col-md-9">
                        <div class="d-flex flex-wrap gap-2" id="reviewFilterGroup">
                            <button type="button" class="btn btn-sm review-filter-btn active" data-rating="all" onclick="filterReviews('all', null, this)">
                                Tất Cả ({{ $reviewSummary['total'] ?? 0 }})
                            </button>
                            <button type="button" class="btn btn-sm review-filter-btn" data-rating="5" onclick="filterReviews(5, null, this)">
                                5 Sao ({{ $reviewSummary['star_counts'][5] ?? 0 }})
                            </button>
                            <button type="button" class="btn btn-sm review-filter-btn" data-rating="4" onclick="filterReviews(4, null, this)">
                                4 Sao ({{ $reviewSummary['star_counts'][4] ?? 0 }})
                            </button>
                            <button type="button" class="btn btn-sm review-filter-btn" data-rating="3" onclick="filterReviews(3, null, this)">
                                3 Sao ({{ $reviewSummary['star_counts'][3] ?? 0 }})
                            </button>
                            <button type="button" class="btn btn-sm review-filter-btn" data-rating="2" onclick="filterReviews(2, null, this)">
                                2 Sao ({{ $reviewSummary['star_counts'][2] ?? 0 }})
                            </button>
                            <button type="button" class="btn btn-sm review-filter-btn" data-rating="1" onclick="filterReviews(1, null, this)">
                                1 Sao ({{ $reviewSummary['star_counts'][1] ?? 0 }})
                            </button>
                            <button type="button" class="btn btn-sm review-filter-btn" data-has-images="true" onclick="filterReviews(null, true, this)">
                                Có Hình Ảnh ({{ $reviewSummary['with_images_count'] ?? 0 }})
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Reviews Container with Loading Spinner -->
            <div id="reviewsListContainer" class="position-relative">
                <div id="reviewsLoadingSpinner" class="d-none position-absolute top-0 start-0 w-100 h-100 bg-white bg-opacity-75 d-flex align-items-center justify-content-center" style="z-index: 5; min-height: 200px;">
                    <div class="spinner-border text-danger" role="status">
                        <span class="visually-hidden">Đang tải đánh giá...</span>
                    </div>
                </div>

                <div id="reviewsContent">
                    @include('storefront.partials.review-items', ['reviews' => $reviews])
                </div>

                <!-- Pagination Container -->
                <div id="reviewsPagination" class="mt-4 d-flex justify-content-center">
                    {{ $reviews->fragment('reviews-section')->links() }}
                </div>
            </div>
        </div>
    </div>

    <!-- Lightbox Image Modal -->
    <div class="modal fade" id="reviewImageModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content bg-transparent border-0 shadow-none">
                <div class="modal-body p-0 text-center position-relative">
                    <button type="button" class="btn btn-dark btn-sm rounded-circle position-absolute top-0 end-0 m-3 shadow" data-bs-dismiss="modal" style="width: 38px; height: 38px; z-index: 10;">
                        <i class="bi bi-x-lg"></i>
                    </button>
                    <img id="reviewModalImage" src="" alt="Ảnh phóng to" class="img-fluid rounded-3 shadow-lg" style="max-height: 85vh; object-fit: contain;">
                </div>
            </div>
        </div>
    </div>

    <!-- Related Products -->
    @if (isset($relatedProducts) && $relatedProducts->count() > 0)
        <div class="mb-5">
            <h4 class="fw-bold text-dark mb-4">Gợi Ý Sản Phẩm Cùng Bộ Sưu Tập</h4>
            <div class="row g-4">
                @foreach ($relatedProducts as $relProduct)
                    <div class="col-6 col-md-3">
                        <div class="product-store-card h-100 d-flex flex-column">
                            <div class="product-store-img-box position-relative">
                                <a href="{{ route('shop.show', $relProduct) }}">
                                    @if ($relProduct->image)
                                        <img src="{{ $relProduct->image }}" alt="{{ $relProduct->name }}" class="product-store-img w-100 object-fit-cover" loading="lazy">
                                    @else
                                        <div class="product-store-placeholder d-flex align-items-center justify-content-center">
                                            <i data-lucide="shopping-bag" style="width: 36px; height: 36px;" class="text-tertiary"></i>
                                        </div>
                                    @endif
                                </a>
                                @if ($relProduct->brand)
                                    <span class="badge bg-dark text-white position-absolute top-0 start-0 m-2 px-2 py-0.5 rounded-pill" style="font-size: 0.68rem;">
                                        {{ $relProduct->brand->name }}
                                    </span>
                                @endif
                            </div>
                            <div class="product-store-content p-3 d-flex flex-column flex-grow-1">
                                <h6 class="fw-bold mb-1">
                                    <a href="{{ route('shop.show', $relProduct) }}" class="product-store-title text-decoration-none">
                                        {{ $relProduct->name }}
                                    </a>
                                </h6>
                                <div class="mt-auto pt-2 border-top fw-bold text-primary">
                                    {{ $relProduct->formatted_price_range }}
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
@endsection

@section('styles')
<style>
    /* Shopee Review Filter Buttons */
    .review-filter-btn {
        background: #ffffff;
        border: 1px solid #dee2e6;
        color: #495057;
        font-weight: 500;
        padding: 0.4rem 0.85rem;
        border-radius: 6px;
        transition: all 0.2s ease;
    }
    .review-filter-btn:hover {
        border-color: #dc3545;
        color: #dc3545;
        background: #fff8f8;
    }
    .review-filter-btn.active {
        background: #dc3545 !important;
        border-color: #dc3545 !important;
        color: #ffffff !important;
        box-shadow: 0 2px 6px rgba(220, 53, 69, 0.25);
    }
    .review-img-wrapper:hover {
        transform: scale(1.05);
        box-shadow: 0 4px 10px rgba(0,0,0,0.15);
    }
    .review-img-wrapper:hover .overlay-zoom {
        opacity: 1 !important;
    }

    /* Variant Option Pills (Shopee / Lazada Style) */
    .variant-option-pill {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        padding: 0.45rem 1rem;
        border-radius: 8px;
        font-size: 0.86rem;
        font-weight: 600;
        color: var(--text-primary);
        background: #ffffff;
        border: 1.5px solid var(--border-default);
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .variant-option-pill .check-icon {
        display: none;
    }

    .variant-option-pill:hover {
        border-color: var(--brand-500);
        color: var(--brand-600);
        background: var(--brand-50);
    }

    .variant-option-pill.active {
        border-color: var(--brand-500) !important;
        color: var(--brand-700) !important;
        background: rgba(224, 86, 56, 0.08) !important;
        box-shadow: 0 0 0 1px var(--brand-500);
    }

    .variant-option-pill.active .check-icon {
        display: inline-block;
        color: var(--brand-600);
    }

    .variant-thumb.active-thumb {
        border-color: var(--brand-500) !important;
        box-shadow: 0 0 0 2px var(--brand-400);
    }
</style>
@endsection

@section('scripts')
<script>
    const hasVariants = {{ $product->has_variants ? 'true' : 'false' }};
    const productVariants = @json($product->variants);
    const productAttributes = @json($product->attributes);

    let selectedOptions = {};
    let currentVariant = null;
    let currentMaxStock = {{ (int) $product->stock }};

    function formatVND(amount) {
        return new Intl.NumberFormat('vi-VN').format(amount) + ' ₫';
    }

    function switchMainImage(url, el) {
        const img = document.getElementById('mainProductImg');
        if (img && url) {
            img.style.opacity = '0.4';
            setTimeout(() => {
                img.src = url;
                img.style.opacity = '1';
            }, 120);
        }
        document.querySelectorAll('.variant-thumb').forEach(t => t.classList.remove('active-thumb'));
        if (el) el.classList.add('active-thumb');
    }

    function selectVariantOption(groupIndex, value, buttonEl) {
        // Update selected option for this group
        selectedOptions[groupIndex] = value;

        // Update active class on siblings
        const parent = buttonEl.closest('.variant-attribute-group');
        if (parent) {
            parent.querySelectorAll('.variant-option-pill').forEach(btn => btn.classList.remove('active'));
            buttonEl.classList.add('active');

            const valDisplay = parent.querySelector('.current-group-val');
            if (valDisplay) valDisplay.textContent = value;
        }

        matchVariant();
    }

    function matchVariant() {
        if (!hasVariants || productVariants.length === 0) {
            updateStockUI({{ (int) $product->stock }});
            return;
        }

        // Selected options array in order of groupIndex
        const selectedValues = Object.keys(selectedOptions)
            .sort((a, b) => a - b)
            .map(k => selectedOptions[k]);

        // Find variant matching all selected options
        currentVariant = productVariants.find(v => {
            const matchesOpt1 = !selectedValues[0] || v.option1_value === selectedValues[0];
            const matchesOpt2 = !selectedValues[1] || v.option2_value === selectedValues[1];
            const matchesOpt3 = !selectedValues[2] || v.option3_value === selectedValues[2];
            return matchesOpt1 && matchesOpt2 && matchesOpt3;
        });

        const summaryText = document.getElementById('selectedVariantTitleText');
        const priceEl = document.getElementById('displayPrice');
        const origPriceEl = document.getElementById('displayOriginalPrice');
        const savingsBadge = document.getElementById('savingsBadge');
        const priceDiscountTag = document.getElementById('priceDiscountTag');
        const skuEl = document.getElementById('displaySku');

        if (currentVariant) {
            if (summaryText) summaryText.textContent = currentVariant.variant_title;
            if (skuEl && currentVariant.sku) skuEl.textContent = currentVariant.sku;

            const effectivePrice = currentVariant.sale_price !== null && parseFloat(currentVariant.sale_price) < parseFloat(currentVariant.price)
                ? parseFloat(currentVariant.sale_price)
                : parseFloat(currentVariant.price);

            if (priceEl) priceEl.textContent = formatVND(effectivePrice);

            if (currentVariant.sale_price !== null && parseFloat(currentVariant.sale_price) < parseFloat(currentVariant.price)) {
                if (origPriceEl) {
                    origPriceEl.textContent = formatVND(currentVariant.price);
                    origPriceEl.classList.remove('d-none');
                }
                if (priceDiscountTag) priceDiscountTag.classList.remove('d-none');
                if (savingsBadge) {
                    savingsBadge.textContent = `Tiết kiệm ` + formatVND(parseFloat(currentVariant.price) - parseFloat(currentVariant.sale_price));
                    savingsBadge.classList.remove('d-none');
                }
            } else {
                if (origPriceEl) origPriceEl.classList.add('d-none');
                if (priceDiscountTag) priceDiscountTag.classList.add('d-none');
                if (savingsBadge) savingsBadge.classList.add('d-none');
            }

            // Switch image if variant has its own image
            if (currentVariant.image) {
                switchMainImage(currentVariant.image, null);
            }

            // Update Stock
            currentMaxStock = parseInt(currentVariant.stock) || 0;
            updateStockUI(currentMaxStock);
        } else {
            if (summaryText) summaryText.textContent = 'Chưa khớp biến thể';
        }
    }

    function updateStockUI(stock) {
        const dot = document.getElementById('stockStatusDot');
        const text = document.getElementById('stockStatusText');
        const actionsContainer = document.getElementById('purchaseActionContainer');
        const outOfStockNotice = document.getElementById('outOfStockNotice');
        const qtyInput = document.getElementById('detailQuantityInput');

        if (stock > 0) {
            if (dot) dot.className = 'status-pulse-dot';
            if (text) {
                text.className = 'small fw-semibold text-success';
                text.textContent = stock > 10 ? `Còn hàng (${stock} chiếc có sẵn tại kho)` : `Chỉ còn ${stock} chiếc cuối cùng!`;
            }
            if (actionsContainer) actionsContainer.classList.remove('d-none');
            if (outOfStockNotice) outOfStockNotice.classList.add('d-none');
            if (qtyInput) {
                qtyInput.max = stock;
                if (parseInt(qtyInput.value) > stock) qtyInput.value = stock;
            }
        } else {
            if (dot) dot.className = 'status-pulse-dot bg-danger';
            if (text) {
                text.className = 'small fw-semibold text-danger';
                text.textContent = 'Phân loại này hiện đã tạm hết hàng';
            }
            if (actionsContainer) actionsContainer.classList.add('d-none');
            if (outOfStockNotice) outOfStockNotice.classList.remove('d-none');
        }
    }

    function adjustDetailQty(delta) {
        const input = document.getElementById('detailQuantityInput');
        if (!input) return;
        let val = parseInt(input.value) || 1;
        let next = val + delta;
        if (next < 1) next = 1;
        if (next > currentMaxStock) {
            next = currentMaxStock;
            if (window.showToast) window.showToast(`Chỉ còn ${currentMaxStock} sản phẩm trong kho.`, 'warning');
        }
        input.value = next;
    }

    function validateDetailQty(input) {
        let val = parseInt(input.value) || 1;
        if (val < 1) val = 1;
        if (val > currentMaxStock) {
            val = currentMaxStock;
            if (window.showToast) window.showToast(`Chỉ còn ${currentMaxStock} sản phẩm trong kho.`, 'warning');
        }
        input.value = val;
    }

    async function handleDetailAddToCart(redirectNow = false) {
        const input = document.getElementById('detailQuantityInput');
        const qty = input ? parseInt(input.value) || 1 : 1;
        const addBtn = document.getElementById('detailAddToCartBtn');
        const buyBtn = document.getElementById('detailBuyNowBtn');

        if (hasVariants && !currentVariant) {
            if (window.showToast) window.showToast('Vui lòng chọn đầy đủ các phân loại sản phẩm.', 'warning');
            return;
        }

        if (currentMaxStock <= 0) {
            if (window.showToast) window.showToast('Phân loại hàng này đã hết.', 'error');
            return;
        }

        const originalAddHtml = addBtn ? addBtn.innerHTML : '';
        const originalBuyHtml = buyBtn ? buyBtn.innerHTML : '';

        if (redirectNow && buyBtn) {
            buyBtn.disabled = true;
            buyBtn.innerHTML = `<span class="spinner-border spinner-border-sm me-1.5"></span> Đang xử lý...`;
        } else if (addBtn) {
            addBtn.disabled = true;
            addBtn.innerHTML = `<span class="spinner-border spinner-border-sm me-1.5"></span> Đang thêm...`;
        }

        try {
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            const payload = {
                product_id: {{ $product->id }},
                quantity: qty
            };

            if (currentVariant) {
                payload.product_variant_id = currentVariant.id;
            }

            const res = await fetch('{{ route("cart.add") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify(payload)
            });

            const result = await res.json();

            if (res.ok && result.success) {
                if (typeof updateCartBadge === 'function') {
                    updateCartBadge(result.cart_count);
                }

                if (redirectNow) {
                    window.location.href = '{{ route("cart.index") }}';
                } else {
                    if (window.showToast) {
                        window.showToast(result.message || 'Đã thêm vào giỏ hàng thành công!', 'success');
                    }
                }
            } else {
                if (window.showToast) {
                    window.showToast(result.message || 'Không thể thêm vào giỏ hàng.', 'error');
                } else {
                    alert(result.message || 'Lỗi khi thêm vào giỏ hàng.');
                }
            }
        } catch (e) {
            alert('Lỗi kết nối máy chủ. Vui lòng thử lại.');
        } finally {
            if (addBtn) {
                addBtn.disabled = false;
                addBtn.innerHTML = originalAddHtml;
            }
            if (buyBtn) {
                buyBtn.disabled = false;
                buyBtn.innerHTML = originalBuyHtml;
            }
            if (typeof lucide !== 'undefined') lucide.createIcons();
        }
    }

    // Review Lightbox Modal
    function showReviewImage(url) {
        const modalImg = document.getElementById('reviewModalImage');
        if (modalImg) {
            modalImg.src = url;
            const modal = new bootstrap.Modal(document.getElementById('reviewImageModal'));
            modal.show();
        }
    }

    // Review AJAX Filter
    let currentReviewRating = null;
    let currentReviewHasImages = null;

    async function filterReviews(rating, hasImages, clickedBtn) {
        if (clickedBtn) {
            document.querySelectorAll('#reviewFilterGroup .review-filter-btn').forEach(btn => btn.classList.remove('active'));
            clickedBtn.classList.add('active');
        }

        currentReviewRating = rating === 'all' ? null : rating;
        currentReviewHasImages = hasImages;

        const spinner = document.getElementById('reviewsLoadingSpinner');
        const content = document.getElementById('reviewsContent');
        const pagination = document.getElementById('reviewsPagination');

        if (spinner) spinner.classList.remove('d-none');

        const params = new URLSearchParams();
        if (currentReviewRating) params.append('rating', currentReviewRating);
        if (currentReviewHasImages) params.append('has_images', '1');

        try {
            const res = await fetch(`{{ route('products.reviews.filter', $product) }}?${params.toString()}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });
            const data = await res.json();
            if (data.success) {
                if (content) content.innerHTML = data.html;
                if (pagination) pagination.innerHTML = data.pagination;
                if (typeof lucide !== 'undefined') lucide.createIcons();
            }
        } catch (err) {
            console.error('Lỗi tải đánh giá:', err);
        } finally {
            if (spinner) spinner.classList.add('d-none');
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        // Init first options for each attribute group
        if (hasVariants && productAttributes.length > 0) {
            productAttributes.forEach((group, idx) => {
                if (group.values && group.values.length > 0) {
                    selectedOptions[idx] = group.values[0].value;
                    const display = document.getElementById(`group-val-${idx}`);
                    if (display) display.textContent = group.values[0].value;
                }
            });
            matchVariant();
        } else {
            updateStockUI({{ (int) $product->stock }});
        }
    });
</script>
@endsection
