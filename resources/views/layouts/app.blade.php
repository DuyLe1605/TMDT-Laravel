<!DOCTYPE html>
<html lang="vi" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Hub') - TMDT Túi Xách Nữ</title>
    
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
<body>
    <div class="admin-layout">
        <!-- Modern Admin Sidebar -->
        <x-sidebar />

        <!-- Main Content Area Wrapper -->
        <div class="admin-main">
            <!-- Top App Header -->
            <header class="admin-header">
                <div class="d-flex align-items-center gap-3">
                    <!-- Mobile Hamburger Button -->
                    <button type="button" class="btn btn-surface d-lg-none p-2" onclick="toggleSidebar()" aria-label="Toggle Navigation">
                        <i data-lucide="menu" style="width: 18px; height: 18px;"></i>
                    </button>

                    <!-- Quick System Breadcrumb / Title -->
                    <div class="d-flex align-items-center gap-2 text-secondary small d-none d-sm-flex">
                        <i data-lucide="sparkles" class="text-primary" style="width: 16px; height: 16px;"></i>
                        <span class="fw-semibold">Túi Xách Nữ &bull; TMDT Admin Portal</span>
                    </div>
                </div>

                <!-- Right Header Actions -->
                <div class="d-flex align-items-center gap-3">
                    <!-- Live Status Indicator -->
                    <div class="system-status-pill d-none d-md-inline-flex">
                        <span class="status-pulse-dot"></span>
                        <span>Trực tuyến</span>
                    </div>

                    <!-- Dark / Light Mode Switcher Toggle Button -->
                    <button type="button" class="theme-toggle-btn" id="themeToggleBtn" onclick="toggleTheme()" title="Chuyển đổi chế độ Sáng / Tối" aria-label="Toggle Dark Mode">
                        <i data-lucide="sun" id="themeIconSun" style="width: 17px; height: 17px; display: none;"></i>
                        <i data-lucide="moon" id="themeIconMoon" style="width: 17px; height: 17px; display: none;"></i>
                    </button>
                </div>
            </header>

            <!-- Global Toast Alert Container -->
            <x-alert />

            <!-- Main Dynamic Page Content Container -->
            <main class="container-fluid px-4 px-xl-5 py-4 flex-grow-1" style="padding-bottom: 2.5rem;">
                @yield('content')
            </main>

            <!-- Sleek Minimal Footer -->
            <x-footer />
        </div>
    </div>

    <!-- Global Logout Confirmation Modal -->
    <div class="modal fade" id="globalLogoutModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" style="max-width: 430px;">
            <div class="modal-content modal-content-modern border-0">
                <div class="p-4 text-center">
                    <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 58px; height: 58px; background: var(--danger-50); color: var(--danger-600);">
                        <i data-lucide="log-out" style="width: 28px; height: 28px;"></i>
                    </div>
                    <h5 class="fw-bold text-dark mb-2">Xác nhận đăng xuất</h5>
                    <p class="text-secondary small mb-4">
                        Bạn có chắc chắn muốn kết thúc phiên làm việc quản trị hiện tại không?
                    </p>
                    <div class="d-flex justify-content-center gap-3">
                        <button type="button" class="btn-surface px-4 py-2" data-bs-dismiss="modal">
                            <span>Ở lại</span>
                        </button>
                        <button type="button" class="btn btn-danger px-4 py-2 rounded-3 fw-semibold d-inline-flex align-items-center" onclick="document.getElementById('globalLogoutForm').submit()">
                            <i data-lucide="log-out" style="width: 16px; height: 16px; margin-right: 0.4rem;"></i>
                            <span>Đăng xuất ngay</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Hidden Global Logout Form -->
    <form id="globalLogoutForm" action="{{ route('logout') }}" method="POST" class="d-none">
        @csrf
    </form>

    <!-- Bootstrap 5 Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Zod-like Client-Side Form Validator & Floating Toast System -->
    <script src="{{ asset('js/validator.js') }}"></script>

    <!-- Global Scripts: Theme, Sidebar, Modals & Icons -->
    <script>
        let logoutModalInstance = null;

        function openLogoutModal() {
            const modalEl = document.getElementById('globalLogoutModal');
            if (modalEl) {
                if (!logoutModalInstance) {
                    logoutModalInstance = new bootstrap.Modal(modalEl);
                }
                logoutModalInstance.show();
                setTimeout(() => {
                    if (typeof lucide !== 'undefined') lucide.createIcons();
                }, 150);
            }
        }

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

        function toggleSidebar() {
            const sidebar = document.getElementById('adminSidebar');
            const backdrop = document.getElementById('sidebarBackdrop');
            if (sidebar && backdrop) {
                sidebar.classList.toggle('show');
                backdrop.classList.toggle('show');
            }
        }

        document.addEventListener('DOMContentLoaded', function () {
            // Setup correct initial theme icon
            const initialTheme = document.documentElement.getAttribute('data-theme') || 'light';
            updateThemeIcon(initialTheme);

            // Initialize Lucide Icons
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }

            // Initialize Bootstrap Tooltips
            const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
            [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));
        });
    </script>
    @yield('scripts')
</body>
</html>
