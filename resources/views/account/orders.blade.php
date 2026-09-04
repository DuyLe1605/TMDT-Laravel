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
                    <a href="{{ route('account.coins') }}" class="btn-surface w-100 text-start py-2 px-3 text-decoration-none d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-coin text-warning"></i>
                            <span>Ví Xu Aurelia</span>
                        </div>
                        <span class="badge bg-warning-subtle text-dark fw-bold rounded-pill px-2" style="font-size: 0.7rem;">
                            {{ number_format(Auth::user()->coins_balance) }} Xu
                        </span>
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
                                        <a href="https://donhang.ghn.vn/?order_code={{ $order->ghn_order_code }}" target="_blank" class="badge bg-primary bg-opacity-10 text-primary border border-primary-subtle d-inline-flex align-items-center gap-1 small text-decoration-none" title="Nhấn để tra cứu trên GHN Portal">
                                            <i data-lucide="truck" style="width: 12px; height: 12px;"></i>
                                            <span>GHN: {{ $order->ghn_order_code }}</span>
                                            <i data-lucide="external-link" style="width: 10px; height: 10px;"></i>
                                        </a>
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
                            @if ($order->ghn_order_code)
                                <div class="p-2.5 px-3 rounded-2 mb-3 bg-primary bg-opacity-10 text-primary small d-flex align-items-center justify-content-between flex-wrap gap-2">
                                    <div class="d-flex align-items-center gap-2">
                                        <i data-lucide="truck" style="width: 16px; height: 16px;"></i>
                                        <span>Trạng thái giao GHN: <strong>{{ $order->ghn_status_name ?: $order->shipping_status_badge['label'] }}</strong></span>
                                    </div>
                                    <div class="d-flex align-items-center gap-3">
                                        @if ($order->expected_delivery_at)
                                            <div class="text-success fw-medium">
                                                Dự kiến giao: {{ $order->expected_delivery_at->format('d/m/Y') }}
                                            </div>
                                        @endif
                                        <a href="https://donhang.ghn.vn/?order_code={{ $order->ghn_order_code }}" target="_blank" class="btn btn-sm btn-primary py-0.5 px-2.5 d-inline-flex align-items-center gap-1" style="font-size: 0.76rem;">
                                            <span>Tra cứu bưu tá GHN</span>
                                            <i data-lucide="external-link" style="width: 11px; height: 11px;"></i>
                                        </a>
                                    </div>
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

                                                <!-- Review item badge/button if delivered -->
                                                @if ($order->shipping_status === 'delivered')
                                                    <div class="mt-1">
                                                        @if ($item->review)
                                                            <span class="badge bg-success-subtle text-success border border-success-subtle d-inline-flex align-items-center gap-1" style="font-size: 0.68rem;">
                                                                <i class="bi bi-patch-check-fill text-warning"></i> Đã đánh giá {{ $item->review->rating }}★
                                                            </span>
                                                        @else
                                                            <button type="button" class="btn btn-sm btn-outline-warning text-dark fw-bold py-0.5 px-2 shadow-xs" 
                                                                    style="border-color: #f59e0b; background: #fffbeb; font-size: 0.72rem;"
                                                                    onclick="openReviewModal({{ $item->id }}, '{{ addslashes($item->product_name) }}', '{{ addslashes($item->variant_title ?? '') }}', '{{ $item->product_image }}')">
                                                                <i class="bi bi-star-fill text-warning"></i> Đánh giá (+1.000 Xu)
                                                            </button>
                                                        @endif
                                                    </div>
                                                @endif
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
                                    <!-- Review incentive button for delivered orders -->
                                    @if ($order->shipping_status === 'delivered')
                                        @php
                                            $unreviewedItem = $order->items->first(fn($i) => !$i->review);
                                        @endphp
                                        @if ($unreviewedItem)
                                            <button type="button" class="btn btn-sm btn-outline-warning text-dark fw-bold d-inline-flex align-items-center gap-1 shadow-sm" 
                                                    style="border-color: #f59e0b; background: #fffbeb;" 
                                                    onclick="openReviewModal({{ $unreviewedItem->id }}, '{{ addslashes($unreviewedItem->product_name) }}', '{{ addslashes($unreviewedItem->variant_title ?? '') }}', '{{ $unreviewedItem->product_image }}')"
                                                    title="Đánh giá sản phẩm nhận Xu">
                                                <i class="bi bi-star-fill text-warning"></i>
                                                <span>Đánh giá (+1.000 Xu)</span>
                                            </button>
                                        @else
                                            <span class="badge bg-success-subtle text-success border border-success-subtle d-inline-flex align-items-center gap-1 small py-1.5 px-2">
                                                <i class="bi bi-check-circle-fill"></i> Đã đánh giá
                                            </span>
                                        @endif
                                    @endif

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
                                            <button type="submit" class="btn btn-sm {{ $order->shipping_status === 'delivered' ? 'btn-brand-primary' : 'btn-outline-primary' }} fw-semibold d-inline-flex align-items-center gap-1 shadow-sm" title="Thêm lại sản phẩm vào giỏ hàng">
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

        const starGroup = document.getElementById('starRatingGroup');
        if (starGroup) {
            starGroup.addEventListener('mouseleave', function () {
                setRating(currentRating);
            });
        }
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
