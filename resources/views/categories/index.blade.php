@extends('layouts.app')

@section('title', 'Quản lý Danh mục Túi Xách Nữ')

@section('content')
<!-- Breadcrumbs & Page Header -->
<div class="mb-4">
    <div class="breadcrumb-modern">
        <a href="{{ route('admin.categories.index') }}">Tổng quan</a>
        <i data-lucide="chevron-right" style="width: 14px; height: 14px;"></i>
        <span class="text-primary fw-medium">Danh mục Túi Xách Nữ</span>
    </div>
    
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mt-2">
        <div>
            <h2 class="fw-bold mb-1 text-dark" style="letter-spacing: -0.03em;">Quản lý Danh mục Túi Xách Nữ</h2>
            <p class="text-secondary mb-0" style="font-size: 0.94rem;">
                Phân nhóm ngành hàng túi xách thời trang (Túi đeo chéo, Túi xách tay, Túi Tote, Kẹp nách, Clutch dạ tiệc)
            </p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <button type="button" class="btn-brand-primary" onclick="openCreateModal()">
                <i data-lucide="plus" style="width: 18px; height: 18px; margin-right: 0.4rem;"></i>
                <span>Thêm danh mục mới</span>
            </button>
        </div>
    </div>
</div>

<!-- KPI / Metric Cards Section -->
<div class="row g-4 mb-4">
    <div class="col-sm-6 col-lg-4">
        <div class="metric-card metric-card-primary">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="metric-label mb-2">Tổng số dòng túi xách</div>
                    <div class="metric-number">{{ method_exists($categories, 'total') ? $categories->total() : $categories->count() }}</div>
                </div>
                <div class="metric-icon-box metric-icon-indigo">
                    <i data-lucide="shopping-bag" style="width: 22px; height: 22px;"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-4">
        <div class="metric-card metric-card-success">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="metric-label mb-2">Trạng thái đồng bộ CSDL</div>
                    <div class="metric-number text-success">100%</div>
                </div>
                <div class="metric-icon-box metric-icon-emerald">
                    <i data-lucide="check-circle-2" style="width: 22px; height: 22px;"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-12 col-lg-4">
        <div class="metric-card metric-card-info">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="metric-label mb-2">Ngành hàng chuyên biệt</div>
                    <div class="metric-number" style="font-size: 1.3rem; font-weight: 700;">Túi Xách Nữ</div>
                </div>
                <div class="metric-icon-box metric-icon-sky">
                    <i data-lucide="sparkles" style="width: 22px; height: 22px;"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Data Table Card Container -->
