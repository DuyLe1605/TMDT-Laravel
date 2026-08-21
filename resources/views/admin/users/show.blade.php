@extends('layouts.app')

@section('title', 'Chi tiết tài khoản #' . $user->id)

@section('content')
<div class="row justify-content-center">
    <div class="col-md-9 col-lg-8 col-xl-7">
        <!-- Breadcrumbs -->
        <div class="breadcrumb-modern">
            <a href="{{ route('admin.users.index') }}">Tài khoản</a>
            <i data-lucide="chevron-right" style="width: 14px; height: 14px;"></i>
            <span class="text-primary fw-medium">Chi tiết #{{ str_pad($user->id, 3, '0', STR_PAD_LEFT) }}</span>
        </div>

        <div class="card-modern">
            <!-- Card Header -->
            <div class="card-modern-header d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <div class="category-squircle" style="width: 44px; height: 44px; margin-right: 1rem !important; flex-shrink: 0;">
                        <i data-lucide="user" style="width: 22px; height: 22px;"></i>
                    </div>
                    <div>
                        <div class="d-flex align-items-center mb-1">
                            <h5 class="fw-bold text-dark mb-0">Hồ Sơ Người Dùng</h5>
                            <span class="badge-mono-id" style="margin-left: 0.85rem !important;">#{{ str_pad($user->id, 3, '0', STR_PAD_LEFT) }}</span>
                        </div>
                        <div class="text-secondary small">Thông tin định danh và vai trò hệ thống</div>
                    </div>
                </div>
                <a href="{{ route('admin.users.index') }}" class="btn-surface" style="padding: 0.55rem 1.1rem; font-size: 0.88rem;">
                    <i data-lucide="arrow-left" style="width: 15px; height: 15px; margin-right: 0.45rem;"></i>
                    <span>Quay lại</span>
                </a>
            </div>
            
            <!-- Card Body -->
            <div class="card-modern-body">
                <!-- User Profile Hero Banner -->
                <div class="hero-banner-modern mb-4">
                    <div class="d-flex align-items-center gap-4">
                        <div class="sidebar-user-avatar flex-shrink-0" style="width: 64px; height: 64px; font-size: 1.5rem; {{ $user->isAdmin() ? 'background: linear-gradient(135deg, #4f46e5, #7c3aed);' : 'background: linear-gradient(135deg, #059669, #10b981);' }}">
                            {{ strtoupper(substr($user->name, 0, 2)) }}
                        </div>
                        <div>
                            <div class="d-flex flex-wrap align-items-center gap-2 mb-1">
                                <h4 class="fw-bold text-dark mb-0">{{ $user->name }}</h4>
                                @if (Auth::id() === $user->id)
                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill" style="font-size: 0.72rem;">(Tài khoản của bạn)</span>
                                @endif
                            </div>
                            <div class="text-secondary small mb-2 font-monospace">{{ $user->email }}</div>
                            <div>
                                @if ($user->isAdmin())
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-3 py-1 rounded-pill fw-semibold" style="font-size: 0.78rem;">
                                        <i data-lucide="shield" style="width: 12px; height: 12px; margin-right: 0.25rem; display: inline-block;"></i>
                                        <span>Quản trị viên (Admin)</span>
                                    </span>
                                @else
                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-1 rounded-pill fw-semibold" style="font-size: 0.78rem;">
                                        <i data-lucide="user" style="width: 12px; height: 12px; margin-right: 0.25rem; display: inline-block;"></i>
                                        <span>Khách hàng (Customer)</span>
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Structured Details Grid -->
                <div class="spec-grid-box mb-4">
                    <div class="row g-4">
                        <div class="col-sm-6">
                            <div class="spec-item">
                                <span class="spec-item-label">Mã định danh (User ID)</span>
                                <span class="spec-item-value font-monospace text-primary">#{{ $user->id }}</span>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="spec-item">
                                <span class="spec-item-label">Phân quyền hệ thống</span>
                                <span class="spec-item-value text-capitalize">{{ $user->role }}</span>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="spec-item">
                                <span class="spec-item-label">Thời điểm đăng ký</span>
                                <span class="spec-item-value d-inline-flex align-items-center text-secondary">
                                    <i data-lucide="calendar" class="text-tertiary" style="width: 15px; height: 15px; margin-right: 0.5rem;"></i>
                                    <span>{{ $user->created_at ? $user->created_at->format('d/m/Y H:i:s') : '---' }}</span>
                                </span>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="spec-item">
                                <span class="spec-item-label">Cập nhật gần nhất</span>
                                <span class="spec-item-value d-inline-flex align-items-center text-secondary">
                                    <i data-lucide="clock" class="text-tertiary" style="width: 15px; height: 15px; margin-right: 0.5rem;"></i>
                                    <span>{{ $user->updated_at ? $user->updated_at->format('d/m/Y H:i:s') : '---' }}</span>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Toolbar -->
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 pt-3 border-top">
                    @if (Auth::id() !== $user->id)
                        <button 
                            type="button" 
                            class="btn btn-outline-danger px-3.5 py-2 rounded-3 d-inline-flex align-items-center"
                            onclick="openDeleteUserModal('{{ $user->id }}', '{{ addslashes($user->name) }}', '{{ $user->email }}')"
                        >
                            <i data-lucide="trash-2" style="width: 15px; height: 15px; margin-right: 0.45rem;"></i>
                            <span>Xóa tài khoản</span>
                        </button>
                    @else
                        <div></div>
                    @endif

                    <a href="{{ route('admin.users.edit', $user) }}" class="btn-brand-primary">
                        <i data-lucide="pencil" style="width: 16px; height: 16px; margin-right: 0.45rem;"></i>
                        <span>Chỉnh sửa tài khoản</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modern Delete Confirmation Modal -->
