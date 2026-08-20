@extends('layouts.app')

@section('title', 'Chi tiết danh mục #' . $category->id)

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8 col-lg-6">
        <div class="card card-custom animate-fade-in">
            <div class="card-header-custom d-flex justify-content-between align-items-center">
                <h5 class="fw-bold mb-0 text-dark">
                    <i class="bi bi-info-circle-fill text-info me-2"></i>Chi Tiết Danh Mục
                </h5>
                <a href="{{ route('categories.index') }}" class="btn btn-sm btn-custom-secondary">
                    <i class="bi bi-arrow-left me-1"></i>Quay lại danh sách
                </a>
            </div>
            
            <div class="card-body p-4">
                <div class="bg-light rounded-3 p-4 mb-4 border border-light-subtle">
                    <div class="mb-3">
                        <small class="text-uppercase text-muted fw-bold">Mã định danh (ID):</small>
                        <div class="mt-1">
                            <span class="badge-id fs-6">#{{ $category->id }}</span>
                        </div>
                    </div>
                    <div class="mb-3">
                        <small class="text-uppercase text-muted fw-bold">Tên danh mục:</small>
                        <div class="fs-4 fw-bold text-primary mt-1">{{ $category->name }}</div>
                    </div>
                    <hr class="text-muted opacity-25 my-3">
                    <div class="row g-3">
                        <div class="col-6">
                            <small class="text-muted d-block mb-1">Ngày tạo:</small>
                            <span class="small fw-semibold text-dark">
                                <i class="bi bi-calendar-event me-1 text-secondary"></i>{{ $category->created_at ? $category->created_at->format('d/m/Y H:i:s') : '---' }}
                            </span>
                        </div>
                        <div class="col-6">
                            <small class="text-muted d-block mb-1">Cập nhật lần cuối:</small>
                            <span class="small fw-semibold text-dark">
                                <i class="bi bi-clock-history me-1 text-secondary"></i>{{ $category->updated_at ? $category->updated_at->format('d/m/Y H:i:s') : '---' }}
                            </span>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('categories.edit', $category) }}" class="btn btn-warning text-white fw-medium">
                        <i class="bi bi-pencil-square me-1"></i>Chỉnh sửa danh mục
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
