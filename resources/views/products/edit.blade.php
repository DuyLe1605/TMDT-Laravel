@extends('layouts.app')

@section('title', 'Chỉnh sửa sản phẩm #' . $product->id)

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10 col-xl-9">
        <!-- Breadcrumbs -->
        <div class="breadcrumb-modern">
            <a href="{{ route('admin.products.index') }}">Sản phẩm</a>
            <i data-lucide="chevron-right" style="width: 14px; height: 14px;"></i>
            <a href="{{ route('admin.products.show', $product) }}">#{{ str_pad($product->id, 3, '0', STR_PAD_LEFT) }}</a>
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
                            <h5 class="fw-bold text-dark mb-0">Chỉnh Sửa Túi Xách</h5>
                            <span class="badge-mono-id" style="margin-left: 0.85rem !important;">#{{ str_pad($product->id, 3, '0', STR_PAD_LEFT) }}</span>
                        </div>
                        <div class="text-secondary small">Cập nhật giá bán, số lượng tồn kho và thông số sản phẩm</div>
                    </div>
                </div>
                <a href="{{ route('admin.products.index') }}" class="btn-surface" style="padding: 0.55rem 1.1rem; font-size: 0.88rem;">
                    <i data-lucide="arrow-left" style="width: 15px; height: 15px; margin-right: 0.45rem;"></i>
                    <span>Quay lại</span>
                </a>
            </div>
            
            <!-- Card Form Body -->
            <div class="card-modern-body">
                <form action="{{ route('admin.products.update', $product) }}" method="POST" id="editProductForm">
                    @csrf
                    @method('PUT')
                    
                    <div class="row g-4">
                        <!-- Tên sản phẩm -->
                        <div class="col-md-8">
                            <label for="name" class="form-label-modern mb-2">
                                <span>Tên sản phẩm túi xách</span>
                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2.5 py-1 rounded-pill" style="font-size: 0.72rem;">Bắt buộc</span>
                            </label>
                            <input 
                                type="text" 
                                class="form-control form-control-modern @error('name') is-invalid @enderror" 
                                id="name" 
                                name="name" 
                                value="{{ old('name', $product->name) }}" 
                                required 
                                autofocus
                            >
                            @error('name')
                                <div class="invalid-feedback d-block mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Danh mục -->
                        <div class="col-md-4">
                            <label for="category_id" class="form-label-modern mb-2">
                                <span>Dòng túi xách</span>
                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2.5 py-1 rounded-pill" style="font-size: 0.72rem;">Bắt buộc</span>
                            </label>
                            <select class="form-select form-control-modern @error('category_id') is-invalid @enderror" id="category_id" name="category_id" required>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <div class="invalid-feedback d-block mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Giá bán gốc -->
                        <div class="col-md-4">
                            <label for="price" class="form-label-modern mb-2">
                                <span>Giá bán gốc (VNĐ)</span>
                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2.5 py-1 rounded-pill" style="font-size: 0.72rem;">Bắt buộc</span>
                            </label>
                            <input 
                                type="number" 
                                step="1000"
                                class="form-control form-control-modern @error('price') is-invalid @enderror" 
                                id="price" 
                                name="price" 
                                value="{{ old('price', (int)$product->price) }}" 
                                required
                            >
                            @error('price')
                                <div class="invalid-feedback d-block mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Giá khuyến mãi -->
                        <div class="col-md-4">
                            <label for="sale_price" class="form-label-modern mb-2">
                                <span>Giá khuyến mãi (VNĐ)</span>
                                <span class="badge bg-secondary-subtle text-secondary px-2.5 py-1 rounded-pill" style="font-size: 0.72rem;">Tùy chọn</span>
                            </label>
                            <input 
                                type="number" 
                                step="1000"
                                class="form-control form-control-modern @error('sale_price') is-invalid @enderror" 
                                id="sale_price" 
                                name="sale_price" 
                                value="{{ old('sale_price', $product->sale_price ? (int)$product->sale_price : '') }}" 
                            >
                            @error('sale_price')
                                <div class="invalid-feedback d-block mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Số lượng tồn kho -->
                        <div class="col-md-4">
                            <label for="stock" class="form-label-modern mb-2">
                                <span>Số lượng nhập kho</span>
                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2.5 py-1 rounded-pill" style="font-size: 0.72rem;">Bắt buộc</span>
                            </label>
                            <input 
                                type="number" 
                                class="form-control form-control-modern @error('stock') is-invalid @enderror" 
                                id="stock" 
                                name="stock" 
                                value="{{ old('stock', $product->stock) }}" 
                                min="0" 
                                required
                            >
                            @error('stock')
                                <div class="invalid-feedback d-block mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Chất liệu -->
                        <div class="col-md-4">
                            <label for="material" class="form-label-modern mb-2">
                                <span>Chất liệu da / vải</span>
                            </label>
                            <input 
                                type="text" 
                                class="form-control form-control-modern" 
                                id="material" 
                                name="material" 
                                value="{{ old('material', $product->material) }}" 
                            >
                        </div>

                        <!-- Kích thước -->
                        <div class="col-md-4">
                            <label for="dimensions" class="form-label-modern mb-2">
                                <span>Kích thước (D x R x C)</span>
                            </label>
                            <input 
                                type="text" 
                                class="form-control form-control-modern" 
                                id="dimensions" 
                                name="dimensions" 
                                value="{{ old('dimensions', $product->dimensions) }}" 
                            >
                        </div>

                        <!-- Màu sắc -->
                        <div class="col-md-4">
                            <label for="color" class="form-label-modern mb-2">
                                <span>Màu sắc</span>
                            </label>
                            <input 
                                type="text" 
                                class="form-control form-control-modern" 
                                id="color" 
                                name="color" 
                                value="{{ old('color', $product->color) }}" 
                            >
                        </div>

                        <!-- Link ảnh sản phẩm -->
                        <div class="col-12">
                            <label for="image" class="form-label-modern mb-2">
                                <span>Đường dẫn ảnh minh họa (Image URL)</span>
                            </label>
                            <input 
                                type="url" 
                                class="form-control form-control-modern" 
                                id="image" 
                                name="image" 
                                value="{{ old('image', $product->image) }}" 
                            >
                        </div>

                        <!-- Mô tả chi tiết -->
                        <div class="col-12">
                            <label for="description" class="form-label-modern mb-2">
                                <span>Mô tả chi tiết túi xách</span>
                            </label>
                            <textarea 
                                class="form-control form-control-modern" 
                                id="description" 
                                name="description" 
                                rows="4" 
                            >{{ old('description', $product->description) }}</textarea>
                        </div>

                        <!-- Tùy chọn nổi bật & hiển thị -->
                        <div class="col-12">
                            <div class="d-flex flex-wrap gap-4 p-3 rounded-3" style="background: var(--bg-surface-subtle); border: 1px solid var(--border-default);">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="is_featured" name="is_featured" value="1" {{ old('is_featured', $product->is_featured) ? 'checked' : '' }}>
                                    <label class="form-check-label text-dark fw-medium" for="is_featured">
                                        Đánh dấu sản phẩm nổi bật (Featured)
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $product->is_active) ? 'checked' : '' }}>
                                    <label class="form-check-label text-dark fw-medium" for="is_active">
                                        Mở bán ngay (Hiển thị trên sàn TMDT)
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Buttons -->
                    <div class="d-flex justify-content-end align-items-center gap-3 pt-4 mt-4 border-top">
                        <a href="{{ route('admin.products.index') }}" class="btn-surface">
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
