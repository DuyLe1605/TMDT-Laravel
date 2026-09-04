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
            <nav class="d-none d-lg-flex align-items-center gap-1">
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
                <a href="{{ route('cart.index') }}" class="btn-surface position-relative p-2 d-inline-flex align-items-center justify-content-center text-decoration-none" title="Giỏ hàng của bạn" aria-label="Giỏ hàng">
                    <i data-lucide="shopping-cart" style="width: 19px; height: 19px;"></i>
                    <span class="cart-badge-count position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.68rem; padding: 0.25em 0.5em; display: none;">
                        0
                    </span>
                </a>

                <!-- Theme Toggle Button -->
                <button type="button" class="theme-toggle-btn" id="themeToggleBtn" onclick="toggleTheme()" title="Chuyển đổi Sáng / Tối" aria-label="Toggle Dark Mode">
                    <i data-lucide="sun" id="themeIconSun" style="width: 17px; height: 17px; display: none;"></i>
                    <i data-lucide="moon" id="themeIconMoon" style="width: 17px; height: 17px; display: none;"></i>
                </button>

                <!-- Auth Navigation Buttons -->
                @guest
                    <a href="{{ route('login') }}" class="btn-surface py-2 px-3 text-decoration-none">
                        <i data-lucide="log-in" style="width: 16px; height: 16px; margin-right: 0.35rem;"></i>
                        <span>Đăng nhập</span>
                    </a>
                    <a href="{{ route('register') }}" class="btn-brand-primary py-2 px-3 text-decoration-none d-none d-sm-inline-flex">
                        <i data-lucide="user-plus" style="width: 16px; height: 16px; margin-right: 0.35rem;"></i>
                        <span>Đăng ký</span>
                    </a>
                @else
                    <!-- Logged in User Dropdown -->
                    <div class="dropdown">
                        <button class="btn-surface py-1.5 px-2.5 d-flex align-items-center gap-2" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <div class="sidebar-user-avatar" style="width: 32px; height: 32px; font-size: 0.82rem;">
                                {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                            </div>
                            <div class="text-start d-none d-sm-block">
                                <div class="fw-bold text-dark small leading-tight">{{ Auth::user()->name }}</div>
                                <div class="text-secondary" style="font-size: 0.72rem;">
                                    @if (Auth::user()->isAdmin())
                                        <span class="badge bg-danger-subtle text-danger p-0">Quản trị viên</span>
                                    @else
                                        <span>Khách hàng</span>
                                    @endif
                                </div>
                            </div>
                            <i data-lucide="chevron-down" style="width: 15px; height: 15px;" class="text-secondary"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-modern dropdown-menu-end shadow">
                            @if (Auth::user()->isAdmin())
                                <li>
                                    <a href="{{ route('admin.dashboard') }}" class="dropdown-item-modern text-primary fw-semibold">
                                        <i data-lucide="layout-dashboard" style="width: 16px; height: 16px; margin-right: 0.5rem;"></i>
                                        <span>Vào trang Quản trị (Admin)</span>
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider-modern"></li>
                            @endif
                            <li>
                                <a href="{{ route('account.orders') }}" class="dropdown-item-modern">
                                    <i data-lucide="package" style="width: 16px; height: 16px; margin-right: 0.5rem;"></i>
                                    <span>Đơn hàng của tôi</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('account.addresses') }}" class="dropdown-item-modern">
                                    <i data-lucide="map-pin" style="width: 16px; height: 16px; margin-right: 0.5rem;"></i>
                                    <span>Sổ địa chỉ nhận hàng</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('account.coins') }}" class="dropdown-item-modern d-flex align-items-center justify-content-between">
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-coin text-warning me-2" style="font-size: 1rem;"></i>
                                        <span>Ví Xu Aurelia</span>
                                    </div>
                                    <span class="badge bg-warning-subtle text-dark fw-bold rounded-pill" style="font-size: 0.72rem;">
                                        {{ number_format(Auth::user()->coins_balance) }} Xu
                                    </span>
                                </a>
                            </li>
                            <li><hr class="dropdown-divider-modern"></li>
                            <li>
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="dropdown-item-modern item-danger w-100 border-0 bg-transparent text-start">
                                        <i data-lucide="log-out" style="width: 16px; height: 16px; margin-right: 0.5rem;"></i>
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

        document.addEventListener('DOMContentLoaded', function () {
            const initialTheme = document.documentElement.getAttribute('data-theme') || 'light';
            updateThemeIcon(initialTheme);
            if (typeof lucide !== 'undefined') lucide.createIcons();
            refreshCartBadge();
        });
    </script>
    
    <!-- Global Quick Add to Cart Modal -->
    <x-quick-add-modal />

    @yield('scripts')
</body>
</html>
