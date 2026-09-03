@extends('layouts.storefront')

@section('title', 'Giỏ Hàng Của Bạn - Aurelia Luxury Bags')

@section('content')
<div class="container py-4 py-lg-5">
    <!-- Breadcrumb & Title -->
    <div class="breadcrumb-modern mb-3">
        <a href="{{ route('home') }}">Trang chủ</a>
        <i data-lucide="chevron-right" style="width: 14px; height: 14px;"></i>
        <a href="{{ route('shop.index') }}">Cửa hàng</a>
        <i data-lucide="chevron-right" style="width: 14px; height: 14px;"></i>
        <span class="text-primary fw-medium">Giỏ hàng</span>
    </div>

    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4 pb-3 border-bottom">
        <div>
            <h1 class="fw-extrabold text-dark mb-1" style="font-size: 1.85rem; letter-spacing: -0.02em;">
                Giỏ Hàng <span class="text-primary fs-5">({{ $cartItems->count() }} loại sản phẩm)</span>
            </h1>
            <p class="text-secondary small mb-0">
                Chọn các món đồ bạn muốn mua và tiến hành thanh toán an toàn
            </p>
        </div>
        <a href="{{ route('shop.index') }}" class="btn-surface py-2 px-3.5 text-decoration-none d-inline-flex align-items-center gap-2">
            <i data-lucide="arrow-left" style="width: 16px; height: 16px;"></i>
            <span>Tiếp tục chọn túi xách</span>
        </a>
    </div>

    @if ($cartItems->isEmpty())
        <!-- Empty Cart State -->
        <div class="card-modern text-center py-5 px-4 my-4">
            <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px; background: var(--brand-50); color: var(--brand-600);">
                <i data-lucide="shopping-cart" style="width: 40px; height: 40px;"></i>
            </div>
            <h4 class="fw-bold text-dark mb-2">Giỏ hàng của bạn đang trống!</h4>
            <p class="text-secondary small mb-4" style="max-width: 450px; margin-left: auto; margin-right: auto; line-height: 1.6;">
                Bạn chưa thêm mẫu túi xách nào vào giỏ. Hãy dạo quanh bộ sưu tập Aurelia để khám phá các dòng túi da cao cấp mới nhất nhé!
            </p>
            <div>
                <a href="{{ route('shop.index') }}" class="btn-brand-primary py-2.5 px-4">
                    <i data-lucide="sparkles" style="width: 16px; height: 16px; margin-right: 0.45rem;"></i>
                    <span>Khám Phá Bộ Sưu Tập Ngay</span>
                </a>
            </div>
        </div>
    @else
        <!-- Cart Items Table / List Form -->
        <form id="cartCheckoutForm" action="{{ route('checkout.index') }}" method="GET">
            <div class="row g-4">
                <!-- Left: Cart Items List -->
                <div class="col-lg-8">
                    <div class="card-modern p-0 overflow-hidden mb-4 shadow-sm border">
                        <!-- Table Header / Master Controls -->
                        <div class="p-3.5 border-bottom d-flex align-items-center justify-content-between" style="background: var(--bg-surface-subtle);">
                            <div class="form-check d-flex align-items-center gap-2 mb-0">
                                <input 
                                    class="form-check-input custom-checkbox" 
                                    type="checkbox" 
                                    id="masterCheckbox" 
                                    onchange="toggleSelectAll(this)"
                                >
                                <label class="form-check-label fw-bold text-dark small user-select-none cursor-pointer" for="masterCheckbox">
                                    Chọn tất cả (<span id="totalItemsCount">{{ $cartItems->count() }}</span> sản phẩm)
                                </label>
                            </div>

                            <!-- Bulk Delete Button (Active when at least 1 item is checked) -->
                            <button 
                                type="button" 
                                id="bulkDeleteBtn" 
                                class="btn btn-sm btn-outline-danger d-inline-flex align-items-center gap-1.5 opacity-50 pointer-events-none"
                                onclick="confirmBulkDelete()"
                                title="Xóa các sản phẩm đang được chọn khỏi giỏ hàng"
                            >
                                <i data-lucide="trash-2" style="width: 14px; height: 14px;"></i>
                                <span>Xóa mục đã chọn</span>
                            </button>
                        </div>

                        <!-- Items List -->
                        <div class="cart-items-wrapper">
                            @foreach ($cartItems as $item)
                                <div class="cart-item-row p-3.5 border-bottom d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3" id="cart-item-row-{{ $item->id }}">
                                    <!-- Checkbox & Product Info -->
                                    <div class="d-flex align-items-center gap-3 flex-grow-1 min-w-0">
                                        <div class="form-check mb-0">
                                            <input 
                                                type="checkbox" 
                                                name="items[]" 
                                                value="{{ $item->id }}" 
                                                class="form-check-input custom-checkbox item-checkbox"
                                                id="item-chk-{{ $item->id }}"
                                                data-id="{{ $item->id }}"
                                                data-price="{{ $item->unit_price }}"
                                                data-quantity="{{ $item->quantity }}"
                                                data-subtotal="{{ $item->subtotal }}"
                                                onchange="onItemCheckboxChanged()"
                                            >
                                        </div>

                                        <!-- Image -->
                                        @php
                                            $itemImage = $item->variant && $item->variant->image ? $item->variant->image : $item->product->image;
                                        @endphp
                                        <a href="{{ route('shop.show', $item->product) }}" class="cart-item-thumb rounded-3 border overflow-hidden flex-shrink-0" style="width: 76px; height: 76px; background: var(--bg-surface-subtle);">
                                            @if ($itemImage)
                                                <img src="{{ $itemImage }}" alt="{{ $item->product->name }}" class="w-100 h-100 object-fit-cover">
                                            @else
                                                <div class="w-100 h-100 d-flex align-items-center justify-content-center">
                                                    <i data-lucide="shopping-bag" class="text-secondary" style="width: 24px; height: 24px;"></i>
                                                </div>
                                            @endif
                                        </a>

                                        <!-- Name & Category -->
                                        <div class="min-w-0 flex-grow-1">
                                            <div class="d-flex align-items-center gap-1.5 mb-1">
                                                @if ($item->product->brand)
                                                    <span class="badge bg-dark-subtle text-dark border px-2 py-0.5 rounded-pill" style="font-size: 0.65rem;">
                                                        👑 {{ $item->product->brand->name }}
                                                    </span>
                                                @endif
                                                <span class="badge bg-primary-subtle text-primary" style="font-size: 0.68rem;">
                                                    {{ $item->product->category?->name ?? 'Túi xách' }}
                                                </span>
                                            </div>
                                            <h6 class="fw-bold text-dark mb-1 text-truncate">
                                                <a href="{{ route('shop.show', $item->product) }}" class="text-decoration-none text-dark hover-primary">
                                                    {{ $item->product->name }}
                                                </a>
                                            </h6>
                                            @if ($item->variant)
                                                <div class="mb-1">
                                                    <span class="badge bg-light text-dark border px-2 py-0.5" style="font-size: 0.72rem;">
                                                        Phân loại: {{ $item->variant->variant_title }}
                                                    </span>
                                                </div>
                                            @endif
                                            <div class="d-flex align-items-baseline gap-2">
                                                <span class="fw-bold text-primary small">
                                                    {{ $item->formatted_unit_price }}
                                                </span>
                                                @if ($item->product->has_discount && !$item->variant)
                                                    <span class="text-muted text-decoration-line-through small" style="font-size: 0.75rem;">
                                                        {{ $item->product->formatted_price }}
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Stepper, Subtotal & Actions -->
                                    <div class="d-flex align-items-center justify-content-between justify-content-md-end gap-3.5 pt-2 pt-md-0 border-top border-md-0">
                                        <!-- Quantity Stepper -->
                                        <div class="qty-stepper d-inline-flex align-items-center rounded-3 border">
                                            <button 
                                                type="button" 
                                                class="btn btn-stepper" 
                                                onclick="updateCartItemQty({{ $item->id }}, -1, {{ $item->available_stock }})"
                                                aria-label="Giảm số lượng"
                                            >
                                                <i data-lucide="minus" style="width: 13px; height: 13px;"></i>
                                            </button>
                                            <input 
                                                type="number" 
                                                id="qty-input-{{ $item->id }}" 
                                                value="{{ $item->quantity }}" 
                                                min="1" 
                                                max="{{ $item->available_stock }}"
                                                class="form-control text-center border-0 qty-input"
                                                style="width: 48px; font-weight: 700; background: transparent;"
                                                onchange="onQtyInputChange({{ $item->id }}, this, {{ $item->available_stock }})"
                                            >
                                            <button 
                                                type="button" 
                                                class="btn btn-stepper" 
                                                onclick="updateCartItemQty({{ $item->id }}, 1, {{ $item->available_stock }})"
                                                aria-label="Tăng số lượng"
                                            >
                                                <i data-lucide="plus" style="width: 13px; height: 13px;"></i>
                                            </button>
                                        </div>

                                        <!-- Item Subtotal -->
                                        <div class="text-end" style="min-width: 110px;">
                                            <div class="text-secondary small d-none d-md-block" style="font-size: 0.72rem;">Thành tiền</div>
                                            <div class="fw-extrabold text-primary item-subtotal-text" id="subtotal-{{ $item->id }}">
                                                {{ $item->formatted_subtotal }}
                                            </div>
                                        </div>

                                        <!-- Delete Single Item -->
                                        <button 
                                            type="button" 
                                            class="btn btn-link text-danger p-1 text-decoration-none hover-scale" 
                                            onclick="deleteCartItem({{ $item->id }}, '{{ addslashes($item->product->name) }}')"
                                            title="Xóa sản phẩm này"
                                        >
                                            <i data-lucide="trash-2" style="width: 17px; height: 17px;"></i>
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Right: Sticky Order Summary & Checkout Action -->
                <div class="col-lg-4">
                    <div class="card-modern p-4 sticky-top shadow-sm border" style="top: 85px;">
                        <h5 class="fw-bold text-dark mb-3 pb-2 border-bottom d-flex align-items-center justify-content-between">
                            <span>Tóm Tắt Đơn Hàng</span>
                            <div class="rounded-circle p-1.5 bg-primary-subtle text-primary d-flex align-items-center justify-content-center" style="width: 28px; height: 28px;">
                                <i data-lucide="receipt" style="width: 16px; height: 16px;"></i>
                            </div>
                        </h5>

                        <div class="d-flex flex-column gap-2.5 mb-3 text-secondary small">
                            <div class="d-flex justify-content-between">
                                <span>Số sản phẩm đã chọn:</span>
                                <span class="fw-bold text-dark" id="selectedItemsCountText">0 sản phẩm</span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span>Tổng tiền hàng:</span>
                                <span class="fw-bold text-dark" id="selectedSubtotalText">0 ₫</span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span>Phí vận chuyển dự kiến:</span>
                                <span class="text-success fw-medium" id="shippingEstimateText">Tính lúc đặt hàng</span>
                            </div>
                        </div>

                        <div class="p-3.5 rounded-3 mb-4" style="background: var(--bg-surface-subtle); border: 1px solid var(--border-default);">
                            <div class="d-flex justify-content-between align-items-baseline mb-1">
                                <span class="fw-bold text-dark">Tổng thanh toán:</span>
                                <span class="fw-extrabold text-primary fs-4" id="displayTotalPrice">0 ₫</span>
                            </div>
                            <div class="text-secondary" style="font-size: 0.75rem;">
                                (Đã bao gồm VAT nếu có)
                            </div>
                        </div>

                        <!-- Notice message when no item is selected -->
                        <div id="noSelectionNotice" class="d-flex align-items-center gap-2 p-3 rounded-3 mb-3 small" style="background: var(--warning-50); border: 1px solid var(--warning-100); color: var(--warning-text, #92400e);">
                            <i data-lucide="info" style="width: 16px; height: 16px; flex-shrink: 0;"></i>
                            <span>Vui lòng tích chọn ít nhất 1 sản phẩm để thanh toán hoặc xóa.</span>
                        </div>

                        <!-- Checkout Submit Button: Dimmed / Disabled if 0 items checked -->
                        <button 
                            type="submit" 
                            id="checkoutSubmitBtn" 
                            class="btn-brand-primary w-100 py-3 justify-content-center fw-bold fs-6 opacity-50 pointer-events-none transition-all shadow-sm"
                        >
                            <i data-lucide="credit-card" style="width: 18px; height: 18px; margin-right: 0.45rem;"></i>
                            <span id="checkoutBtnLabel">Mua Hàng (0)</span>
                        </button>

                        <div class="mt-3 text-center">
                            <span class="text-secondary small d-inline-flex align-items-center gap-1.5" style="font-size: 0.75rem;">
                                <i data-lucide="shield-check" class="text-success" style="width: 14px; height: 14px;"></i>
                                <span>Thanh toán an toàn 100% &bull; Bảo mật thông tin</span>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    @endif