<div class="card-modern">
    <!-- Card Header with Sleek Integrated Toolbar Strictly Same Row -->
    <div class="card-modern-header d-flex flex-wrap justify-content-between align-items-center gap-3">
        <div class="d-flex align-items-center">
            <span class="fw-bold text-dark" style="font-size: 1.1rem;">Tất cả danh mục</span>
            <span class="badge-count-pill">
                {{ method_exists($categories, 'total') ? $categories->total() : $categories->count() }} bản ghi
            </span>
        </div>
        
        <!-- Controls strictly on the same row (flex-nowrap) -->
        <div class="d-flex align-items-center gap-2.5 flex-nowrap">
            <!-- Modern Integrated Sort Dropdown -->
            <div class="select-box-modern">
                <i data-lucide="arrow-up-down" class="select-icon" style="width: 15px; height: 15px;"></i>
                <select id="categorySortSelect" class="form-select form-select-modern" onchange="applySortCategories()">
                    <option value="created_desc">Mới nhất trước</option>
                    <option value="created_asc">Cũ nhất trước</option>
                    <option value="name_asc">Tên A &rarr; Z</option>
                    <option value="name_desc">Tên Z &rarr; A</option>
                    <option value="id_asc">Mã ID tăng dần</option>
                </select>
            </div>

            <!-- Accent-insensitive Search Box -->
            <div class="search-box-modern">
                <i data-lucide="search" class="search-icon" style="width: 16px; height: 16px;"></i>
                <input 
                    type="text" 
                    id="categorySearchInput" 
                    class="form-control form-control-modern" 
                    placeholder="Tìm theo tên dòng túi..."
                    onkeyup="filterCategoryRows()"
                >
            </div>
        </div>
    </div>

    <!-- Table Content -->
    <div class="table-responsive">
        <table class="table-modern" id="categoryTable">
            <thead>
                <tr>
                    <th style="width: 110px;" class="text-center">Mã ID</th>
                    <th>Tên danh mục & Phân loại Túi Xách</th>
                    <th style="width: 220px;">Ngày tạo</th>
                    <th style="width: 220px;">Cập nhật cuối</th>
                    <th style="width: 100px;" class="text-center">Thao tác</th>
                </tr>
            </thead>
            <tbody id="categoryTableBody">
                @forelse ($categories as $category)
                    @php
                        $slug = \Illuminate\Support\Str::slug($category->name);
                        $createdAt = $category->created_at ? $category->created_at->format('d/m/Y H:i') : '---';
                        $createdTimestamp = $category->created_at ? $category->created_at->timestamp : 0;
                        $updatedAt = $category->updated_at ? $category->updated_at->format('d/m/Y H:i') : '---';
                    @endphp
                    <tr class="category-data-row" 
                        data-id="{{ $category->id }}" 
                        data-name="{{ $category->name }}" 
                        data-created="{{ $createdTimestamp }}">
                        <td class="text-center">
                            <span class="badge-mono-id">#{{ str_pad($category->id, 3, '0', STR_PAD_LEFT) }}</span>
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-3">
                                <div class="category-squircle">
                                    <i data-lucide="shopping-bag" style="width: 18px; height: 18px;"></i>
                                </div>
                                <div>
                                    <a href="{{ route('admin.categories.show', $category) }}" class="category-name-text d-block text-decoration-none">
                                        {{ $category->name }}
                                    </a>
                                    <span class="text-tertiary" style="font-size: 0.8rem;">
                                        /danh-muc/{{ $slug }} &bull; Ngành hàng Túi xách nữ
                                    </span>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-2 text-secondary" style="font-size: 0.875rem;">
                                <i data-lucide="calendar" class="text-tertiary" style="width: 15px; height: 15px;"></i>
                                <span>{{ $createdAt }}</span>
                            </div>
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-2 text-secondary" style="font-size: 0.875rem;">
                                <i data-lucide="clock" class="text-tertiary" style="width: 15px; height: 15px;"></i>
                                <span>{{ $updatedAt }}</span>
                            </div>
                        </td>
                        <td class="text-center">
                            <!-- 3-Dots Action Dropdown -->
                            <div class="dropdown">
                                <button 
                                    class="btn-action-dropdown" 
                                    type="button" 
                                    data-bs-toggle="dropdown" 
                                    data-bs-display="static"
                                    aria-expanded="false"
                                    title="Tùy chọn thao tác"
                                >
                                    <i data-lucide="more-horizontal" style="width: 16px; height: 16px;"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-modern dropdown-menu-end shadow">
                                    <li>
                                        <!-- Direct Link to Dedicated Show Page -->
                                        <a href="{{ route('admin.categories.show', $category) }}" class="dropdown-item-modern">
                                            <i data-lucide="eye" style="width: 16px; height: 16px; margin-right: 0.5rem;"></i>
                                            <span>Xem chi tiết</span>
                                        </a>
                                    </li>
                                    <li>
                                        <button 
                                            type="button" 
                                            class="dropdown-item-modern" 
                                            onclick="openEditModal('{{ $category->id }}', '{{ addslashes($category->name) }}')"
                                        >
                                            <i data-lucide="pencil" style="width: 16px; height: 16px; margin-right: 0.5rem;"></i>
                                            <span>Chỉnh sửa</span>
                                        </button>
                                    </li>
                                    <li><hr class="dropdown-divider-modern"></li>
                                    <li>
                                        <button 
                                            type="button" 
                                            class="dropdown-item-modern item-danger" 
                                            onclick="openDeleteModal('{{ $category->id }}', '{{ addslashes($category->name) }}')"
                                        >
                                            <i data-lucide="trash-2" style="width: 16px; height: 16px; margin-right: 0.5rem;"></i>
                                            <span>Xóa danh mục</span>
                                        </button>
                                    </li>
                                </ul>
                            </div>

                            <form id="delete-form-{{ $category->id }}" action="{{ route('admin.categories.destroy', $category) }}" method="POST" class="d-none">
                                @csrf
                                @method('DELETE')
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center py-5">
                            <div class="py-4">
                                <div class="category-squircle mx-auto mb-3" style="width: 56px; height: 56px;">
                                    <i data-lucide="inbox" style="width: 28px; height: 28px;"></i>
                                </div>
                                <h5 class="fw-bold text-dark mb-1">Chưa có phân loại túi xách nào</h5>
                                <p class="text-secondary small mb-4">Bắt đầu bằng cách tạo danh mục túi xách nữ đầu tiên.</p>
                                <button type="button" class="btn-brand-primary" onclick="openCreateModal()">
                                    <i data-lucide="plus" style="width: 16px; height: 16px; margin-right: 0.4rem;"></i>
                                    <span>Tạo danh mục ngay</span>
                                </button>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination Footer -->
    @if ($categories->hasPages())
        <div class="card-modern-footer d-flex justify-content-end">
            {{ $categories->links('pagination::bootstrap-5') }}
        </div>
    @endif
