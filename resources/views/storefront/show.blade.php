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
                        <img src="{{ $product->image }}" alt="{{ $product->name }}" class="img-fluid w-100 object-fit-cover" style="max-height: 420px; min-height: 320px;">
                    @else
                        <div class="d-flex align-items-center justify-content-center" style="height: 360px; background: var(--bg-surface-subtle);">
                            <i data-lucide="shopping-bag" style="width: 64px; height: 64px;" class="text-tertiary"></i>
                        </div>
                    @endif

                    @if ($product->has_discount)
                        <span class="badge bg-danger text-white fw-bold px-3 py-1.5 rounded-pill position-absolute top-0 start-0 m-3" style="font-size: 0.8rem;">
                            Tiết kiệm {{ number_format((float)$product->price - (float)$product->sale_price, 0, ',', '.') }} ₫
                        </span>
                    @endif
                </div>
            </div>

            <!-- Right: Product Information & Purchase CTAs -->
            <div class="col-lg-7">
                <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-1 rounded-pill" style="font-size: 0.78rem;">
                        {{ $product->category?->name ?? 'Túi xách' }}
                    </span>
                    @if ($product->is_featured)
                        <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-2.5 py-1 rounded-pill" style="font-size: 0.75rem;">
                            ⭐ Nổi bật
                        </span>
                    @endif
                    <span class="badge bg-success-subtle text-success border border-success-subtle px-2.5 py-1 rounded-pill" style="font-size: 0.75rem;">
                        Chính hãng 100%
                    </span>
                </div>

                <h1 class="fw-extrabold text-dark mb-3" style="letter-spacing: -0.02em; font-size: 1.75rem;">
                    {{ $product->name }}
                </h1>

                <!-- Price Box -->
                <div class="d-flex align-items-baseline gap-3 p-3 rounded-3 mb-4" style="background: var(--bg-surface-subtle); border: 1px solid var(--border-default);">
                    <div class="fs-2 fw-extrabold text-primary">
                        {{ $product->has_discount ? $product->formatted_sale_price : $product->formatted_price }}
                    </div>
                    @if ($product->has_discount)
                        <div class="text-muted text-decoration-line-through fs-5">
                            {{ $product->formatted_price }}
                        </div>
                    @endif
                </div>

                <!-- Specs Breakdown -->
                <div class="row g-3 mb-4">
                    <div class="col-6 col-sm-4">
                        <div class="p-2.5 rounded-3 border" style="background: var(--bg-surface);">
                            <div class="text-secondary small" style="font-size: 0.75rem;">Chất liệu</div>
                            <div class="fw-semibold text-dark small text-truncate">{{ $product->material ?? 'Da PU cao cấp' }}</div>
                        </div>
                    </div>
                    <div class="col-6 col-sm-4">
                        <div class="p-2.5 rounded-3 border" style="background: var(--bg-surface);">
                            <div class="text-secondary small" style="font-size: 0.75rem;">Màu sắc</div>
                            <div class="fw-semibold text-dark small text-truncate">{{ $product->color ?? 'Tiêu chuẩn' }}</div>
                        </div>
                    </div>
                    <div class="col-6 col-sm-4">
                        <div class="p-2.5 rounded-3 border" style="background: var(--bg-surface);">
                            <div class="text-secondary small" style="font-size: 0.75rem;">Kích thước</div>
                            <div class="fw-semibold text-dark small text-truncate">{{ $product->dimensions ?? 'Chuẩn thời trang' }}</div>
                        </div>
                    </div>
                </div>

                <!-- Stock Status -->
                <div class="d-flex align-items-center gap-2 mb-4">
                    <span class="status-pulse-dot {{ $product->stock > 0 ? '' : 'bg-danger' }}"></span>
                    <span class="small fw-semibold {{ $product->stock > 0 ? 'text-success' : 'text-danger' }}">
                        @if ($product->stock > 10)
                            Còn hàng ({{ $product->stock }} chiếc có sẵn tại kho)
                        @elseif ($product->stock > 0)
                            Chỉ còn {{ $product->stock }} chiếc cuối cùng
                        @else
                            Tạm hết hàng
                        @endif
                    </span>
                </div>

                <!-- Action CTA -->
                <div class="d-flex flex-wrap gap-3 mb-4">
                    <a href="tel:19008888" class="btn-brand-primary py-2.5 px-4 fs-6">
                        <i data-lucide="phone-call" style="width: 18px; height: 18px; margin-right: 0.5rem;"></i>
                        <span>Đặt mua qua Hotline (1900 8888)</span>
                    </a>
                    <a href="{{ route('shop.index') }}" class="btn-surface py-2.5 px-4 text-decoration-none">
                        <span>Xem mẫu khác</span>
                    </a>
                </div>

                <!-- Guarantees List -->
                <div class="pt-3 border-top d-flex flex-wrap gap-4 text-secondary small">
                    <div class="d-flex align-items-center gap-1.5">
                        <i data-lucide="truck" class="text-primary" style="width: 16px; height: 16px;"></i>
                        <span>Giao hàng toàn quốc</span>
                    </div>
                    <div class="d-flex align-items-center gap-1.5">
                        <i data-lucide="refresh-cw" class="text-primary" style="width: 16px; height: 16px;"></i>
                        <span>Đổi trả 30 ngày</span>
                    </div>
                    <div class="d-flex align-items-center gap-1.5">
                        <i data-lucide="shield-check" class="text-primary" style="width: 16px; height: 16px;"></i>
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
    </div>

    <!-- Related Products -->
    @if (isset($relatedProducts) && $relatedProducts->count() > 0)
        <div class="mb-5">
            <h4 class="fw-bold text-dark mb-4">Sản Phẩm Cùng Dòng Túi</h4>
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
                            </div>
                            <div class="product-store-content p-3 d-flex flex-column flex-grow-1">
                                <h6 class="fw-bold mb-1">
                                    <a href="{{ route('shop.show', $relProduct) }}" class="product-store-title text-decoration-none">
                                        {{ $relProduct->name }}
                                    </a>
                                </h6>
                                <div class="mt-auto pt-2 border-top fw-bold text-primary">
                                    {{ $relProduct->has_discount ? $relProduct->formatted_sale_price : $relProduct->formatted_price }}
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