</div>

<!-- Bulk Delete Hidden Form -->
<form id="bulkDeleteForm" action="{{ route('cart.bulk_remove') }}" method="POST" class="d-none">
    @csrf
</form>

<!-- Single Delete Hidden Form -->
<form id="singleDeleteForm" method="POST" class="d-none">
    @csrf
    @method('DELETE')
</form>

<!-- Modal Xác nhận xóa hàng loạt -->
<div class="modal fade" id="confirmBulkDeleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 420px;">
        <div class="modal-content modal-content-modern border-0">
            <div class="p-4 text-center">
                <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 54px; height: 54px; background: var(--danger-50); color: var(--danger-600);">
                    <i data-lucide="trash-2" style="width: 26px; height: 26px;"></i>
                </div>
                <h5 class="fw-bold text-dark mb-2">Xóa các sản phẩm đã chọn?</h5>
                <p class="text-secondary small mb-4" id="bulkDeleteConfirmText">
                    Bạn có chắc chắn muốn xóa các sản phẩm đang chọn ra khỏi giỏ hàng không?
                </p>
                <div class="d-flex justify-content-center gap-3">
                    <button type="button" class="btn-surface px-4 py-2" data-bs-dismiss="modal">Hủy bỏ</button>
                    <button type="button" class="btn btn-danger px-4 py-2 rounded-3 fw-semibold" onclick="executeBulkDelete()">
                        <span>Xác nhận xóa</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    let bulkDeleteModalInstance = null;

    function formatVND(amount) {
        return new Intl.NumberFormat('vi-VN').format(amount) + ' ₫';
    }

    // Toggle Select All Checkbox
    function toggleSelectAll(master) {
        const checkboxes = document.querySelectorAll('.item-checkbox');
        checkboxes.forEach(chk => {
            chk.checked = master.checked;
        });
        onItemCheckboxChanged();
    }

    // When any item checkbox state changes
    function onItemCheckboxChanged() {
        const checkboxes = document.querySelectorAll('.item-checkbox');
        const master = document.getElementById('masterCheckbox');
        const bulkDeleteBtn = document.getElementById('bulkDeleteBtn');
        const checkoutBtn = document.getElementById('checkoutSubmitBtn');
        const checkoutBtnLabel = document.getElementById('checkoutBtnLabel');
        const noSelectionNotice = document.getElementById('noSelectionNotice');
        
        let checkedCount = 0;
        let totalCheckedPrice = 0;

        checkboxes.forEach(chk => {
            if (chk.checked) {
                checkedCount++;
                const subtotal = parseFloat(chk.getAttribute('data-subtotal')) || 0;
                totalCheckedPrice += subtotal;
            }
        });

        // Update master checkbox state
        if (master) {
            master.checked = (checkedCount === checkboxes.length && checkboxes.length > 0);
            master.indeterminate = (checkedCount > 0 && checkedCount < checkboxes.length);
        }

        // Update summary text
        const selectedItemsCountText = document.getElementById('selectedItemsCountText');
        const selectedSubtotalText = document.getElementById('selectedSubtotalText');
        const displayTotalPrice = document.getElementById('displayTotalPrice');

        if (selectedItemsCountText) selectedItemsCountText.textContent = `${checkedCount} sản phẩm`;
        if (selectedSubtotalText) selectedSubtotalText.textContent = formatVND(totalCheckedPrice);
        if (displayTotalPrice) displayTotalPrice.textContent = formatVND(totalCheckedPrice);

        // Core Rule: If NO item is checked -> Total is 0 ₫, checkout button dimmed / disabled, bulk delete disabled
        if (checkedCount === 0) {
            if (bulkDeleteBtn) {
                bulkDeleteBtn.classList.add('opacity-50', 'pointer-events-none');
            }
            if (checkoutBtn) {
                checkoutBtn.classList.add('opacity-50', 'pointer-events-none');
            }
            if (checkoutBtnLabel) {
                checkoutBtnLabel.textContent = `Mua Hàng (0)`;
            }
            if (noSelectionNotice) {
                noSelectionNotice.classList.remove('d-none');
            }
        } else {
            if (bulkDeleteBtn) {
                bulkDeleteBtn.classList.remove('opacity-50', 'pointer-events-none');
            }
            if (checkoutBtn) {
                checkoutBtn.classList.remove('opacity-50', 'pointer-events-none');
            }
            if (checkoutBtnLabel) {
                checkoutBtnLabel.textContent = `Mua Hàng (${checkedCount})`;
            }
            if (noSelectionNotice) {
                noSelectionNotice.classList.add('d-none');
            }
        }
    }

    // Update quantity stepper
    async function updateCartItemQty(itemId, delta, stockLimit) {
        const input = document.getElementById(`qty-input-${itemId}`);
        let currentQty = parseInt(input.value) || 1;
        let newQty = currentQty + delta;

        if (newQty < 1) newQty = 1;
        if (newQty > stockLimit) {
            newQty = stockLimit;
            if (window.showToast) window.showToast(`Sản phẩm này chỉ còn ${stockLimit} chiếc trong kho.`, 'warning');
        }

        input.value = newQty;
        await saveCartItemQty(itemId, newQty);
    }

    function onQtyInputChange(itemId, input, stockLimit) {
        let val = parseInt(input.value) || 1;
        if (val < 1) val = 1;
        if (val > stockLimit) {
            val = stockLimit;
            if (window.showToast) window.showToast(`Sản phẩm này chỉ còn ${stockLimit} chiếc trong kho.`, 'warning');
        }
        input.value = val;
        saveCartItemQty(itemId, val);
    }

    async function saveCartItemQty(itemId, quantity) {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        
        try {
            const response = await fetch(`/cart/update/${itemId}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({ quantity: quantity })
            });

            const result = await response.json();

            if (response.ok && result.success) {
                // Update row subtotal in UI
                const subtotalEl = document.getElementById(`subtotal-${itemId}`);
                if (subtotalEl) {
                    subtotalEl.textContent = result.item.formatted_subtotal;
                }

                // Update data attributes on checkbox
                const chk = document.getElementById(`item-chk-${itemId}`);
                if (chk) {
                    chk.setAttribute('data-quantity', result.item.quantity);
                    chk.setAttribute('data-subtotal', result.item.subtotal);
                }

                // Update header badge
                if (typeof updateCartBadge === 'function') {
                    updateCartBadge(result.cart_count);
                }

                // Re-calculate summary
                onItemCheckboxChanged();
            } else {
                if (window.showToast) window.showToast(result.message || 'Không thể cập nhật số lượng.', 'error');
            }
        } catch (err) {
            console.error(err);
        }
    }

    // Delete single item
    async function deleteCartItem(itemId, productName) {
        if (!confirm(`Bạn có chắc muốn xóa "${productName}" khỏi giỏ hàng?`)) {
            return;
        }

        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        try {
            const response = await fetch(`/cart/remove/${itemId}`, {
                method: 'DELETE',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                }
            });

            const result = await response.json();
            if (response.ok && result.success) {
                const row = document.getElementById(`cart-item-row-${itemId}`);
                if (row) {
                    row.remove();
                }

                if (typeof updateCartBadge === 'function') {
                    updateCartBadge(result.cart_count);
                }

                // If no more items, reload to show empty state
                const remaining = document.querySelectorAll('.cart-item-row');
                if (remaining.length === 0) {
                    window.location.reload();
                    return;
                }

                const totalItemsCount = document.getElementById('totalItemsCount');
                if (totalItemsCount) totalItemsCount.textContent = remaining.length;

                onItemCheckboxChanged();
                if (window.showToast) window.showToast('Đã xóa sản phẩm khỏi giỏ hàng!', 'success');
            }
        } catch (err) {
            console.error(err);
        }
    }

    // Bulk Delete
    function confirmBulkDelete() {
        const checked = document.querySelectorAll('.item-checkbox:checked');
        if (checked.length === 0) {
            alert('Vui lòng tích chọn ít nhất 1 sản phẩm để xóa.');
            return;
        }

        const modalEl = document.getElementById('confirmBulkDeleteModal');
        const confirmText = document.getElementById('bulkDeleteConfirmText');
        confirmText.textContent = `Bạn có chắc chắn muốn xóa ${checked.length} sản phẩm đang được chọn ra khỏi giỏ hàng không?`;

        if (!bulkDeleteModalInstance) {
            bulkDeleteModalInstance = new bootstrap.Modal(modalEl);
        }
        bulkDeleteModalInstance.show();
    }

    async function executeBulkDelete() {
        const checked = document.querySelectorAll('.item-checkbox:checked');
        const itemIds = Array.from(checked).map(c => parseInt(c.value));
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

        try {
            const response = await fetch('{{ route("cart.bulk_remove") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({ item_ids: itemIds })
            });

            const result = await response.json();
            if (response.ok && result.success) {
                if (bulkDeleteModalInstance) {
                    bulkDeleteModalInstance.hide();
                }
                window.location.reload();
            } else {
                alert(result.message || 'Lỗi khi xóa sản phẩm');
            }
        } catch (err) {
            console.error(err);
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        onItemCheckboxChanged();
    });
</script>
@endsection
