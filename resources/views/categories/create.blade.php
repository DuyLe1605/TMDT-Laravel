@extends('layouts.app')

@section('title', 'Thêm danh mục mới')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-9 col-lg-8 col-xl-7">
        <!-- Breadcrumbs -->
        <div class="breadcrumb-modern mb-3">
            <a href="{{ route('admin.categories.index') }}">Danh mục</a>
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
                        <div class="text-secondary small">Hỗ trợ phân cấp Cha - Con cho ngành hàng túi xách</div>
                    </div>
                </div>
                <a href="{{ route('admin.categories.index') }}" class="btn-surface" style="padding: 0.55rem 1.1rem; font-size: 0.88rem;">
                    <i data-lucide="arrow-left" style="width: 15px; height: 15px; margin-right: 0.45rem;"></i>
                    <span>Quay lại</span>
                </a>
            </div>
            
            <!-- Card Form Body -->
            <div class="card-modern-body p-4">
                <form action="{{ route('admin.categories.store') }}" method="POST" id="createCategoryForm">
                    @csrf
                    
                    <div class="row g-4">
                        <!-- Danh mục cha (Parent Category) -->
                        <div class="col-12">
                            <label for="parent_id" class="form-label-modern mb-2">
                                <span>Danh mục cha (Tùy chọn - Liên kết Cha/Con)</span>
                            </label>
                            <select class="form-select form-control-modern @error('parent_id') is-invalid @enderror" id="parent_id" name="parent_id">
                                <option value="">-- Là danh mục gốc (Cấp cao nhất) --</option>
                                @foreach ($parentCategories as $parent)
                                    <option value="{{ $parent->id }}" {{ old('parent_id') == $parent->id ? 'selected' : '' }}>
                                        📁 {{ $parent->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('parent_id')
                                <div class="invalid-feedback d-block mt-1">{{ $message }}</div>
                            @enderror
                            <div class="form-text text-secondary small mt-1">
                                Ví dụ: Nếu chọn "Túi Xách Nữ", danh mục này sẽ là con (sub-category) như Túi đeo chéo, Túi xách tay...
                            </div>
                        </div>

                        <!-- Tên danh mục -->
                        <div class="col-12">
                            <label for="name" class="form-label-modern mb-2">
                                <span>Tên danh mục</span>
                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2.5 py-1 rounded-pill" style="font-size: 0.72rem;">Bắt buộc</span>
                            </label>
                            <input 
                                type="text" 
                                class="form-control form-control-modern @error('name') is-invalid @enderror" 
                                id="name" 
                                name="name" 
                                value="{{ old('name') }}" 
                                placeholder="Ví dụ: Túi Đeo Chéo Nữ, Túi Kẹp Nách Y2K..." 
                                required 
                                autofocus
                            >
                            @error('name')
                                <div class="invalid-feedback d-block mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Ảnh minh họa danh mục -->
                        <div class="col-12">
                            <label for="image" class="form-label-modern mb-2">
                                <span>Ảnh đại diện danh mục (Image URL)</span>
                            </label>
                            <input 
                                type="url" 
                                class="form-control form-control-modern @error('image') is-invalid @enderror" 
                                id="image" 
                                name="image" 
                                value="{{ old('image') }}" 
                                placeholder="https://images.unsplash.com/photo-..."
                            >
                            @error('image')
                                <div class="invalid-feedback d-block mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Mô tả danh mục -->
                        <div class="col-12">
                            <label for="description" class="form-label-modern mb-2">
                                <span>Mô tả ngắn danh mục</span>
                            </label>
                            <textarea 
                                class="form-control form-control-modern" 
                                id="description" 
                                name="description" 
                                rows="3" 
                                placeholder="Mô tả phong cách và các sản phẩm thuộc danh mục này..."
                            >{{ old('description') }}</textarea>
                        </div>

                        <!-- Kích hoạt -->
                        <div class="col-12">
                            <div class="p-3 rounded-3" style="background: var(--bg-surface-subtle); border: 1px solid var(--border-default);">
                                <div class="form-check mb-0">
                                    <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', '1') ? 'checked' : '' }}>
                                    <label class="form-check-label text-dark fw-medium user-select-none" for="is_active">
                                        Hiển thị danh mục trên menu Storefront
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end align-items-center gap-3 pt-4 mt-4 border-top">
                        <a href="{{ route('admin.categories.index') }}" class="btn-surface">
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
