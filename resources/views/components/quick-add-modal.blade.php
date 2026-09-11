<!-- Quick Add to Cart Modal (Hỗ trợ chọn Phân Loại Hàng / Biến Thể Nhanh) -->
<div class="modal fade" id="quickAddCartModal" tabindex="-1" aria-labelledby="quickAddCartModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 480px;">
        <div class="modal-content modal-content-modern border-0 shadow-lg">
            <div class="modal-header border-bottom pb-3 pt-3 px-4">
                <div class="d-flex align-items-center gap-2">
                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 34px; height: 34px; background: var(--brand-50, #fff5f3); color: var(--brand-600, #c4472b);">
                        <i data-lucide="shopping-bag" style="width: 18px; height: 18px;"></i>
                    </div>
                    <h5 class="modal-title fw-bold text-dark fs-6 mb-0" id="quickAddCartModalLabel">Thêm vào giỏ hàng</h5>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <form id="quickAddCartForm" onsubmit="submitQuickAddToCart(event)">
                <input type="hidden" id="quickAddProductId" name="product_id" value="">
                <input type="hidden" id="quickAddVariantId" name="product_variant_id" value="">
                
                <div class="modal-body p-4">
                    <!-- Product Info Header -->
                    <div class="d-flex gap-3 mb-3 pb-3 border-bottom align-items-start">
                        <div class="quick-add-img-wrapper rounded-3 border overflow-hidden flex-shrink-0 position-relative" style="width: 82px; height: 82px; background: var(--bg-surface-subtle, #f8f9fa);">
                            <img id="quickAddProductImg" src="" alt="" class="w-100 h-100 object-fit-cover transition-all">
                        </div>
                        <div class="flex-grow-1 min-w-0">
                            <span id="quickAddProductCategory" class="badge bg-primary-subtle text-primary mb-1" style="font-size: 0.68rem; font-weight: 600;">Túi xách</span>
                            <h6 id="quickAddProductName" class="fw-bold text-dark text-truncate mb-1" style="font-size: 0.95rem;">Tên sản phẩm</h6>
                            <div class="d-flex align-items-baseline gap-2 flex-wrap">
                                <span id="quickAddProductPrice" class="fw-extrabold text-primary fs-5">0 ₫</span>
                                <span id="quickAddProductOriginalPrice" class="text-muted small text-decoration-line-through d-none" style="font-size: 0.8rem;">0 ₫</span>
                                <span id="quickAddDiscountBadge" class="badge bg-danger-subtle text-danger d-none" style="font-size: 0.65rem; font-weight: 700;">-0%</span>
                            </div>
                            <div class="text-muted small mt-0.5" id="quickAddSkuWrapper" style="font-size: 0.72rem;">
                                SKU: <span id="quickAddSkuText" class="font-monospace text-dark">---</span>
                            </div>
                        </div>
                    </div>

                    <!-- Variant Loading State -->
                    <div id="quickAddVariantLoading" class="text-center py-3 d-none">
                        <div class="spinner-border spinner-border-sm text-primary me-2" role="status"></div>
                        <span class="small text-secondary fw-medium">Đang tải phân loại sản phẩm...</span>
                    </div>

                    <!-- Variant Selection Wrapper -->
                    <div id="quickAddVariantSection" class="mb-3 d-none">
                        <div class="p-3 rounded-3 border bg-light-subtle">
                            <div class="d-flex align-items-center justify-content-between mb-2 pb-1 border-bottom">
                                <span class="small fw-bold text-dark text-uppercase d-flex align-items-center gap-1" style="font-size: 0.75rem; letter-spacing: 0.3px;">
                                    <i data-lucide="layers" class="text-primary" style="width: 14px; height: 14px;"></i>
                                    <span>Chọn phân loại hàng</span>
                                </span>
                                <span class="text-secondary small" style="font-size: 0.75rem;">
                                    Đang chọn: <span class="fw-bold text-primary" id="quickAddVariantSummary">---</span>
                                </span>
                            </div>
                            <div id="quickAddAttributesContainer" class="d-flex flex-column gap-2.5 pt-1">
                                <!-- Attributes & option pills generated via JS -->
                            </div>
                        </div>
                    </div>

                    <!-- Stock Status -->
                    <div class="d-flex align-items-center justify-content-between mb-3 px-3 py-2 rounded-3" style="background: var(--bg-surface-subtle, #f8f9fa); border: 1px solid var(--border-default, #e9ecef);">
                        <span class="text-secondary small fw-medium">Trạng thái kho hàng:</span>
                        <span id="quickAddProductStock" class="fw-semibold text-success small">Còn hàng (10)</span>
                    </div>

                    <!-- Quantity Picker & Subtotal -->
                    <div class="mb-2">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <label class="form-label-modern small fw-semibold mb-1 d-block">Số lượng</label>
                                <div class="qty-stepper d-inline-flex align-items-center rounded-3 border bg-white">
                                    <button type="button" class="btn btn-stepper px-2.5 py-1 border-0" onclick="adjustQuickQty(-1)" aria-label="Giảm">
                                        <i data-lucide="minus" style="width: 14px; height: 14px;"></i>
                                    </button>
                                    <input 
                                        type="number" 
                                        id="quickAddQuantity" 
                                        name="quantity" 
                                        class="form-control text-center border-0 qty-input p-0" 
                                        value="1" 
                                        min="1" 
                                        max="99" 
                                        onchange="validateQuickQty(this)"
                                        style="width: 50px; font-weight: 700; background: transparent; height: 34px;"
                                    >
                                    <button type="button" class="btn btn-stepper px-2.5 py-1 border-0" onclick="adjustQuickQty(1)" aria-label="Tăng">
                                        <i data-lucide="plus" style="width: 14px; height: 14px;"></i>
                                    </button>
                                </div>
                            </div>

                            <!-- Real-time Subtotal calculation -->
                            <div class="text-end">
                                <div class="text-secondary small" style="font-size: 0.75rem;">Tạm tính</div>
                                <div id="quickAddSubtotal" class="fw-bold text-dark fs-5 text-primary">0 ₫</div>
                            </div>
                        </div>
                    </div>

                    <!-- Alert message box -->
                    <div id="quickAddAlert" class="alert alert-danger py-2 px-3 small d-none mt-3 mb-0"></div>
                </div>

                <div class="modal-footer border-top px-4 py-3 bg-light-subtle d-flex align-items-center justify-content-between gap-2">
                    <a id="quickAddDetailLink" href="#" class="btn btn-outline-secondary btn-sm px-3 py-2 text-decoration-none rounded-3 d-inline-flex align-items-center gap-1">
                        <span>Chi tiết</span>
                        <i data-lucide="arrow-up-right" style="width: 14px; height: 14px;"></i>
                    </a>
                    
                    <div class="d-flex align-items-center gap-2">
                        <button type="submit" id="quickAddSubmitBtn" class="btn btn-brand-primary px-3.5 py-2 rounded-3 d-inline-flex align-items-center gap-1.5 shadow-sm">
                            <i data-lucide="shopping-bag" style="width: 16px; height: 16px;"></i>
                            <span>Thêm vào giỏ</span>
                        </button>
                        <button type="button" id="quickBuyNowBtn" onclick="submitQuickAddToCart(event, true)" class="btn btn-dark px-3.5 py-2 rounded-3 d-inline-flex align-items-center gap-1.5 shadow-sm">
                            <i data-lucide="zap" style="width: 15px; height: 15px;"></i>
                            <span>Mua ngay</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .quick-variant-pill {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        padding: 0.35rem 0.75rem;
        border-radius: 7px;
        font-size: 0.8rem;
        font-weight: 600;
        color: var(--text-primary, #2d3748);
        background: #ffffff;
        border: 1.5px solid var(--border-default, #e2e8f0);
        cursor: pointer;
        transition: all 0.18s ease;
        line-height: 1.2;
    }
    .quick-variant-pill .check-icon {
        display: none;
    }
    .quick-variant-pill:hover {
        border-color: var(--brand-500, #e05638);
        color: var(--brand-600, #c4472b);
        background: var(--brand-50, #fff5f3);
    }
    .quick-variant-pill.active {
        border-color: var(--brand-500, #e05638) !important;
        color: var(--brand-700, #9f311a) !important;
        background: rgba(224, 86, 56, 0.08) !important;
        box-shadow: 0 0 0 1px var(--brand-500, #e05638);
    }
    .quick-variant-pill.active .check-icon {
        display: inline-block;
        color: var(--brand-600, #c4472b);
    }
</style>

<script>
    let currentQuickProduct = {
        id: null,
        name: '',
        price: 0,
        originalPrice: null,
        image: '',
        category: 'Túi xách',
        stock: 1,
        has_variants: false,
        detail_url: '#',
        attributes: [],
        variants: []
    };

    let selectedQuickOptions = {};
    let currentQuickVariant = null;
    let quickAddModalInstance = null;

    async function openQuickAddModal(product) {
        currentQuickProduct = {
            id: product.id,
            name: product.name,
            price: parseFloat(product.effective_price || product.price || 0),
            originalPrice: product.original_price ? parseFloat(product.original_price) : null,
            image: product.image || '',
            category: product.category_name || 'Túi xách',
            stock: parseInt(product.stock || 0),
            has_variants: Boolean(product.has_variants),
            detail_url: product.detail_url || `/shop/${product.id}`,
            attributes: [],
            variants: []
        };

        selectedQuickOptions = {};
        currentQuickVariant = null;

        document.getElementById('quickAddProductId').value = currentQuickProduct.id;
        document.getElementById('quickAddVariantId').value = '';
        document.getElementById('quickAddProductName').textContent = currentQuickProduct.name;
        document.getElementById('quickAddProductCategory').textContent = currentQuickProduct.category;
        document.getElementById('quickAddProductPrice').textContent = formatCurrency(currentQuickProduct.price);
        document.getElementById('quickAddDetailLink').href = currentQuickProduct.detail_url;
        
        const origPriceEl = document.getElementById('quickAddProductOriginalPrice');
        const discountBadge = document.getElementById('quickAddDiscountBadge');
        if (currentQuickProduct.originalPrice && currentQuickProduct.originalPrice > currentQuickProduct.price) {
            origPriceEl.textContent = formatCurrency(currentQuickProduct.originalPrice);
            origPriceEl.classList.remove('d-none');
            
            const pct = Math.round(((currentQuickProduct.originalPrice - currentQuickProduct.price) / currentQuickProduct.originalPrice) * 100);
            discountBadge.textContent = `-${pct}%`;
            discountBadge.classList.remove('d-none');
        } else {
            origPriceEl.classList.add('d-none');
            discountBadge.classList.add('d-none');
        }

        const skuText = document.getElementById('quickAddSkuText');
        if (skuText) skuText.textContent = product.sku || 'BAG-' + product.id;

        const imgEl = document.getElementById('quickAddProductImg');
        imgEl.src = currentQuickProduct.image || 'https://images.unsplash.com/photo-1584917865442-de89df76afd3?auto=format&fit=crop&w=400&q=80';
        imgEl.alt = currentQuickProduct.name;

        // Reset Qty
        const qtyInput = document.getElementById('quickAddQuantity');
        qtyInput.value = 1;
        qtyInput.max = Math.max(currentQuickProduct.stock, 1);

        updateQuickStockUI(currentQuickProduct.stock);
        updateQuickSubtotal();
        hideQuickAlert();

        const modalEl = document.getElementById('quickAddCartModal');
        if (!quickAddModalInstance) {
            quickAddModalInstance = new bootstrap.Modal(modalEl);
        }
        quickAddModalInstance.show();

        // If product has variants, fetch dynamic variant data
        const variantSection = document.getElementById('quickAddVariantSection');
        const variantLoading = document.getElementById('quickAddVariantLoading');

        if (currentQuickProduct.has_variants) {
            variantSection.classList.add('d-none');
            variantLoading.classList.remove('d-none');

            try {
                const res = await fetch(`/products/${currentQuickProduct.id}/quick-data`, {
                    headers: { 'Accept': 'application/json' }
                });
                const data = await res.json();

                if (data.success && data.product) {
                    currentQuickProduct.attributes = data.product.attributes || [];
                    currentQuickProduct.variants = data.product.variants || [];

                    renderQuickVariantSelectors();
                    variantLoading.classList.add('d-none');
                    variantSection.classList.remove('d-none');
                } else {
                    variantLoading.classList.add('d-none');
                }
            } catch (err) {
                console.error('Lỗi khi tải dữ liệu phân loại nhanh:', err);
                variantLoading.classList.add('d-none');
            }
        } else {
            variantSection.classList.add('d-none');
            variantLoading.classList.add('d-none');
        }

        setTimeout(() => {
            if (typeof lucide !== 'undefined') lucide.createIcons();
        }, 100);
    }

    function renderQuickVariantSelectors() {
        const container = document.getElementById('quickAddAttributesContainer');
        container.innerHTML = '';
        selectedQuickOptions = {};

        if (!currentQuickProduct.attributes || currentQuickProduct.attributes.length === 0) {
            document.getElementById('quickAddVariantSection').classList.add('d-none');
            return;
        }

        currentQuickProduct.attributes.forEach((attr, groupIndex) => {
            const groupDiv = document.createElement('div');
            groupDiv.className = 'quick-attribute-group';
            groupDiv.setAttribute('data-group-index', groupIndex);

            // Default select the first option of each attribute
            const firstValue = attr.values && attr.values.length > 0 ? attr.values[0] : '';
            selectedQuickOptions[groupIndex] = firstValue;

            let pillsHtml = '';
            (attr.values || []).forEach((val, valIndex) => {
                const isActive = valIndex === 0 ? 'active' : '';
                pillsHtml += `
                    <button 
                        type="button" 
                        class="btn quick-variant-pill ${isActive}" 
                        data-group-index="${groupIndex}"
                        data-value="${escapeHtml(val)}"
                        onclick="selectQuickVariantOption(${groupIndex}, '${escapeHtml(val)}', this)"
                    >
                        <i data-lucide="check" class="check-icon" style="width: 12px; height: 12px;"></i>
                        <span>${escapeHtml(val)}</span>
                    </button>
                `;
            });

            groupDiv.innerHTML = `
                <div class="d-flex align-items-center gap-1.5 mb-1.5">
                    <span class="text-secondary small fw-semibold" style="font-size: 0.78rem;">${escapeHtml(attr.name)}:</span>
                    <span class="fw-bold text-dark small quick-group-val" id="quick-group-val-${groupIndex}" style="font-size: 0.78rem;">${escapeHtml(firstValue)}</span>
                </div>
                <div class="d-flex flex-wrap align-items-center gap-1.5">
                    ${pillsHtml}
                </div>
            `;

            container.appendChild(groupDiv);
        });

        matchQuickVariant();
        if (typeof lucide !== 'undefined') lucide.createIcons();
    }

    function selectQuickVariantOption(groupIndex, value, buttonEl) {
        selectedQuickOptions[groupIndex] = value;

        const parent = buttonEl.closest('.quick-attribute-group');
        if (parent) {
            parent.querySelectorAll('.quick-variant-pill').forEach(btn => btn.classList.remove('active'));
            buttonEl.classList.add('active');

            const valDisplay = parent.querySelector('.quick-group-val');
            if (valDisplay) valDisplay.textContent = value;
        }

        matchQuickVariant();
        if (typeof lucide !== 'undefined') lucide.createIcons();
    }

    function matchQuickVariant() {
        if (!currentQuickProduct.has_variants || !currentQuickProduct.variants || currentQuickProduct.variants.length === 0) {
            updateQuickStockUI(currentQuickProduct.stock);
            return;
        }

        const selectedValues = Object.keys(selectedQuickOptions)
            .sort((a, b) => a - b)
            .map(k => selectedQuickOptions[k]);

        currentQuickVariant = currentQuickProduct.variants.find(v => {
            const m1 = !selectedValues[0] || v.option1_value === selectedValues[0];
            const m2 = !selectedValues[1] || v.option2_value === selectedValues[1];
            const m3 = !selectedValues[2] || v.option3_value === selectedValues[2];
            return m1 && m2 && m3;
        });

        const summaryText = document.getElementById('quickAddVariantSummary');
        const priceEl = document.getElementById('quickAddProductPrice');
        const origPriceEl = document.getElementById('quickAddProductOriginalPrice');
        const discountBadge = document.getElementById('quickAddDiscountBadge');
        const skuText = document.getElementById('quickAddSkuText');
        const variantInput = document.getElementById('quickAddVariantId');

        if (currentQuickVariant) {
            variantInput.value = currentQuickVariant.id;
            if (summaryText) summaryText.textContent = currentQuickVariant.variant_title;
            if (skuText && currentQuickVariant.sku) skuText.textContent = currentQuickVariant.sku;

            const effectivePrice = currentQuickVariant.effective_price || currentQuickVariant.price;
            currentQuickProduct.price = parseFloat(effectivePrice);
            priceEl.textContent = formatCurrency(effectivePrice);

            if (currentQuickVariant.sale_price && parseFloat(currentQuickVariant.sale_price) < parseFloat(currentQuickVariant.price)) {
                origPriceEl.textContent = formatCurrency(currentQuickVariant.price);
                origPriceEl.classList.remove('d-none');

                const pct = Math.round(((parseFloat(currentQuickVariant.price) - parseFloat(currentQuickVariant.sale_price)) / parseFloat(currentQuickVariant.price)) * 100);
                discountBadge.textContent = `-${pct}%`;
                discountBadge.classList.remove('d-none');
            } else {
                origPriceEl.classList.add('d-none');
                discountBadge.classList.add('d-none');
            }

            if (currentQuickVariant.image) {
                document.getElementById('quickAddProductImg').src = currentQuickVariant.image;
            }

            updateQuickStockUI(currentQuickVariant.stock);
        } else {
            variantInput.value = '';
            if (summaryText) summaryText.textContent = 'Không có sẵn';
            updateQuickStockUI(0);
        }

        updateQuickSubtotal();
    }

    function updateQuickStockUI(stock) {
        const stockEl = document.getElementById('quickAddProductStock');
        const submitBtn = document.getElementById('quickAddSubmitBtn');
        const buyNowBtn = document.getElementById('quickBuyNowBtn');
        const qtyInput = document.getElementById('quickAddQuantity');

        if (stock > 0) {
            stockEl.textContent = `Còn hàng (${stock} sản phẩm)`;
            stockEl.className = 'fw-semibold text-success small';
            if (submitBtn) submitBtn.disabled = false;
            if (buyNowBtn) buyNowBtn.disabled = false;
            qtyInput.max = stock;
            hideQuickAlert();
        } else {
            stockEl.textContent = 'Hết hàng tạm thời';
            stockEl.className = 'fw-semibold text-danger small';
            if (submitBtn) submitBtn.disabled = true;
            if (buyNowBtn) buyNowBtn.disabled = true;
            showQuickAlert('Phiên bản được chọn hiện đã hết hàng. Vui lòng chọn phân loại khác.');
        }
    }

    function adjustQuickQty(delta) {
        const qtyInput = document.getElementById('quickAddQuantity');
        let current = parseInt(qtyInput.value) || 1;
        let next = current + delta;
        const maxStock = currentQuickVariant ? currentQuickVariant.stock : currentQuickProduct.stock;

        if (next < 1) next = 1;
        if (next > maxStock) {
            next = maxStock;
            showQuickAlert(`Chỉ còn ${maxStock} sản phẩm trong kho.`);
        } else {
            hideQuickAlert();
        }

        qtyInput.value = next;
        updateQuickSubtotal();
    }

    function validateQuickQty(input) {
        let val = parseInt(input.value) || 1;
        const maxStock = currentQuickVariant ? currentQuickVariant.stock : currentQuickProduct.stock;
        if (val < 1) val = 1;
        if (val > maxStock) {
            val = maxStock;
            showQuickAlert(`Số lượng tối đa có thể chọn là ${maxStock}.`);
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

    function escapeHtml(str) {
        if (!str) return '';
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    async function submitQuickAddToCart(event, redirect = false) {
        if (event) event.preventDefault();

        if (currentQuickProduct.has_variants && !currentQuickVariant) {
            showQuickAlert('Vui lòng chọn đầy đủ phân loại sản phẩm.');
            return;
        }

        const submitBtn = document.getElementById('quickAddSubmitBtn');
        const buyNowBtn = document.getElementById('quickBuyNowBtn');
        const originalSubmitHtml = submitBtn.innerHTML;
        const originalBuyHtml = buyNowBtn ? buyNowBtn.innerHTML : '';
        
        if (redirect && buyNowBtn) {
            buyNowBtn.disabled = true;
            buyNowBtn.innerHTML = `<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Đang xử lý...`;
        } else {
            submitBtn.disabled = true;
            submitBtn.innerHTML = `<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Đang thêm...`;
        }

        const productId = document.getElementById('quickAddProductId').value;
        const variantId = document.getElementById('quickAddVariantId').value;
        const quantity = document.getElementById('quickAddQuantity').value;

        const payload = {
            product_id: productId,
            quantity: quantity
        };

        if (variantId) {
            payload.product_variant_id = variantId;
        }

        try {
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            const response = await fetch('{{ route("cart.add") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify(payload)
            });

            const result = await response.json();

            if (response.ok && result.success) {
                updateCartBadge(result.cart_count);

                if (quickAddModalInstance) {
                    quickAddModalInstance.hide();
                }

                if (redirect) {
                    window.location.href = '{{ route("checkout.index") }}';
                } else {
                    showGlobalToast(result.message || 'Đã thêm vào giỏ hàng thành công!', 'success');
                }
            } else {
                showQuickAlert(result.message || 'Không thể thêm vào giỏ hàng. Vui lòng thử lại.');
            }
        } catch (error) {
            showQuickAlert('Đã xảy ra lỗi kết nối. Vui lòng thử lại.');
        } finally {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalSubmitHtml;
            if (buyNowBtn) {
                buyNowBtn.disabled = false;
                buyNowBtn.innerHTML = originalBuyHtml;
            }
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
        } else if (typeof Swal !== 'undefined') {
            Swal.fire({
                text: message,
                icon: type === 'success' ? 'success' : 'info',
                timer: 3000,
                showConfirmButton: false,
                toast: true,
                position: 'top-end'
            });
        }
    }
</script>