</div>

<!-- =======================================================================
     DIALOG 1: CREATE CATEGORY MODAL
     ======================================================================= -->
<div class="modal fade" id="createCategoryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 520px;">
        <div class="modal-content modal-content-modern border-0">
            <div class="modal-header-modern">
                <div class="d-flex align-items-center">
                    <div class="category-squircle" style="width: 44px; height: 44px; margin-right: 1rem !important; flex-shrink: 0;">
                        <i data-lucide="folder-plus" style="width: 22px; height: 22px;"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold text-dark mb-1">Thêm Danh Mục Túi Xách</h5>
                        <div class="text-secondary small">Khởi tạo phân nhóm túi xách nữ mới cho sàn TMDT</div>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <form action="{{ route('admin.categories.store') }}" method="POST">
                @csrf
                <div class="modal-body-modern">
                    <div class="mb-4">
                        <label for="create_name" class="form-label-modern mb-2">
                            <span>Tên dòng túi xách</span>
                            <span class="badge bg-danger-subtle text-danger px-2.5 py-1 rounded-pill" style="font-size: 0.72rem;">Bắt buộc</span>
                        </label>
                        <input 
                            type="text" 
                            class="form-control form-control-modern" 
                            id="create_name" 
                            name="name" 
                            placeholder="Ví dụ: Túi Đeo Chéo, Túi Tote, Túi Kẹp Nách..." 
                            required 
                            autofocus
                            oninput="updateModalSlugPreview('create_name', 'create_slug_preview')"
                        >
                    </div>

                    <!-- Slug preview box -->
                    <div class="slug-preview-box">
                        <i data-lucide="link-2" class="text-tertiary" style="width: 16px; height: 16px; margin-right: 0.5rem;"></i>
                        <span>Đường dẫn dự kiến:</span>
                        <span class="slug-preview-path" id="create_slug_preview">/danh-muc/chua-co-ten</span>
                    </div>
                </div>
                
                <div class="modal-footer-modern">
                    <button type="button" class="btn-surface" data-bs-dismiss="modal">Hủy bỏ</button>
                    <button type="submit" class="btn-brand-primary">
                        <i data-lucide="check" style="width: 17px; height: 17px; margin-right: 0.5rem;"></i>
                        <span>Lưu danh mục</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- =======================================================================
     DIALOG 2: EDIT CATEGORY MODAL
     ======================================================================= -->
