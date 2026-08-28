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

                <!-- Price Box with Savings Tag -->
                <div class="p-3.5 rounded-3 mb-4 d-flex flex-wrap align-items-center justify-content-between gap-3" style="background: var(--bg-surface-subtle); border: 1px solid var(--border-default);">
                    <div>
                        <div class="text-secondary small mb-0.5" style="font-size: 0.75rem;">Giá niêm yết chính hãng</div>
                        <div class="d-flex align-items-baseline gap-3">
                            <span class="fs-2 fw-extrabold text-primary" style="letter-spacing: -0.02em;">
                                {{ $product->has_discount ? $product->formatted_sale_price : $product->formatted_price }}
                            </span>
                            @if ($product->has_discount)
                                <span class="text-muted text-decoration-line-through fs-6">
                                    {{ $product->formatted_price }}
                                </span>
                            @endif
                        </div>
                    </div>

                    @if ($product->has_discount)
                        <div class="badge bg-danger-subtle text-danger border border-danger-subtle px-3 py-1.5 rounded-pill fw-bold" style="font-size: 0.8rem;">
                            Tiết kiệm {{ number_format((float)$product->price - (float)$product->sale_price, 0, ',', '.') }} ₫
                        </div>
                    @endif
                </div>

                <!-- Specs Breakdown (Luxury Metric Cards) -->
                <div class="row g-2.5 mb-4">
                    <div class="col-4">
                        <div class="p-2.5 rounded-3 h-100" style="background: var(--bg-surface-subtle); border: 1px solid var(--border-default);">
                            <div class="d-flex align-items-center gap-1.5 text-secondary mb-1" style="font-size: 0.72rem;">
                                <i data-lucide="layers" class="text-primary" style="width: 14px; height: 14px;"></i>
                                <span>Chất liệu</span>
                            </div>
                            <div class="fw-bold text-dark small text-truncate" title="{{ $product->material ?? 'Da PU cao cấp' }}">{{ $product->material ?? 'Da PU cao cấp' }}</div>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="p-2.5 rounded-3 h-100" style="background: var(--bg-surface-subtle); border: 1px solid var(--border-default);">
                            <div class="d-flex align-items-center gap-1.5 text-secondary mb-1" style="font-size: 0.72rem;">
                                <i data-lucide="palette" class="text-primary" style="width: 14px; height: 14px;"></i>
                                <span>Màu sắc</span>
                            </div>
                            <div class="fw-bold text-dark small text-truncate" title="{{ $product->color ?? 'Tiêu chuẩn' }}">{{ $product->color ?? 'Tiêu chuẩn' }}</div>
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

                @if ($product->stock > 0)
                    <!-- Quantity Stepper for Detail Page -->
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
                                onchange="validateDetailQty(this, {{ $product->stock }})"
                            >
                            <button type="button" class="btn btn-stepper px-3 py-2" onclick="adjustDetailQty(1, {{ $product->stock }})" aria-label="Tăng">
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
                @else
                    <div class="alert alert-danger py-2.5 px-3.5 mb-4 small">
                        Sản phẩm này tạm thời đã hết hàng. Vui lòng quay lại sau hoặc liên hệ Hotline để được hỗ trợ.
                    </div>
                @endif

                <!-- Guarantees List with Proper Spacing & Badges -->
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

@section('scripts')
<script>
    function adjustDetailQty(delta, maxStock = 99) {
        const input = document.getElementById('detailQuantityInput');
        if (!input) return;
        let val = parseInt(input.value) || 1;
        let next = val + delta;
        if (next < 1) next = 1;
        if (next > maxStock) {
            next = maxStock;
            if (window.showToast) window.showToast(`Chỉ còn ${maxStock} sản phẩm trong kho.`, 'warning');
        }
        input.value = next;
    }

    function validateDetailQty(input, maxStock) {
        let val = parseInt(input.value) || 1;
        if (val < 1) val = 1;
        if (val > maxStock) {
            val = maxStock;
            if (window.showToast) window.showToast(`Chỉ còn ${maxStock} sản phẩm trong kho.`, 'warning');
        }
        input.value = val;
    }

    async function handleDetailAddToCart(redirectNow = false) {
        const input = document.getElementById('detailQuantityInput');
        const qty = input ? parseInt(input.value) || 1 : 1;
        const addBtn = document.getElementById('detailAddToCartBtn');
        const buyBtn = document.getElementById('detailBuyNowBtn');

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
            const res = await fetch('{{ route("cart.add") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({
                    product_id: {{ $product->id }},
                    quantity: qty
                })
            });

            const result = await res.json();

            if (res.ok && result.success) {
                if (typeof updateCartBadge === 'function') {
                    updateCartBadge(result.cart_count);
                }

                if (redirectNow) {
                    // Redirect to cart page with item ready
                    window.location.href = '{{ route("cart.index") }}';
                } else {
                    if (window.showToast) {
                        window.showToast(result.message || 'Đã thêm vào giỏ hàng!', 'success');
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
            alert('Lỗi kết nối. Vui lòng thử lại.');
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
</script>
@endsection
