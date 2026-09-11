@extends('layouts.app')

@section('title', 'Chỉnh Sửa Tùy Chọn Quà Tặng - Admin Portal')

@section('content')
<div class="mb-4">
    <div class="breadcrumb-modern">
        <span>Admin Portal</span>
        <i data-lucide="chevron-right" style="width: 14px; height: 14px;"></i>
        <a href="{{ route('admin.gift-options.index') }}" class="text-secondary text-decoration-none">Gói Quà & Thiệp Mừng</a>
        <i data-lucide="chevron-right" style="width: 14px; height: 14px;"></i>
        <span class="text-primary fw-medium">Chỉnh sửa</span>
    </div>

    <div class="d-flex justify-content-between align-items-center mt-2">
        <div>
            <h2 class="fw-bold mb-1 text-dark" style="letter-spacing: -0.03em;">Chỉnh Sửa: {{ $giftOption->name }}</h2>
            <p class="text-secondary mb-0" style="font-size: 0.94rem;">Cập nhật thông tin mẫu giấy gói hoặc mẫu thiệp mừng</p>
        </div>
        <a href="{{ route('admin.gift-options.index') }}" class="btn btn-surface d-flex align-items-center gap-1.5 px-3 py-2">
            <i data-lucide="arrow-left" style="width: 16px; height: 16px;"></i>
            <span>Trở Lại</span>
        </a>
    </div>
</div>

<div class="card-modern shadow-sm border p-4" style="max-width: 720px;">
    <form action="{{ route('admin.gift-options.update', $giftOption) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label-modern small fw-bold">Phân loại <span class="text-danger">*</span></label>
                <select name="type" class="form-select form-select-modern @error('type') is-invalid @enderror" required>
                    <option value="paper" {{ old('type', $giftOption->type) === 'paper' ? 'selected' : '' }}>Giấy gói quà cao cấp</option>
                    <option value="card" {{ old('type', $giftOption->type) === 'card' ? 'selected' : '' }}>Thiệp chúc mừng</option>
                </select>
                @error('type') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-6">
                <label class="form-label-modern small fw-bold">Mã Code (duy nhất) <span class="text-danger">*</span></label>
                <input type="text" name="code" class="form-control form-control-modern font-monospace @error('code') is-invalid @enderror" value="{{ old('code', $giftOption->code) }}" required>
                @error('code') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-12">
                <label class="form-label-modern small fw-bold">Tên hiển thị <span class="text-danger">*</span></label>
                <input type="text" name="name" class="form-control form-control-modern @error('name') is-invalid @enderror" value="{{ old('name', $giftOption->name) }}" required>
                @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-6">
                <label class="form-label-modern small fw-bold">Giá phụ thu (VNĐ) <span class="text-danger">*</span></label>
                <input type="number" id="priceInput" name="price" step="1000" min="0" class="form-control form-control-modern @error('price') is-invalid @enderror" value="{{ old('price', (float) $giftOption->price) }}" required>
                <div class="d-flex flex-wrap gap-1 mt-1.5">
                    <button type="button" class="btn btn-sm btn-surface py-0 px-2 rounded-pill text-secondary" onclick="document.getElementById('priceInput').value = 0" style="font-size: 0.72rem;">0 ₫ (Miễn phí)</button>
                    <button type="button" class="btn btn-sm btn-surface py-0 px-2 rounded-pill text-secondary" onclick="document.getElementById('priceInput').value = 15000" style="font-size: 0.72rem;">15.000 ₫</button>
                    <button type="button" class="btn btn-sm btn-surface py-0 px-2 rounded-pill text-secondary" onclick="document.getElementById('priceInput').value = 25000" style="font-size: 0.72rem;">25.000 ₫</button>
                    <button type="button" class="btn btn-sm btn-surface py-0 px-2 rounded-pill text-secondary" onclick="document.getElementById('priceInput').value = 35000" style="font-size: 0.72rem;">35.000 ₫</button>
                    <button type="button" class="btn btn-sm btn-surface py-0 px-2 rounded-pill text-secondary" onclick="document.getElementById('priceInput').value = 50000" style="font-size: 0.72rem;">50.000 ₫</button>
                </div>
                @error('price') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-6">
                <label class="form-label-modern small fw-bold">Ảnh mẫu / Mã màu / Icon</label>
                <input type="text" name="image" class="form-control form-control-modern @error('image') is-invalid @enderror" value="{{ old('image', $giftOption->image) }}">
                <div class="form-text small">Hỗ trợ mã màu HEX (#881337), emoji hoặc URL ảnh</div>
                @error('image') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-12">
                <label class="form-label-modern small fw-bold">Mô tả ngắn</label>
                <textarea name="description" rows="2" class="form-control form-control-modern @error('description') is-invalid @enderror">{{ old('description', $giftOption->description) }}</textarea>
                @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-6">
                <label class="form-label-modern small fw-bold">Thứ tự sắp xếp</label>
                <input type="number" name="sort_order" min="0" class="form-control form-control-modern @error('sort_order') is-invalid @enderror" value="{{ old('sort_order', $giftOption->sort_order) }}">
                @error('sort_order') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-6 d-flex align-items-center pt-md-4">
                <div class="form-check form-switch mt-2">
                    <input type="checkbox" name="is_active" value="1" class="form-check-input" id="isActiveSwitch" {{ old('is_active', $giftOption->is_active) ? 'checked' : '' }} style="cursor: pointer;">
                    <label class="form-check-label small fw-semibold cursor-pointer" for="isActiveSwitch">Kích hoạt hiển thị cho khách hàng</label>
                </div>
            </div>

            <div class="col-12 pt-3 border-top d-flex gap-2 justify-content-end">
                <a href="{{ route('admin.gift-options.index') }}" class="btn btn-surface px-4">Hủy</a>
                <button type="submit" class="btn btn-brand-primary px-4">
                    <span>Cập Nhật Tùy Chọn</span>
                </button>
            </div>
        </div>
    </form>
</div>
@endsection
