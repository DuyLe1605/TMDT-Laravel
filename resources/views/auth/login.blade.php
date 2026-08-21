@extends('layouts.auth')

@section('title', 'Đăng nhập hệ thống')

@section('content')
<div class="auth-card">
    <div class="auth-card-header text-center mb-4">
        <div class="auth-icon-badge mx-auto mb-3">
            <i data-lucide="log-in" style="width: 24px; height: 24px;"></i>
        </div>
        <h3 class="fw-bold text-dark mb-1" style="letter-spacing: -0.02em;">Đăng nhập tài khoản</h3>
        <p class="text-secondary small mb-0">Truy cập hệ thống TMDT Túi Xách Nữ &bull; Aurelia Luxury</p>
    </div>

    <!-- Login Form -->
    <form action="{{ route('login') }}" method="POST" class="auth-form" id="loginForm" novalidate>
        @csrf

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
                    autofocus
                >
            </div>
            @error('email')
                <div class="text-danger small mt-1.5 d-flex align-items-center gap-1.5 server-invalid-feedback">
                    <i data-lucide="alert-circle" style="width: 15px; height: 15px; flex-shrink: 0;"></i>
                    <span>{{ $message }}</span>
                </div>
            @enderror
        </div>

        <!-- Password with Toggle Visibility -->
        <div class="form-group-modern mb-4">
            <div class="d-flex justify-content-between align-items-center mb-1">
                <label for="password" class="form-label-modern mb-0">
                    <span>Mật khẩu</span>
                    <span class="text-danger ms-0.5">*</span>
                </label>
            </div>
            <div class="input-group-modern position-relative">
                <span class="input-group-icon">
                    <i data-lucide="lock" style="width: 18px; height: 18px;"></i>
                </span>
                <input 
                    type="password" 
                    class="form-control form-control-modern has-toggle @error('password') is-invalid @enderror" 
                    id="password" 
                    name="password" 
                    placeholder="Nhập tối thiểu 8 ký tự" 
                    autocomplete="current-password"
                >
                <!-- Toggle Password Visibility Button -->
                <button 
                    type="button" 
                    class="btn-toggle-password" 
                    id="togglePasswordBtn" 
                    onclick="togglePasswordVisibility('password', 'togglePasswordBtn')" 
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

        <!-- Remember Me Checkbox -->
        <div class="d-flex justify-content-between align-items-center mb-4 pt-1">
            <div class="form-check d-flex align-items-center gap-2 mb-0">
                <input class="form-check-input mt-0" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                <label class="form-check-label text-secondary small cursor-pointer" for="remember" style="user-select: none;">
                    Ghi nhớ đăng nhập
                </label>
            </div>
        </div>

        <!-- Submit Button -->
        <button type="submit" class="btn-brand-primary w-100 py-2.5 justify-content-center fw-semibold fs-6">
            <i data-lucide="arrow-right" style="width: 18px; height: 18px; margin-right: 0.45rem;"></i>
            <span>Đăng nhập hệ thống</span>
        </button>
    </form>

    <!-- Quick Demo Accounts Helper -->
    <div class="auth-demo-hint mt-4 p-3 rounded-3">
        <div class="d-flex align-items-center gap-1.5 text-primary fw-semibold small mb-2">
            <i data-lucide="key-round" style="width: 15px; height: 15px;"></i>
            <span>Tài khoản mẫu thử nghiệm:</span>
        </div>
        <div class="d-flex flex-column gap-1 text-secondary small" style="font-size: 0.82rem;">
            <div class="d-flex align-items-center gap-1.5">
                <span class="badge bg-danger-subtle text-danger px-1.5 py-0.5 rounded">Admin</span>
                <code>admin@tuixach.vn</code> / <code>password</code>
            </div>
        </div>
    </div>

    <!-- Switch to Register -->
    <div class="text-center mt-4 pt-3 border-top">
        <span class="text-secondary small">Chưa có tài khoản?</span>
        <a href="{{ route('register') }}" class="text-primary fw-semibold small text-decoration-none ms-1">
            Đăng ký ngay
        </a>
    </div>
</div>
@endsection
