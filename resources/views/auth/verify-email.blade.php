@extends('layouts.auth')

@section('title', 'Xác thực Email')

@section('content')
<div class="auth-card">
    <div class="auth-card-header text-center mb-4">
        <div class="auth-icon-badge mx-auto mb-3" style="background: var(--warning-50); color: var(--warning-600);">
            <i data-lucide="mail-check" style="width: 24px; height: 24px;"></i>
        </div>
        <h3 class="fw-bold text-dark mb-1" style="letter-spacing: -0.02em;">Xác thực Email</h3>
        <p class="text-secondary small mb-0">Vui lòng kiểm tra hộp thư email để xác thực tài khoản của bạn.</p>
    </div>

    {{-- Thông báo đã gửi lại --}}
    @if (session('message'))
        <div class="d-flex align-items-center gap-2 p-3 rounded-3 mb-4" style="background: var(--success-50); border: 1px solid var(--success-100);">
            <i data-lucide="check-circle-2" style="width: 18px; height: 18px; color: var(--success-600); flex-shrink: 0;"></i>
            <span class="small fw-medium" style="color: var(--success-text);">{{ session('message') }}</span>
        </div>
    @endif

    {{-- Flash success --}}
    @if (session('success'))
        <div class="d-flex align-items-center gap-2 p-3 rounded-3 mb-4" style="background: var(--success-50); border: 1px solid var(--success-100);">
            <i data-lucide="check-circle-2" style="width: 18px; height: 18px; color: var(--success-600); flex-shrink: 0;"></i>
            <span class="small fw-medium" style="color: var(--success-text);">{{ session('success') }}</span>
        </div>
    @endif

    {{-- Hướng dẫn --}}
    <div class="p-3 rounded-3 mb-4" style="background: var(--bg-surface-subtle); border: 1px solid var(--border-default);">
        <div class="d-flex align-items-start gap-2">
            <i data-lucide="info" style="width: 18px; height: 18px; color: var(--info-600); flex-shrink: 0; margin-top: 2px;"></i>
            <div class="small" style="color: var(--text-secondary); line-height: 1.6;">
                Chúng tôi đã gửi một email xác thực đến địa chỉ <strong style="color: var(--text-primary);">{{ Auth::user()->email }}</strong>. 
                Hãy nhấn vào liên kết trong email để kích hoạt tài khoản. 
                Nếu không thấy email, vui lòng kiểm tra thư mục <strong>Spam / Junk</strong>.
            </div>
        </div>
    </div>

    {{-- Nút gửi lại email --}}
    <form method="POST" action="{{ route('verification.send') }}">
        @csrf
        <button type="submit" class="btn-brand-primary w-100 py-2.5 justify-content-center fw-semibold fs-6">
            <i data-lucide="send" style="width: 18px; height: 18px; margin-right: 0.45rem;"></i>
            <span>Gửi lại email xác thực</span>
        </button>
    </form>

    {{-- Đăng xuất --}}
    <div class="text-center mt-4 pt-3 border-top">
        <span class="text-secondary small">Muốn dùng email khác?</span>
        <form action="{{ route('logout') }}" method="POST" class="d-inline">
            @csrf
            <button type="submit" class="text-primary fw-semibold small text-decoration-none ms-1 border-0 bg-transparent p-0" style="cursor: pointer; color: var(--brand-600);">
                Đăng xuất
            </button>
        </form>
    </div>
</div>
@endsection
