@extends('layouts.app')

@section('title', 'Chi tiết danh mục #' . $category->id)

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8 col-lg-6">
        <div class="card card-custom animate-fade-in">
            <div class="card-header-custom d-flex justify-content-between align-items-center">
                <h5 class="fw-bold mb-0 text-dark d-flex align-items-center gap-2">
                    <i data-lucide="info" class="text-info" style="width: 22px; height: 22px;"></i>
                    <span>Chi Tiết Danh Mục</span>
                </h5>
                <a href="{{ route('categories.index') }}" class="btn btn-sm btn-custom-secondary d-inline-flex align-items-center gap-1">
                    <i data-lucide="arrow-left" style="width: 16px; height: 16px;"></i>
                    <span>Quay lại</span>
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
                            <span class="small fw-semibold text-dark d-inline-flex align-items-center gap-1">
                                <i data-lucide="calendar" class="text-secondary" style="width: 15px; height: 15px;"></i>
                                <span>{{ $category->created_at ? $category->created_at->format('d/m/Y H:i:s') : '---' }}</span>
                            </span>
                        </div>
                        <div class="col-6">
                            <small class="text-muted d-block mb-1">Cập nhật lần cuối:</small>
                            <span class="small fw-semibold text-dark d-inline-flex align-items-center gap-1">
                                <i data-lucide="clock" class="text-secondary" style="width: 15px; height: 15px;"></i>
                                <span>{{ $category->updated_at ? $category->updated_at->format('d/m/Y H:i:s') : '---' }}</span>
                            </span>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('categories.edit', $category) }}" class="btn btn-warning text-white fw-medium d-inline-flex align-items-center gap-1">
                        <i data-lucide="pencil" style="width: 16px; height: 16px;"></i>
                        <span>Chỉnh sửa danh mục</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
