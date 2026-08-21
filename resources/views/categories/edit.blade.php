@extends('layouts.app')

@section('title', 'Chỉnh sửa danh mục #' . $category->id)

@section('content')
<div class="row justify-content-center">
    <div class="col-md-9 col-lg-7 col-xl-6">
        <!-- Breadcrumbs -->
        <div class="breadcrumb-modern">
            <a href="{{ route('categories.index') }}">Danh mục</a>
            <i data-lucide="chevron-right" style="width: 14px; height: 14px;"></i>
            <a href="{{ route('categories.show', $category) }}">#{{ str_pad($category->id, 3, '0', STR_PAD_LEFT) }}</a>
            <i data-lucide="chevron-right" style="width: 14px; height: 14px;"></i>
            <span class="text-primary fw-medium">Chỉnh sửa</span>
        </div>

        <div class="card-modern">
            <!-- Card Header -->
            <div class="card-modern-header d-flex justify-content-between align-items-center">
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
                <a href="{{ route('categories.index') }}" class="btn-surface" style="padding: 0.55rem 1.1rem; font-size: 0.88rem;">
                    <i data-lucide="arrow-left" style="width: 15px; height: 15px; margin-right: 0.45rem;"></i>
                    <span>Quay lại</span>
                </a>
            </div>
            
            <!-- Card Form Body -->
            <div class="card-modern-body">
                <form action="{{ route('categories.update', $category) }}" method="POST" id="editCategoryForm">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-4">
                        <label for="name" class="form-label-modern mb-2">
                            <span>Tên danh mục</span>
                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2.5 py-1 rounded-pill" style="font-size: 0.72rem;">Bắt buộc</span>
                        </label>
                        <div class="position-relative">
                            <input 
                                type="text" 
                                class="form-control form-control-modern @error('name') is-invalid @enderror" 
                                id="name" 
                                name="name" 
                                value="{{ old('name', $category->name) }}" 
                                placeholder="Nhập tên danh mục..." 
                                required 
                                autofocus
                                oninput="updateSlugPreview(this.value)"
                            >
                        </div>
                        @error('name')
                            <div class="invalid-feedback d-block mt-1">
                                {{ $message }}
                            </div>
                        @enderror
                        <div class="form-text text-secondary small mt-2">
                            Mã định danh hệ thống: <strong class="badge-mono-id">#{{ $category->id }}</strong>. Cập nhật tên sẽ làm mới dữ liệu liên kết.
                        </div>
                    </div>

                    <!-- Slug Preview Widget -->
                    <div class="mb-4">
                        <div class="slug-preview-box">
                            <i data-lucide="link-2" class="text-tertiary" style="width: 16px; height: 16px; margin-right: 0.5rem;"></i>
                            <span>Đường dẫn liên kết cập nhật:</span>
                            <span class="slug-preview-path" id="slugPreviewText">/danh-muc/...</span>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end align-items-center gap-3 pt-3 border-top">
                        <a href="{{ route('categories.index') }}" class="btn-surface">
                            <span>Hủy bỏ</span>
                        </a>
                        <button type="submit" class="btn-brand-primary">
                            <i data-lucide="refresh-cw" style="width: 17px; height: 17px; margin-right: 0.45rem;"></i>
                            <span>Cập nhật thay đổi</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function updateSlugPreview(val) {
        const previewEl = document.getElementById('slugPreviewText');
        if (!val || !val.trim()) {
            previewEl.innerText = '/danh-muc/chua-co-ten';
            return;
        }

        let slug = val.toLowerCase()
            .normalize('NFD')
            .replace(/[\u0300-\u036f]/g, '')
            .replace(/[đĐ]/g, 'd')
            .replace(/[^a-z0-9\s-]/g, '')
            .trim()
            .replace(/\s+/g, '-');

        previewEl.innerText = `/danh-muc/${slug || 'chua-co-ten'}`;
    }

    document.addEventListener('DOMContentLoaded', () => {
        const input = document.getElementById('name');
        if (input && input.value) {
            updateSlugPreview(input.value);
        }
    });
</script>
@endsection
