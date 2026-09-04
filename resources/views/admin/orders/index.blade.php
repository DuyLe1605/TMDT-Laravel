@extends('layouts.app')

@section('title', 'Quản lý Đơn hàng - Admin Portal')

@section('content')
<!-- Page Header -->
<div class="mb-4">
    <div class="breadcrumb-modern">
        <span>Admin Portal</span>
        <i data-lucide="chevron-right" style="width: 14px; height: 14px;"></i>
        <span>Quản lý E-Commerce</span>
        <i data-lucide="chevron-right" style="width: 14px; height: 14px;"></i>
        <span class="text-primary fw-medium">Quản lý đơn hàng</span>
    </div>

    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mt-2">
        <div>
            <h2 class="fw-bold mb-1 text-dark" style="letter-spacing: -0.03em;">Quản Lý Đơn Hàng & Vận Chuyển GHN</h2>
            <p class="text-secondary mb-0" style="font-size: 0.94rem;">
                Theo dõi tiến trình xử lý đơn hàng, điều phối giao vận Giao Hàng Nhanh và quản lý thanh toán
            </p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('admin.orders.index') }}" class="btn-surface">
                <i data-lucide="refresh-cw" style="width: 16px; height: 16px; margin-right: 0.35rem;"></i>
                <span>Làm mới danh sách</span>
            </a>
        </div>
    </div>
</div>

<!-- Status KPI Summary Cards -->
<div class="row g-3 mb-4">
    <div class="col-6 col-md-4 col-xl-2">
        <a href="{{ route('admin.orders.index') }}" class="text-decoration-none">
            <div class="metric-card {{ !request('shipping_status') ? 'border-primary shadow-sm' : '' }} p-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-secondary small fw-medium">Tất cả đơn</div>
                        <div class="fs-4 fw-extrabold text-dark mt-1">{{ $statusCounts['all'] }}</div>
                    </div>
                    <div class="p-2 rounded-3 bg-light text-secondary">
                        <i data-lucide="inbox" style="width: 20px; height: 20px;"></i>
                    </div>
                </div>
            </div>
        </a>
    </div>

    <div class="col-6 col-md-4 col-xl-2">
        <a href="{{ route('admin.orders.index', ['shipping_status' => 'pending']) }}" class="text-decoration-none">
            <div class="metric-card {{ request('shipping_status') === 'pending' ? 'border-warning shadow-sm' : '' }} p-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-secondary small fw-medium">Chờ xác nhận</div>
                        <div class="fs-4 fw-extrabold text-warning mt-1">{{ $statusCounts['pending'] }}</div>
                    </div>
                    <div class="p-2 rounded-3 bg-warning-subtle text-warning">
                        <i data-lucide="clock" style="width: 20px; height: 20px;"></i>
                    </div>
                </div>
            </div>
        </a>
    </div>

    <div class="col-6 col-md-4 col-xl-2">
        <a href="{{ route('admin.orders.index', ['shipping_status' => 'processing']) }}" class="text-decoration-none">
            <div class="metric-card {{ request('shipping_status') === 'processing' ? 'border-info shadow-sm' : '' }} p-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-secondary small fw-medium">Đang chuẩn bị</div>
                        <div class="fs-4 fw-extrabold text-info mt-1">{{ $statusCounts['processing'] }}</div>
                    </div>
                    <div class="p-2 rounded-3 bg-info-subtle text-info">
                        <i data-lucide="package" style="width: 20px; height: 20px;"></i>
                    </div>
                </div>
            </div>
        </a>
    </div>

    <div class="col-6 col-md-4 col-xl-2">
        <a href="{{ route('admin.orders.index', ['shipping_status' => 'shipping']) }}" class="text-decoration-none">
            <div class="metric-card {{ request('shipping_status') === 'shipping' ? 'border-primary shadow-sm' : '' }} p-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-secondary small fw-medium">Đang giao hàng</div>
                        <div class="fs-4 fw-extrabold text-primary mt-1">{{ $statusCounts['shipping'] }}</div>
                    </div>
                    <div class="p-2 rounded-3 bg-primary-subtle text-primary">
                        <i data-lucide="truck" style="width: 20px; height: 20px;"></i>
                    </div>
                </div>
            </div>
        </a>
    </div>

    <div class="col-6 col-md-4 col-xl-2">
        <a href="{{ route('admin.orders.index', ['shipping_status' => 'delivered']) }}" class="text-decoration-none">
            <div class="metric-card {{ request('shipping_status') === 'delivered' ? 'border-success shadow-sm' : '' }} p-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-secondary small fw-medium">Đã giao thành công</div>
                        <div class="fs-4 fw-extrabold text-success mt-1">{{ $statusCounts['delivered'] }}</div>
                    </div>
                    <div class="p-2 rounded-3 bg-success-subtle text-success">
                        <i data-lucide="check-circle" style="width: 20px; height: 20px;"></i>
                    </div>
                </div>
            </div>
        </a>
    </div>

    <div class="col-6 col-md-4 col-xl-2">
        <a href="{{ route('admin.orders.index', ['shipping_status' => 'cancelled']) }}" class="text-decoration-none">
            <div class="metric-card {{ request('shipping_status') === 'cancelled' ? 'border-danger shadow-sm' : '' }} p-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-secondary small fw-medium">Đã hủy / Hoàn</div>
                        <div class="fs-4 fw-extrabold text-danger mt-1">{{ $statusCounts['cancelled'] + $statusCounts['returning'] }}</div>
                    </div>
                    <div class="p-2 rounded-3 bg-danger-subtle text-danger">
                        <i data-lucide="x-circle" style="width: 20px; height: 20px;"></i>
                    </div>
                </div>
            </div>
        </a>
    </div>
