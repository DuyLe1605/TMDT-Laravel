@extends('layouts.app')

@section('title', 'Quản lý Tài khoản Người dùng')

@section('content')
<!-- Breadcrumbs & Page Header -->
<div class="mb-4">
    <div class="breadcrumb-modern">
        <a href="{{ route('admin.dashboard') }}">Bảng điều khiển</a>
        <i data-lucide="chevron-right" style="width: 14px; height: 14px;"></i>
        <span class="text-primary fw-medium">Quản lý Tài khoản</span>
    </div>
    
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mt-2">
        <div>
            <h2 class="fw-bold mb-1 text-dark" style="letter-spacing: -0.03em;">Quản Lý Tài Khoản Người Dùng</h2>
            <p class="text-secondary mb-0" style="font-size: 0.94rem;">
                Danh sách toàn bộ Quản trị viên và Khách hàng trong hệ sinh thái TMDT Túi Xách Nữ
            </p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('admin.users.create') }}" class="btn-brand-primary">
                <i data-lucide="user-plus" style="width: 18px; height: 18px; margin-right: 0.45rem;"></i>
                <span>Thêm tài khoản mới</span>
            </a>
        </div>
    </div>
</div>

<!-- KPI Metric Cards Grid -->
<div class="row g-4 mb-4">
    <div class="col-sm-6 col-lg-4">
        <div class="metric-card metric-card-primary">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="metric-label mb-2">Tổng tài khoản</div>
                    <div class="metric-number">{{ method_exists($users, 'total') ? $users->total() : $users->count() }}</div>
                </div>
                <div class="metric-icon-box metric-icon-indigo">
                    <i data-lucide="users" style="width: 22px; height: 22px;"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-4">
        <div class="metric-card metric-card-info">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="metric-label mb-2">Quản trị viên (Admin)</div>
                    <div class="metric-number text-primary">{{ $totalAdmins }}</div>
                </div>
                <div class="metric-icon-box metric-icon-sky">
                    <i data-lucide="shield-check" style="width: 22px; height: 22px;"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-12 col-lg-4">
        <div class="metric-card metric-card-success">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="metric-label mb-2">Khách hàng thành viên</div>
                    <div class="metric-number text-success">{{ $totalCustomers }}</div>
                </div>
                <div class="metric-icon-box metric-icon-emerald">
                    <i data-lucide="user-check" style="width: 22px; height: 22px;"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Main Table Card -->
