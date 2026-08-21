<aside class="admin-sidebar" id="adminSidebar">
    <!-- Sidebar Header & Brand -->
    <div class="sidebar-header">
        <a href="{{ route('products.index') }}" class="d-flex align-items-center text-decoration-none">
            <div class="brand-logo-badge">
                <i data-lucide="layers" style="width: 20px; height: 20px;"></i>
            </div>
            <div class="d-flex align-items-center">
                <span class="brand-title">TMDT</span>
                <span class="brand-tag">PORTAL</span>
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
        <a href="{{ route('products.index') }}" class="sidebar-nav-link">
            <div class="d-flex align-items-center">
                <span class="sidebar-icon-box">
                    <i data-lucide="layout-dashboard" style="width: 18px; height: 18px;"></i>
                </span>
                <span>Bảng điều khiển</span>
            </div>
        </a>

        <!-- Section: E-Commerce Operations -->
        <div class="sidebar-section-title mt-3">QUẢN LÝ E-COMMERCE</div>
        
        <!-- Active Products Module -->
        <a href="{{ route('products.index') }}" class="sidebar-nav-link {{ request()->routeIs('products.*') ? 'active' : '' }}">
            <div class="d-flex align-items-center">
                <span class="sidebar-icon-box">
                    <i data-lucide="package" style="width: 18px; height: 18px;"></i>
                </span>
                <span>Sản phẩm túi xách</span>
            </div>
            <span class="badge bg-primary bg-opacity-25 text-primary-emphasis px-2 py-0.5 rounded-pill" style="font-size: 0.7rem;">Active</span>
        </a>

        <!-- Active Categories Module -->
        <a href="{{ route('categories.index') }}" class="sidebar-nav-link {{ request()->routeIs('categories.*') ? 'active' : '' }}">
            <div class="d-flex align-items-center">
                <span class="sidebar-icon-box">
                    <i data-lucide="folder-tree" style="width: 18px; height: 18px;"></i>
                </span>
                <span>Dòng túi xách</span>
            </div>
        </a>

        <a href="javascript:void(0)" class="sidebar-nav-link disabled" title="Tính năng đang phát triển">
            <div class="d-flex align-items-center">
                <span class="sidebar-icon-box">
                    <i data-lucide="shopping-cart" style="width: 18px; height: 18px;"></i>
                </span>
                <span>Đơn hàng</span>
            </div>
            <span class="badge bg-secondary bg-opacity-25 text-secondary px-2 py-0.5 rounded-pill" style="font-size: 0.65rem;">Sắp có</span>
        </a>

        <a href="javascript:void(0)" class="sidebar-nav-link disabled" title="Tính năng đang phát triển">
            <div class="d-flex align-items-center">
                <span class="sidebar-icon-box">
                    <i data-lucide="users" style="width: 18px; height: 18px;"></i>
                </span>
                <span>Khách hàng</span>
            </div>
            <span class="badge bg-secondary bg-opacity-25 text-secondary px-2 py-0.5 rounded-pill" style="font-size: 0.65rem;">Sắp có</span>
        </a>

        <!-- Section: System & Settings -->
        <div class="sidebar-section-title mt-3">HỆ THỐNG</div>
        <a href="javascript:void(0)" class="sidebar-nav-link disabled" title="Tính năng đang phát triển">
            <div class="d-flex align-items-center">
                <span class="sidebar-icon-box">
                    <i data-lucide="bar-chart-3" style="width: 18px; height: 18px;"></i>
                </span>
                <span>Báo cáo doanh thu</span>
            </div>
        </a>

        <a href="javascript:void(0)" class="sidebar-nav-link disabled" title="Tính năng đang phát triển">
            <div class="d-flex align-items-center">
                <span class="sidebar-icon-box">
                    <i data-lucide="settings" style="width: 18px; height: 18px;"></i>
                </span>
                <span>Cài đặt chung</span>
            </div>
        </a>
    </div>

    <!-- Sidebar Bottom Profile Footer -->
    <div class="sidebar-footer">
        <div class="d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center">
                <div class="sidebar-user-avatar">
                    AD
                </div>
                <div>
                    <div class="sidebar-user-name">Admin User</div>
                    <div class="sidebar-user-role">Super Administrator</div>
                </div>
            </div>
            <div class="status-pulse-dot" title="Trực tuyến"></div>
        </div>
    </div>
</aside>

<!-- Mobile Sidebar Backdrop Overlay -->
<div class="sidebar-backdrop" id="sidebarBackdrop" onclick="toggleSidebar()"></div>
