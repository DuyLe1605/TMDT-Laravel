@extends('layouts.storefront')

@section('title', 'Chi Tiết Đơn Hàng ' . $order->order_code . ' - Aurelia Luxury')

@section('content')
<div class="container py-4 py-lg-5">
    <!-- Breadcrumb -->
    <div class="breadcrumb-modern mb-3">
        <a href="{{ route('home') }}">Trang chủ</a>
        <i data-lucide="chevron-right" style="width: 14px; height: 14px;"></i>
        <a href="{{ route('account.orders') }}">Đơn hàng của tôi</a>
        <i data-lucide="chevron-right" style="width: 14px; height: 14px;"></i>
        <span class="text-primary fw-medium font-monospace">{{ $order->order_code }}</span>
    </div>

    <!-- Header info & status -->
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4 pb-3 border-bottom">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <h1 class="fw-extrabold text-dark mb-0 font-monospace" style="font-size: 1.85rem; letter-spacing: -0.02em;">
                    {{ $order->order_code }}
                </h1>
                <span class="badge {{ $order->shipping_status_badge['class'] }} fs-6 px-3 py-1.5">
                    {{ $order->shipping_status_badge['label'] }}
                </span>
                <span class="badge {{ $order->payment_status_badge['class'] }} fs-6 px-3 py-1.5">
                    {{ $order->payment_status_badge['label'] }}
                </span>
            </div>
            <p class="text-secondary small mb-0">
                Ngày đặt hàng: <strong>{{ $order->created_at->format('d/m/Y H:i') }}</strong>
                &bull; Cập nhật lần cuối: <strong>{{ $order->updated_at->format('d/m/Y H:i') }}</strong>
            </p>
        </div>

        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('account.orders') }}" class="btn-surface py-2 px-3 text-decoration-none">
                <i data-lucide="arrow-left" style="width: 15px; height: 15px; margin-right: 0.3rem;"></i>
                <span>Trở về đơn hàng</span>
            </a>

            <!-- Confirm Delivery button if shipping -->
            @if ($order->shipping_status === 'shipping')
                <form action="{{ route('account.orders.confirm_delivery', $order) }}" method="POST" class="d-inline" onsubmit="return confirm('Bạn xác nhận đã nhận đầy đủ hàng và hài lòng với kiện hàng này?');">
                    @csrf
                    <button type="submit" class="btn btn-success py-2 px-3.5 d-inline-flex align-items-center gap-1.5 fw-semibold">
                        <i data-lucide="check-check" style="width: 16px; height: 16px;"></i>
                        <span>Đã nhận được hàng</span>
                    </button>
                </form>
            @endif

            <!-- Review Incentive Button if delivered -->
            @if ($order->shipping_status === 'delivered')
                @php
                    $unreviewed = $order->items->first(fn($i) => !$i->review);
                @endphp
                @if ($unreviewed)
                    <button type="button" class="btn btn-outline-warning text-dark fw-bold py-2 px-3.5 d-inline-flex align-items-center gap-1.5 shadow-sm"
                            style="border-color: #f59e0b; background: #fffbeb;"
                            onclick="openReviewModal({{ $unreviewed->id }}, '{{ addslashes($unreviewed->product_name) }}', '{{ addslashes($unreviewed->variant_title ?? '') }}', '{{ $unreviewed->product_image }}')">
                        <i class="bi bi-star-fill text-warning"></i>
                        <span>Đánh Giá Nhận Xu</span>
                    </button>
                @endif
            @endif

            <!-- Reorder Button -->
            @if ($order->canReorder())
                <form action="{{ route('account.orders.reorder', $order) }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-brand-primary py-2 px-3.5 d-inline-flex align-items-center gap-1.5">
                        <i data-lucide="repeat" style="width: 16px; height: 16px;"></i>
                        <span>Mua lại đơn này</span>
                    </button>
                </form>
            @endif

            <!-- Cancel Button if pending -->
            @if ($order->canBeCancelledByCustomer())
                <button type="button" class="btn btn-outline-danger py-2 px-3" data-bs-toggle="modal" data-bs-target="#cancelModal">
                    <i data-lucide="x" style="width: 15px; height: 15px; margin-right: 0.2rem;"></i>
                    <span>Hủy đơn</span>
                </button>
            @endif
        </div>
    </div>

    <!-- Order Timeline Progress Bar (Shopee Style) -->
    @if ($order->shipping_status !== 'cancelled')
        <div class="card-modern p-4 mb-4 shadow-sm border">
            <h6 class="fw-bold text-dark mb-4 pb-2 border-bottom">Tiến Trình Đơn Hàng</h6>

            @php
                $steps = [
                    'pending'    => ['label' => 'Đơn Hàng Đã Đặt', 'desc' => $order->created_at->format('d/m H:i'), 'icon' => 'file-text'],
                    'processing' => ['label' => 'Đang Chuẩn Bị Hàng', 'desc' => 'Người bán chuẩn bị gói hàng', 'icon' => 'package'],
                    'shipping'   => ['label' => 'Đang Giao Hàng', 'desc' => $order->ghn_status_name ?: 'Đơn vị vận chuyển GHN', 'icon' => 'truck'],
                    'delivered'  => ['label' => 'Đã Giao Thành Công', 'desc' => $order->shipping_status === 'delivered' ? 'Đã hoàn tất' : 'Dự kiến: ' . ($order->expected_delivery_at ? $order->expected_delivery_at->format('d/m') : '2-3 ngày'), 'icon' => 'check-circle'],
                ];

                $stepOrder = ['pending', 'processing', 'shipping', 'delivered'];
                $currentIndex = array_search($order->shipping_status, $stepOrder);
                if ($currentIndex === false) $currentIndex = 0;
            @endphp

            <div class="row g-3 text-center position-relative">
                @foreach ($stepOrder as $idx => $stepKey)
                    @php
                        $step = $steps[$stepKey];
                        $isCompleted = $idx <= $currentIndex;
                        $isCurrent = $idx === $currentIndex;
                    @endphp
                    <div class="col-3 position-relative">
                        <div class="d-flex flex-column align-items-center">
                            <div 
                                class="rounded-circle d-flex align-items-center justify-content-center mb-2 shadow-sm"
                                style="width: 48px; height: 48px; {{ $isCompleted ? 'background: var(--brand-600); color: #fff;' : 'background: #f1f5f9; color: #94a3b8;' }} transition: all 0.3s;"
                            >
                                <i data-lucide="{{ $step['icon'] }}" style="width: 22px; height: 22px;"></i>
                            </div>
                            <div class="fw-bold text-dark small {{ $isCurrent ? 'text-primary fw-extrabold' : '' }}">
                                {{ $step['label'] }}
                            </div>
                            <div class="text-secondary" style="font-size: 0.72rem;">
                                {{ $step['desc'] }}
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @else
        <!-- Cancelled Alert Box -->
        <div class="card-modern p-4 mb-4 shadow-sm border border-danger-subtle bg-danger-subtle bg-opacity-25">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 48px; height: 48px; background: #fee2e2; color: #dc2626;">
                    <i data-lucide="x-circle" style="width: 26px; height: 26px;"></i>
                </div>
                <div>
                    <h5 class="fw-bold text-danger mb-1">Đơn Hàng Đã Bị Hủy</h5>
                    <p class="text-secondary small mb-0">
                        Đơn hàng đã được hủy vào lúc <strong>{{ $order->cancelled_at ? $order->cancelled_at->format('d/m/Y H:i') : '' }}</strong>.
                        @if ($order->cancel_reason)
                            Lý do: <em>"{{ $order->cancel_reason }}"</em>.
                        @endif
                        Toàn bộ số lượng tồn kho sản phẩm đã được tự động hoàn lại.
                    </p>
                </div>
            </div>
        </div>
    @endif

    <div class="row g-4">
        <!-- Left: Delivery info & Items -->
        <div class="col-lg-8">
            <!-- GHN Tracking Box if dispatched -->
            @if ($order->isGhnOrder())
                <div class="card-modern p-4 mb-4 shadow-sm border" style="background: rgba(30, 77, 233, 0.03); border-color: rgba(30, 77, 233, 0.2) !important;">
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3 pb-2 border-bottom">
                        <div class="d-flex align-items-center gap-2">
                            <i data-lucide="truck" class="text-primary" style="width: 20px; height: 20px;"></i>
                            <h6 class="fw-bold text-dark mb-0">Theo Dõi Vận Chuyển GHN</h6>
                        </div>
                        <a href="https://donhang.ghn.vn/?order_code={{ $order->ghn_order_code }}" target="_blank" class="btn btn-sm btn-outline-primary d-inline-flex align-items-center gap-1">
                            <span>Tra cứu trên GHN</span>
                            <i data-lucide="external-link" style="width: 13px; height: 13px;"></i>
                        </a>
                    </div>

                    <div class="row g-3 small">
                        <div class="col-sm-4">
                            <span class="text-secondary">Mã vận đơn:</span>
                            <div class="fw-extrabold text-primary font-monospace fs-6">{{ $order->ghn_order_code }}</div>
                        </div>
                        <div class="col-sm-4">
                            <span class="text-secondary">Trạng thái:</span>
                            <div class="fw-bold text-dark">{{ $order->ghn_status_name ?: $order->ghn_status }}</div>
                        </div>
                        <div class="col-sm-4">
                            <span class="text-secondary">Dự kiến giao:</span>
                            <div class="fw-bold text-success">{{ $order->expected_delivery_at ? $order->expected_delivery_at->format('d/m/Y') : 'Đang cập nhật' }}</div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Delivery Info -->
            <div class="card-modern p-4 mb-4 shadow-sm border">
                <h6 class="fw-bold text-dark mb-3 pb-2 border-bottom d-flex align-items-center gap-2">
                    <i data-lucide="map-pin" class="text-primary" style="width: 18px; height: 18px;"></i>
                    <span>Thông Tin Nhận Hàng</span>
                </h6>
                <div class="row g-3 small">
                    <div class="col-sm-6">
                        <span class="text-secondary">Người nhận:</span>
                        <div class="fw-bold text-dark fs-6">{{ $order->recipient_name }}</div>
                    </div>
                    <div class="col-sm-6">
                        <span class="text-secondary">Số điện thoại:</span>
                        <div class="fw-bold text-dark fs-6 font-monospace">{{ $order->phone }}</div>
                    </div>
                    <div class="col-12">
                        <span class="text-secondary">Địa chỉ giao hàng:</span>
                        <div class="fw-medium text-dark">{{ $order->shipping_address }}</div>
                    </div>
                    <div class="col-sm-6">
                        <span class="text-secondary">Hình thức thanh toán:</span>
                        <div class="fw-medium text-dark">{{ $order->payment_method_label }}</div>
                    </div>
                    <div class="col-sm-6">
                        <span class="text-secondary">Gói vận chuyển:</span>
                        <div class="fw-medium text-dark">{{ $order->shipping_method_label }}</div>
                    </div>
                    @if ($order->notes)
                        <div class="col-12">
                            <span class="text-secondary">Ghi chú giao hàng:</span>
                            <div class="fst-italic text-dark">{{ $order->notes }}</div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Items table -->
            <div class="card-modern p-4 shadow-sm border">
                <h6 class="fw-bold text-dark mb-3 pb-2 border-bottom d-flex align-items-center gap-2">
                    <i data-lucide="package" class="text-primary" style="width: 18px; height: 18px;"></i>
                    <span>Danh Sách Sản Phẩm ({{ $order->items->count() }} món)</span>
                </h6>

                <div class="d-flex flex-column gap-3">
                    @foreach ($order->items as $item)
                        <div class="d-flex align-items-center justify-content-between gap-3 p-2.5 rounded-3 border" style="background: var(--bg-surface-subtle);">
                            <div class="d-flex align-items-center gap-3 min-w-0">
                                <div class="rounded-3 border overflow-hidden flex-shrink-0" style="width: 60px; height: 60px;">
                                    @if ($item->product_image)
                                        <img src="{{ $item->product_image }}" alt="{{ $item->product_name }}" class="w-100 h-100 object-fit-cover">
                                    @else
                                        <div class="w-100 h-100 d-flex align-items-center justify-content-center bg-light">
                                            <i data-lucide="shopping-bag" class="text-secondary" style="width: 20px; height: 20px;"></i>
                                        </div>
                                    @endif
                                </div>
                                <div class="min-w-0">
                                    <h6 class="fw-bold text-dark mb-1 text-truncate">{{ $item->product_name }}</h6>
                                    @if ($item->variant_title)
                                        <div class="text-primary small mb-1" style="font-size: 0.75rem; font-weight: 500;">
                                            Phân loại: {{ $item->variant_title }}
                                        </div>
                                    @endif
                                    <div class="text-secondary small mb-2">
                                        Đơn giá: {{ $item->formatted_price }} &times; <span class="fw-bold text-dark">{{ $item->quantity }}</span>
                                    </div>

                                    <!-- Review Status / Review Action Button -->
                                    @if ($order->shipping_status === 'delivered')
                                        <div class="pt-1.5 border-top border-secondary border-opacity-10">
                                            @if ($item->review)
                                                <div class="d-inline-flex align-items-center gap-1.5 text-success small">
                                                    <i class="bi bi-patch-check-fill text-warning"></i>
                                                    <span class="fw-semibold">Đã đánh giá: {{ $item->review->rating }}★ (+{{ number_format($item->review->coins_rewarded) }} Xu)</span>
                                                </div>
                                            @else
                                                <button type="button" class="btn btn-sm btn-outline-warning text-dark fw-bold d-inline-flex align-items-center gap-1.5 py-1 px-2.5 shadow-sm"
                                                        onclick="openReviewModal({{ $item->id }}, '{{ addslashes($item->product_name) }}', '{{ addslashes($item->variant_title ?? '') }}', '{{ $item->product_image }}')"
                                                        style="border-color: #f59e0b; background: #fffbeb; font-size: 0.8rem;">
                                                    <i class="bi bi-star-fill text-warning"></i>
                                                    <span>Đánh Giá (+1.000 Xu)</span>
                                                </button>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="text-end flex-shrink-0">
                                <span class="fw-extrabold text-primary fs-6">{{ $item->formatted_subtotal }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Right: Summary -->
        <div class="col-lg-4">
            <div class="card-modern p-4 shadow-sm border sticky-top" style="top: 85px;">
                <h6 class="fw-bold text-dark mb-3 pb-2 border-bottom d-flex align-items-center justify-content-between">
                    <span>Tóm Tắt Thanh Toán</span>
                    <span class="badge {{ $order->payment_status_badge['class'] }}">
                        {{ $order->payment_status_badge['label'] }}
                    </span>
                </h6>

                <div class="d-flex flex-column gap-2 text-secondary small mb-3">
                    <div class="d-flex justify-content-between">
                        <span>Tiền hàng:</span>
                        <span class="fw-bold text-dark">{{ $order->formatted_subtotal }}</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span>Phí vận chuyển GHN:</span>
                        <span class="fw-bold text-dark">{{ $order->formatted_shipping_fee }}</span>
                    </div>
                    @if ($order->discount_amount > 0 || $order->voucher_code)
                        <div class="d-flex justify-content-between text-success align-items-center">
                            <span class="d-flex align-items-center gap-1">
                                <i data-lucide="ticket" style="width: 14px; height: 14px;"></i>
                                <span>Voucher ưu đãi {{ $order->voucher_code ? "({$order->voucher_code})" : '' }}:</span>
                            </span>
                            <span class="fw-bold">-{{ number_format($order->discount_amount, 0, ',', '.') }} ₫</span>
                        </div>
                    @endif
                    @if ($order->coins_used > 0)
                        <div class="d-flex justify-content-between text-warning-emphasis align-items-center">
                            <span class="d-flex align-items-center gap-1">
                                <i class="bi bi-coin text-warning"></i>
                                <span>Xu Aurelia đã dùng (-{{ number_format($order->coins_used, 0, ',', '.') }} Xu):</span>
                            </span>
                            <span class="fw-bold">-{{ number_format($order->coins_discount_amount, 0, ',', '.') }} ₫</span>
                        </div>
                    @endif
                    <div class="d-flex justify-content-between align-items-baseline pt-2 border-top">
                        <span class="fw-bold text-dark fs-6">Tổng thanh toán:</span>
                        <span class="fw-extrabold text-primary fs-4">{{ $order->formatted_total_amount }}</span>
                    </div>
                </div>

                <div class="p-3 bg-light rounded-3 small mb-3">
                    <div class="mb-1">
                        <span class="text-secondary">Phương thức:</span>
                        <strong class="text-dark ms-1">{{ $order->payment_method_label }}</strong>
                    </div>
                    @if ($order->paid_at)
                        <div>
                            <span class="text-secondary">Đã thanh toán lúc:</span>
                            <strong class="text-success ms-1">{{ $order->paid_at->format('d/m/Y H:i') }}</strong>
                        </div>
                    @endif
                </div>

                <div class="d-flex flex-column gap-2 pt-2 border-top">
                    <!-- Review incentive in summary card if delivered -->
                    @if ($order->shipping_status === 'delivered')
                        @php
                            $unreviewed = $order->items->first(fn($i) => !$i->review);
                        @endphp
                        @if ($unreviewed)
                            <button type="button" class="btn btn-outline-warning text-dark fw-bold w-100 py-2.5 d-flex align-items-center justify-content-center gap-2 shadow-xs"
                                    style="border-color: #f59e0b; background: #fffbeb;"
                                    onclick="openReviewModal({{ $unreviewed->id }}, '{{ addslashes($unreviewed->product_name) }}', '{{ addslashes($unreviewed->variant_title ?? '') }}', '{{ $unreviewed->product_image }}')">
                                <i class="bi bi-star-fill text-warning"></i>
                                <span>Đánh Giá Nhận Xu (+1.000 Xu)</span>
                            </button>
                        @endif
                    @endif

                    <!-- Reorder Button -->
                    @if ($order->canReorder())
                        <form action="{{ route('account.orders.reorder', $order) }}" method="POST" class="w-100">
                            @csrf
                            <button type="submit" class="btn btn-brand-primary w-100 py-2.5 d-flex align-items-center justify-content-center gap-2">
                                <i data-lucide="repeat" style="width: 16px; height: 16px;"></i>
                                <span>Mua Lại Đơn Hàng</span>
                            </button>
                        </form>
                    @endif

                    <!-- Back to orders button -->
                    <a href="{{ route('account.orders') }}" class="btn btn-surface w-100 py-2.5 text-decoration-none d-flex align-items-center justify-content-center gap-2">
                        <i data-lucide="arrow-left" style="width: 16px; height: 16px;"></i>
                        <span>Quay Lại Danh Sách</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Cancel Order Modal -->
@if ($order->canBeCancelledByCustomer())
    <div class="modal fade" id="cancelModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" style="max-width: 440px;">
            <div class="modal-content modal-content-modern border-0">
                <form action="{{ route('account.orders.cancel', $order) }}" method="POST">
                    @csrf
                    <div class="modal-header p-4 border-bottom">
                        <h6 class="fw-bold text-danger mb-0 d-flex align-items-center gap-2">
                            <i data-lucide="alert-triangle" style="width: 18px; height: 18px;"></i>
                            <span>Xác Nhận Hủy Đơn Hàng</span>
                        </h6>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-4 text-start">
                        <p class="text-secondary small mb-3">
                            Bạn có chắc chắn muốn hủy đơn hàng <strong class="font-monospace text-dark">{{ $order->order_code }}</strong>?
                        </p>
                        <div class="mb-2">
                            <label class="form-label small fw-semibold text-secondary">Vui lòng chọn lý do hủy đơn <span class="text-danger">*</span></label>
                            <select name="cancel_reason" class="form-select mb-2" required>
                                <option value="Muốn thay đổi địa chỉ nhận hàng">Muốn thay đổi địa chỉ nhận hàng</option>
                                <option value="Muốn thay đổi sản phẩm / phân loại">Muốn thay đổi sản phẩm / phân loại</option>
                                <option value="Đổi ý không muốn mua nữa">Đổi ý không muốn mua nữa</option>
                                <option value="Thời gian giao hàng dự kiến quá lâu">Thời gian giao hàng dự kiến quá lâu</option>
                                <option value="Tìm thấy giá rẻ hơn ở nơi khác">Tìm thấy giá rẻ hơn ở nơi khác</option>
                                <option value="Lý do khác">Lý do khác</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer p-3 border-top">
                        <button type="button" class="btn btn-surface" data-bs-dismiss="modal">Giữ đơn hàng</button>
                        <button type="submit" class="btn btn-danger">Hủy đơn hàng ngay</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endif

<!-- Review Modal (Shopee-style Coin Reward) -->
<div class="modal fade" id="reviewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <form action="{{ route('reviews.store') }}" method="POST" enctype="multipart/form-data" id="reviewForm">
                @csrf
                <input type="hidden" name="order_item_id" id="modalOrderItemId" value="">
                
                <!-- Header -->
                <div class="modal-header bg-light border-bottom p-4">
                    <div>
                        <h5 class="fw-bold text-dark mb-1 d-flex align-items-center gap-2">
                            <i class="bi bi-star-fill text-warning"></i>
                            Đánh Giá Sản Phẩm
                        </h5>
                        <p class="text-secondary small mb-0">Đóng góp đánh giá để giúp cộng đồng mua sắm và nhận thưởng Xu Aurelia!</p>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <!-- Body -->
                <div class="modal-body p-4">
                    <!-- Product Info Snapshot -->
                    <div class="d-flex align-items-center gap-3 p-3 rounded-3 bg-light mb-4 border">
                        <img id="modalProductImg" src="" alt="" class="rounded-2 object-fit-cover border" style="width: 56px; height: 56px;">
                        <div>
                            <h6 id="modalProductName" class="fw-bold text-dark mb-1">Tên sản phẩm</h6>
                            <div id="modalProductVariant" class="text-secondary small"></div>
                        </div>
                    </div>

                    <!-- Coin Incentive Banner (Shopee Style) -->
                    <div class="p-3 rounded-3 mb-4 d-flex align-items-center gap-3" style="background: #fffbeb; border: 1px solid #fde68a;">
                        <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 44px; height: 44px; background: #fef3c7; color: #d97706;">
                            <i class="bi bi-coin fs-4"></i>
                        </div>
                        <div class="small">
                            <div class="fw-bold text-dark">Thưởng Xu Aurelia cực hấp dẫn:</div>
                            <div class="text-secondary">
                                &bull; <strong class="text-warning-emphasis">+1.000 Xu</strong>: Đánh giá có hình ảnh kèm ít nhất 50 ký tự.<br>
                                &bull; <strong class="text-warning-emphasis">+300 Xu</strong>: Đánh giá từ 50 ký tự không có ảnh.<br>
                                &bull; <strong class="text-warning-emphasis">+100 Xu</strong>: Đánh giá ngắn hoặc chấm sao.
                            </div>
                        </div>
                    </div>

                    <!-- Rating Stars -->
                    <div class="text-center mb-4">
                        <label class="form-label fw-bold text-dark d-block mb-2">Chất lượng sản phẩm:</label>
                        <div class="d-inline-flex gap-2 text-warning fs-2" id="starRatingGroup" style="cursor: pointer;">
                            <i class="bi bi-star-fill star-item" data-value="1"></i>
                            <i class="bi bi-star-fill star-item" data-value="2"></i>
                            <i class="bi bi-star-fill star-item" data-value="3"></i>
                            <i class="bi bi-star-fill star-item" data-value="4"></i>
                            <i class="bi bi-star-fill star-item" data-value="5"></i>
                        </div>
                        <input type="hidden" name="rating" id="ratingInput" value="5">
                        <div id="starRatingLabel" class="fw-semibold text-danger small mt-1">Tuyệt vời</div>
                    </div>

                    <!-- Comment Textarea -->
                    <div class="mb-4">
                        <label class="form-label fw-bold text-dark d-flex justify-content-between">
                            <span>Bình luận & Chia sẻ trải nghiệm:</span>
                            <span class="text-muted small fw-normal"><span id="charCount">0</span>/2000 ký tự</span>
                        </label>
                        <textarea 
                            name="comment" 
                            id="reviewCommentInput"
                            rows="4" 
                            class="form-control" 
                            placeholder="Hãy chia sẻ cảm nhận về chất lượng da, đường may, form dáng và dịch vụ đóng gói giao hàng..."
                            maxlength="2000"
                            oninput="updateCharCount(this)"
                        ></textarea>
                    </div>

                    <!-- Image Upload (Max 5 files) -->
                    <div class="mb-2">
                        <label class="form-label fw-bold text-dark d-flex justify-content-between align-items-center">
                            <span>Hình ảnh thực tế sản phẩm (tối đa 5 ảnh):</span>
                            <span class="text-success small fw-semibold">
                                <i class="bi bi-image me-1"></i>Thêm ảnh để nhận tối đa 1.000 Xu
                            </span>
                        </label>
                        
                        <input type="file" name="images[]" id="reviewImagesInput" class="d-none" multiple accept="image/png,image/jpeg,image/webp" onchange="handleImageSelect(event)">

                        <div class="d-flex flex-wrap gap-2 align-items-center" id="imagePreviewList">
                            <button type="button" class="btn btn-outline-secondary border-dashed d-flex flex-column align-items-center justify-content-center p-3 rounded-3" 
                                    style="width: 80px; height: 80px; border-style: dashed !important;"
                                    onclick="document.getElementById('reviewImagesInput').click()">
                                <i class="bi bi-camera fs-5"></i>
                                <span style="font-size: 0.65rem;">Thêm ảnh</span>
                            </button>
                        </div>
                        <small class="text-muted d-block mt-1" style="font-size: 0.75rem;">Định dạng: JPG, PNG, WEBP. Tối đa 5MB mỗi ảnh.</small>
                    </div>
                </div>

                <!-- Footer -->
                <div class="modal-footer p-3 bg-light border-top">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Để sau</button>
                    <button type="submit" class="btn btn-danger fw-bold px-4 d-inline-flex align-items-center gap-2 shadow-sm">
                        <i class="bi bi-send-fill"></i>
                        <span>Hoàn Tất & Nhận Xu</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    let currentRating = 5;
    const starLabels = {
        1: 'Tệ',
        2: 'Không hài lòng',
        3: 'Bình thường',
        4: 'Hài lòng',
        5: 'Tuyệt vời'
    };

    function openReviewModal(itemId, productName, variantTitle, imgUrl) {
        // Reset form first
        document.getElementById('reviewForm').reset();

        document.getElementById('modalOrderItemId').value = itemId;
        document.getElementById('modalProductName').textContent = productName;
        document.getElementById('modalProductVariant').textContent = variantTitle ? `Phân loại: ${variantTitle}` : '';
        document.getElementById('modalProductImg').src = imgUrl || 'https://placehold.co/100';

        document.getElementById('imagePreviewList').innerHTML = `
            <button type="button" class="btn btn-outline-secondary border-dashed d-flex flex-column align-items-center justify-content-center p-3 rounded-3" 
                    style="width: 80px; height: 80px; border-style: dashed !important;"
                    onclick="document.getElementById('reviewImagesInput').click()">
                <i class="bi bi-camera fs-5"></i>
                <span style="font-size: 0.65rem;">Thêm ảnh</span>
            </button>
        `;
        document.getElementById('charCount').textContent = '0';
        setRating(5);

        const modal = new bootstrap.Modal(document.getElementById('reviewModal'));
        modal.show();
    }

    function setRating(val) {
        currentRating = parseInt(val);
        document.getElementById('ratingInput').value = currentRating;
        document.getElementById('starRatingLabel').textContent = starLabels[currentRating] || 'Tuyệt vời';

        const stars = document.querySelectorAll('#starRatingGroup .star-item');
        stars.forEach(star => {
            const starVal = parseInt(star.getAttribute('data-value'));
            if (starVal <= currentRating) {
                star.className = 'bi bi-star-fill star-item text-warning';
            } else {
                star.className = 'bi bi-star star-item text-muted opacity-25';
            }
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        const stars = document.querySelectorAll('#starRatingGroup .star-item');
        stars.forEach(star => {
            star.addEventListener('click', function () {
                setRating(this.getAttribute('data-value'));
            });
            star.addEventListener('mouseenter', function () {
                const hoverVal = parseInt(this.getAttribute('data-value'));
                stars.forEach(s => {
                    const sVal = parseInt(s.getAttribute('data-value'));
                    s.className = sVal <= hoverVal ? 'bi bi-star-fill star-item text-warning' : 'bi bi-star star-item text-muted opacity-25';
                });
                document.getElementById('starRatingLabel').textContent = starLabels[hoverVal] || '';
            });
        });

        document.getElementById('starRatingGroup').addEventListener('mouseleave', function () {
            setRating(currentRating);
        });
    });

    function updateCharCount(el) {
        const len = el.value.length;
        document.getElementById('charCount').textContent = len;
    }

    function handleImageSelect(event) {
        const files = Array.from(event.target.files).slice(0, 5);
        const container = document.getElementById('imagePreviewList');
        
        // Remove existing previews except the add button
        container.querySelectorAll('.preview-thumb').forEach(el => el.remove());

        files.forEach((file, index) => {
            const reader = new FileReader();
            reader.onload = function (e) {
                const div = document.createElement('div');
                div.className = 'position-relative rounded-3 overflow-hidden border preview-thumb';
                div.style.cssText = 'width: 80px; height: 80px;';
                div.innerHTML = `
                    <img src="${e.target.result}" class="w-100 h-100 object-fit-cover">
                `;
                container.insertBefore(div, container.firstChild);
            };
            reader.readAsDataURL(file);
        });
    }
</script>
@endsection
