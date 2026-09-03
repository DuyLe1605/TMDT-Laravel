@extends('layouts.app')

@section('title', 'Thêm Sản Phẩm Túi Xách Mới')

@section('content')
<div class="row justify-content-center">
    <div class="col-xl-11 col-xxl-10">
        <!-- Breadcrumbs -->
        <div class="breadcrumb-modern mb-3">
            <a href="{{ route('admin.products.index') }}">Sản phẩm</a>
            <i data-lucide="chevron-right" style="width: 14px; height: 14px;"></i>
            <span class="text-primary fw-medium">Thêm túi xách mới</span>
        </div>

        <form action="{{ route('admin.products.store') }}" method="POST" id="productForm">
            @csrf

            <!-- Form Top Header -->
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
                <div>
                    <h3 class="fw-bold text-dark mb-1" style="letter-spacing: -0.02em;">Thêm Mới Sản Phẩm Túi Xách</h3>
                    <p class="text-secondary small mb-0">
                        Khai báo thông tin thương hiệu, phân cấp danh mục và thiết lập ma trận biến thể thông minh
                    </p>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <a href="{{ route('admin.products.index') }}" class="btn-surface">
                        <i data-lucide="arrow-left" style="width: 15px; height: 15px; margin-right: 0.4rem;"></i>
                        <span>Quay lại</span>
                    </a>
                    <button type="submit" class="btn-brand-primary">
                        <i data-lucide="check" style="width: 16px; height: 16px; margin-right: 0.4rem;"></i>
                        <span>Lưu sản phẩm</span>
                    </button>
                </div>
            </div>

            <div class="row g-4">
                <!-- LEFT COLUMN: Main Form Cards -->
                <div class="col-lg-8">
                    <!-- CARD 1: Thông tin cơ bản -->
                    <div class="card-modern p-4 mb-4">
                        <h5 class="fw-bold text-dark mb-3 pb-2 border-bottom d-flex align-items-center gap-2">
                            <i data-lucide="info" class="text-primary" style="width: 18px; height: 18px;"></i>
                            <span>Thông tin cơ bản</span>
                        </h5>

                        <div class="row g-3">
                            <div class="col-12">
                                <label for="name" class="form-label-modern mb-1.5">
                                    <span>Tên sản phẩm túi xách</span>
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-0.5 rounded-pill" style="font-size: 0.7rem;">Bắt buộc</span>
                                </label>
                                <input 
                                    type="text" 
                                    class="form-control form-control-modern @error('name') is-invalid @enderror" 
                                    id="name" 
                                    name="name" 
                                    value="{{ old('name') }}" 
                                    placeholder="Ví dụ: Túi Hermès Constance Box Da Thật Epsom..." 
                                    required 
                                    autofocus
                                    oninput="onProductNameInput(this.value)"
                                >
                                @error('name')
                                    <div class="invalid-feedback d-block mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="brand_id" class="form-label-modern mb-1.5">
                                    <span>Thương hiệu nhà mốt</span>
                                </label>
                                <select class="form-select form-control-modern @error('brand_id') is-invalid @enderror" id="brand_id" name="brand_id">
                                    <option value="">-- Không chọn thương hiệu --</option>
                                    @foreach ($brands as $brand)
                                        <option value="{{ $brand->id }}" {{ old('brand_id') == $brand->id ? 'selected' : '' }}>
                                            👑 {{ $brand->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('brand_id')
                                    <div class="invalid-feedback d-block mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="category_id" class="form-label-modern mb-1.5">
                                    <span>Dòng túi xách (Danh mục)</span>
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-0.5 rounded-pill" style="font-size: 0.7rem;">Bắt buộc</span>
                                </label>
                                <select class="form-select form-control-modern @error('category_id') is-invalid @enderror" id="category_id" name="category_id" required>
                                    <option value="">-- Chọn dòng túi xách --</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                            {{ $category->parent_id ? '↳ ' . $category->name : '📁 ' . $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('category_id')
                                    <div class="invalid-feedback d-block mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label for="description" class="form-label-modern mb-1.5">
                                    <span>Mô tả chi tiết & Đặc tính túi xách</span>
                                </label>
                                <textarea 
                                    class="form-control form-control-modern" 
                                    id="description" 
                                    name="description" 
                                    rows="4" 
                                    placeholder="Mô tả chất liệu da, khóa xoay mạ vàng, số lượng ngăn chứa đồ, phụ kiện đi kèm..."
                                >{{ old('description') }}</textarea>
                            </div>
                        </div>
                    </div>

                    <!-- CARD 2: Cấu hình Biến Thể Thông Minh (Smart Variant Engine) -->
                    <div class="card-modern p-4 mb-4">
                        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 pb-3 border-bottom mb-3">
                            <div>
                                <h5 class="fw-bold text-dark mb-1 d-flex align-items-center gap-2">
                                    <i data-lucide="layers" class="text-primary" style="width: 18px; height: 18px;"></i>
                                    <span>Phân Loại & Biến Thể Thông Minh</span>
                                </h5>
                                <div class="text-secondary small">
                                    Tạo các tùy chọn phân loại (như <em>Chất liệu: Da thật / Da PU</em>, <em>Màu sắc: Cam / Đen</em>, <em>Kích thước: 20 / 25</em>)
                                </div>
                            </div>
                            <div class="form-check form-switch form-switch-lg mb-0">
                                <input 
                                    class="form-check-input cursor-pointer" 
                                    type="checkbox" 
                                    id="has_variants" 
                                    name="has_variants" 
                                    value="1" 
                                    {{ old('has_variants') ? 'checked' : '' }}
                                    onchange="toggleVariantsMode(this.checked)"
                                >
                                <label class="form-check-label fw-bold text-dark user-select-none cursor-pointer" for="has_variants">
                                    Sản phẩm có nhiều biến thể
                                </label>
                            </div>
                        </div>

                        <!-- SECTION A: Đơn giá mặc định (Khi tắt biến thể) -->
                        <div id="simplePricingSection" class="{{ old('has_variants') ? 'd-none' : '' }}">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label for="price" class="form-label-modern mb-1.5">
                                        <span>Giá bán gốc (VNĐ)</span>
                                        <span class="badge bg-danger-subtle text-danger border px-2 py-0.5 rounded-pill" style="font-size: 0.7rem;">Bắt buộc</span>
                                    </label>
                                    <input 
                                        type="number" 
                                        step="1000" 
                                        class="form-control form-control-modern @error('price') is-invalid @enderror" 
                                        id="price" 
                                        name="price" 
                                        value="{{ old('price', '750000') }}" 
                                        placeholder="750000"
                                    >
                                    @error('price')
                                        <div class="invalid-feedback d-block mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4">
                                    <label for="sale_price" class="form-label-modern mb-1.5">
                                        <span>Giá khuyến mãi (VNĐ)</span>
                                    </label>
                                    <input 
                                        type="number" 
                                        step="1000" 
                                        class="form-control form-control-modern @error('sale_price') is-invalid @enderror" 
                                        id="sale_price" 
                                        name="sale_price" 
                                        value="{{ old('sale_price') }}" 
                                        placeholder="590000"
                                    >
                                </div>
                                <div class="col-md-4">
                                    <label for="stock" class="form-label-modern mb-1.5">
                                        <span>Số lượng tồn kho</span>
                                        <span class="badge bg-danger-subtle text-danger border px-2 py-0.5 rounded-pill" style="font-size: 0.7rem;">Bắt buộc</span>
                                    </label>
                                    <input 
                                        type="number" 
                                        class="form-control form-control-modern @error('stock') is-invalid @enderror" 
                                        id="stock" 
                                        name="stock" 
                                        value="{{ old('stock', '50') }}" 
                                        min="0"
                                    >
                                </div>
                            </div>
                        </div>

                        <!-- SECTION B: Ma trận biến thể thông minh (Khi bật biến thể) -->
                        <div id="variantsEngineSection" class="{{ old('has_variants') ? '' : 'd-none' }}">
                            <!-- Attribute Groups Container -->
                            <div class="mb-4">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <span class="fw-bold text-dark small text-uppercase">Các Nhóm Phân Loại (Tối đa 3 nhóm):</span>
                                    <button type="button" id="btnAddAttrGroup" class="btn btn-sm btn-outline-primary rounded-pill px-3 py-1 fw-semibold d-inline-flex align-items-center gap-1" onclick="addAttributeGroup()">
                                        <i data-lucide="plus" style="width: 14px; height: 14px;"></i>
                                        <span>Thêm nhóm phân loại</span>
                                    </button>
                                </div>

                                <div id="attributeGroupsWrapper" class="d-flex flex-column gap-3">
                                    <!-- Dynamic attribute group cards injected by JS -->
                                </div>
                            </div>

                            <!-- Variant Matrix Table Header & Bulk Actions -->
                            <div class="mt-4 pt-3 border-top" id="matrixContainer">
                                <div class="p-3 rounded-3 mb-3" style="background: var(--bg-surface-subtle); border: 1px solid var(--border-default);">
                                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="fw-bold text-dark small">⚡ ÁP DỤNG HÀNG LOẠT CHO TẤT CẢ BIẾN THỂ:</span>
                                        </div>
                                        <div class="d-flex flex-wrap align-items-center gap-2">
                                            <input type="number" id="bulkPrice" placeholder="Giá bán (VNĐ)" class="form-control form-control-sm form-control-modern" style="width: 140px;">
                                            <input type="number" id="bulkSalePrice" placeholder="Giá sale (VNĐ)" class="form-control form-control-sm form-control-modern" style="width: 140px;">
                                            <input type="number" id="bulkStock" placeholder="Tồn kho" class="form-control form-control-sm form-control-modern" style="width: 100px;">
                                            <button type="button" class="btn btn-sm btn-brand-primary px-3" onclick="applyBulkToMatrix()">
                                                Áp dụng
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <!-- The Matrix Table -->
                                <div class="table-responsive rounded-3 border">
                                    <table class="table table-hover align-middle mb-0" id="matrixTable">
                                        <thead style="background: var(--bg-surface-subtle);">
                                            <tr>
                                                <th style="width: 220px;">Biến thể phân loại</th>
                                                <th style="width: 160px;">Mã SKU</th>
                                                <th style="width: 140px;">Giá bán (VNĐ) *</th>
                                                <th style="width: 140px;">Giá sale (VNĐ)</th>
                                                <th style="width: 100px;">Tồn kho *</th>
                                                <th>Ảnh riêng (URL)</th>
                                                <th style="width: 70px;" class="text-center">Bật</th>
                                            </tr>
                                        </thead>
                                        <tbody id="matrixTableBody">
                                            <!-- Dynamically generated rows -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- RIGHT COLUMN: Secondary Info & Actions -->
                <div class="col-lg-4">
                    <!-- CARD 3: Ảnh chính & Nhận diện -->
                    <div class="card-modern p-4 mb-4">
                        <h6 class="fw-bold text-dark mb-3 pb-2 border-bottom d-flex align-items-center gap-2">
                            <i data-lucide="image" class="text-primary" style="width: 16px; height: 16px;"></i>
                            <span>Ảnh đại diện chính</span>
                        </h6>

                        <div class="mb-3">
                            <label for="image" class="form-label-modern mb-1.5">Đường dẫn ảnh (Image URL)</label>
                            <input 
                                type="url" 
                                class="form-control form-control-modern" 
                                id="image" 
                                name="image" 
                                value="{{ old('image') }}" 
                                placeholder="https://images.unsplash.com/photo-..."
                                oninput="previewMainImage(this.value)"
                            >
                        </div>

                        <!-- Main Image Preview Box -->
                        <div class="rounded-3 border overflow-hidden d-flex align-items-center justify-content-center mb-3" style="height: 190px; background: var(--bg-surface-subtle);" id="mainImagePreviewBox">
                            <i data-lucide="shopping-bag" style="width: 48px; height: 48px;" class="text-secondary opacity-50"></i>
                        </div>

                        <div class="mb-3">
                            <label for="sku" class="form-label-modern mb-1.5">Mã SKU sản phẩm gốc</label>
                            <input 
                                type="text" 
                                class="form-control form-control-modern" 
                                id="sku" 
                                name="sku" 
                                value="{{ old('sku') }}" 
                                placeholder="Để trống hệ thống tự sinh..."
                            >
                        </div>
                    </div>

                    <!-- CARD 4: Thông số kỹ thuật nhanh -->
                    <div class="card-modern p-4 mb-4">
                        <h6 class="fw-bold text-dark mb-3 pb-2 border-bottom d-flex align-items-center gap-2">
                            <i data-lucide="sliders" class="text-primary" style="width: 16px; height: 16px;"></i>
                            <span>Thông số kỹ thuật</span>
                        </h6>

                        <div class="mb-3">
                            <label for="material" class="form-label-modern mb-1">Chất liệu chính</label>
                            <input type="text" class="form-control form-control-modern" id="material" name="material" value="{{ old('material', 'Da bò Epsom / Da PU') }}" placeholder="Ví dụ: Da bò thật, Da Togo...">
                        </div>

                        <div class="mb-3">
                            <label for="dimensions" class="form-label-modern mb-1">Kích thước (D x R x C)</label>
                            <input type="text" class="form-control form-control-modern" id="dimensions" name="dimensions" value="{{ old('dimensions', '22 x 8 x 15 cm') }}" placeholder="Ví dụ: 20 x 7 x 14 cm">
                        </div>

                        <div class="mb-3">
                            <label for="color" class="form-label-modern mb-1">Màu sắc chủ đạo</label>
                            <input type="text" class="form-control form-control-modern" id="color" name="color" value="{{ old('color', 'Cam Hermès, Đen, Trắng') }}" placeholder="Ví dụ: Cam Hermès, Đen...">
                        </div>
                    </div>

                    <!-- CARD 5: Trạng thái hiển thị -->
                    <div class="card-modern p-4">
                        <h6 class="fw-bold text-dark mb-3 pb-2 border-bottom">Trạng thái phát hành</h6>

                        <div class="d-flex flex-column gap-2.5">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', '1') ? 'checked' : '' }}>
                                <label class="form-check-label text-dark fw-medium" for="is_active">
                                    Mở bán ngay (Hiển thị Storefront)
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="is_featured" name="is_featured" value="1" {{ old('is_featured') ? 'checked' : '' }}>
                                <label class="form-check-label text-dark fw-medium" for="is_featured">
                                    ⭐ Đánh dấu là sản phẩm nổi bật
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // State management for Attribute Groups & Variants Matrix
    let attributeGroups = [
        { name: 'Chất liệu', values: ['Da thật Epsom', 'Da PU cao cấp'] },
        { name: 'Màu sắc', values: ['Cam Hermès', 'Đen Obsidian'] }
    ];

    // Cache of custom inputs per variant signature
    let variantValuesCache = {};

    function toggleVariantsMode(hasVariants) {
        const simpleSection = document.getElementById('simplePricingSection');
        const variantsSection = document.getElementById('variantsEngineSection');

        if (hasVariants) {
            simpleSection.classList.add('d-none');
            variantsSection.classList.remove('d-none');
            renderAttributeGroups();
            generateMatrix();
        } else {
            simpleSection.classList.remove('d-none');
            variantsSection.classList.add('d-none');
        }
    }

    function onProductNameInput(val) {
        // Can be used for SKU or slug recommendations
    }

    function previewMainImage(url) {
        const box = document.getElementById('mainImagePreviewBox');
        if (!url || !url.trim()) {
            box.innerHTML = `<i data-lucide="shopping-bag" style="width: 48px; height: 48px;" class="text-secondary opacity-50"></i>`;
        } else {
            box.innerHTML = `<img src="${url}" alt="Preview" class="w-100 h-100 object-fit-cover">`;
        }
        if (typeof lucide !== 'undefined') lucide.createIcons();
    }

    function addAttributeGroup() {
        if (attributeGroups.length >= 3) {
            if (window.showToast) window.showToast('Chỉ có thể tạo tối đa 3 nhóm phân loại.', 'warning');
            return;
        }

        const defaultNames = ['Kích thước', 'Phụ kiện', 'Dòng da'];
        const nextName = defaultNames[attributeGroups.length] || 'Phân loại ' + (attributeGroups.length + 1);
        attributeGroups.push({ name: nextName, values: [] });
        renderAttributeGroups();
        generateMatrix();
    }

    function removeAttributeGroup(index) {
        attributeGroups.splice(index, 1);
        renderAttributeGroups();
        generateMatrix();
    }

    function updateGroupName(index, newName) {
        attributeGroups[index].name = newName;
        generateMatrix();
    }

    function addAttributeValue(groupIndex, value) {
        const val = value.trim();
        if (!val) return;

        if (!attributeGroups[groupIndex].values.includes(val)) {
            attributeGroups[groupIndex].values.push(val);
            renderAttributeGroups();
            generateMatrix();
        }
    }

    function removeAttributeValue(groupIndex, valIndex) {
        attributeGroups[groupIndex].values.splice(valIndex, 1);
        renderAttributeGroups();
        generateMatrix();
    }

    function renderAttributeGroups() {
        const wrapper = document.getElementById('attributeGroupsWrapper');
        const btnAdd = document.getElementById('btnAddAttrGroup');
        if (!wrapper) return;

        if (btnAdd) {
            btnAdd.style.display = attributeGroups.length >= 3 ? 'none' : 'inline-flex';
        }

        wrapper.innerHTML = '';

        attributeGroups.forEach((group, gIdx) => {
            const groupCard = document.createElement('div');
            groupCard.className = 'p-3 rounded-3 border bg-white';

            let tagsHtml = group.values.map((v, vIdx) => `
                <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2.5 py-1.5 rounded-pill d-inline-flex align-items-center gap-1.5" style="font-size: 0.82rem;">
                    <span>${v}</span>
                    <button type="button" class="btn-close btn-close-white" style="font-size: 0.6rem; filter: invert(0.3);" onclick="removeAttributeValue(${gIdx}, ${vIdx})" title="Xóa giá trị"></button>
                </span>
            `).join('');

            groupCard.innerHTML = `
                <div class="d-flex align-items-center justify-content-between gap-3 mb-2">
                    <div class="d-flex align-items-center gap-2 flex-grow-1">
                        <span class="badge bg-dark rounded-circle p-1 d-inline-flex align-items-center justify-content-center text-white" style="width: 22px; height: 22px; font-size: 0.75rem;">${gIdx + 1}</span>
                        <input 
                            type="text" 
                            class="form-control form-control-sm form-control-modern fw-bold text-dark" 
                            style="max-width: 200px;" 
                            value="${group.name}" 
                            placeholder="Tên nhóm (ví dụ: Chất liệu)"
                            onchange="updateGroupName(${gIdx}, this.value)"
                        >
                        <span class="text-secondary small d-none d-md-inline">Ví dụ gợi ý: 
                            <a href="javascript:void(0)" onclick="updateGroupName(${gIdx}, 'Chất liệu'); renderAttributeGroups();" class="text-decoration-none">Chất liệu</a>, 
                            <a href="javascript:void(0)" onclick="updateGroupName(${gIdx}, 'Màu sắc'); renderAttributeGroups();" class="text-decoration-none">Màu sắc</a>, 
                            <a href="javascript:void(0)" onclick="updateGroupName(${gIdx}, 'Kích thước'); renderAttributeGroups();" class="text-decoration-none">Kích thước</a>
                        </span>
                    </div>
                    <button type="button" class="btn btn-sm btn-link text-danger p-0" onclick="removeAttributeGroup(${gIdx})" title="Xóa nhóm phân loại này">
                        <i data-lucide="trash-2" style="width: 16px; height: 16px;"></i>
                    </button>
                </div>

                <!-- Hidden inputs for backend submission -->
                <input type="hidden" name="attributes[${gIdx}][name]" value="${group.name}">
                <input type="hidden" name="attributes[${gIdx}][values]" value="${group.values.join(',')}">

                <!-- Tags list & Tag input -->
                <div class="d-flex flex-wrap align-items-center gap-2 pt-2 border-top">
                    ${tagsHtml}
                    <input 
                        type="text" 
                        class="form-control form-control-sm form-control-modern" 
                        style="width: 190px;" 
                        placeholder="+ Nhập tag rồi Enter..." 
                        onkeydown="if(event.key === 'Enter' || event.key === ',') { event.preventDefault(); addAttributeValue(${gIdx}, this.value); this.value=''; }"
                    >
                </div>
            `;

            wrapper.appendChild(groupCard);
        });

        if (typeof lucide !== 'undefined') lucide.createIcons();
    }

    // Cartesian Product of arrays of values
    function cartesian(arrays) {
        return arrays.reduce((acc, curr) => {
            return acc.flatMap(a => curr.map(c => [...a, c]));
        }, [[]]);
    }

    // Cache current matrix table inputs so user doesn't lose inputs on dynamic re-renders
    function saveCurrentMatrixInputs() {
        const rows = document.querySelectorAll('#matrixTableBody tr');
        rows.forEach(row => {
            const sig = row.getAttribute('data-sig');
            if (sig) {
                variantValuesCache[sig] = {
                    sku: row.querySelector('.var-sku')?.value || '',
                    price: row.querySelector('.var-price')?.value || '',
                    sale_price: row.querySelector('.var-sale-price')?.value || '',
                    stock: row.querySelector('.var-stock')?.value || '',
                    image: row.querySelector('.var-image')?.value || '',
                    is_active: row.querySelector('.var-active')?.checked ?? true,
                };
            }
        });
    }

    function generateMatrix() {
        saveCurrentMatrixInputs();

        const activeGroups = attributeGroups.filter(g => g.name.trim() !== '' && g.values.length > 0);
        const tbody = document.getElementById('matrixTableBody');
        if (!tbody) return;

        if (activeGroups.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="7" class="text-center py-4 text-muted small">
                        Vui lòng thêm ít nhất 1 giá trị phân loại vào các nhóm ở trên để sinh bảng cấu hình ma trận biến thể.
                    </td>
                </tr>
            `;
            return;
        }

        const valueSets = activeGroups.map(g => g.values);
        const combinations = cartesian(valueSets);

        let defaultBasePrice = document.getElementById('price')?.value || '750000';
        let defaultBaseSalePrice = document.getElementById('sale_price')?.value || '';
        let defaultBaseStock = document.getElementById('stock')?.value || '20';
        let mainImage = document.getElementById('image')?.value || '';

        tbody.innerHTML = '';

        combinations.forEach((combo, idx) => {
            const sig = combo.join(' / ');
            const cached = variantValuesCache[sig] || {};

            const sku = cached.sku || ('BAG-' + combo.map(v => v.substring(0, 3).toUpperCase().replace(/[^A-Z0-9]/g, '')).join('-'));
            const price = cached.price || defaultBasePrice;
            const salePrice = cached.sale_price !== undefined ? cached.sale_price : defaultBaseSalePrice;
            const stock = cached.stock || defaultBaseStock;
            const image = cached.image || mainImage;
            const isActive = cached.is_active !== undefined ? cached.is_active : true;

            const opt1 = combo[0] || '';
            const opt2 = combo[1] || '';
            const opt3 = combo[2] || '';

            const tr = document.createElement('tr');
            tr.setAttribute('data-sig', sig);
            tr.innerHTML = `
                <td>
                    <div class="fw-bold text-dark small">${sig}</div>
                    <input type="hidden" name="variants[${idx}][variant_title]" value="${sig}">
                    <input type="hidden" name="variants[${idx}][option1_value]" value="${opt1}">
                    <input type="hidden" name="variants[${idx}][option2_value]" value="${opt2}">
                    <input type="hidden" name="variants[${idx}][option3_value]" value="${opt3}">
                </td>
                <td>
                    <input type="text" name="variants[${idx}][sku]" value="${sku}" class="form-control form-control-sm form-control-modern var-sku">
                </td>
                <td>
                    <input type="number" step="1000" name="variants[${idx}][price]" value="${price}" class="form-control form-control-sm form-control-modern var-price" required>
                </td>
                <td>
                    <input type="number" step="1000" name="variants[${idx}][sale_price]" value="${salePrice}" class="form-control form-control-sm form-control-modern var-sale-price">
                </td>
                <td>
                    <input type="number" name="variants[${idx}][stock]" value="${stock}" class="form-control form-control-sm form-control-modern var-stock" min="0" required>
                </td>
                <td>
                    <input type="url" name="variants[${idx}][image]" value="${image}" placeholder="URL ảnh riêng..." class="form-control form-control-sm form-control-modern var-image">
                </td>
                <td class="text-center">
                    <input type="checkbox" name="variants[${idx}][is_active]" value="1" class="form-check-input var-active" ${isActive ? 'checked' : ''}>
                </td>
            `;

            tbody.appendChild(tr);
        });
    }

    function applyBulkToMatrix() {
        const bulkPrice = document.getElementById('bulkPrice').value;
        const bulkSalePrice = document.getElementById('bulkSalePrice').value;
        const bulkStock = document.getElementById('bulkStock').value;

        const rows = document.querySelectorAll('#matrixTableBody tr');
        let count = 0;

        rows.forEach(row => {
            if (bulkPrice) {
                const pInput = row.querySelector('.var-price');
                if (pInput) pInput.value = bulkPrice;
            }
            if (bulkSalePrice) {
                const spInput = row.querySelector('.var-sale-price');
                if (spInput) spInput.value = bulkSalePrice;
            }
            if (bulkStock) {
                const sInput = row.querySelector('.var-stock');
                if (sInput) sInput.value = bulkStock;
            }
            count++;
        });

        if (window.showToast) {
            window.showToast(`Đã áp dụng thông số đồng loạt cho ${count} biến thể!`, 'success');
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        const hasVariants = document.getElementById('has_variants')?.checked;
        if (hasVariants) {
            renderAttributeGroups();
            generateMatrix();
        }
        if (typeof lucide !== 'undefined') lucide.createIcons();
    });
</script>
@endsection
