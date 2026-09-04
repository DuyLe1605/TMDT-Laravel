@extends('layouts.storefront')

@section('title', 'Lịch Sử Đơn Hàng - Tài Khoản Của Tôi')

@section('content')
<div class="container py-4 py-lg-5">
    <!-- Breadcrumb -->
    <div class="breadcrumb-modern mb-3">
        <a href="{{ route('home') }}">Trang chủ</a>
        <i data-lucide="chevron-right" style="width: 14px; height: 14px;"></i>
        <span>Tài khoản</span>
        <i data-lucide="chevron-right" style="width: 14px; height: 14px;"></i>
        <span class="text-primary fw-medium">Lịch sử đơn hàng</span>
    </div>

    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4 pb-3 border-bottom">
        <div>
            <h1 class="fw-extrabold text-dark mb-1" style="font-size: 1.85rem; letter-spacing: -0.02em;">
                Đơn Hàng Của Tôi
            </h1>
            <p class="text-secondary small mb-0">
                Theo dõi tiến trình xử lý, trạng thái giao hàng GHN và lịch sử mua sắm tại Aurelia Luxury
            </p>
        </div>
        <a href="{{ route('shop.index') }}" class="btn-brand-primary py-2 px-3.5 text-decoration-none">
            <i data-lucide="shopping-bag" style="width: 16px; height: 16px; margin-right: 0.35rem;"></i>
            <span>Tiếp tục mua sắm</span>
        </a>
    </div>

    <div class="row g-4">
        <!-- Sidebar Navigation -->
        <div class="col-lg-3">
            <div class="card-modern p-3 shadow-sm border sticky-top" style="top: 85px;">
                <div class="d-flex align-items-center gap-3 p-2 mb-3 border-bottom">
                    <div class="sidebar-user-avatar" style="width: 44px; height: 44px; font-size: 1rem;">
                        {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                    </div>
                    <div class="min-w-0">
                        <div class="fw-bold text-dark text-truncate">{{ Auth::user()->name }}</div>
                        <div class="text-secondary small text-truncate">{{ Auth::user()->email }}</div>
                    </div>
                </div>

                <div class="d-flex flex-column gap-1">
                    <a href="{{ route('account.orders') }}" class="btn-brand-primary w-100 text-start py-2 px-3 text-decoration-none d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center gap-2">
                            <i data-lucide="package" style="width: 16px; height: 16px;"></i>
                            <span>Đơn hàng của tôi</span>
                        </div>
                        <span class="badge bg-white text-primary rounded-pill px-2" style="font-size: 0.72rem;">{{ $statusCounts['all'] ?? 0 }}</span>
                    </a>
                    <a href="{{ route('account.addresses') }}" class="btn-surface w-100 text-start py-2 px-3 text-decoration-none d-flex align-items-center gap-2">
                        <i data-lucide="map-pin" style="width: 16px; height: 16px;"></i>
                        <span>Sổ địa chỉ nhận hàng</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Main Orders List -->
        <div class="col-lg-9">
            <!-- Shopee-Style Order Status Tabs -->
            <div class="card-modern mb-4 p-1 shadow-sm border overflow-hidden">
                <div class="d-flex flex-nowrap overflow-x-auto gap-1 p-1" style="scrollbar-width: thin;">
                    @php
                        $tabKeys = [
                            'all'        => ['label' => 'Tất cả', 'icon' => 'inbox'],
                            'pending'    => ['label' => 'Chờ xử lý', 'icon' => 'clock'],
                            'processing' => ['label' => 'Đang chuẩn bị', 'icon' => 'package'],
                            'shipping'   => ['label' => 'Đang giao', 'icon' => 'truck'],
                            'delivered'  => ['label' => 'Đã giao', 'icon' => 'check-circle'],
                            'cancelled'  => ['label' => 'Đã hủy', 'icon' => 'x-circle'],
                        ];
                    @endphp

                    @foreach ($tabKeys as $key => $tab)
                        @php
                            $isActive = ($key === 'all' && empty($currentStatus)) || ($currentStatus === $key);
                            $count = $statusCounts[$key] ?? 0;
                            $url = ($key === 'all') ? route('account.orders') : route('account.orders', ['status' => $key]);
                        @endphp
                        <a 
                            href="{{ $url }}" 
                            class="btn btn-sm d-flex align-items-center gap-1.5 px-3 py-2 text-nowrap rounded-3 transition text-decoration-none {{ $isActive ? 'btn-brand-primary' : 'btn-surface' }}"
                            style="font-size: 0.85rem;"
                        >
                            <i data-lucide="{{ $tab['icon'] }}" style="width: 15px; height: 15px;"></i>
                            <span>{{ $tab['label'] }}</span>
                            @if ($count > 0)
                                <span class="badge {{ $isActive ? 'bg-white text-primary' : 'bg-secondary-subtle text-secondary' }} rounded-pill px-1.5" style="font-size: 0.68rem;">
                                    {{ $count }}
                                </span>
                            @endif
                        </a>
                    @endforeach
                </div>
            </div>

            @if ($orders->isEmpty())
                <div class="card-modern text-center py-5 px-4 shadow-sm border">
                    <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 68px; height: 68px; background: var(--brand-50); color: var(--brand-600);">
                        <i data-lucide="shopping-bag" style="width: 34px; height: 34px;"></i>
                    </div>
                    <h5 class="fw-bold text-dark mb-2">Chưa có đơn hàng nào</h5>
                    <p class="text-secondary small mb-4">
                        @if ($currentStatus)
                            Bạn không có đơn hàng nào ở trạng thái "<strong>{{ $statusTabs[$currentStatus] ?? $currentStatus }}</strong>".
                        @else
                            Khám phá ngay bộ sưu tập túi xách thời thượng và đặt đơn hàng đầu tiên của bạn!
                        @endif
                    </p>
                    <div class="d-flex justify-content-center gap-2">
                        @if ($currentStatus)
                            <a href="{{ route('account.orders') }}" class="btn-surface py-2 px-3 text-decoration-none">
                                <span>Xem tất cả đơn hàng</span>
                            </a>
                        @endif
                        <a href="{{ route('shop.index') }}" class="btn-brand-primary py-2 px-4 text-decoration-none">
                            <span>Khám Phá Cửa Hàng Ngay</span>
                        </a>
                    </div>
                </div>
            @else
                <div class="d-flex flex-column gap-3.5">
                    @foreach ($orders as $order)
                        <div class="card-modern p-4 shadow-sm border">
                            <!-- Order Header -->
                            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 pb-3 mb-3 border-bottom">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="text-secondary small">Mã đơn:</span>
                                    <span class="fw-extrabold text-dark font-monospace fs-6">{{ $order->order_code }}</span>
                                    <span class="text-secondary small">&bull; {{ $order->created_at->format('d/m/Y H:i') }}</span>
                                </div>
                                <div class="d-flex flex-wrap align-items-center gap-2">
                                    @if ($order->ghn_order_code)
                                        <span class="badge bg-primary bg-opacity-10 text-primary border border-primary-subtle d-inline-flex align-items-center gap-1 small" title="Mã vận đơn GHN">
                                            <i data-lucide="truck" style="width: 12px; height: 12px;"></i>
                                            <span>GHN: {{ $order->ghn_order_code }}</span>
                                        </span>
                                    @endif
                                    <span class="badge {{ $order->shipping_status_badge['class'] }} small">
                                        {{ $order->shipping_status_badge['label'] }}
                                    </span>
                                    <span class="badge {{ $order->payment_status_badge['class'] }} small">
                                        {{ $order->payment_status_badge['label'] }}
                                    </span>
                                </div>
                            </div>

                            <!-- GHN Tracking Alert if delivering -->
                            @if ($order->shipping_status === 'shipping' && $order->ghn_status_name)
                                <div class="p-2.5 px-3 rounded-2 mb-3 bg-primary bg-opacity-10 text-primary small d-flex align-items-center justify-content-between">
                                    <div class="d-flex align-items-center gap-2">
                                        <i data-lucide="truck" style="width: 16px; height: 16px;"></i>
                                        <span>Trạng thái giao: <strong>{{ $order->ghn_status_name }}</strong></span>
                                    </div>
                                    @if ($order->expected_delivery_at)
                                        <div class="text-success fw-medium">
                                            Dự kiến giao: {{ $order->expected_delivery_at->format('d/m/Y') }}
                                        </div>
                                    @endif
                                </div>
                            @endif

                            <!-- Items preview list -->
                            <div class="d-flex flex-column gap-2 mb-3">
                                @foreach ($order->items as $item)
                                    <div class="d-flex align-items-center justify-content-between gap-3 p-2 rounded-2" style="background: var(--bg-surface-subtle);">
                                        <div class="d-flex align-items-center gap-2.5 min-w-0">
                                            <div class="rounded-2 border overflow-hidden flex-shrink-0" style="width: 48px; height: 48px;">
                                                @if ($item->product_image)
                                                    <img src="{{ $item->product_image }}" alt="{{ $item->product_name }}" class="w-100 h-100 object-fit-cover">
                                                @else
                                                    <div class="w-100 h-100 d-flex align-items-center justify-content-center bg-light">
                                                        <i data-lucide="shopping-bag" style="width: 16px; height: 16px;"></i>
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="min-w-0">
                                                <div class="fw-semibold text-dark small text-truncate">{{ $item->product_name }}</div>
                                                @if ($item->variant_title)
                                                    <div class="text-primary" style="font-size: 0.72rem; font-weight: 500;">
                                                        Phân loại: {{ $item->variant_title }}
                                                    </div>
                                                @endif
                                                <div class="text-secondary" style="font-size: 0.75rem;">
                                                    {{ $item->formatted_price }} &times; {{ $item->quantity }}
                                                </div>
                                            </div>
                                        </div>
                                        <span class="fw-bold text-dark small flex-shrink-0">{{ $item->formatted_subtotal }}</span>
                                    </div>
                                @endforeach
                            </div>

                            <!-- Footer summary & Action Buttons -->
                            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 pt-3 border-top bg-light-subtle rounded-bottom p-2 px-3">
                                <div class="small">
                                    <span class="text-secondary">Tổng thanh toán:</span>
                                    <span class="fw-extrabold text-primary fs-6 ms-1">{{ $order->formatted_total_amount }}</span>
                                    <span class="text-secondary ms-2" style="font-size: 0.75rem;">(Ship: {{ $order->formatted_shipping_fee }})</span>
                                </div>

                                <div class="d-flex align-items-center gap-2">
                                    <!-- Detail button -->
                                    <a href="{{ route('account.orders.show', $order) }}" class="btn btn-sm btn-surface text-decoration-none fw-semibold">
                                        <span>Xem chi tiết</span>
                                        <i data-lucide="chevron-right" style="width: 14px; height: 14px; margin-left: 0.2rem;"></i>
                                    </a>

                                    <!-- Confirm Delivery button if shipping -->
                                    @if ($order->shipping_status === 'shipping')
                                        <form action="{{ route('account.orders.confirm_delivery', $order) }}" method="POST" class="d-inline" onsubmit="return confirm('Bạn xác nhận đã nhận đầy đủ hàng và hài lòng với kiện hàng này?');">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-success fw-semibold d-inline-flex align-items-center gap-1" title="Xác nhận đã nhận hàng">
                                                <i data-lucide="check-check" style="width: 14px; height: 14px;"></i>
                                                <span>Đã nhận hàng</span>
                                            </button>
                                        </form>
                                    @endif

                                    <!-- Reorder button (Mua lại) if delivered or cancelled -->
                                    @if ($order->canReorder())
                                        <form action="{{ route('account.orders.reorder', $order) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-primary fw-semibold d-inline-flex align-items-center gap-1" title="Thêm lại sản phẩm vào giỏ hàng">
                                                <i data-lucide="repeat" style="width: 13px; height: 13px;"></i>
                                                <span>Mua lại</span>
                                            </button>
                                        </form>
                                    @endif

                                    <!-- Cancel Order button (if pending) -->
                                    @if ($order->canBeCancelledByCustomer())
                                        <button 
                                            type="button" 
                                            class="btn btn-sm btn-outline-danger fw-semibold" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#cancelModal_{{ $order->id }}"
                                        >
                                            <span>Hủy đơn</span>
                                        </button>

                                        <!-- Cancel Modal -->
                                        <div class="modal fade" id="cancelModal_{{ $order->id }}" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered" style="max-width: 440px;">
                                                <div class="modal-content modal-content-modern border-0">
                                                    <form action="{{ route('account.orders.cancel', $order) }}" method="POST">
                                                        @csrf
                                                        <div class="modal-header p-3.5 border-bottom">
                                                            <h6 class="fw-bold text-danger mb-0 d-flex align-items-center gap-2">
                                                                <i data-lucide="alert-triangle" style="width: 18px; height: 18px;"></i>
                                                                <span>Xác nhận hủy đơn hàng</span>
                                                            </h6>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                        </div>
                                                        <div class="modal-body p-3.5 text-start">
                                                            <p class="text-secondary small mb-3">
                                                                Bạn có chắc chắn muốn hủy đơn hàng <strong class="font-monospace text-dark">{{ $order->order_code }}</strong>?
                                                            </p>
                                                            <div class="mb-2">
                                                                <label class="form-label small fw-semibold text-secondary">Lý do hủy đơn:</label>
                                                                <select name="cancel_reason" class="form-select form-select-sm mb-2" required>
                                                                    <option value="Muốn thay đổi địa chỉ nhận hàng">Muốn thay đổi địa chỉ nhận hàng</option>
                                                                    <option value="Muốn thay đổi sản phẩm / phân loại">Muốn thay đổi sản phẩm / phân loại</option>
                                                                    <option value="Đổi ý không muốn mua nữa">Đổi ý không muốn mua nữa</option>
                                                                    <option value="Thời gian giao hàng quá lâu">Thời gian giao hàng quá lâu</option>
                                                                    <option value="Lý do khác">Lý do khác</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer p-2.5 border-top">
                                                            <button type="button" class="btn btn-sm btn-surface" data-bs-dismiss="modal">Không hủy</button>
                                                            <button type="submit" class="btn btn-sm btn-danger">Hủy đơn hàng</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach

                    <div class="mt-3">
                        {{ $orders->links() }}
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
