@extends('layouts.auth')

@section('title', 'Đăng ký tài khoản mới')

@section('content')
<div class="auth-card">
    <div class="auth-card-header text-center mb-4">
        <div class="auth-icon-badge mx-auto mb-3" style="background: var(--brand-50); color: var(--brand-600);">
            <i data-lucide="user-plus" style="width: 24px; height: 24px;"></i>
        </div>
        <h3 class="fw-bold text-dark mb-1" style="letter-spacing: -0.02em;">Tạo tài khoản mới</h3>
        <p class="text-secondary small mb-0">Trở thành thành viên Aurelia Luxury &bull; Mua sắm túi xách cao cấp</p>
    </div>

    <!-- Register Form -->
    <form action="{{ route('register') }}" method="POST" class="auth-form" id="registerForm" novalidate>
        @csrf

        <!-- Full Name -->
        <div class="form-group-modern mb-4">
            <label for="name" class="form-label-modern">
                <span>Họ và tên</span>
                <span class="text-danger ms-0.5">*</span>
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
                    value="{{ old('name') }}" 
                    placeholder="Ví dụ: Nguyễn Thị Mai" 
                    autocomplete="name" 
                    autofocus
                >
            </div>
            @error('name')
                <div class="text-danger small mt-1.5 d-flex align-items-center gap-1.5 server-invalid-feedback">
                    <i data-lucide="alert-circle" style="width: 15px; height: 15px; flex-shrink: 0;"></i>
                    <span>{{ $message }}</span>
                </div>
            @enderror
        </div>

        <!-- Email Address -->
        <div class="form-group-modern mb-4">
            <label for="email" class="form-label-modern">
                <span>Địa chỉ Email</span>
                <span class="text-danger ms-0.5">*</span>
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
                    value="{{ old('email') }}" 
                    placeholder="name@example.com" 
                    autocomplete="email"
                >
            </div>
            @error('email')
                <div class="text-danger small mt-1.5 d-flex align-items-center gap-1.5 server-invalid-feedback">
                    <i data-lucide="alert-circle" style="width: 15px; height: 15px; flex-shrink: 0;"></i>
                    <span>{{ $message }}</span>
                </div>
            @enderror
        </div>

        <!-- Password with Toggle -->
        <div class="form-group-modern mb-4">
            <label for="password" class="form-label-modern">
                <span>Mật khẩu</span>
                <span class="text-danger ms-0.5">*</span>
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
                    placeholder="Tối thiểu 8 ký tự" 
                    autocomplete="new-password"
                >
                <!-- Toggle Password Visibility Button -->
                <button 
                    type="button" 
                    class="btn-toggle-password" 
                    id="toggleRegPassBtn" 
                    onclick="togglePasswordVisibility('password', 'toggleRegPassBtn')" 
                    tabindex="-1"
                    title="Ẩn / Hiện mật khẩu"
                    aria-label="Toggle password visibility"
                >
                    <i data-lucide="eye" style="width: 18px; height: 18px;"></i>
                </button>
            </div>
            @error('password')
                <div class="text-danger small mt-1.5 d-flex align-items-center gap-1.5 server-invalid-feedback">
                    <i data-lucide="alert-circle" style="width: 15px; height: 15px; flex-shrink: 0;"></i>
                    <span>{{ $message }}</span>
                </div>
            @enderror
        </div>

        <!-- Confirm Password with Toggle -->
        <div class="form-group-modern mb-4">
            <label for="password_confirmation" class="form-label-modern">
                <span>Xác nhận mật khẩu</span>
                <span class="text-danger ms-0.5">*</span>
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
                    placeholder="Nhập lại chính xác mật khẩu" 
                    autocomplete="new-password"
                >
                <!-- Toggle Confirm Password Visibility Button -->
                <button 
                    type="button" 
                    class="btn-toggle-password" 
                    id="toggleRegConfirmPassBtn" 
                    onclick="togglePasswordVisibility('password_confirmation', 'toggleRegConfirmPassBtn')" 
                    tabindex="-1"
                    title="Ẩn / Hiện mật khẩu"
                    aria-label="Toggle confirm password visibility"
                >
                    <i data-lucide="eye" style="width: 18px; height: 18px;"></i>
                </button>
            </div>
        </div>

        <!-- Submit Button -->
        <button type="submit" class="btn-brand-primary w-100 py-2.5 justify-content-center fw-semibold fs-6">
            <i data-lucide="check" style="width: 18px; height: 18px; margin-right: 0.45rem;"></i>
            <span>Đăng ký thành viên</span>
        </button>
    </form>

    <!-- Switch to Login -->
    <div class="text-center mt-4 pt-3 border-top">
        <span class="text-secondary small">Đã có tài khoản?</span>
        <a href="{{ route('login') }}" class="text-primary fw-semibold small text-decoration-none ms-1">
            Đăng nhập ngay
        </a>
    </div>
</div>
@endsection
