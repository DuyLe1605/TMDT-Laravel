@extends('layouts.app')

@section('title', 'Thêm Thương Hiệu Mới')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8 col-xl-7">
        <div class="breadcrumb-modern mb-3">
            <a href="{{ route('admin.brands.index') }}">Thương hiệu</a>
            <i data-lucide="chevron-right" style="width: 14px; height: 14px;"></i>
            <span class="text-primary fw-medium">Thêm mới</span>
        </div>

        <div class="card-modern">
            <div class="card-modern-header d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <div class="category-squircle" style="width: 44px; height: 44px; margin-right: 1rem !important;">
                        <i data-lucide="award" style="width: 22px; height: 22px;"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold mb-1 text-dark">Thêm Thương Hiệu Mới</h5>
                        <div class="text-secondary small">Khai báo nhà mốt / thương hiệu thời trang túi xách</div>
                    </div>
                </div>
                <a href="{{ route('admin.brands.index') }}" class="btn-surface" style="padding: 0.5rem 1rem; font-size: 0.88rem;">
                    <i data-lucide="arrow-left" style="width: 15px; height: 15px; margin-right: 0.4rem;"></i>
                    <span>Quay lại</span>
                </a>
            </div>

            <div class="card-modern-body p-4">
                <form action="{{ route('admin.brands.store') }}" method="POST">
                    @csrf

                    <div class="row g-4">
                        <div class="col-md-8">
                            <label for="name" class="form-label-modern mb-2">
                                <span>Tên thương hiệu</span>
                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-0.5 rounded-pill" style="font-size: 0.7rem;">Bắt buộc</span>
                            </label>
                            <input 
                                type="text" 
                                class="form-control form-control-modern @error('name') is-invalid @enderror" 
                                id="name" 
                                name="name" 
                                value="{{ old('name') }}" 
                                placeholder="Ví dụ: Hermès, Chanel, Gucci, Aurelia..." 
                                required 
                                autofocus
                            >
                            @error('name')
                                <div class="invalid-feedback d-block mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label for="slug" class="form-label-modern mb-2">
                                <span>Slug định danh (URL)</span>
                            </label>
                            <input 
                                type="text" 
                                class="form-control form-control-modern @error('slug') is-invalid @enderror" 
                                id="slug" 
                                name="slug" 
                                value="{{ old('slug') }}" 
                                placeholder="Tự động sinh nếu để trống"
                            >
                            @error('slug')
                                <div class="invalid-feedback d-block mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="logo" class="form-label-modern mb-2">
                                <span>Đường dẫn Logo (Image URL)</span>
                            </label>
                            <input 
                                type="url" 
                                class="form-control form-control-modern @error('logo') is-invalid @enderror" 
                                id="logo" 
                                name="logo" 
                                value="{{ old('logo') }}" 
                                placeholder="https://images.unsplash.com/..."
                            >
                            @error('logo')
                                <div class="invalid-feedback d-block mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="website" class="form-label-modern mb-2">
                                <span>Website chính thức</span>
                            </label>
                            <input 
                                type="url" 
                                class="form-control form-control-modern @error('website') is-invalid @enderror" 
                                id="website" 
                                name="website" 
                                value="{{ old('website') }}" 
                                placeholder="https://www.hermes.com"
                            >
                            @error('website')
                                <div class="invalid-feedback d-block mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12">
                            <label for="description" class="form-label-modern mb-2">
                                <span>Mô tả thương hiệu / Lịch sử nhà mốt</span>
                            </label>
                            <textarea 
                                class="form-control form-control-modern" 
                                id="description" 
                                name="description" 
                                rows="3" 
                                placeholder="Mô tả phong cách thiết kế, đặc trưng chế tác đồ da..."
                            >{{ old('description') }}</textarea>
                        </div>

                        <div class="col-12">
                            <div class="p-3 rounded-3" style="background: var(--bg-surface-subtle); border: 1px solid var(--border-default);">
                                <div class="form-check mb-0">
                                    <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', '1') ? 'checked' : '' }}>
                                    <label class="form-check-label text-dark fw-medium user-select-none" for="is_active">
                                        Kích hoạt thương hiệu ngay (Hiển thị trên bộ lọc Storefront)
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end align-items-center gap-3 pt-4 mt-4 border-top">
                        <a href="{{ route('admin.brands.index') }}" class="btn-surface">Hủy bỏ</a>
                        <button type="submit" class="btn-brand-primary">
                            <i data-lucide="check" style="width: 16px; height: 16px; margin-right: 0.4rem;"></i>
                            <span>Lưu thương hiệu</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