</div>

<!-- Filter Bar -->
<div class="card-modern p-4 mb-4 shadow-sm border">
    <form action="{{ route('admin.orders.index') }}" method="GET" class="row g-3 align-items-end">
        <!-- Search Keyword -->
        <div class="col-md-4 col-lg-3">
            <label class="form-label small fw-semibold text-secondary">Tìm kiếm đơn hàng</label>
            <div class="input-group">
                <span class="input-group-text bg-white border-end-0 text-secondary">
                    <i data-lucide="search" style="width: 16px; height: 16px;"></i>
                </span>
                <input 
                    type="text" 
                    name="search" 
                    class="form-control border-start-0 ps-0" 
                    placeholder="Mã đơn, tên, SĐT, mã GHN..." 
                    value="{{ request('search') }}"
                >
            </div>
        </div>

        <!-- Shipping Status Filter -->
        <div class="col-6 col-md-3 col-lg-2">
            <label class="form-label small fw-semibold text-secondary">Trạng thái vận chuyển</label>
            <select name="shipping_status" class="form-select">
                <option value="">Tất cả trạng thái</option>
                @foreach ($shippingStatusOptions as $key => $label)
                    <option value="{{ $key }}" {{ request('shipping_status') === $key ? 'selected' : '' }}>
                        {{ $label }}
                    </option>
                @endforeach
            </select>
        </div>

        <!-- Payment Status Filter -->
        <div class="col-6 col-md-3 col-lg-2">
            <label class="form-label small fw-semibold text-secondary">Thanh toán</label>
            <select name="payment_status" class="form-select">
                <option value="">Tất cả thanh toán</option>
                <option value="pending" {{ request('payment_status') === 'pending' ? 'selected' : '' }}>Chưa thanh toán</option>
                <option value="paid" {{ request('payment_status') === 'paid' ? 'selected' : '' }}>Đã thanh toán</option>
                <option value="refunded" {{ request('payment_status') === 'refunded' ? 'selected' : '' }}>Đã hoàn tiền</option>
                <option value="refunding" {{ request('payment_status') === 'refunding' ? 'selected' : '' }}>Đang hoàn tiền</option>
            </select>
        </div>

        <!-- Date Range Filter -->
        <div class="col-6 col-md-3 col-lg-2">
            <label class="form-label small fw-semibold text-secondary">Từ ngày</label>
            <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
        </div>
        <div class="col-6 col-md-3 col-lg-2">
            <label class="form-label small fw-semibold text-secondary">Đến ngày</label>
            <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
        </div>

        <!-- Filter Actions -->
        <div class="col-12 col-lg-1 d-flex gap-2">
            <button type="submit" class="btn btn-brand-primary w-100 py-2 d-inline-flex align-items-center justify-content-center" title="Áp dụng bộ lọc">
                <i data-lucide="filter" style="width: 16px; height: 16px;"></i>
            </button>
            @if(request()->anyFilled(['search', 'shipping_status', 'payment_status', 'date_from', 'date_to', 'sort']))
                <a href="{{ route('admin.orders.index') }}" class="btn btn-surface py-2 px-2.5" title="Xóa bộ lọc">
                    <i data-lucide="x" style="width: 16px; height: 16px;"></i>
                </a>
            @endif
        </div>
    </form>
