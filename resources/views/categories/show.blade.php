@extends('layouts.app')

@section('title', 'Chi tiết danh mục #' . $category->id)

@section('content')
<div class="row justify-content-center">
    <div class="col-md-9 col-lg-8 col-xl-7">
        <!-- Breadcrumbs -->
        <div class="breadcrumb-modern">
            <a href="{{ route('admin.categories.index') }}">Danh mục</a>
            <i data-lucide="chevron-right" style="width: 14px; height: 14px;"></i>
            <span class="text-primary fw-medium">Chi tiết #{{ str_pad($category->id, 3, '0', STR_PAD_LEFT) }}</span>
        </div>

        <div class="card-modern">
            <!-- Card Header -->
            <div class="card-modern-header d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <div class="category-squircle" style="width: 44px; height: 44px; margin-right: 1rem !important; flex-shrink: 0;">
                        <i data-lucide="folder" style="width: 22px; height: 22px;"></i>
                    </div>
                    <div>
                        <div class="d-flex align-items-center mb-1">
                            <h5 class="fw-bold text-dark mb-0">Chi Tiết Danh Mục</h5>
                            <span class="badge-mono-id" style="margin-left: 0.85rem !important;">#{{ str_pad($category->id, 3, '0', STR_PAD_LEFT) }}</span>
                        </div>
                        <div class="text-secondary small">Thông số kỹ thuật & thuộc tính ngành hàng</div>
                    </div>
                </div>
                <a href="{{ route('admin.categories.index') }}" class="btn-surface" style="padding: 0.55rem 1.1rem; font-size: 0.88rem;">
                    <i data-lucide="arrow-left" style="width: 15px; height: 15px; margin-right: 0.45rem;"></i>
                    <span>Quay lại</span>
                </a>
            </div>
            
            <!-- Card Body with Structured Spec Grid -->
            <div class="card-modern-body">
                <!-- Theme-Aware Category Hero Banner -->
                <div class="hero-banner-modern">
                    <div class="d-flex flex-wrap justify-content-between align-items-start gap-2 mb-2">
                        <span class="text-uppercase text-secondary fw-bold" style="font-size: 0.75rem; letter-spacing: 0.06em;">Tên danh mục</span>
                        <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-1 rounded-pill" style="font-size: 0.75rem;">
                            <i data-lucide="check" style="width: 12px; height: 12px; margin-right: 0.25rem; display: inline-block;"></i>
                            <span>Đang hoạt động</span>
                        </span>
                    </div>
                    <div class="hero-banner-title">
                        {{ $category->name }}
                    </div>
                    <div class="slug-preview-path" style="font-size: 0.85rem;">
                        Đường dẫn hệ thống: /danh-muc/{{ \Illuminate\Support\Str::slug($category->name) }}
                    </div>
                </div>

                <!-- Structured Metadata Grid -->
                <div class="spec-grid-box mb-4">
                    <div class="row g-4">
                        <div class="col-sm-6">
                            <div class="spec-item">
                                <span class="spec-item-label">Mã định danh (ID)</span>
                                <span class="spec-item-value font-monospace text-primary">#{{ $category->id }}</span>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="spec-item">
                                <span class="spec-item-label">Trạng thái lưu trữ</span>
                                <span class="spec-item-value text-success">Đã đồng bộ DB</span>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="spec-item">
                                <span class="spec-item-label">Thời điểm khởi tạo</span>
                                <span class="spec-item-value d-inline-flex align-items-center text-secondary">
                                    <i data-lucide="calendar" class="text-tertiary" style="width: 15px; height: 15px; margin-right: 0.5rem;"></i>
                                    <span>{{ $category->created_at ? $category->created_at->format('d/m/Y H:i:s') : 'Chưa xác định' }}</span>
                                </span>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="spec-item">
                                <span class="spec-item-label">Lần cập nhật cuối</span>
                                <span class="spec-item-value d-inline-flex align-items-center text-secondary">
                                    <i data-lucide="clock" class="text-tertiary" style="width: 15px; height: 15px; margin-right: 0.5rem;"></i>
                                    <span>{{ $category->updated_at ? $category->updated_at->format('d/m/Y H:i:s') : 'Chưa xác định' }}</span>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Toolbar: Only Delete & Edit via Dialogs -->
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 pt-3 border-top">
                    <button 
                        type="button" 
                        class="btn btn-outline-danger px-3.5 py-2 rounded-3 d-inline-flex align-items-center"
                        onclick="openDeleteModal('{{ $category->id }}', '{{ addslashes($category->name) }}')"
                    >
                        <i data-lucide="trash-2" style="width: 15px; height: 15px; margin-right: 0.45rem;"></i>
                        <span>Xóa danh mục</span>
                    </button>

                    <button 
                        type="button" 
                        class="btn-brand-primary"
                        onclick="openEditModal('{{ $category->id }}', '{{ addslashes($category->name) }}')"
                    >
                        <i data-lucide="pencil" style="width: 16px; height: 16px; margin-right: 0.45rem;"></i>
                        <span>Chỉnh sửa danh mục</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- =======================================================================
     EDIT CATEGORY DIALOG MODAL
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
                            <span class="badge-mono-id" style="margin-left: 0.85rem !important;">#{{ str_pad($category->id, 3, '0', STR_PAD_LEFT) }}</span>
                        </div>
                        <div class="text-secondary small">Cập nhật thông tin phân loại sản phẩm</div>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <form id="editCategoryForm" action="{{ route('admin.categories.update', $category) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body-modern">
                    <div class="mb-4">
                        <label for="edit_name" class="form-label-modern mb-2">
                            <span>Tên danh mục</span>
                            <span class="badge bg-danger-subtle text-danger px-2.5 py-1 rounded-pill" style="font-size: 0.72rem;">Bắt buộc</span>
                        </label>
                        <input 
                            type="text" 
                            class="form-control form-control-modern" 
                            id="edit_name" 
                            name="name" 
                            value="{{ $category->name }}"
                            required 
                            autofocus
                            oninput="updateModalSlugPreview('edit_name', 'edit_slug_preview')"
                        >
                    </div>

                    <!-- Slug preview box -->
                    <div class="slug-preview-box">
                        <i data-lucide="link-2" class="text-tertiary" style="width: 16px; height: 16px; margin-right: 0.5rem;"></i>
                        <span>Đường dẫn cập nhật:</span>
                        <span class="slug-preview-path" id="edit_slug_preview">/danh-muc/{{ \Illuminate\Support\Str::slug($category->name) }}</span>
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
     DELETE CONFIRMATION DIALOG MODAL
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
                    Bạn có chắc chắn muốn xóa danh mục <strong id="deleteCategoryName" class="text-dark"></strong> (#<span id="deleteCategoryId"></span>)? Thao tác này không thể hoàn tác.
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

<form id="delete-form-{{ $category->id }}" action="{{ route('admin.categories.destroy', $category) }}" method="POST" class="d-none">
    @csrf
    @method('DELETE')
</form>
@endsection

@section('scripts')
<script>
    let editModalInstance = null;
    let deleteModalInstance = null;

    function generateSlug(text) {
        if (!text || !text.trim()) return 'chua-co-ten';
        return text.toLowerCase()
            .normalize('NFD')
            .replace(/[\u0300-\u036f]/g, '')
            .replace(/[đĐ]/g, 'd')
            .replace(/[^a-z0-9\s-]/g, '')
            .trim()
            .replace(/\s+/g, '-');
    }

    function updateModalSlugPreview(inputId, previewId) {
        const input = document.getElementById(inputId);
        const preview = document.getElementById(previewId);
        if (input && preview) {
            preview.innerText = `/danh-muc/${generateSlug(input.value)}`;
        }
    }

    function openEditModal(id, name) {
        const modalEl = document.getElementById('editCategoryModal');
        if (!editModalInstance) editModalInstance = new bootstrap.Modal(modalEl);
        editModalInstance.show();
        setTimeout(() => {
            document.getElementById('edit_name').focus();
            if (typeof lucide !== 'undefined') lucide.createIcons();
        }, 200);
    }

    function openDeleteModal(id, name) {
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
        const form = document.getElementById('delete-form-{{ $category->id }}');
        if (form) {
            form.submit();
        }
    }
</script>
@endsection
