<!DOCTYPE html>
<html lang="vi" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Không Tìm Thấy Trang &bull; Aurelia Luxury Bags</title>
    
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
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
</head>
<body class="error-page-body">
    <div class="error-page-container">
        <!-- Top Nav Branding -->
        <header class="d-flex justify-content-between align-items-center py-4">
            <a href="{{ route('home') }}" class="d-flex align-items-center text-decoration-none">
                <div class="brand-logo-badge me-2.5">
                    <i data-lucide="shopping-bag" style="width: 20px; height: 20px;"></i>
                </div>
                <div class="d-flex align-items-baseline gap-1">
                    <span class="fw-extrabold text-dark tracking-tight fs-5" style="font-family: 'Plus Jakarta Sans', sans-serif;">AURELIA</span>
                    <span class="badge bg-primary-subtle text-primary fw-bold px-2 py-0.5 rounded-pill" style="font-size: 0.65rem;">TÚI XÁCH NỮ</span>
                </div>
            </a>

            <!-- Theme Toggle -->
            <button type="button" class="theme-toggle-btn" id="themeToggleBtn" onclick="toggleTheme()" title="Chuyển đổi Sáng / Tối" aria-label="Toggle Dark Mode">
                <i data-lucide="sun" id="themeIconSun" style="width: 17px; height: 17px; display: none;"></i>
                <i data-lucide="moon" id="themeIconMoon" style="width: 17px; height: 17px; display: none;"></i>
            </button>
        </header>

        <!-- Main 404 Hero Card -->
        <main class="error-card-wrapper my-auto py-4">
            <div class="error-card text-center p-4 p-md-5">
                <!-- Icon Visual Badge -->
                <div class="error-visual-badge mx-auto mb-3">
                    <i data-lucide="compass" style="width: 36px; height: 36px;"></i>
                </div>

                <!-- Big 404 Number -->
                <div class="error-code-badge mb-2">
                    <span>404</span>
                </div>

                <h1 class="fw-bold text-dark mb-2" style="letter-spacing: -0.02em; font-size: 1.65rem;">
                    Không Tìm Thấy Trang Yêu Cầu
                </h1>
                
                <p class="text-secondary small mb-4 mx-auto" style="max-width: 460px; line-height: 1.7; font-size: 0.92rem;">
                    Đường dẫn bạn vừa truy cập không tồn tại, đã được thay đổi hoặc bạn không có quyền hạn truy cập khu vực này.
                </p>

                <!-- Navigation CTAs -->
                <div class="d-flex flex-wrap justify-content-center align-items-center gap-3 mb-4">
                    <a href="{{ route('home') }}" class="btn-brand-primary py-2.5 px-4">
                        <i data-lucide="home" style="width: 17px; height: 17px; margin-right: 0.45rem;"></i>
                        <span>Về Trang Chủ</span>
                    </a>
                    
                    <a href="{{ route('shop.index') }}" class="btn-surface py-2.5 px-4 text-decoration-none">
                        <i data-lucide="shopping-bag" style="width: 17px; height: 17px; margin-right: 0.45rem;"></i>
                        <span>Xem Bộ Sưu Tập</span>
                    </a>
                </div>

                <!-- Fast Links Helper -->
                <div class="error-links-box p-3 rounded-3 mt-4 text-start mx-auto" style="max-width: 480px;">
                    <div class="fw-semibold text-dark small mb-2 d-flex align-items-center gap-1.5">
                        <i data-lucide="sparkles" class="text-primary" style="width: 15px; height: 15px;"></i>
                        <span>Gợi ý điều hướng nhanh:</span>
                    </div>
                    <div class="row g-2 small">
                        <div class="col-6">
                            <a href="{{ route('home') }}" class="text-decoration-none text-secondary d-flex align-items-center gap-1.5 hover-primary py-1">
                                <i data-lucide="chevron-right" style="width: 13px; height: 13px;"></i>
                                <span>Trang chủ bán hàng</span>
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="{{ route('shop.index') }}" class="text-decoration-none text-secondary d-flex align-items-center gap-1.5 hover-primary py-1">
                                <i data-lucide="chevron-right" style="width: 13px; height: 13px;"></i>
                                <span>Tất cả mẫu túi xách</span>
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="{{ route('login') }}" class="text-decoration-none text-secondary d-flex align-items-center gap-1.5 hover-primary py-1">
                                <i data-lucide="chevron-right" style="width: 13px; height: 13px;"></i>
                                <span>Đăng nhập tài khoản</span>
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="{{ route('register') }}" class="text-decoration-none text-secondary d-flex align-items-center gap-1.5 hover-primary py-1">
                                <i data-lucide="chevron-right" style="width: 13px; height: 13px;"></i>
                                <span>Đăng ký thành viên</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </main>

        <!-- Footer -->
        <footer class="py-3 text-center text-secondary small">
            &copy; {{ date('Y') }} Aurelia Luxury Bags &bull; Hệ thống TMDT Túi Xách Nữ
        </footer>
    </div>

    <!-- Bootstrap 5 Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
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

        document.addEventListener('DOMContentLoaded', function () {
            const initialTheme = document.documentElement.getAttribute('data-theme') || 'light';
            updateThemeIcon(initialTheme);
            if (typeof lucide !== 'undefined') lucide.createIcons();
        });
    </script>
</body>
</html>