@if (Auth::id() !== $user->id)
    <div class="modal fade" id="deleteUserConfirmModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" style="max-width: 450px;">
            <div class="modal-content modal-content-modern border-0">
                <div class="p-4 text-center">
                    <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 58px; height: 58px; background: var(--danger-50); color: var(--danger-600);">
                        <i data-lucide="alert-triangle" style="width: 28px; height: 28px;"></i>
                    </div>
                    <h5 class="fw-bold text-dark mb-2">Xác nhận xóa tài khoản</h5>
                    <p class="text-secondary small mb-4">
                        Bạn có chắc chắn muốn xóa người dùng <strong id="deleteUserName" class="text-dark"></strong> (<span id="deleteUserEmail" class="font-monospace text-primary"></span>)? Thao tác này không thể hoàn tác.
                    </p>
                    <div class="d-flex justify-content-center gap-3">
                        <button type="button" class="btn-surface px-4 py-2" data-bs-dismiss="modal">
                            <span>Hủy bỏ</span>
                        </button>
                        <button type="button" class="btn btn-danger px-4 py-2 rounded-3 fw-semibold d-inline-flex align-items-center" id="confirmDeleteUserBtn" onclick="submitDeleteUserForm()">
                            <i data-lucide="trash-2" style="width: 16px; height: 16px; margin-right: 0.4rem;"></i>
                            <span>Xóa vĩnh viễn</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <form id="delete-user-form-{{ $user->id }}" action="{{ route('admin.users.destroy', $user) }}" method="POST" class="d-none">
        @csrf
        @method('DELETE')
    </form>
@endif
@endsection

@section('scripts')
@if (Auth::id() !== $user->id)
<script>
    let deleteUserModalInstance = null;

    function openDeleteUserModal(id, name, email) {
        document.getElementById('deleteUserName').innerText = name;
        document.getElementById('deleteUserEmail').innerText = email;
        
        const modalEl = document.getElementById('deleteUserConfirmModal');
        if (!deleteUserModalInstance) deleteUserModalInstance = new bootstrap.Modal(modalEl);
        deleteUserModalInstance.show();
        setTimeout(() => {
            if (typeof lucide !== 'undefined') lucide.createIcons();
        }, 200);
    }

    function submitDeleteUserForm() {
        const form = document.getElementById('delete-user-form-{{ $user->id }}');
        if (form) {
            form.submit();
        }
    }
</script>
@endif
@endsection
