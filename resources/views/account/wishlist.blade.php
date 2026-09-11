@extends('layouts.storefront')

@section('title', 'Danh Sách Yêu Thích - Tài Khoản Của Tôi')

@section('content')
<div class="container py-4 py-lg-5">
    <!-- Breadcrumb -->
    <div class="breadcrumb-modern mb-3">
        <a href="{{ route('home') }}">Trang chủ</a>
        <i data-lucide="chevron-right" style="width: 14px; height: 14px;"></i>
        <span>Tài khoản</span>
        <i data-lucide="chevron-right" style="width: 14px; height: 14px;"></i>
        <span class="text-primary fw-medium">Danh sách yêu thích</span>
    </div>

    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4 pb-3 border-bottom">
        <div>
            <h1 class="fw-extrabold text-dark mb-1" style="font-size: 1.85rem; letter-spacing: -0.02em;">
                Danh Sách Yêu Thích
            </h1>
            <p class="text-secondary small mb-0">
                Các mẫu túi xách bạn đã thả tim — lưu lại để ngắm nghía và mua sắm khi sẵn sàng
            </p>
        </div>
        <a href="{{ route('shop.index') }}" class="btn-brand-primary py-2 px-3.5 text-decoration-none">
            <i data-lucide="shopping-bag" style="width: 16px; height: 16px; margin-right: 0.35rem;"></i>
            <span>Tiếp Tục Mua Sắm</span>
        </a>
    </div>

    <div class="row g-4">
        <!-- Sidebar Navigation -->
        <div class="col-lg-3">
            <div class="card-modern p-3 shadow-sm border sticky-top" style="top: 85px;">
                <div class="d-flex align-items-center gap-3 p-2 mb-3 border-bottom">
                    <img src="{{ Auth::user()->avatar_url }}" alt="{{ Auth::user()->name }}" class="rounded-circle object-fit-cover shadow-sm border flex-shrink-0" style="width: 44px; height: 44px;">
                    <div class="min-w-0">
                        <div class="fw-bold text-dark text-truncate">{{ Auth::user()->name }}</div>
                        <div class="text-secondary small text-truncate">{{ Auth::user()->email }}</div>
                    </div>
                </div>

                <div class="d-flex flex-column gap-1">
                    <a href="{{ route('account.profile') }}" class="btn-surface w-100 text-start py-2 px-3 text-decoration-none d-flex align-items-center gap-2">
                        <i data-lucide="user" style="width: 16px; height: 16px;"></i>
                        <span>Thông tin tài khoản</span>
                    </a>
                    <a href="{{ route('account.orders') }}" class="btn-surface w-100 text-start py-2 px-3 text-decoration-none d-flex align-items-center gap-2">
                        <i data-lucide="package" style="width: 16px; height: 16px;"></i>
                        <span>Đơn hàng của tôi</span>
                    </a>
                    <a href="{{ route('account.addresses') }}" class="btn-surface w-100 text-start py-2 px-3 text-decoration-none d-flex align-items-center gap-2">
                        <i data-lucide="map-pin" style="width: 16px; height: 16px;"></i>
                        <span>Sổ địa chỉ nhận hàng</span>
                    </a>
                    <a href="{{ route('account.wishlist') }}" class="btn-brand-primary w-100 text-start py-2 px-3 text-decoration-none d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center gap-2">
                            <i data-lucide="heart" style="width: 16px; height: 16px;"></i>
                            <span>Danh sách yêu thích</span>
                        </div>
                        <span class="badge bg-white text-primary rounded-pill px-2" style="font-size: 0.72rem;">{{ $wishlistItems->count() }}</span>
                    </a>
                    <a href="{{ route('account.coins') }}" class="btn-surface w-100 text-start py-2 px-3 text-decoration-none d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-coin fs-6"></i>
                            <span>Ví Xu Aurelia</span>
                        </div>
                        <span class="badge bg-warning-subtle text-dark fw-bold rounded-pill px-2" style="font-size: 0.72rem;">
                            {{ number_format(Auth::user()->coins_balance) }} Xu
                        </span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="col-lg-9">
            @if ($wishlistItems->isEmpty())
                <!-- Empty State -->
                <div class="card-modern p-5 text-center shadow-sm border">
                    <div class="mb-4">
                        <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-3" style="width: 80px; height: 80px; background: var(--bg-surface-subtle);">
                            <i data-lucide="heart" style="width: 36px; height: 36px;" class="text-tertiary"></i>
                        </div>
                        <h4 class="fw-bold text-dark mb-2">Chưa có sản phẩm yêu thích</h4>
                        <p class="text-secondary mb-0" style="max-width: 420px; margin: 0 auto;">
                            Hãy bấm vào biểu tượng trái tim <i data-lucide="heart" style="width: 14px; height: 14px;" class="text-danger"></i> trên các mẫu túi xách để lưu lại những chiếc túi bạn yêu thích nhé!
                        </p>
                    </div>
                    <a href="{{ route('shop.index') }}" class="btn-brand-primary py-2.5 px-4">
                        <i data-lucide="shopping-bag" style="width: 18px; height: 18px; margin-right: 0.5rem;"></i>
                        <span>Khám Phá Bộ Sưu Tập</span>
                    </a>
                </div>
            @else
                <!-- Wishlist Grid -->
                <div class="row g-3">
                    @foreach ($wishlistItems as $item)
                        @if ($item->product)
                            <div class="col-sm-6 col-md-4" id="wishlist-item-{{ $item->product->id }}">
                                <div class="card-modern border shadow-sm overflow-hidden h-100 position-relative">
                                    <!-- Remove from Wishlist Button (Perfect Circle) -->
                                    <form action="{{ route('wishlist.remove', $item->product) }}" method="POST" class="position-absolute top-0 end-0 m-2" style="z-index: 5;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-wishlist-remove" title="Bỏ yêu thích">
                                            <i data-lucide="x" style="width: 15px; height: 15px;" class="text-danger"></i>
                                        </button>
                                    </form>

                                    <!-- Product Image -->
                                    <a href="{{ route('shop.show', $item->product) }}" class="d-block overflow-hidden" style="height: 200px;">
                                        @if ($item->product->image)
                                            <img src="{{ $item->product->image }}" alt="{{ $item->product->name }}" class="w-100 h-100 object-fit-cover" style="transition: transform 0.3s ease;">
                                        @else
                                            <div class="d-flex align-items-center justify-content-center h-100" style="background: var(--bg-surface-subtle);">
                                                <i data-lucide="shopping-bag" style="width: 48px; height: 48px;" class="text-tertiary"></i>
                                            </div>
                                        @endif
                                    </a>

                                    <!-- Product Info -->
                                    <div class="p-3">
                                        <div class="d-flex align-items-center gap-2 mb-1.5">
                                            @if ($item->product->brand)
                                                <span class="badge bg-dark text-white px-2 py-0.5 rounded-pill" style="font-size: 0.68rem;">
                                                    {{ $item->product->brand->name }}
                                                </span>
                                            @endif
                                            <span class="badge bg-primary-subtle text-primary px-2 py-0.5 rounded-pill" style="font-size: 0.68rem;">
                                                {{ $item->product->category?->name ?? 'Túi xách' }}
                                            </span>
                                        </div>

                                        <a href="{{ route('shop.show', $item->product) }}" class="text-decoration-none">
                                            <h6 class="fw-bold text-dark mb-2 text-truncate" title="{{ $item->product->name }}">
                                                {{ $item->product->name }}
                                            </h6>
                                        </a>

                                        <!-- Price -->
                                        <div class="d-flex align-items-baseline gap-2 mb-3">
                                            <span class="fw-extrabold text-primary" style="font-size: 1.1rem;">
                                                {{ $item->product->has_variants ? $item->product->formatted_price_range : ($item->product->has_discount ? $item->product->formatted_sale_price : $item->product->formatted_price) }}
                                            </span>
                                            @if ($item->product->has_discount && !$item->product->has_variants)
                                                <span class="text-muted text-decoration-line-through small">
                                                    {{ $item->product->formatted_price }}
                                                </span>
                                            @endif
                                        </div>

                                        <!-- Stock Status & Add to Cart -->
                                        @if ($item->product->total_stock > 0)
                                            <button type="button" class="btn-surface w-100 py-2 fw-semibold d-flex align-items-center justify-content-center gap-2 text-primary" style="border-color: var(--brand-400) !important;" onclick="addWishlistItemToCart({{ $item->product->id }})">
                                                <i data-lucide="shopping-cart" style="width: 16px; height: 16px;"></i>
                                                <span>Thêm vào giỏ hàng</span>
                                            </button>
                                        @else
                                            <div class="text-center py-2 text-danger small fw-semibold">
                                                <i data-lucide="alert-circle" style="width: 14px; height: 14px; margin-right: 0.3rem;"></i>
                                                Tạm hết hàng
                                            </div>
                                        @endif
                                    </div>

                                    <!-- Saved Date -->
                                    <div class="px-3 pb-2">
                                        <small class="text-tertiary" style="font-size: 0.72rem;">
                                            <i data-lucide="clock" style="width: 12px; height: 12px; margin-right: 0.25rem;"></i>
                                            Đã lưu {{ $item->created_at->diffForHumans() }}
                                        </small>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    /**
     * Add wishlist product to cart via AJAX (simple add, quantity=1, no variant).
     */
    async function addWishlistItemToCart(productId) {
        try {
            const res = await fetch('{{ route("cart.add") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ product_id: productId, quantity: 1 }),
            });
            const data = await res.json();
            if (data.success || res.ok) {
                if (typeof refreshCartBadge === 'function') refreshCartBadge();
                showToast(data.message || 'Đã thêm sản phẩm vào giỏ hàng!', 'success');
            } else {
                showToast(data.message || 'Không thể thêm vào giỏ hàng. Vui lòng xem chi tiết sản phẩm để chọn phân loại.', 'warning');
            }
        } catch (err) {
            showToast('Lỗi kết nối. Vui lòng thử lại.', 'error');
        }
    }

    /**
     * Simple toast notification helper.
     */
    function showToast(message, type = 'success') {
        const alertContainer = document.querySelector('.toast-container') || document.body;
        const toast = document.createElement('div');
        toast.className = `alert alert-${type === 'error' ? 'danger' : type} alert-dismissible fade show position-fixed shadow-lg`;
        toast.style.cssText = 'top: 90px; right: 20px; z-index: 9999; min-width: 300px; max-width: 420px;';
        toast.innerHTML = `
            <div class="d-flex align-items-center gap-2">
                <span>${message}</span>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
            </div>
        `;
        document.body.appendChild(toast);
        setTimeout(() => toast.remove(), 4000);
    }
</script>
@endsection