<div class="card-modern">
    <!-- Header with Filters -->
    <div class="card-modern-header d-flex flex-wrap justify-content-between align-items-center gap-3">
        <div class="d-flex align-items-center">
            <span class="fw-bold text-dark" style="font-size: 1.1rem;">Danh sách người dùng</span>
            <span class="badge-count-pill">
                {{ method_exists($users, 'total') ? $users->total() : $users->count() }} thành viên
            </span>
        </div>
        
        <div class="d-flex align-items-center gap-2.5 flex-nowrap">
            <!-- Filter by Role -->
            <form method="GET" action="{{ route('admin.users.index') }}" class="d-flex align-items-center gap-2.5 flex-nowrap mb-0">
                <div class="select-box-modern" style="width: 175px;">
                    <i data-lucide="shield" class="select-icon" style="width: 15px; height: 15px;"></i>
                    <select name="role" class="form-select form-select-modern" onchange="this.form.submit()">
                        <option value="">Tất cả vai trò</option>
                        <option value="admin" {{ (isset($role) && $role === 'admin') ? 'selected' : '' }}>Quản trị viên (Admin)</option>
                        <option value="customer" {{ (isset($role) && $role === 'customer') ? 'selected' : '' }}>Khách hàng (Customer)</option>
                    </select>
                </div>

                <div class="select-box-modern" style="width: 165px;">
                    <i data-lucide="arrow-up-down" class="select-icon" style="width: 15px; height: 15px;"></i>
                    <select name="sort" class="form-select form-select-modern" onchange="this.form.submit()">
                        <option value="created_desc" {{ (isset($sort) && $sort === 'created_desc') ? 'selected' : '' }}>Mới nhất trước</option>
                        <option value="created_asc" {{ (isset($sort) && $sort === 'created_asc') ? 'selected' : '' }}>Cũ nhất trước</option>
                        <option value="name_asc" {{ (isset($sort) && $sort === 'name_asc') ? 'selected' : '' }}>Tên: A &rarr; Z</option>
                        <option value="name_desc" {{ (isset($sort) && $sort === 'name_desc') ? 'selected' : '' }}>Tên: Z &rarr; A</option>
                    </select>
                </div>
            </form>

            <!-- Instant Search Box -->
            <div class="search-box-modern">
                <i data-lucide="search" class="search-icon" style="width: 16px; height: 16px;"></i>
                <input 
                    type="text" 
                    id="userSearchInput" 
                    class="form-control form-control-modern" 
                    placeholder="Tìm theo tên, email..."
                    onkeyup="filterUserRows()"
                >
            </div>
        </div>
    </div>

    <!-- Table Body -->
    <div class="table-responsive">
        <table class="table-modern" id="userTable">
            <thead>
                <tr>
                    <th style="width: 80px;" class="text-center">ID</th>
                    <th>Thành viên & Họ tên</th>
                    <th style="width: 240px;">Địa chỉ Email</th>
                    <th style="width: 160px;" class="text-center">Vai trò</th>
                    <th style="width: 160px;">Ngày đăng ký</th>
                    <th style="width: 90px;" class="text-center">Thao tác</th>
                </tr>
            </thead>
            <tbody id="userTableBody">
                @forelse ($users as $user)
                    <tr class="user-data-row" 
                        data-id="{{ $user->id }}" 
                        data-name="{{ $user->name }}" 
                        data-email="{{ $user->email }}" 
                        data-role="{{ $user->role }}">
                        <td class="text-center">
                            <span class="badge-mono-id">#{{ str_pad($user->id, 3, '0', STR_PAD_LEFT) }}</span>
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-3">
                                <div class="sidebar-user-avatar flex-shrink-0" style="width: 40px; height: 40px; font-size: 0.9rem; {{ $user->isAdmin() ? 'background: linear-gradient(135deg, #4f46e5, #7c3aed);' : 'background: linear-gradient(135deg, #059669, #10b981);' }}">
                                    {{ strtoupper(substr($user->name, 0, 2)) }}
                                </div>
                                <div>
                                    <a href="{{ route('admin.users.show', $user) }}" class="category-name-text d-block text-decoration-none">
                                        {{ $user->name }}
                                        @if (Auth::id() === $user->id)
                                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill ms-1" style="font-size: 0.68rem;">(Bạn)</span>
                                        @endif
                                    </a>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="font-monospace text-secondary" style="font-size: 0.88rem;">{{ $user->email }}</span>
                        </td>
                        <td class="text-center">
                            @if ($user->isAdmin())
                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-3 py-1 rounded-pill fw-semibold" style="font-size: 0.78rem;">
                                    <i data-lucide="shield" style="width: 12px; height: 12px; margin-right: 0.25rem; display: inline-block;"></i>
                                    <span>Quản trị viên</span>
                                </span>
                            @else
                                <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-1 rounded-pill fw-semibold" style="font-size: 0.78rem;">
                                    <i data-lucide="user" style="width: 12px; height: 12px; margin-right: 0.25rem; display: inline-block;"></i>
                                    <span>Khách hàng</span>
                                </span>
                            @endif
                        </td>
                        <td>
                            <span class="text-secondary small">
                                {{ $user->created_at ? $user->created_at->format('d/m/Y H:i') : '---' }}
                            </span>
                        </td>
                        <td class="text-center">
                            <!-- 3-Dots Action Dropdown -->
                            <div class="dropdown">
                                <button 
                                    class="btn-action-dropdown" 
                                    type="button" 
                                    data-bs-toggle="dropdown" 
                                    data-bs-display="static"
                                    aria-expanded="false"
                                    title="Tùy chọn thao tác"
                                >
                                    <i data-lucide="more-horizontal" style="width: 16px; height: 16px;"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-modern dropdown-menu-end shadow">
                                    <li>
                                        <a href="{{ route('admin.users.show', $user) }}" class="dropdown-item-modern">
                                            <i data-lucide="eye" style="width: 16px; height: 16px; margin-right: 0.5rem;"></i>
                                            <span>Xem thông tin</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ route('admin.users.edit', $user) }}" class="dropdown-item-modern">
                                            <i data-lucide="pencil" style="width: 16px; height: 16px; margin-right: 0.5rem;"></i>
                                            <span>Chỉnh sửa</span>
                                        </a>
                                    </li>
                                    @if (Auth::id() !== $user->id)
                                        <li><hr class="dropdown-divider-modern"></li>
                                        <li>
                                            <button 
                                                type="button" 
                                                class="dropdown-item-modern item-danger" 
                                                onclick="openDeleteUserModal('{{ $user->id }}', '{{ addslashes($user->name) }}', '{{ $user->email }}')"
                                            >
                                                <i data-lucide="trash-2" style="width: 16px; height: 16px; margin-right: 0.5rem;"></i>
                                                <span>Xóa tài khoản</span>
                                            </button>
                                        </li>
                                    @endif
                                </ul>
                            </div>

                            @if (Auth::id() !== $user->id)
                                <form id="delete-user-form-{{ $user->id }}" action="{{ route('admin.users.destroy', $user) }}" method="POST" class="d-none">
                                    @csrf
                                    @method('DELETE')
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-5">
                            <div class="py-4">
                                <div class="category-squircle mx-auto mb-3" style="width: 56px; height: 56px;">
                                    <i data-lucide="users" style="width: 28px; height: 28px;"></i>
                                </div>
                                <h5 class="fw-bold text-dark mb-1">Chưa có người dùng nào</h5>
                                <p class="text-secondary small mb-4">Khởi tạo tài khoản thành viên đầu tiên cho hệ thống.</p>
                                <a href="{{ route('admin.users.create') }}" class="btn-brand-primary">
                                    <i data-lucide="user-plus" style="width: 16px; height: 16px; margin-right: 0.45rem;"></i>
                                    <span>Thêm tài khoản ngay</span>
                                </a>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination Footer -->
    @if ($users->hasPages())
        <div class="card-modern-footer d-flex justify-content-end">
            {{ $users->links('pagination::bootstrap-5') }}
        </div>
    @endif
