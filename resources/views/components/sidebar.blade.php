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
        
        <!-- Active Orders Module -->
        <a href="{{ route('admin.orders.index') }}" class="sidebar-nav-link {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}">
            <div class="d-flex align-items-center">
                <span class="sidebar-icon-box">
                    <i data-lucide="shopping-bag" style="width: 18px; height: 18px;"></i>
                </span>
                <span>Quản lý Đơn hàng</span>
            </div>
            @php
                $pendingOrdersCount = \App\Models\Order::where('shipping_status', 'pending')->count();
            @endphp
            @if($pendingOrdersCount > 0)
                <span class="badge bg-warning text-dark px-2 py-0.5 rounded-pill fw-bold" style="font-size: 0.72rem;">{{ $pendingOrdersCount }}</span>
            @endif
        </a>

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
                <span>Dòng túi xách (Danh mục)</span>
            </div>
        </a>

        <!-- Active Brands Module -->
        <a href="{{ route('admin.brands.index') }}" class="sidebar-nav-link {{ request()->routeIs('admin.brands.*') ? 'active' : '' }}">
            <div class="d-flex align-items-center">
                <span class="sidebar-icon-box">
                    <i data-lucide="award" style="width: 18px; height: 18px;"></i>
                </span>
                <span>Thương hiệu thời trang</span>
            </div>
        </a>

        <!-- Vouchers & Promotion Module -->
        <a href="{{ route('admin.vouchers.index') }}" class="sidebar-nav-link {{ request()->routeIs('admin.vouchers.*') ? 'active' : '' }}">
            <div class="d-flex align-items-center">
                <span class="sidebar-icon-box">
                    <i data-lucide="ticket" style="width: 18px; height: 18px;"></i>
                </span>
                <span>Mã giảm giá (Voucher)</span>
            </div>
            @php
                $activeVouchersCount = \App\Models\Voucher::active()->count();
            @endphp
            @if($activeVouchersCount > 0)
                <span class="badge bg-success bg-opacity-20 text-success px-2 py-0.5 rounded-pill fw-bold" style="font-size: 0.72rem;">{{ $activeVouchersCount }} đang chạy</span>
            @endif
        </a>

        <!-- Product Reviews Module -->
        <a href="{{ route('admin.reviews.index') }}" class="sidebar-nav-link {{ request()->routeIs('admin.reviews.*') ? 'active' : '' }}">
            <div class="d-flex align-items-center">
                <span class="sidebar-icon-box">
                    <i data-lucide="star" style="width: 18px; height: 18px;"></i>
                </span>
                <span>Đánh giá sản phẩm</span>
            </div>
            @php
                $pendingReviewsCount = \App\Models\Review::whereNull('admin_reply')->count();
            @endphp
            @if($pendingReviewsCount > 0)
                <span class="badge bg-warning bg-opacity-20 text-warning-emphasis px-2 py-0.5 rounded-pill fw-bold" style="font-size: 0.72rem;">{{ $pendingReviewsCount }} chờ trả lời</span>
            @endif
        </a>

        <!-- Section: System & Users -->
        <div class="sidebar-section-title mt-3">HỆ THỐNG & TÀI KHOẢN</div>

        <!-- Users Management Module -->
        <a href="{{ route('admin.users.index') }}" class="sidebar-nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
            <div class="d-flex align-items-center">
                <span class="sidebar-icon-box">
                    <i data-lucide="users" style="width: 18px; height: 18px;"></i>
                </span>
                <span>Quản lý Tài khoản</span>
            </div>
        </a>
    </div>

    <!-- Sidebar Bottom Profile Footer with Dropdown -->
    <div class="sidebar-footer">
        <div class="dropdown dropup w-100">
            <button class="sidebar-user-btn d-flex align-items-center justify-content-between w-100 p-2 rounded-3 border-0 bg-transparent" type="button" data-bs-toggle="dropdown" aria-expanded="false" title="Tùy chọn tài khoản">
                <div class="d-flex align-items-center gap-2.5 overflow-hidden">
                    <div class="sidebar-user-avatar flex-shrink-0" style="width: 36px; height: 36px; font-size: 0.85rem;">
                        {{ Auth::check() ? strtoupper(substr(Auth::user()->name, 0, 2)) : 'AD' }}
                    </div>
                    <div class="text-start overflow-hidden">
                        <div class="sidebar-user-name text-truncate" style="font-size: 0.88rem;">{{ Auth::check() ? Auth::user()->name : 'Admin' }}</div>
                        <div class="sidebar-user-role text-truncate" style="font-size: 0.72rem;">{{ Auth::check() && Auth::user()->isAdmin() ? 'Super Administrator' : 'Administrator' }}</div>
                    </div>
                </div>
                <i data-lucide="chevrons-up-down" style="width: 16px; height: 16px;" class="text-secondary flex-shrink-0 ms-1"></i>
            </button>

            <ul class="dropdown-menu dropdown-menu-modern dropdown-menu-dark w-100 shadow-lg mb-2 p-2">
                <li class="px-2 py-1.5 border-bottom mb-1">
                    <div class="fw-bold text-dark small text-truncate">{{ Auth::user()->name ?? 'Admin' }}</div>
                    <div class="text-secondary small font-monospace" style="font-size: 0.75rem;">{{ Auth::user()->email ?? 'admin@tuixach.vn' }}</div>
                </li>
                <li>
                    <a href="{{ route('home') }}" class="dropdown-item-modern rounded-2 py-1.5" target="_blank">
                        <i data-lucide="external-link" style="width: 15px; height: 15px; margin-right: 0.5rem;"></i>
                        <span>Xem Storefront</span>
                    </a>
                </li>
                @if (Auth::check())
                    <li>
                        <a href="{{ route('admin.users.show', Auth::user()) }}" class="dropdown-item-modern rounded-2 py-1.5">
                            <i data-lucide="user" style="width: 15px; height: 15px; margin-right: 0.5rem;"></i>
                            <span>Hồ sơ cá nhân</span>
                        </a>
                    </li>
                @endif
                <li><hr class="dropdown-divider-modern my-1"></li>
                <li>
                    <button type="button" class="dropdown-item-modern item-danger rounded-2 py-1.5 w-100 border-0 bg-transparent text-start" onclick="openLogoutModal()">
                        <i data-lucide="log-out" style="width: 15px; height: 15px; margin-right: 0.5rem;"></i>
                        <span>Đăng xuất hệ thống</span>
                    </button>
                </li>
            </ul>
        </div>
    </div>
</aside>

<!-- Mobile Sidebar Backdrop Overlay -->
<div class="sidebar-backdrop" id="sidebarBackdrop" onclick="toggleSidebar()"></div>
