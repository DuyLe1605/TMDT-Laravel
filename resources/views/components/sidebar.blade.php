<aside class="admin-sidebar" id="adminSidebar">
    <!-- Sidebar Header & Brand -->
    <div class="sidebar-header">
        <a href="{{ route('admin.dashboard') }}" class="d-flex align-items-center text-decoration-none">
            <div class="brand-logo-badge">
                <i data-lucide="layers" style="width: 20px; height: 20px;"></i>
            </div>
            <div class="d-flex align-items-center">
                <span class="brand-title">AURELIA</span>
                <span class="brand-tag">ADMIN</span>
            </div>
        </a>

        <!-- Mobile Close Button -->
        <button type="button" class="btn btn-sm btn-link text-secondary d-lg-none p-0" onclick="toggleSidebar()" aria-label="Close sidebar">
            <i data-lucide="x" style="width: 20px; height: 20px;"></i>
        </button>
    </div>

    <!-- Sidebar Navigation Scroll Area -->
    <div class="sidebar-content">
        <!-- Section: Overview -->
        <div class="sidebar-section-title">TỔNG QUAN</div>
        <a href="{{ route('admin.dashboard') }}" class="sidebar-nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <div class="d-flex align-items-center">
                <span class="sidebar-icon-box">
                    <i data-lucide="layout-dashboard" style="width: 18px; height: 18px;"></i>
                </span>
                <span>Bảng điều khiển</span>
            </div>
        </a>

        <!-- Public Storefront Quick Link -->
        <a href="{{ route('home') }}" class="sidebar-nav-link" target="_blank">
            <div class="d-flex align-items-center">
                <span class="sidebar-icon-box">
                    <i data-lucide="external-link" style="width: 18px; height: 18px;"></i>
                </span>
                <span>Xem Cửa hàng (Public)</span>
            </div>
        </a>

        <!-- Section: E-Commerce Operations -->
        <div class="sidebar-section-title mt-3">QUẢN LÝ E-COMMERCE</div>
        
        <!-- Active Products Module -->
        <a href="{{ route('admin.products.index') }}" class="sidebar-nav-link {{ request()->routeIs('admin.products.*') ? 'active' : '' }}">
            <div class="d-flex align-items-center">
                <span class="sidebar-icon-box">
                    <i data-lucide="package" style="width: 18px; height: 18px;"></i>
                </span>
                <span>Sản phẩm túi xách</span>
            </div>
            <span class="badge bg-primary bg-opacity-25 text-primary-emphasis px-2 py-0.5 rounded-pill" style="font-size: 0.7rem;">Active</span>
        </a>

        <!-- Active Categories Module -->
        <a href="{{ route('admin.categories.index') }}" class="sidebar-nav-link {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
            <div class="d-flex align-items-center">
                <span class="sidebar-icon-box">
                    <i data-lucide="folder-tree" style="width: 18px; height: 18px;"></i>
                </span>
                <span>Dòng túi xách</span>
            </div>
        </a>
    </div>

    <!-- Sidebar Bottom Profile Footer -->
    <div class="sidebar-footer">
        <div class="d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center">
                <div class="sidebar-user-avatar">
                    {{ Auth::check() ? strtoupper(substr(Auth::user()->name, 0, 2)) : 'AD' }}
                </div>
                <div class="overflow-hidden" style="max-width: 120px;">
                    <div class="sidebar-user-name text-truncate">{{ Auth::check() ? Auth::user()->name : 'Admin' }}</div>
                    <div class="sidebar-user-role text-truncate">{{ Auth::check() && Auth::user()->isAdmin() ? 'Super Administrator' : 'Administrator' }}</div>
                </div>
            </div>
            <form action="{{ route('logout') }}" method="POST" class="mb-0" id="sidebarLogoutForm">
                @csrf
                <button type="submit" class="btn btn-sm btn-surface p-1.5" title="Đăng xuất" aria-label="Logout">
                    <i data-lucide="log-out" style="width: 16px; height: 16px;" class="text-danger"></i>
                </button>
            </form>
        </div>
    </div>
</aside>

<!-- Mobile Sidebar Backdrop Overlay -->
<div class="sidebar-backdrop" id="sidebarBackdrop" onclick="toggleSidebar()"></div>
