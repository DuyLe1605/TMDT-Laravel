@extends('layouts.app')

@section('title', 'Chỉnh sửa tài khoản #' . $user->id)

@section('content')
<div class="row justify-content-center">
    <div class="col-md-9 col-lg-8 col-xl-7">
        <!-- Breadcrumbs -->
        <div class="breadcrumb-modern">
            <a href="{{ route('admin.users.index') }}">Tài khoản</a>
            <i data-lucide="chevron-right" style="width: 14px; height: 14px;"></i>
            <a href="{{ route('admin.users.show', $user) }}">#{{ str_pad($user->id, 3, '0', STR_PAD_LEFT) }}</a>
            <i data-lucide="chevron-right" style="width: 14px; height: 14px;"></i>
            <span class="text-primary fw-medium">Chỉnh sửa</span>
        </div>

        <div class="card-modern">
            <!-- Card Header -->
            <div class="card-modern-header d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <div class="category-squircle" style="width: 44px; height: 44px; margin-right: 1rem !important; flex-shrink: 0; background: var(--warning-50); color: var(--warning-600); border-color: var(--warning-100);">
                        <i data-lucide="user-cog" style="width: 22px; height: 22px;"></i>
                    </div>
                    <div>
                        <div class="d-flex align-items-center mb-1">
                            <h5 class="fw-bold text-dark mb-0">Chỉnh Sửa Tài Khoản</h5>
                            <span class="badge-mono-id" style="margin-left: 0.85rem !important;">#{{ str_pad($user->id, 3, '0', STR_PAD_LEFT) }}</span>
                        </div>
                        <div class="text-secondary small">Cập nhật họ tên, địa chỉ email, vai trò hoặc cấp lại mật khẩu</div>
                    </div>
                </div>
                <a href="{{ route('admin.users.index') }}" class="btn-surface" style="padding: 0.55rem 1.1rem; font-size: 0.88rem;">
                    <i data-lucide="arrow-left" style="width: 15px; height: 15px; margin-right: 0.45rem;"></i>
                    <span>Quay lại</span>
                </a>
            </div>
            
            <!-- Card Form Body -->
            <div class="card-modern-body">
                <form action="{{ route('admin.users.update', $user) }}" method="POST" id="editUserForm">
                    @csrf
                    @method('PUT')
                    
                    <!-- Họ và tên -->
                    <div class="form-group-modern mb-4">
                        <label for="name" class="form-label-modern">
                            <span>Họ và tên người dùng</span>
                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-0.5 rounded-pill ms-1" style="font-size: 0.72rem;">Bắt buộc</span>
                        </label>
                        <div class="input-group-modern">
                            <span class="input-group-icon">
                                <i data-lucide="user" style="width: 18px; height: 18px;"></i>
                            </span>
                            <input 
                                type="text" 
                                class="form-control form-control-modern @error('name') is-invalid @enderror" 
                                id="name" 
                                name="name" 
                                value="{{ old('name', $user->name) }}" 
                                autofocus
                            >
                        </div>
                        @error('name')
                            <div class="text-danger small mt-1.5 d-flex align-items-center gap-1.5">
                                <i data-lucide="alert-circle" style="width: 15px; height: 15px; flex-shrink: 0;"></i>
                                <span>{{ $message }}</span>
                            </div>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div class="form-group-modern mb-4">
                        <label for="email" class="form-label-modern">
                            <span>Địa chỉ Email đăng nhập</span>
                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-0.5 rounded-pill ms-1" style="font-size: 0.72rem;">Bắt buộc</span>
                        </label>
                        <div class="input-group-modern">
                            <span class="input-group-icon">
                                <i data-lucide="mail" style="width: 18px; height: 18px;"></i>
                            </span>
                            <input 
                                type="email" 
                                class="form-control form-control-modern @error('email') is-invalid @enderror" 
                                id="email" 
                                name="email" 
                                value="{{ old('email', $user->email) }}"
                            >
                        </div>
                        @error('email')
                            <div class="text-danger small mt-1.5 d-flex align-items-center gap-1.5">
                                <i data-lucide="alert-circle" style="width: 15px; height: 15px; flex-shrink: 0;"></i>
                                <span>{{ $message }}</span>
                            </div>
                        @enderror
                    </div>

                    <!-- Role Selection -->
                    <div class="form-group-modern mb-4">
                        <label for="role" class="form-label-modern">
                            <span>Vai trò phân quyền (Role)</span>
                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-0.5 rounded-pill ms-1" style="font-size: 0.72rem;">Bắt buộc</span>
                        </label>
                        <div class="input-group-modern">
                            <span class="input-group-icon">
                                <i data-lucide="shield" style="width: 18px; height: 18px;"></i>
                            </span>
                            <select class="form-select form-control-modern @error('role') is-invalid @enderror" id="role" name="role" {{ (Auth::id() === $user->id && $user->isAdmin()) ? 'disabled' : '' }}>
                                <option value="customer" {{ old('role', $user->role) === 'customer' ? 'selected' : '' }}>
                                    Khách hàng (Customer) — Mua sắm tại Storefront
                                </option>
                                <option value="admin" {{ old('role', $user->role) === 'admin' ? 'selected' : '' }}>
                                    Quản trị viên (Admin) — Toàn quyền quản trị hệ thống
                                </option>
                            </select>
                            @if (Auth::id() === $user->id && $user->isAdmin())
                                <input type="hidden" name="role" value="admin">
                            @endif
                        </div>
                        @if (Auth::id() === $user->id)
                            <div class="text-secondary small mt-1" style="font-size: 0.78rem;">
                                * Bạn không thể tự hạ quyền tài khoản quản trị đang đăng nhập của chính mình.
                            </div>
                        @endif
                        @error('role')
                            <div class="text-danger small mt-1.5 d-flex align-items-center gap-1.5">
                                <i data-lucide="alert-circle" style="width: 15px; height: 15px; flex-shrink: 0;"></i>
                                <span>{{ $message }}</span>
                            </div>
                        @enderror
                    </div>

                    <!-- Đổi Mật khẩu Mới (Optional) -->
                    <div class="p-3.5 rounded-3 mb-4" style="background: var(--bg-surface-subtle); border: 1px dashed var(--border-default);">
                        <div class="fw-semibold text-dark small mb-2 d-flex align-items-center gap-1.5">
                            <i data-lucide="key" class="text-primary" style="width: 15px; height: 15px;"></i>
                            <span>Đổi Mật Khẩu (Để trống nếu giữ nguyên mật khẩu cũ):</span>
                        </div>

                        <!-- Mật khẩu mới -->
                        <div class="form-group-modern mb-3">
                            <label for="password" class="form-label-modern small">
                                <span>Mật khẩu mới</span>
                                <span class="text-secondary" style="font-weight: 400;">(Tối thiểu 8 ký tự)</span>
                            </label>
                            <div class="input-group-modern position-relative">
                                <span class="input-group-icon">
                                    <i data-lucide="lock" style="width: 18px; height: 18px;"></i>
                                </span>
                                <input 
                                    type="password" 
                                    class="form-control form-control-modern has-toggle @error('password') is-invalid @enderror" 
                                    id="password" 
                                    name="password" 
                                    placeholder="Nhập mật khẩu mới nếu muốn thay đổi"
                                >
                                <button 
                                    type="button" 
                                    class="btn-toggle-password" 
                                    id="toggleUserPassBtn" 
                                    onclick="togglePasswordVisibility('password', 'toggleUserPassBtn')" 
                                    tabindex="-1"
                                    title="Ẩn / Hiện mật khẩu"
                                >
                                    <i data-lucide="eye" style="width: 18px; height: 18px;"></i>
                                </button>
                            </div>
                            @error('password')
                                <div class="text-danger small mt-1.5 d-flex align-items-center gap-1.5">
                                    <i data-lucide="alert-circle" style="width: 15px; height: 15px; flex-shrink: 0;"></i>
                                    <span>{{ $message }}</span>
                                </div>
                            @enderror
                        </div>

                        <!-- Xác nhận mật khẩu mới -->
                        <div class="form-group-modern mb-0">
                            <label for="password_confirmation" class="form-label-modern small">
                                <span>Xác nhận lại mật khẩu mới</span>
                            </label>
                            <div class="input-group-modern position-relative">
                                <span class="input-group-icon">
                                    <i data-lucide="shield-check" style="width: 18px; height: 18px;"></i>
                                </span>
                                <input 
                                    type="password" 
                                    class="form-control form-control-modern has-toggle" 
                                    id="password_confirmation" 
                                    name="password_confirmation" 
                                    placeholder="Nhập lại mật khẩu mới"
                                >
                                <button 
                                    type="button" 
                                    class="btn-toggle-password" 
                                    id="toggleUserConfirmPassBtn" 
                                    onclick="togglePasswordVisibility('password_confirmation', 'toggleUserConfirmPassBtn')" 
                                    tabindex="-1"
                                    title="Ẩn / Hiện mật khẩu"
                                >
                                    <i data-lucide="eye" style="width: 18px; height: 18px;"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Buttons -->
                    <div class="d-flex justify-content-end align-items-center gap-3 pt-3 border-top">
                        <a href="{{ route('admin.users.index') }}" class="btn-surface">
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

@section('scripts')
<script>
    function togglePasswordVisibility(inputId, toggleBtnId) {
        const input = document.getElementById(inputId);
        const btn = document.getElementById(toggleBtnId);
        if (!input || !btn) return;

        const isPassword = input.type === 'password';
        input.type = isPassword ? 'text' : 'password';

        btn.innerHTML = isPassword 
            ? '<i data-lucide="eye-off" style="width: 18px; height: 18px;"></i>'
            : '<i data-lucide="eye" style="width: 18px; height: 18px;"></i>';

        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
    }
</script>
@endsection
