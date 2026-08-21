@extends('layouts.app')

@section('title', 'Thêm danh mục mới')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-9 col-lg-7 col-xl-6">
        <!-- Breadcrumbs -->
        <div class="breadcrumb-modern">
            <a href="{{ route('categories.index') }}">Danh mục</a>
            <i data-lucide="chevron-right" style="width: 14px; height: 14px;"></i>
            <span class="text-primary fw-medium">Thêm mới</span>
        </div>

        <div class="card-modern">
            <!-- Card Header -->
            <div class="card-modern-header d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <div class="category-squircle" style="width: 44px; height: 44px; margin-right: 1rem !important; flex-shrink: 0;">
                        <i data-lucide="folder-plus" style="width: 22px; height: 22px;"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold mb-1 text-dark" style="letter-spacing: -0.02em;">Thêm Danh Mục Mới</h5>
                        <div class="text-secondary small">Khởi tạo nhóm ngành hàng mới cho hệ thống</div>
                    </div>
                </div>
                <a href="{{ route('categories.index') }}" class="btn-surface" style="padding: 0.55rem 1.1rem; font-size: 0.88rem;">
                    <i data-lucide="arrow-left" style="width: 15px; height: 15px; margin-right: 0.45rem;"></i>
                    <span>Quay lại</span>
                </a>
            </div>
            
            <!-- Card Form Body -->
            <div class="card-modern-body">
                <form action="{{ route('categories.store') }}" method="POST" id="createCategoryForm">
                    @csrf
                    
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
                                value="{{ old('name') }}" 
                                placeholder="Ví dụ: Thiết bị di động, Thời trang nam..." 
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
                            Tên danh mục là duy nhất và đại diện cho nhóm sản phẩm trên sàn TMDT.
                        </div>
                    </div>

                    <!-- Slug Preview Widget -->
                    <div class="mb-4">
                        <div class="slug-preview-box">
                            <i data-lucide="link-2" class="text-tertiary" style="width: 16px; height: 16px; margin-right: 0.5rem;"></i>
                            <span>Đường dẫn liên kết dự kiến:</span>
                            <span class="slug-preview-path" id="slugPreviewText">/danh-muc/chua-co-ten</span>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end align-items-center gap-3 pt-3 border-top">
                        <a href="{{ route('categories.index') }}" class="btn-surface">
                            <span>Hủy bỏ</span>
                        </a>
                        <button type="submit" class="btn-brand-primary">
                            <i data-lucide="check" style="width: 17px; height: 17px; margin-right: 0.45rem;"></i>
                            <span>Lưu danh mục</span>
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

        // Convert Vietnamese accents and spaces to slug
        let slug = val.toLowerCase()
            .normalize('NFD')
            .replace(/[\u0300-\u036f]/g, '')
            .replace(/[đĐ]/g, 'd')
            .replace(/[^a-z0-9\s-]/g, '')
            .trim()
            .replace(/\s+/g, '-');

        previewEl.innerText = `/danh-muc/${slug || 'chua-co-ten'}`;
    }

    // Trigger on initial load if old value exists
    document.addEventListener('DOMContentLoaded', () => {
        const input = document.getElementById('name');
        if (input && input.value) {
            updateSlugPreview(input.value);
        }
    });
</script>
@endsection
