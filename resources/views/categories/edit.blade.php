@extends('layouts.app')

@section('title', 'Chỉnh sửa danh mục')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8 col-lg-6">
        <div class="card card-custom animate-fade-in">
            <div class="card-header-custom d-flex justify-content-between align-items-center">
                <h5 class="fw-bold mb-0 text-dark">
                    <i class="bi bi-pencil-square text-warning me-2"></i>Chỉnh Sửa Danh Mục
                </h5>
                <a href="{{ route('categories.index') }}" class="btn btn-sm btn-custom-secondary">
                    <i class="bi bi-arrow-left me-1"></i>Quay lại
                </a>
            </div>
            
            <div class="card-body p-4">
                <form action="{{ route('categories.update', $category) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-4">
                        <label for="name" class="form-label fw-semibold text-secondary">
                            Tên danh mục <span class="text-danger">*</span>
                        </label>
                        <input 
                            type="text" 
                            class="form-control form-control-custom @error('name') is-invalid @enderror" 
                            id="name" 
                            name="name" 
                            value="{{ old('name', $category->name) }}" 
                            placeholder="Nhập tên danh mục..." 
                            required 
                            autofocus
                        >
                        @error('name')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                        <div class="form-text text-muted">
                            Mã danh mục trong hệ thống: <strong class="badge-id">#{{ $category->id }}</strong>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 pt-2">
                        <a href="{{ route('categories.index') }}" class="btn btn-custom-secondary">Hủy bỏ</a>
                        <button type="submit" class="btn btn-custom-primary">
                            <i class="bi bi-arrow-repeat me-1"></i>Cập nhật thay đổi
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
