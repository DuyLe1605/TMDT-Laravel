@extends('layouts.app')

@section('title', 'Danh sách danh mục')

@section('content')
<div class="card card-custom">
    <!-- Header Section -->
    <div class="card-header-custom d-flex flex-wrap justify-content-between align-items-center gap-3">
        <div>
            <h4 class="fw-bold mb-1 text-dark d-flex align-items-center gap-2">
                <i data-lucide="folder-open" class="text-primary" style="width: 26px; height: 26px;"></i>
                <span>Quản lý Danh mục (Categories)</span>
            </h4>
            <span class="text-muted small">Phân loại và quản lý tất cả các nhóm ngành hàng sản phẩm trên sàn TMDT</span>
        </div>
        <div>
            <a href="{{ route('categories.create') }}" class="btn btn-custom-primary d-inline-flex align-items-center gap-2">
                <i data-lucide="plus" style="width: 18px; height: 18px;"></i>
                <span>Thêm danh mục mới</span>
            </a>
        </div>
    </div>
    
    <!-- Table Body -->
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-custom align-middle mb-0">
                <thead>
                    <tr>
                        <th style="width: 90px;" class="text-center">Mã ID</th>
                        <th>Tên danh mục</th>
                        <th style="width: 220px;">Ngày tạo</th>
                        <th style="width: 220px;">Cập nhật cuối</th>
                        <th style="width: 230px;" class="text-center">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($categories as $category)
                        <tr>
                            <td class="text-center">
                                <span class="badge-id">#{{ $category->id }}</span>
                            </td>
                            <td>
                                <span class="fw-semibold text-dark fs-6">{{ $category->name }}</span>
                            </td>
                            <td class="text-muted small">
                                <span class="d-inline-flex align-items-center gap-1">
                                    <i data-lucide="calendar" style="width: 14px; height: 14px;"></i>
                                    {{ $category->created_at ? $category->created_at->format('d/m/Y H:i') : '---' }}
                                </span>
                            </td>
                            <td class="text-muted small">
                                <span class="d-inline-flex align-items-center gap-1">
                                    <i data-lucide="clock" style="width: 14px; height: 14px;"></i>
                                    {{ $category->updated_at ? $category->updated_at->format('d/m/Y H:i') : '---' }}
                                </span>
                            </td>
                            <td class="text-center">
                                <div class="btn-group btn-group-sm" role="group">
                                    <a href="{{ route('categories.show', $category) }}" class="btn btn-outline-primary d-inline-flex align-items-center gap-1" title="Xem chi tiết">
                                        <i data-lucide="eye" style="width: 14px; height: 14px;"></i>
                                        <span>Xem</span>
                                    </a>
                                    <a href="{{ route('categories.edit', $category) }}" class="btn btn-outline-warning d-inline-flex align-items-center gap-1" title="Chỉnh sửa">
                                        <i data-lucide="pencil" style="width: 14px; height: 14px;"></i>
                                        <span>Sửa</span>
                                    </a>
                                    <button type="button" class="btn btn-outline-danger d-inline-flex align-items-center gap-1" title="Xóa" onclick="handleDelete('{{ $category->id }}', '{{ addslashes($category->name) }}')">
                                        <i data-lucide="trash-2" style="width: 14px; height: 14px;"></i>
                                        <span>Xóa</span>
                                    </button>
                                </div>

                                <form id="delete-form-{{ $category->id }}" action="{{ route('categories.destroy', $category) }}" method="POST" class="d-none">
                                    @csrf
                                    @method('DELETE')
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">
                                <i data-lucide="inbox" class="d-block mx-auto mb-2 text-secondary opacity-50" style="width: 48px; height: 48px;"></i>
                                <p class="mb-2 fw-medium">Chưa có danh mục nào trong hệ thống</p>
                                <a href="{{ route('categories.create') }}" class="btn btn-sm btn-custom-primary d-inline-flex align-items-center gap-1">
                                    <i data-lucide="plus" style="width: 16px; height: 16px;"></i>
                                    <span>Tạo danh mục đầu tiên</span>
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination Footer -->
    @if ($categories->hasPages())
        <div class="card-footer bg-white border-top py-3 px-4 d-flex justify-content-end">
            {{ $categories->links('pagination::bootstrap-5') }}
        </div>
    @endif
</div>
@endsection

@section('scripts')
<script>
    function handleDelete(id, name) {
        if (confirm(`Bạn có chắc chắn muốn xóa danh mục "${name}" (#${id}) không? Thao tác này không thể hoàn tác.`)) {
            const form = document.getElementById('delete-form-' + id);
            if (form) {
                form.submit();
            }
        }
    }
</script>
@endsection
