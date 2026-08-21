@extends('layouts.app')

@section('title', 'Thêm danh mục mới')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8 col-lg-6">
        <div class="card card-custom animate-fade-in">
            <div class="card-header-custom d-flex justify-content-between align-items-center">
                <h5 class="fw-bold mb-0 text-dark d-flex align-items-center gap-2">
                    <i data-lucide="folder-plus" class="text-primary" style="width: 22px; height: 22px;"></i>
                    <span>Thêm Danh Mục Mới</span>
                </h5>
                <a href="{{ route('categories.index') }}" class="btn btn-sm btn-custom-secondary d-inline-flex align-items-center gap-1">
                    <i data-lucide="arrow-left" style="width: 16px; height: 16px;"></i>
                    <span>Quay lại</span>
                </a>
            </div>
            
            <div class="card-body p-4">
                <form action="{{ route('categories.store') }}" method="POST">
                    @csrf
                    
                    <div class="mb-4">
                        <label for="name" class="form-label fw-semibold text-secondary">
                            Tên danh mục <span class="text-danger">*</span>
                        </label>
                        <input 
                            type="text" 
                            class="form-control form-control-custom @error('name') is-invalid @enderror" 
                            id="name" 
                            name="name" 
                            value="{{ old('name') }}" 
                            placeholder="Ví dụ: Thiết bị di động, Laptop cao cấp..." 
                            required 
                            autofocus
                        >
                        @error('name')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                        <div class="form-text text-muted">
                            Tên danh mục là duy nhất và dùng để liên kết các sản phẩm trên sàn TMDT.
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 pt-2">
                        <a href="{{ route('categories.index') }}" class="btn btn-custom-secondary d-inline-flex align-items-center gap-1">
                            <i data-lucide="x" style="width: 16px; height: 16px;"></i>
                            <span>Hủy bỏ</span>
                        </a>
                        <button type="submit" class="btn btn-custom-primary d-inline-flex align-items-center gap-1">
                            <i data-lucide="check" style="width: 18px; height: 18px;"></i>
                            <span>Lưu danh mục</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