<div class="modal fade" id="editCategoryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 520px;">
        <div class="modal-content modal-content-modern border-0">
            <div class="modal-header-modern">
                <div class="d-flex align-items-center">
                    <div class="category-squircle" style="width: 44px; height: 44px; margin-right: 1rem !important; flex-shrink: 0; background: var(--warning-50); color: var(--warning-600); border-color: var(--warning-100);">
                        <i data-lucide="pencil" style="width: 22px; height: 22px;"></i>
                    </div>
                    <div>
                        <div class="d-flex align-items-center mb-1">
                            <h5 class="fw-bold text-dark mb-0">Chỉnh Sửa Danh Mục</h5>
                            <span class="badge-mono-id" style="margin-left: 0.85rem !important;" id="editCategoryBadge">#000</span>
                        </div>
                        <div class="text-secondary small">Cập nhật thông tin phân loại túi xách</div>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <form id="editCategoryForm" action="" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body-modern">
                    <div class="mb-4">
                        <label for="edit_name" class="form-label-modern mb-2">
                            <span>Tên dòng túi xách</span>
                            <span class="badge bg-danger-subtle text-danger px-2.5 py-1 rounded-pill" style="font-size: 0.72rem;">Bắt buộc</span>
                        </label>
                        <input 
                            type="text" 
                            class="form-control form-control-modern" 
                            id="edit_name" 
                            name="name" 
                            required 
                            autofocus
                            oninput="updateModalSlugPreview('edit_name', 'edit_slug_preview')"
                        >
                    </div>

                    <!-- Slug preview box -->
                    <div class="slug-preview-box">
                        <i data-lucide="link-2" class="text-tertiary" style="width: 16px; height: 16px; margin-right: 0.5rem;"></i>
                        <span>Đường dẫn cập nhật:</span>
                        <span class="slug-preview-path" id="edit_slug_preview">/danh-muc/...</span>
                    </div>
                </div>
                
                <div class="modal-footer-modern">
                    <button type="button" class="btn-surface" data-bs-dismiss="modal">Hủy bỏ</button>
                    <button type="submit" class="btn-brand-primary">
                        <i data-lucide="refresh-cw" style="width: 17px; height: 17px; margin-right: 0.5rem;"></i>
                        <span>Cập nhật thay đổi</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- =======================================================================
     DIALOG 3: DELETE CONFIRMATION MODAL
     ======================================================================= -->