</div>

<!-- =======================================================================
     DELETE CONFIRMATION MODAL
     ======================================================================= -->
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
@endsection

@section('scripts')
<script>
    let activeDeleteUserId = null;
    let deleteUserModalInstance = null;

    function removeVietnameseTones(str) {
        if (!str) return '';
        return str.normalize('NFD')
            .replace(/[\u0300-\u036f]/g, '')
            .replace(/[đĐ]/g, 'd')
            .replace(/[^a-z0-9\s-]/gi, '')
            .toLowerCase()
            .trim();
    }

    function openDeleteUserModal(id, name, email) {
        activeDeleteUserId = id;
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
        if (activeDeleteUserId) {
            const form = document.getElementById('delete-user-form-' + activeDeleteUserId);
            if (form) form.submit();
        }
    }

    function filterUserRows() {
        const query = removeVietnameseTones(document.getElementById('userSearchInput').value);
        const rows = document.querySelectorAll('.user-data-row');
        
        rows.forEach(row => {
            const name = removeVietnameseTones(row.getAttribute('data-name') || '');
            const email = (row.getAttribute('data-email') || '').toLowerCase();
            const id = row.getAttribute('data-id') || '';
            const role = removeVietnameseTones(row.getAttribute('data-role') || '');
            
            const match = name.includes(query) || email.includes(query) || id.includes(query) || role.includes(query);
            row.style.display = match ? '' : 'none';
        });
    }
</script>
@endsection