</div>

<!-- Orders Table Card -->
<div class="card-modern shadow-sm border overflow-hidden">
    @if ($orders->isEmpty())
        <div class="text-center py-5 px-4">
            <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 64px; height: 64px; background: var(--bg-surface-subtle); color: var(--text-secondary);">
                <i data-lucide="shopping-bag" style="width: 32px; height: 32px;"></i>
            </div>
            <h5 class="fw-bold text-dark mb-1">Không tìm thấy đơn hàng nào</h5>
            <p class="text-secondary small mb-3">
                @if(request()->anyFilled(['search', 'shipping_status', 'payment_status', 'date_from', 'date_to']))
                    Không có đơn hàng nào khớp với điều kiện tìm kiếm. Hãy thử điều chỉnh bộ lọc.
                @else
                    Hiện chưa có đơn hàng nào được tạo trong hệ thống.
                @endif
            </p>
            @if(request()->anyFilled(['search', 'shipping_status', 'payment_status', 'date_from', 'date_to']))
                <a href="{{ route('admin.orders.index') }}" class="btn btn-sm btn-surface">
                    <span>Xóa toàn bộ bộ lọc</span>
                </a>
            @endif
        </div>
    @else
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light-subtle text-secondary small text-uppercase">
                    <tr>
                        <th class="ps-4 py-3" style="width: 170px;">Mã đơn hàng</th>
                        <th class="py-3" style="min-width: 200px;">Khách hàng</th>
                        <th class="py-3" style="min-width: 220px;">Sản phẩm</th>
                        <th class="py-3 text-end" style="width: 140px;">Tổng tiền</th>
                        <th class="py-3 text-center" style="width: 130px;">Thanh toán</th>
                        <th class="py-3 text-center" style="width: 160px;">Vận chuyển</th>
                        <th class="py-3 text-center" style="width: 130px;">Ngày tạo</th>
                        <th class="pe-4 py-3 text-end" style="width: 140px;">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="border-top-0">
                    @foreach ($orders as $order)
                        <tr>
                            <!-- Order Code & GHN Badge -->
                            <td class="ps-4 py-3">
                                <a href="{{ route('admin.orders.show', $order) }}" class="fw-extrabold text-primary font-monospace text-decoration-none">
                                    {{ $order->order_code }}
                                </a>
                                @if ($order->ghn_order_code)
                                    <div class="mt-1">
                                        <a href="https://donhang.ghn.vn/?order_code={{ $order->ghn_order_code }}" target="_blank" class="badge bg-primary bg-opacity-10 text-primary border border-primary-subtle d-inline-flex align-items-center gap-1 text-decoration-none" style="font-size: 0.7rem;" title="Nhấn để tra cứu hành trình trên GHN Portal">
                                            <i data-lucide="truck" style="width: 11px; height: 11px;"></i>
                                            <span>{{ $order->ghn_order_code }}</span>
                                            <i data-lucide="external-link" style="width: 9px; height: 9px;"></i>
                                        </a>
                                    </div>
                                @endif
                            </td>

                            <!-- Customer Info -->
                            <td class="py-3">
                                <div class="fw-bold text-dark">{{ $order->recipient_name }}</div>
                                <div class="text-secondary small font-monospace">{{ $order->phone }}</div>
                                <div class="text-muted small text-truncate" style="max-width: 230px;" title="{{ $order->shipping_address }}">
                                    {{ $order->shipping_address }}
                                </div>
                            </td>

                            <!-- Products summary -->
                            <td class="py-3">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="badge bg-secondary-subtle text-secondary fw-semibold">
                                        {{ $order->items->sum('quantity') }} món
                                    </span>
                                    <span class="text-dark small text-truncate" style="max-width: 180px;">
                                        {{ $order->items->first()?->product_name ?? 'Sản phẩm' }}
                                        @if($order->items->count() > 1)
                                            <span class="text-secondary">(+{{ $order->items->count() - 1 }})</span>
                                        @endif
                                    </span>
                                </div>
                                <div class="text-secondary" style="font-size: 0.75rem;">
                                    TL: {{ number_format($order->total_weight ?? 600) }}g &bull; {{ $order->shipping_method_label }}
                                </div>
                            </td>

                            <!-- Total Amount -->
                            <td class="py-3 text-end">
                                <div class="fw-extrabold text-dark fs-6">{{ $order->formatted_total_amount }}</div>
                                <div class="text-secondary" style="font-size: 0.75rem;">
                                    Ship: {{ $order->formatted_shipping_fee }}
                                </div>
                            </td>

                            <!-- Payment Status -->
                            <td class="py-3 text-center">
                                <div>
                                    <span class="badge {{ $order->payment_status_badge['class'] }}">
                                        {{ $order->payment_status_badge['label'] }}
                                    </span>
                                </div>
                                <div class="text-secondary mt-1" style="font-size: 0.72rem;">
                                    {{ $order->payment_method_label }}
                                </div>
                            </td>

                            <!-- Shipping Status -->
                            <td class="py-3 text-center">
                                <div>
                                    <span class="badge {{ $order->shipping_status_badge['class'] }}">
                                        {{ $order->shipping_status_badge['label'] }}
                                    </span>
                                </div>
                                @if ($order->ghn_status_name)
                                    <div class="text-primary text-truncate mt-1" style="font-size: 0.72rem; max-width: 150px; margin: 0 auto;" title="{{ $order->ghn_status_name }}">
                                        {{ $order->ghn_status_name }}
                                    </div>
                                @endif
                            </td>

                            <!-- Created Date -->
                            <td class="py-3 text-center text-secondary small">
                                <div>{{ $order->created_at->format('d/m/Y') }}</div>
                                <div style="font-size: 0.75rem;">{{ $order->created_at->format('H:i') }}</div>
                            </td>

                            <!-- Actions -->
                            <td class="pe-4 py-3 text-end">
                                <div class="d-inline-flex align-items-center gap-1.5">
                                    <!-- View detail -->
                                    <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-sm btn-surface p-1.5 px-2" title="Xem chi tiết đơn hàng">
                                        <i data-lucide="eye" style="width: 15px; height: 15px;"></i>
                                    </a>

                                    <!-- Quick Confirm if pending -->
                                    @if ($order->canBeConfirmed())
                                        <!-- 1-Click Confirm & Send to GHN -->
                                        <form action="{{ route('admin.orders.update_status', $order) }}" method="POST" class="d-inline" onsubmit="return confirm('Xác nhận đơn và đẩy thông tin sang GHN ngay?');">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="action" value="confirm_and_ghn">
                                            <button type="submit" class="btn btn-sm btn-success p-1.5 px-2" title="Xác nhận & Đẩy GHN ngay (1-Click)">
                                                <i data-lucide="send" style="width: 15px; height: 15px;"></i>
                                            </button>
                                        </form>

                                        <!-- Confirm only (prepare goods) -->
                                        <form action="{{ route('admin.orders.update_status', $order) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="action" value="confirm">
                                            <button type="submit" class="btn btn-sm btn-outline-primary p-1.5 px-2" title="Chỉ xác nhận (Chuẩn bị hàng)">
                                                <i data-lucide="check" style="width: 15px; height: 15px;"></i>
                                            </button>
                                        </form>
                                    @endif

                                    <!-- Quick Send GHN if processing -->
                                    @if ($order->canBeSentToGhn())
                                        <form action="{{ route('admin.orders.send_ghn', $order) }}" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc chắn muốn gửi đơn hàng này sang GHN để lấy hàng?');">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-success p-1.5 px-2" title="Gửi đơn hàng sang GHN (Lấy mã bưu tá)">
                                                <i data-lucide="send" style="width: 15px; height: 15px;"></i>
                                            </button>
                                        </form>
                                    @endif

                                    <!-- Quick GHN Link & Print Label if has GHN code -->
                                    @if ($order->isGhnOrder())
                                        <a href="https://donhang.ghn.vn/?order_code={{ $order->ghn_order_code }}" target="_blank" class="btn btn-sm btn-surface p-1.5 px-2 text-primary" title="Tra cứu trên GHN Portal">
                                            <i data-lucide="external-link" style="width: 15px; height: 15px;"></i>
                                        </a>
                                        <a href="{{ route('admin.orders.print_label', $order) }}" target="_blank" class="btn btn-sm btn-surface p-1.5 px-2" title="In vận đơn GHN (A5)">
                                            <i data-lucide="printer" style="width: 15px; height: 15px;"></i>
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if ($orders->hasPages())
            <div class="d-flex justify-content-between align-items-center p-3 px-4 border-top">
                <div class="text-secondary small">
                    Hiển thị từ {{ $orders->firstItem() }} đến {{ $orders->lastItem() }} trên tổng số {{ $orders->total() }} đơn hàng
                </div>
                <div>
                    {{ $orders->links() }}
                </div>
            </div>
        @endif
    @endif
</div>
@endsection
