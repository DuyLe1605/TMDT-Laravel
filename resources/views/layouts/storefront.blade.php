<!DOCTYPE html>
<html lang="vi" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Cửa Hàng Túi Xách Nữ Cao Cấp') - Aurelia Luxury</title>
    
    <!-- Anti-flicker Theme Init Script -->
    <script>
        (function () {
            const savedTheme = localStorage.getItem('tmdt-theme') || 
                (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
            document.documentElement.setAttribute('data-theme', savedTheme);
        })();
    </script>

    <!-- Google Fonts: Plus Jakarta Sans & JetBrains Mono -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@500;600&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
    
    <!-- SweetAlert2 Modern Dialogs -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <!-- Bespoke Design System Stylesheet -->
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}?v={{ filemtime(public_path('css/custom.css')) }}">
    @yield('styles')
</head>
<body class="storefront-body">
    <!-- Top Announcement Bar -->
    <div class="storefront-announcement py-2">
        <div class="container d-flex flex-wrap justify-content-between align-items-center gap-2">
            <div class="d-flex align-items-center gap-2.5 small">
                <span class="badge bg-white text-primary px-2.5 py-1 rounded-pill fw-bold" style="font-size: 0.72rem; letter-spacing: 0.03em;">ƯU ĐÃI</span>
                <span class="text-white fw-medium">Tặng Voucher Freeship 30K &bull; Giao hàng toàn quốc &bull; Đổi trả 30 ngày</span>
            </div>
            <div class="d-flex align-items-center gap-3 small d-none d-md-flex text-white-50">
                <span class="d-inline-flex align-items-center gap-1.5 text-white">
                    <i data-lucide="shield-check" style="width: 15px; height: 15px;"></i>
                    <span>100% Da cao cấp tuyển chọn</span>
                </span>
                <span class="text-white-50">&bull;</span>
                <span class="d-inline-flex align-items-center gap-1.5 text-white">
                    <i data-lucide="phone" style="width: 14px; height: 14px;"></i>
                    <span>Hotline: 1900 8888</span>
                </span>
            </div>
        </div>
    </div>

    <!-- Main Storefront Navigation Header -->
    <header class="storefront-navbar sticky-top" style="background-color: var(--bg-surface) !important; z-index: 1050 !important;">
        <div class="container d-flex align-items-center justify-content-between py-2.5">
            <!-- Brand Logo -->
            <a href="{{ route('home') }}" class="d-flex align-items-center text-decoration-none">
                <div class="brand-logo-badge me-2.5">
                    <i data-lucide="shopping-bag" style="width: 22px; height: 22px;"></i>
                </div>
                <div>
                    <div class="d-flex align-items-baseline gap-1">
                        <span class="fw-extrabold text-dark tracking-tight fs-4" style="font-family: 'Plus Jakarta Sans', sans-serif;">AURELIA</span>
                        <span class="badge bg-primary-subtle text-primary fw-bold px-2 py-0.5 rounded-pill" style="font-size: 0.68rem; letter-spacing: 0.05em;">TÚI XÁCH NỮ</span>
                    </div>
                </div>
            </a>

            <!-- Nav Links -->
            <nav class="d-none d-lg-flex align-items-center gap-2">
                <a href="{{ route('home') }}" class="storefront-nav-link {{ request()->routeIs('home') ? 'active' : '' }}">
                    <span>Trang chủ</span>
                </a>
                <a href="{{ route('shop.index') }}" class="storefront-nav-link {{ request()->routeIs('shop.*') ? 'active' : '' }}">
                    <span>Bộ sưu tập túi</span>
                </a>
            </nav>

            <!-- Header Right Actions (Search / Cart / Theme / Auth) -->
            <div class="d-flex align-items-center gap-2.5">
                <!-- Shopping Cart Button with Dynamic Live Badge -->
                <a href="{{ route('cart.index') }}" class="header-icon-btn" title="Giỏ hàng của bạn" aria-label="Giỏ hàng">
                    <i data-lucide="shopping-cart"></i>
                    <span class="header-icon-badge cart-badge-count" style="display: none;">
                        0
                    </span>
                </a>

                <!-- Wishlist Heart Button -->
                @auth
                <a href="{{ route('account.wishlist') }}" class="header-icon-btn" title="Danh sách yêu thích" aria-label="Yêu thích">
                    <i data-lucide="heart"></i>
                    @php $wCount = Auth::user()->wishlists()->count(); @endphp
                    <span class="header-icon-badge wishlist-badge-count" style="{{ $wCount > 0 ? '' : 'display: none;' }}">
                        {{ $wCount }}
                    </span>
                </a>
                @else
                <a href="{{ route('login') }}" class="header-icon-btn" title="Danh sách yêu thích (Yêu cầu đăng nhập)" aria-label="Yêu thích">
                    <i data-lucide="heart"></i>
                </a>
                @endauth

                <!-- Theme Toggle Button -->
                <button type="button" class="header-icon-btn" id="themeToggleBtn" onclick="toggleTheme()" title="Chuyển đổi Sáng / Tối" aria-label="Toggle Dark Mode">
                    <i data-lucide="sun" id="themeIconSun" style="display: none;"></i>
                    <i data-lucide="moon" id="themeIconMoon" style="display: none;"></i>
                </button>

                <!-- Auth Navigation Buttons -->
                @guest
                    <a href="{{ route('login') }}" class="header-auth-btn header-auth-btn-surface">
                        <i data-lucide="log-in"></i>
                        <span>Đăng nhập</span>
                    </a>
                    <a href="{{ route('register') }}" class="header-auth-btn header-auth-btn-primary d-none d-sm-inline-flex">
                        <i data-lucide="user-plus"></i>
                        <span>Đăng ký</span>
                    </a>
                @else
                    <div class="vr mx-1 opacity-25 d-none d-sm-block" style="height: 24px;"></div>

                    <!-- Logged in User Dropdown -->
                    <div class="dropdown">
                        <button class="header-user-btn" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <img src="{{ Auth::user()->avatar_url }}" alt="{{ Auth::user()->name }}" class="rounded-circle object-fit-cover border shadow-sm flex-shrink-0" style="width: 28px; height: 28px;">
                            <div class="text-start d-none d-sm-block">
                                <div class="fw-bold text-dark small leading-tight">{{ Auth::user()->name }}</div>
                                <div class="text-secondary" style="font-size: 0.72rem;">
                                    @if (Auth::user()->isAdmin())
                                        <span class="badge bg-danger-subtle text-danger px-1.5 py-0.5 rounded-pill fw-semibold" style="font-size: 0.65rem;">Quản trị viên</span>
                                    @else
                                        <span>Khách hàng</span>
                                    @endif
                                </div>
                            </div>
                            <i data-lucide="chevron-down" style="width: 15px; height: 15px;" class="text-secondary"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-modern dropdown-menu-end shadow-lg" style="min-width: 270px;">
                            <!-- User Mini Header in Dropdown -->
                            <li class="dropdown-user-header d-flex align-items-center gap-2.5">
                                <img src="{{ Auth::user()->avatar_url }}" alt="{{ Auth::user()->name }}" class="rounded-circle object-fit-cover border shadow-sm flex-shrink-0" style="width: 42px; height: 42px;">
                                <div class="min-w-0 flex-grow-1">
                                    <div class="fw-bold text-dark text-truncate" style="font-size: 0.9rem; letter-spacing: -0.01em;">{{ Auth::user()->name }}</div>
                                    <div class="text-secondary text-truncate" style="font-size: 0.74rem;">{{ Auth::user()->email }}</div>
                                </div>
                            </li>

                            @if (Auth::user()->isAdmin())
                                <li class="my-1">
                                    <a href="{{ route('admin.dashboard') }}" class="dropdown-item-modern dropdown-item-admin fw-semibold d-flex align-items-center justify-content-between">
                                        <span class="d-flex align-items-center gap-2">
                                            <i data-lucide="layout-dashboard" style="width: 16px; height: 16px;" class="text-primary flex-shrink-0"></i>
                                            <span>Trang Quản trị (Admin)</span>
                                        </span>
                                        <i data-lucide="arrow-up-right" style="width: 14px; height: 14px;" class="text-primary opacity-50 flex-shrink-0"></i>
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider-modern my-1"></li>
                            @endif

                            <li class="{{ Auth::user()->isAdmin() ? '' : 'pt-1' }}">
                                <a href="{{ route('account.profile') }}" class="dropdown-item-modern">
                                    <i data-lucide="user" style="width: 16px; height: 16px; margin-right: 0.65rem;" class="text-secondary"></i>
                                    <span>Thông tin tài khoản</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('account.orders') }}" class="dropdown-item-modern">
                                    <i data-lucide="package" style="width: 16px; height: 16px; margin-right: 0.65rem;" class="text-secondary"></i>
                                    <span>Đơn hàng của tôi</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('account.addresses') }}" class="dropdown-item-modern">
                                    <i data-lucide="map-pin" style="width: 16px; height: 16px; margin-right: 0.65rem;" class="text-secondary"></i>
                                    <span>Sổ địa chỉ nhận hàng</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('account.wishlist') }}" class="dropdown-item-modern">
                                    <i data-lucide="heart" style="width: 16px; height: 16px; margin-right: 0.65rem;" class="text-danger"></i>
                                    <span>Danh sách yêu thích</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('account.coins') }}" class="dropdown-item-modern d-flex align-items-center justify-content-between">
                                    <div class="d-flex align-items-center">
                                        <i data-lucide="coins" style="width: 16px; height: 16px; margin-right: 0.65rem;" class="text-warning"></i>
                                        <span>Ví Xu Aurelia</span>
                                    </div>
                                    <span class="badge bg-warning-subtle text-dark fw-bold rounded-pill px-2" style="font-size: 0.72rem;">
                                        {{ number_format(Auth::user()->coins_balance) }} Xu
                                    </span>
                                </a>
                            </li>
                            <li><hr class="dropdown-divider-modern"></li>
                            <li>
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="dropdown-item-modern item-danger w-100 border-0 bg-transparent text-start">
                                        <i data-lucide="log-out" style="width: 16px; height: 16px; margin-right: 0.65rem;"></i>
                                        <span>Đăng xuất</span>
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                @endguest
            </div>
        </div>
    </header>

    <!-- Global Toast Alert Container -->
    <x-alert />

    <!-- Main Content Area -->
    <main>
        @yield('content')
    </main>

    <!-- Sleek Storefront Footer -->
    <footer class="storefront-footer">
        <div class="container py-5">
            <div class="row g-4 justify-content-between">
                <!-- Brand Column -->
                <div class="col-lg-4 col-md-6">
                    <div class="d-flex align-items-center mb-3">
                        <div class="brand-logo-badge me-2.5">
                            <i data-lucide="shopping-bag" style="width: 20px; height: 20px;"></i>
                        </div>
                        <span class="fw-bold text-dark fs-5">AURELIA BAGS</span>
                    </div>
                    <p class="text-secondary small mb-3" style="line-height: 1.7;">
                        Thương hiệu thời trang túi xách nữ cao cấp. Mang đến cho phái đẹp sự sang trọng, thanh lịch và cuốn hút trong từng chi tiết thiết kế.
                    </p>
                    <div class="d-flex align-items-center gap-2 text-secondary small">
                        <i data-lucide="map-pin" class="text-primary" style="width: 16px; height: 16px;"></i>
                        <span>Việt Nam &bull; Dự Án TMDT</span>
                    </div>
                </div>

                <!-- Fast Links -->
                <div class="col-lg-2 col-md-3 col-6">
                    <h6 class="fw-bold text-dark mb-3">Danh Mục</h6>
                    <ul class="list-unstyled d-flex flex-column gap-2 small text-secondary mb-0">
                        <li><a href="{{ route('shop.index') }}" class="text-decoration-none text-secondary hover-primary">Tất cả túi xách</a></li>
                        <li><a href="{{ route('shop.index', ['sort' => 'created_desc']) }}" class="text-decoration-none text-secondary hover-primary">Hàng mới về</a></li>
                        <li><a href="{{ route('shop.index', ['sort' => 'price_asc']) }}" class="text-decoration-none text-secondary hover-primary">Ưu đãi tốt nhất</a></li>
                    </ul>
                </div>

                <!-- Support & Guarantees -->
                <div class="col-lg-2 col-md-3 col-6">
                    <h6 class="fw-bold text-dark mb-3">Chính Sách</h6>
                    <ul class="list-unstyled d-flex flex-column gap-2 small text-secondary mb-0">
                        <li><span>Đổi trả 30 ngày</span></li>
                        <li><span>Bảo hành da 12 tháng</span></li>
                        <li><span>Giao hàng hỏa tốc</span></li>
                        <li><span>Kiểm tra khi nhận hàng</span></li>
                    </ul>
                </div>

                <!-- Business Info & Features -->
                <div class="col-lg-3 col-md-6">
                    <h6 class="fw-bold text-dark mb-3">Cam Kết Chất Lượng</h6>
                    <div class="p-3 rounded-3" style="background: var(--bg-surface-subtle); border: 1px solid var(--border-default);">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <i data-lucide="check-circle-2" class="text-success" style="width: 18px; height: 18px;"></i>
                            <span class="fw-semibold text-dark small">100% Ảnh thật sản phẩm</span>
                        </div>
                        <p class="text-secondary small mb-0" style="font-size: 0.82rem;">
                            Sản phẩm sắc nét từng đường kim mũi chỉ, đúng như hình ảnh hiển thị trên website.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Bottom Copyright -->
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 pt-4 mt-5 border-top small text-secondary">
                <div>
                    <span>&copy; {{ date('Y') }} <strong>Aurelia Luxury Bags</strong>. Dự án TMDT Túi Xách Nữ.</span>
                </div>
                <div class="d-flex align-items-center gap-3">
                    <span class="d-inline-flex align-items-center gap-1">
                        <i data-lucide="sparkles" class="text-primary" style="width: 14px; height: 14px;"></i>
                        <span>Laravel 13 Architecture</span>
                    </span>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap 5 Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Client-Side Form Validator -->
    <script src="{{ asset('js/validator.js') }}"></script>
    
    <!-- Vietnam Administrative Locations Cascading Selector Helper -->
    <script src="{{ asset('js/vn-locations.js') }}"></script>
    
    <script>
        function updateThemeIcon(theme) {
            const sunIcon = document.getElementById('themeIconSun');
            const moonIcon = document.getElementById('themeIconMoon');
            if (sunIcon && moonIcon) {
                if (theme === 'dark') {
                    sunIcon.style.display = 'block';
                    moonIcon.style.display = 'none';
                } else {
                    sunIcon.style.display = 'none';
                    moonIcon.style.display = 'block';
                }
            }
        }

        function toggleTheme() {
            const currentTheme = document.documentElement.getAttribute('data-theme') || 'light';
            const targetTheme = currentTheme === 'dark' ? 'light' : 'dark';
            document.documentElement.setAttribute('data-theme', targetTheme);
            localStorage.setItem('tmdt-theme', targetTheme);
            updateThemeIcon(targetTheme);
            if (typeof lucide !== 'undefined') lucide.createIcons();
        }

        // Fetch current cart count dynamically
        async function refreshCartBadge() {
            try {
                const res = await fetch('{{ route("cart.count") }}', {
                    headers: { 'Accept': 'application/json' }
                });
                const data = await res.json();
                if (data && typeof data.count !== 'undefined') {
                    const badges = document.querySelectorAll('.cart-badge-count');
                    badges.forEach(b => {
                        b.textContent = data.count;
                        b.style.display = data.count > 0 ? 'inline-flex' : 'none';
                    });
                }
            } catch (e) {}
        }

        /**
         * Global AJAX toggle wishlist for product cards and details with SweetAlert2.
         */
        async function toggleWishlist(productId, btnEl) {
            @guest
                Swal.fire({
                    title: 'Đăng nhập để yêu thích',
                    text: 'Vui lòng đăng nhập để lưu sản phẩm vào danh sách yêu thích của bạn.',
                    icon: 'info',
                    showCancelButton: true,
                    confirmButtonColor: '#e11d48',
                    cancelButtonColor: '#64748b',
                    confirmButtonText: 'Đăng nhập ngay',
                    cancelButtonText: 'Để sau',
                    customClass: { popup: 'rounded-4 shadow-lg' }
                }).then((res) => {
                    if (res.isConfirmed) {
                        window.location.href = "{{ route('login') }}";
                    }
                });
                return;
            @endguest

            try {
                const res = await fetch(`/wishlist/toggle/${productId}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                });

                if (res.status === 401) {
                    window.location.href = "{{ route('login') }}";
                    return;
                }

                const data = await res.json();
                if (data.success) {
                    // Update all buttons for this productId across current page
                    const buttons = document.querySelectorAll(`[data-wishlist-id="${productId}"]`);
                    buttons.forEach(btn => {
                        const icon = btn.querySelector('i, svg');
                        if (data.added) {
                            btn.classList.add('wishlisted', 'active');
                            if (icon) {
                                icon.classList.remove('text-secondary');
                                icon.classList.add('text-danger');
                                icon.style.fill = 'currentColor';
                            }
                        } else {
                            btn.classList.remove('wishlisted', 'active');
                            if (icon) {
                                icon.classList.remove('text-danger');
                                icon.classList.add('text-secondary');
                                icon.style.fill = 'none';
                            }
                        }
                    });

                    // Update wishlist badge in header
                    const badges = document.querySelectorAll('.wishlist-badge-count');
                    badges.forEach(b => {
                        b.textContent = data.count;
                        b.style.display = data.count > 0 ? 'inline-flex' : 'none';
                    });

                    // Modern Toast notification
                    const Toast = Swal.mixin({
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 2500,
                        timerProgressBar: true
                    });
                    Toast.fire({
                        icon: data.added ? 'success' : 'info',
                        title: data.message
                    });

                    if (typeof lucide !== 'undefined') lucide.createIcons();
                }
            } catch (err) {
                console.error('Wishlist toggle error:', err);
            }
        }

        // Global Dialog Helpers replacing window.alert / window.confirm
        window.showToast = function(message, icon = 'success') {
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true
            });
            Toast.fire({ icon: icon, title: message });
        };

        window.showModalAlert = function(title, message, icon = 'info') {
            Swal.fire({
                title: title,
                text: message,
                icon: icon,
                confirmButtonColor: '#e11d48',
                customClass: { popup: 'rounded-4 shadow-lg' }
            });
        };

        window.showConfirmDialog = function(arg1, arg2, arg3) {
            let title = 'Xác nhận';
            let text = '';
            let icon = 'question';
            let confirmText = 'Đồng ý';
            let cancelText = 'Hủy bỏ';
            let onConfirm = null;

            if (typeof arg1 === 'object' && arg1 !== null) {
                title = arg1.title || title;
                text = arg1.text || '';
                icon = arg1.icon || icon;
                confirmText = arg1.confirmText || confirmText;
                cancelText = arg1.cancelText || cancelText;
                onConfirm = arg1.onConfirm;
            } else {
                text = arg1 || '';
                onConfirm = arg2;
                if (arg3) title = arg3;
            }

            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: title,
                    text: text,
                    icon: icon,
                    showCancelButton: true,
                    confirmButtonColor: '#e11d48',
                    cancelButtonColor: '#64748b',
                    confirmButtonText: confirmText,
                    cancelButtonText: cancelText,
                    customClass: { popup: 'rounded-4 shadow-lg' }
                }).then((res) => {
                    if (res.isConfirmed && typeof onConfirm === 'function') {
                        onConfirm();
                    }
                });
            } else if (confirm(text)) {
                if (typeof onConfirm === 'function') onConfirm();
            }
        };

        document.addEventListener('DOMContentLoaded', function () {
            const initialTheme = document.documentElement.getAttribute('data-theme') || 'light';
            updateThemeIcon(initialTheme);
            if (typeof lucide !== 'undefined') lucide.createIcons();
            refreshCartBadge();

            // Intercept data-confirm forms
            document.addEventListener('submit', function(e) {
                const form = e.target;
                if (form && form.dataset && form.dataset.confirm) {
                    e.preventDefault();
                    const msg = form.dataset.confirm;
                    const title = form.dataset.confirmTitle || 'Xác nhận thao tác';
                    window.showConfirmDialog(msg, () => {
                        const savedMsg = form.dataset.confirm;
                        delete form.dataset.confirm;
                        form.submit();
                        form.dataset.confirm = savedMsg;
                    }, title);
                }
            });
        });
    </script>
    
    <!-- Global Quick Add to Cart Modal -->
    <x-quick-add-modal />

    @yield('scripts')
</body>
</html>
