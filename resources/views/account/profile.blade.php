@extends('layouts.storefront')

@section('title', 'Thông Tin Tài Khoản - Aurelia Store')

@section('content')
<div class="container py-4 py-lg-5">
    <!-- Breadcrumb -->
    <div class="breadcrumb-modern mb-3">
        <a href="{{ route('home') }}">Trang chủ</a>
        <i data-lucide="chevron-right" style="width: 14px; height: 14px;"></i>
        <span>Tài khoản</span>
        <i data-lucide="chevron-right" style="width: 14px; height: 14px;"></i>
        <span class="text-primary fw-medium">Hồ sơ cá nhân</span>
    </div>

    <!-- Page Header -->
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4 pb-3 border-bottom">
        <div>
            <h1 class="fw-extrabold text-dark mb-1" style="font-size: 1.85rem; letter-spacing: -0.02em;">
                Hồ Sơ Của Tôi
            </h1>
            <p class="text-secondary small mb-0">
                Quản lý thông tin hồ sơ bảo mật tài khoản và ảnh đại diện của bạn tại Aurelia
            </p>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success d-flex align-items-center gap-2 mb-4 border-0 shadow-sm" style="border-radius: 12px; background: #ecfdf5; color: #065f46;">
            <i data-lucide="check-circle" style="width: 20px; height: 20px; color: #10b981;"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger d-flex flex-column gap-1 mb-4 border-0 shadow-sm" style="border-radius: 12px; background: #fef2f2; color: #991b1b;">
            <div class="d-flex align-items-center gap-2 fw-semibold">
                <i data-lucide="alert-circle" style="width: 20px; height: 20px; color: #ef4444;"></i>
                <span>Vui lòng kiểm tra lại thông tin:</span>
            </div>
            <ul class="mb-0 ps-4 small">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row g-4">
        <!-- Sidebar Navigation -->
        <div class="col-lg-3">
            <div class="card-modern p-3 shadow-sm border sticky-top" style="top: 85px;">
                <div class="d-flex align-items-center gap-3 p-2 mb-3 border-bottom">
                    <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="rounded-circle object-fit-cover shadow-sm border" style="width: 48px; height: 48px;" id="sidebarAvatarPreview">
                    <div class="min-w-0">
                        <div class="fw-bold text-dark text-truncate">{{ $user->name }}</div>
                        <div class="text-secondary small text-truncate">{{ $user->email }}</div>
                    </div>
                </div>

                <div class="d-flex flex-column gap-1">
                    <a href="{{ route('account.profile') }}" class="btn-brand-primary w-100 text-start py-2 px-3 text-decoration-none d-flex align-items-center gap-2">
                        <i data-lucide="user" style="width: 16px; height: 16px;"></i>
                        <span>Thông tin tài khoản</span>
                    </a>
                    <a href="{{ route('account.orders') }}" class="btn-surface w-100 text-start py-2 px-3 text-decoration-none d-flex align-items-center gap-2">
                        <i data-lucide="package" style="width: 16px; height: 16px;"></i>
                        <span>Đơn hàng của tôi</span>
                    </a>
                    <a href="{{ route('account.addresses') }}" class="btn-surface w-100 text-start py-2 px-3 text-decoration-none d-flex align-items-center gap-2">
                        <i data-lucide="map-pin" style="width: 16px; height: 16px;"></i>
                        <span>Sổ địa chỉ nhận hàng</span>
                    </a>
                    <a href="{{ route('account.wishlist') }}" class="btn-surface w-100 text-start py-2 px-3 text-decoration-none d-flex align-items-center gap-2">
                        <i data-lucide="heart" style="width: 16px; height: 16px;"></i>
                        <span>Danh sách yêu thích</span>
                    </a>
                    <a href="{{ route('account.coins') }}" class="btn-surface w-100 text-start py-2 px-3 text-decoration-none d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-coin text-warning"></i>
                            <span>Ví Xu Aurelia</span>
                        </div>
                        <span class="badge bg-warning-subtle text-dark fw-bold rounded-pill px-2" style="font-size: 0.7rem;">
                            {{ number_format($user->coins_balance) }} Xu
                        </span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="col-lg-9">
            <!-- Profile Form Card -->
            <div class="card-modern p-4 p-md-4 mb-4 shadow-sm border">
                <h2 class="h5 fw-bold text-dark mb-4 d-flex align-items-center gap-2 border-bottom pb-3">
                    <i data-lucide="user-check" style="width: 20px; height: 20px; color: var(--brand-600);"></i>
                    <span>Thông Tin Cơ Bản</span>
                </h2>

                <form action="{{ route('account.profile.update') }}" method="POST" enctype="multipart/form-data" id="profileForm">
                    @csrf
                    @method('PUT')

                    <!-- Avatar Upload Section -->
                    <div class="d-flex flex-column flex-sm-row align-items-center gap-4 mb-4 pb-4 border-bottom">
                        <div class="position-relative">
                            <div class="position-relative overflow-hidden rounded-circle border shadow-sm" style="width: 100px; height: 100px; background: #f8fafc;">
                                <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" id="avatarPreview" class="w-100 h-100 object-fit-cover">
                            </div>
                            <label for="avatarInput" class="position-absolute bottom-0 end-0 bg-primary text-white rounded-circle shadow d-flex align-items-center justify-content-center cursor-pointer hover-lift" style="width: 32px; height: 32px; cursor: pointer;" title="Tải ảnh mới">
                                <i data-lucide="camera" style="width: 16px; height: 16px;"></i>
                            </label>
                            <input type="file" name="avatar" id="avatarInput" class="d-none" accept="image/jpeg,image/png,image/webp,image/gif" onchange="handleAvatarChange(this)">
                        </div>
                        <div class="text-center text-sm-start">
                            <h3 class="fw-bold text-dark mb-1" style="font-size: 1.05rem;">Ảnh đại diện</h3>
                            <p class="text-secondary small mb-2">Tải ảnh thật từ thiết bị của bạn. Hỗ trợ JPG, PNG, WEBP (tối đa 2MB).</p>
                            <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-1.5 d-inline-flex align-items-center gap-1.5" onclick="document.getElementById('avatarInput').click()">
                                <i data-lucide="upload" style="width: 14px; height: 14px;"></i>
                                <span>Chọn ảnh từ máy</span>
                            </button>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-dark small">Họ và tên <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $user->name) }}" required placeholder="Nhập họ và tên">
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-dark small">Địa chỉ Email</label>
                            <input type="email" class="form-control bg-light" value="{{ $user->email }}" disabled readonly>
                            <span class="form-text text-muted small" style="font-size: 0.75rem;">Email được dùng để đăng nhập và bảo mật tài khoản.</span>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-dark small">Số điện thoại</label>
                            <input type="tel" name="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone', $user->phone) }}" placeholder="VD: 0912345678">
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-dark small">Ngày sinh</label>
                            <input type="date" name="birthday" class="form-control @error('birthday') is-invalid @enderror" value="{{ old('birthday', $user->birthday?->format('Y-m-d')) }}" max="{{ date('Y-m-d') }}">
                            @error('birthday')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mt-4 pt-3 border-top text-end">
                        <button type="submit" class="btn-brand-primary py-2 px-4 d-inline-flex align-items-center gap-2">
                            <i data-lucide="save" style="width: 16px; height: 16px;"></i>
                            <span>Lưu Thay Đổi</span>
                        </button>
                    </div>
                </form>
            </div>

            <!-- Change Password Card -->
            <div class="card-modern p-4 p-md-4 shadow-sm border">
                <h2 class="h5 fw-bold text-dark mb-4 d-flex align-items-center gap-2 border-bottom pb-3">
                    <i data-lucide="lock" style="width: 20px; height: 20px; color: var(--brand-600);"></i>
                    <span>Đổi Mật Khẩu</span>
                </h2>

                <form action="{{ route('account.password.update') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold text-dark small">Mật khẩu hiện tại <span class="text-danger">*</span></label>
                            <input type="password" name="current_password" class="form-control @error('current_password') is-invalid @enderror" required placeholder="••••••••">
                            @error('current_password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold text-dark small">Mật khẩu mới <span class="text-danger">*</span></label>
                            <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" required placeholder="Tối thiểu 8 ký tự">
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold text-dark small">Xác nhận mật khẩu mới <span class="text-danger">*</span></label>
                            <input type="password" name="password_confirmation" class="form-control" required placeholder="Nhập lại mật khẩu mới">
                        </div>
                    </div>

                    <div class="mt-4 pt-3 border-top text-end">
                        <button type="submit" class="btn btn-outline-dark rounded-pill py-2 px-4 fw-semibold d-inline-flex align-items-center gap-2">
                            <i data-lucide="shield-check" style="width: 16px; height: 16px;"></i>
                            <span>Cập Nhật Mật Khẩu</span>
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
    function handleAvatarChange(input) {
        if (input.files && input.files[0]) {
            const file = input.files[0];
            
            // Validate file size (2MB)
            if (file.size > 2 * 1024 * 1024) {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Ảnh quá dung lượng',
                        text: 'Dung lượng ảnh đại diện vượt quá giới hạn cho phép (tối đa 2MB). Vui lòng chọn ảnh khác.',
                        confirmButtonColor: '#e11d48',
                        customClass: { popup: 'rounded-4 shadow-lg' }
                    });
                }
                input.value = '';
                return;
            }

            // Real-time preview image
            const reader = new FileReader();
            reader.onload = function(e) {
                const preview = document.getElementById('avatarPreview');
                if (preview) {
                    preview.src = e.target.result;
                }
                const sidebarPreview = document.getElementById('sidebarAvatarPreview');
                if (sidebarPreview) {
                    sidebarPreview.src = e.target.result;
                }
            };
            reader.readAsDataURL(file);
        }
    }
</script>
@endsection