<div class="modal fade" id="deleteConfirmModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 450px;">
        <div class="modal-content modal-content-modern border-0">
            <div class="p-4 text-center">
                <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 58px; height: 58px; background: var(--danger-50); color: var(--danger-600);">
                    <i data-lucide="alert-triangle" style="width: 28px; height: 28px;"></i>
                </div>
                <h5 class="fw-bold text-dark mb-2">Xác nhận xóa danh mục</h5>
                <p class="text-secondary small mb-4">
                    Bạn có chắc chắn muốn xóa danh mục <strong id="deleteCategoryName" class="text-dark"></strong> (#<span id="deleteCategoryId"></span>)? Toàn bộ sản phẩm thuộc dòng này sẽ bị ảnh hưởng.
                </p>
                <div class="d-flex justify-content-center gap-3">
                    <button type="button" class="btn-surface px-4 py-2" data-bs-dismiss="modal">
                        <span>Hủy bỏ</span>
                    </button>
                    <button type="button" class="btn btn-danger px-4 py-2 rounded-3 fw-semibold d-inline-flex align-items-center" id="confirmDeleteBtn" onclick="submitDeleteForm()">
                        <i data-lucide="trash-2" style="width: 16px; height: 16px; margin-right: 0.4rem;"></i>
                        <span>Xóa vĩnh viễn</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    let activeDeleteId = null;
    let createModalInstance = null;
    let editModalInstance = null;
    let deleteModalInstance = null;

    // Helper: Remove Vietnamese Tones for Accent-Insensitive Search
    function removeVietnameseTones(str) {
        if (!str) return '';
        return str.normalize('NFD')
            .replace(/[\u0300-\u036f]/g, '')
            .replace(/[đĐ]/g, 'd')
            .replace(/[^a-z0-9\s-]/gi, '')
            .toLowerCase()
            .trim();
    }

    // Helper: Convert string to slug
    function generateSlug(text) {
        if (!text || !text.trim()) return 'chua-co-ten';
        return removeVietnameseTones(text).replace(/\s+/g, '-');
    }

    function updateModalSlugPreview(inputId, previewId) {
        const input = document.getElementById(inputId);
        const preview = document.getElementById(previewId);
        if (input && preview) {
            preview.innerText = `/danh-muc/${generateSlug(input.value)}`;
        }
    }

    // Modal Triggers
    function openCreateModal() {
        const modalEl = document.getElementById('createCategoryModal');
        if (!createModalInstance) createModalInstance = new bootstrap.Modal(modalEl);
        document.getElementById('create_name').value = '';
        updateModalSlugPreview('create_name', 'create_slug_preview');
        createModalInstance.show();
        setTimeout(() => {
            document.getElementById('create_name').focus();
            if (typeof lucide !== 'undefined') lucide.createIcons();
        }, 200);
    }

    function openEditModal(id, name) {
        const modalEl = document.getElementById('editCategoryModal');
        if (!editModalInstance) editModalInstance = new bootstrap.Modal(modalEl);
        
        document.getElementById('editCategoryBadge').innerText = '#' + String(id).padStart(3, '0');
        const nameInput = document.getElementById('edit_name');
        nameInput.value = name;
        updateModalSlugPreview('edit_name', 'edit_slug_preview');
        
        const form = document.getElementById('editCategoryForm');
        form.action = `/categories/${id}`;
        
        editModalInstance.show();
        setTimeout(() => {
            nameInput.focus();
            if (typeof lucide !== 'undefined') lucide.createIcons();
        }, 200);
    }

    function openDeleteModal(id, name) {
        activeDeleteId = id;
        document.getElementById('deleteCategoryId').innerText = id;
        document.getElementById('deleteCategoryName').innerText = name;
        
        const modalEl = document.getElementById('deleteConfirmModal');
        if (!deleteModalInstance) deleteModalInstance = new bootstrap.Modal(modalEl);
        deleteModalInstance.show();
        setTimeout(() => {
            if (typeof lucide !== 'undefined') lucide.createIcons();
        }, 200);
    }

    function submitDeleteForm() {
        if (activeDeleteId) {
            const form = document.getElementById('delete-form-' + activeDeleteId);
            if (form) form.submit();
        }
    }

    // Search: Accent-Insensitive Filter
    function filterCategoryRows() {
        const query = removeVietnameseTones(document.getElementById('categorySearchInput').value);
        const rows = document.querySelectorAll('.category-data-row');
        
        rows.forEach(row => {
            const rawName = row.getAttribute('data-name') || '';
            const normalizedName = removeVietnameseTones(rawName);
            const id = row.getAttribute('data-id') || '';
            
            const match = normalizedName.includes(query) || id.includes(query);
            row.style.display = match ? '' : 'none';
        });
    }

    // Sort: Client-side dynamic sort
    function applySortCategories() {
        const sortType = document.getElementById('categorySortSelect').value;
        const tbody = document.getElementById('categoryTableBody');
        const rows = Array.from(tbody.querySelectorAll('.category-data-row'));

        rows.sort((a, b) => {
            const idA = parseInt(a.getAttribute('data-id'), 10) || 0;
            const idB = parseInt(b.getAttribute('data-id'), 10) || 0;
            const nameA = (a.getAttribute('data-name') || '').toLowerCase();
            const nameB = (b.getAttribute('data-name') || '').toLowerCase();
            const createdA = parseInt(a.getAttribute('data-created'), 10) || 0;
            const createdB = parseInt(b.getAttribute('data-created'), 10) || 0;

            switch (sortType) {
                case 'created_desc':
                    return createdB - createdA || idB - idA;
                case 'created_asc':
                    return createdA - createdB || idA - idB;
                case 'name_asc':
                    return nameA.localeCompare(nameB, 'vi');
                case 'name_desc':
                    return nameB.localeCompare(nameA, 'vi');
                case 'id_asc':
                    return idA - idB;
                default:
                    return createdB - createdA;
            }
        });

        // Re-append sorted rows
        rows.forEach(row => tbody.appendChild(row));
    }
</script>
@endsection
