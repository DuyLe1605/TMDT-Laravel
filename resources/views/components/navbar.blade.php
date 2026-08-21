<nav class="navbar navbar-expand-lg navbar-dark navbar-obsidian">
    <div class="container">
        <!-- Brand Logo & Identity -->
        <a class="navbar-brand d-flex align-items-center gap-2" href="{{ route('admin.dashboard') }}">
            <div class="brand-logo-badge">
                <i data-lucide="layers" style="width: 20px; height: 20px;"></i>
            </div>
            <div class="d-flex align-items-center gap-2">
                <span class="brand-title">TMDT</span>
                <span class="brand-tag">PORTAL</span>
            </div>
        </a>

        <!-- Mobile Menu Toggle -->
        <button class="navbar-toggler border-0 p-2 text-white opacity-75" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain" aria-controls="navbarMain" aria-expanded="false" aria-label="Toggle navigation">
            <i data-lucide="menu" style="width: 22px; height: 22px;"></i>
        </button>

        <!-- Navbar Links & Actions -->
        <div class="collapse navbar-collapse" id="navbarMain">
            <ul class="navbar-nav me-auto ps-lg-4 gap-1 my-2 my-lg-0">
                <li class="nav-item">
                    <a class="nav-link nav-link-obsidian {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}" href="{{ route('admin.categories.index') }}">
                        <i data-lucide="folder-tree" style="width: 16px; height: 16px;"></i>
                        <span>Quản lý Danh mục</span>
                    </a>
                </li>
            </ul>

            <!-- Right Controls: System Status & User Profile -->
            <div class="d-flex align-items-center gap-3 pt-2 pt-lg-0">
                <!-- Real-time Status Indicator -->
                <div class="system-status-pill d-none d-sm-inline-flex">
                    <span class="status-pulse-dot"></span>
                    <span>Hệ thống trực tuyến</span>
                </div>

                <!-- Admin Profile Pill -->
                <div class="d-flex align-items-center gap-2 ps-2 border-start border-white border-opacity-10">
                    <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold text-white bg-secondary bg-opacity-50" style="width: 32px; height: 32px; font-size: 0.8rem; border: 1px solid rgba(255,255,255,0.15);">
                        AD
                    </div>
                    <span class="text-white small fw-medium d-none d-md-inline" style="font-size: 0.85rem;">Admin</span>
                </div>
            </div>
        </div>
    </div>
</nav>
