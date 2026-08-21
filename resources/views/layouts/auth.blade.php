<!DOCTYPE html>
<html lang="vi" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Xác thực tài khoản') - TMDT Túi Xách Nữ Cao Cấp</title>

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
    @yield('styles')
</head>
<body class="auth-body">
    <div class="auth-container">
        <!-- Floating Brand Header -->
        <header class="auth-top-nav">
            <a href="{{ route('home') }}" class="auth-brand-link">
                <div class="brand-logo-badge">
                    <i data-lucide="shopping-bag" style="width: 20px; height: 20px;"></i>
                </div>
                <div class="d-flex align-items-baseline gap-1.5">
                    <span class="auth-brand-name">AURELIA</span>
                    <span class="auth-brand-tag">BAGS</span>
                </div>
            </a>
            
            <a href="{{ route('home') }}" class="auth-back-home">
                <i data-lucide="arrow-left" style="width: 16px; height: 16px;"></i>
                <span>Trang chủ</span>
            </a>
        </header>

        <!-- Main Auth Card Container -->
        <main class="auth-main-wrapper">
            <x-alert />
            @yield('content')
        </main>

        <!-- Footer Note -->
        <footer class="auth-footer text-center">
            <p class="text-secondary small mb-0">
                &copy; {{ date('Y') }} TMDT Túi Xách Nữ. Bản quyền thuộc về hệ sinh thái Aurelia Bags.
            </p>
        </footer>
    </div>

    <!-- Bootstrap 5 Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Client-Side Form Validator -->
    <script src="{{ asset('js/validator.js') }}"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }
        });

        // Toggle Password Visibility Helper
        function togglePasswordVisibility(inputId, toggleBtnId) {
            const input = document.getElementById(inputId);
            const btn = document.getElementById(toggleBtnId);
            if (!input || !btn) return;

            const isCurrentlyPassword = input.type === 'password';
            input.type = isCurrentlyPassword ? 'text' : 'password';

            btn.innerHTML = isCurrentlyPassword 
                ? '<i data-lucide="eye-off" style="width: 18px; height: 18px;"></i>'
                : '<i data-lucide="eye" style="width: 18px; height: 18px;"></i>';

            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }
        }
    </script>
    @yield('scripts')
</body>
</html>
