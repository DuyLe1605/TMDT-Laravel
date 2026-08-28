<!-- Quick Add to Cart Modal -->
<div class="modal fade" id="quickAddCartModal" tabindex="-1" aria-labelledby="quickAddCartModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 460px;">
        <div class="modal-content modal-content-modern border-0">
            <div class="modal-header border-bottom pb-3 pt-3 px-4">
                <div class="d-flex align-items-center gap-2">
                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 34px; height: 34px; background: var(--brand-50); color: var(--brand-600);">
                        <i data-lucide="shopping-bag" style="width: 18px; height: 18px;"></i>
                    </div>
                    <h5 class="modal-title fw-bold text-dark fs-6" id="quickAddCartModalLabel">Thêm vào giỏ hàng</h5>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <form id="quickAddCartForm" onsubmit="submitQuickAddToCart(event)">
                <input type="hidden" id="quickAddProductId" name="product_id" value="">
                
                <div class="modal-body p-4">
                    <!-- Product Info Header -->
                    <div class="d-flex gap-3 mb-3.5 pb-3 border-bottom">
                        <div class="quick-add-img-wrapper rounded-3 border overflow-hidden flex-shrink-0" style="width: 80px; height: 80px; background: var(--bg-surface-subtle);">
                            <img id="quickAddProductImg" src="" alt="" class="w-100 h-100 object-fit-cover">
                        </div>
                        <div class="flex-grow-1 min-w-0">
                            <span id="quickAddProductCategory" class="badge bg-primary-subtle text-primary mb-1" style="font-size: 0.7rem;">Túi xách</span>
                            <h6 id="quickAddProductName" class="fw-bold text-dark text-truncate mb-1" style="font-size: 0.95rem;">Tên sản phẩm</h6>
                            <div class="d-flex align-items-baseline gap-2">
                                <span id="quickAddProductPrice" class="fw-extrabold text-primary fs-5">0 ₫</span>
                                <span id="quickAddProductOriginalPrice" class="text-muted small text-decoration-line-through d-none">0 ₫</span>
                            </div>
                        </div>
                    </div>

                    <!-- Stock Status -->
                    <div class="d-flex align-items-center justify-content-between mb-3 px-3 py-2 rounded-3" style="background: var(--bg-surface-subtle); border: 1px solid var(--border-default);">
                        <span class="text-secondary small">Trạng thái kho hàng:</span>
                        <span id="quickAddProductStock" class="fw-semibold text-success small">Còn hàng (10)</span>
                    </div>

                    <!-- Quantity Picker -->
                    <div class="mb-3">
                        <label class="form-label-modern small fw-semibold mb-2">Số lượng cần mua</label>
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="qty-stepper d-inline-flex align-items-center rounded-3 border">
                                <button type="button" class="btn btn-stepper" onclick="adjustQuickQty(-1)" aria-label="Giảm">
                                    <i data-lucide="minus" style="width: 14px; height: 14px;"></i>
                                </button>
                                <input 
                                    type="number" 
                                    id="quickAddQuantity" 
                                    name="quantity" 
                                    class="form-control text-center border-0 qty-input" 
                                    value="1" 
                                    min="1" 
                                    max="99" 
                                    onchange="validateQuickQty(this)"
                                    style="width: 60px; font-weight: 700; background: transparent;"
                                >
                                <button type="button" class="btn btn-stepper" onclick="adjustQuickQty(1)" aria-label="Tăng">
                                    <i data-lucide="plus" style="width: 14px; height: 14px;"></i>
                                </button>
                            </div>

                            <!-- Real-time Subtotal calculation -->
                            <div class="text-end">
                                <div class="text-secondary small" style="font-size: 0.75rem;">Tạm tính</div>
                                <div id="quickAddSubtotal" class="fw-bold text-dark fs-6">0 ₫</div>
                            </div>
                        </div>
                    </div>

                    <div id="quickAddAlert" class="alert alert-danger py-2 px-3 small d-none mb-0"></div>
                </div>

                <div class="modal-footer border-top px-4 py-3 bg-light-subtle">
                    <button type="button" class="btn btn-surface px-3 py-2" data-bs-dismiss="modal">Đóng</button>
                    <button type="submit" id="quickAddSubmitBtn" class="btn-brand-primary px-4 py-2">
                        <i data-lucide="shopping-bag" style="width: 16px; height: 16px; margin-right: 0.4rem;"></i>
                        <span>Thêm vào giỏ</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    let currentQuickProduct = {
        id: null,
        price: 0,
        stock: 1
    };

    let quickAddModalInstance = null;

    function openQuickAddModal(product) {
        currentQuickProduct = {
            id: product.id,
            name: product.name,
            price: parseFloat(product.effective_price || product.price),
            originalPrice: product.original_price ? parseFloat(product.original_price) : null,
            image: product.image || '',
            category: product.category_name || 'Túi Xách',
            stock: parseInt(product.stock || 10)
        };

        document.getElementById('quickAddProductId').value = currentQuickProduct.id;
        document.getElementById('quickAddProductName').textContent = currentQuickProduct.name;
        document.getElementById('quickAddProductCategory').textContent = currentQuickProduct.category;
        document.getElementById('quickAddProductPrice').textContent = formatCurrency(currentQuickProduct.price);
        
        const origPriceEl = document.getElementById('quickAddProductOriginalPrice');
        if (currentQuickProduct.originalPrice && currentQuickProduct.originalPrice > currentQuickProduct.price) {
            origPriceEl.textContent = formatCurrency(currentQuickProduct.originalPrice);
            origPriceEl.classList.remove('d-none');
        } else {
            origPriceEl.classList.add('d-none');
        }

        const imgEl = document.getElementById('quickAddProductImg');
        imgEl.src = currentQuickProduct.image || 'https://images.unsplash.com/photo-1584917865442-de89df76afd3?auto=format&fit=crop&w=400&q=80';
        imgEl.alt = currentQuickProduct.name;

        const stockEl = document.getElementById('quickAddProductStock');
        if (currentQuickProduct.stock > 0) {
            stockEl.textContent = `Còn hàng (${currentQuickProduct.stock} sản phẩm)`;
            stockEl.className = 'fw-semibold text-success small';
            document.getElementById('quickAddSubmitBtn').disabled = false;
        } else {
            stockEl.textContent = 'Hết hàng tạm thời';
            stockEl.className = 'fw-semibold text-danger small';
            document.getElementById('quickAddSubmitBtn').disabled = true;
        }

        const qtyInput = document.getElementById('quickAddQuantity');
        qtyInput.value = 1;
        qtyInput.max = currentQuickProduct.stock;

        updateQuickSubtotal();
        hideQuickAlert();

        const modalEl = document.getElementById('quickAddCartModal');
        if (!quickAddModalInstance) {
            quickAddModalInstance = new bootstrap.Modal(modalEl);
        }
        quickAddModalInstance.show();

        setTimeout(() => {
            if (typeof lucide !== 'undefined') lucide.createIcons();
        }, 100);
    }

    function adjustQuickQty(delta) {
        const qtyInput = document.getElementById('quickAddQuantity');
        let current = parseInt(qtyInput.value) || 1;
        let next = current + delta;

        if (next < 1) next = 1;
        if (next > currentQuickProduct.stock) {
            next = currentQuickProduct.stock;
            showQuickAlert(`Chỉ còn ${currentQuickProduct.stock} sản phẩm trong kho.`);
        } else {
            hideQuickAlert();
        }

        qtyInput.value = next;
        updateQuickSubtotal();
    }

    function validateQuickQty(input) {
        let val = parseInt(input.value) || 1;
        if (val < 1) val = 1;
        if (val > currentQuickProduct.stock) {
            val = currentQuickProduct.stock;
            showQuickAlert(`Số lượng tối đa có thể chọn là ${currentQuickProduct.stock}.`);
        } else {
            hideQuickAlert();
        }
        input.value = val;
        updateQuickSubtotal();
    }

    function updateQuickSubtotal() {
        const qty = parseInt(document.getElementById('quickAddQuantity').value) || 1;
        const subtotal = currentQuickProduct.price * qty;
        document.getElementById('quickAddSubtotal').textContent = formatCurrency(subtotal);
    }

    function showQuickAlert(msg) {
        const el = document.getElementById('quickAddAlert');
        el.textContent = msg;
        el.classList.remove('d-none');
    }

    function hideQuickAlert() {
        const el = document.getElementById('quickAddAlert');
        el.textContent = '';
        el.classList.add('d-none');
    }

    function formatCurrency(amount) {
        return new Intl.NumberFormat('vi-VN').format(amount) + ' ₫';
    }

    async function submitQuickAddToCart(event) {
        event.preventDefault();
        const submitBtn = document.getElementById('quickAddSubmitBtn');
        const originalHtml = submitBtn.innerHTML;
        
        submitBtn.disabled = true;
        submitBtn.innerHTML = `<span class="spinner-border spinner-border-sm me-1.5" role="status" aria-hidden="true"></span> Đang thêm...`;

        const productId = document.getElementById('quickAddProductId').value;
        const quantity = document.getElementById('quickAddQuantity').value;

        try {
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            const response = await fetch('{{ route("cart.add") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({
                    product_id: productId,
                    quantity: quantity
                })
            });

            const result = await response.json();

            if (response.ok && result.success) {
                // Update header badge
                updateCartBadge(result.cart_count);

                // Hide modal
                if (quickAddModalInstance) {
                    quickAddModalInstance.hide();
                }

                // Show toast
                showGlobalToast(result.message || 'Đã thêm vào giỏ hàng!', 'success');
            } else {
                showQuickAlert(result.message || 'Không thể thêm vào giỏ hàng. Vui lòng thử lại.');
            }
        } catch (error) {
            showQuickAlert('Đã xảy ra lỗi kết nối. Vui lòng thử lại.');
        } finally {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalHtml;
            if (typeof lucide !== 'undefined') lucide.createIcons();
        }
    }

    function updateCartBadge(count) {
        const badges = document.querySelectorAll('.cart-badge-count');
        badges.forEach(b => {
            b.textContent = count;
            b.style.display = count > 0 ? 'inline-flex' : 'none';
        });
    }

    function showGlobalToast(message, type = 'success') {
        if (window.showToast) {
            window.showToast(message, type);
            return;
        }

        // Fallback toast
        const toastContainer = document.getElementById('toastContainer');
        if (toastContainer) {
            const toastEl = document.createElement('div');
            toastEl.className = `alert alert-${type === 'success' ? 'success' : 'danger'} alert-dismissible fade show shadow-sm`;
            toastEl.innerHTML = `
                <div class="d-flex align-items-center gap-2">
                    <i data-lucide="${type === 'success' ? 'check-circle' : 'alert-circle'}" style="width: 16px; height: 16px;"></i>
                    <span>${message}</span>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            toastContainer.appendChild(toastEl);
            if (typeof lucide !== 'undefined') lucide.createIcons();
            setTimeout(() => toastEl.remove(), 4000);
        } else {
            alert(message);
        }
    }
</script>
