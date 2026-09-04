@extends('layouts.app')

@section('title', 'Chi tiết Đơn hàng ' . $order->order_code . ' - Admin Portal')

@section('content')
<!-- Page Header -->
<div class="mb-4">
    <div class="breadcrumb-modern">
        <span>Admin Portal</span>
        <i data-lucide="chevron-right" style="width: 14px; height: 14px;"></i>
        <a href="{{ route('admin.orders.index') }}" class="text-decoration-none">Quản lý đơn hàng</a>
        <i data-lucide="chevron-right" style="width: 14px; height: 14px;"></i>
        <span class="text-primary fw-medium font-monospace">{{ $order->order_code }}</span>
    </div>

    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mt-2">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <h2 class="fw-bold mb-0 text-dark font-monospace" style="letter-spacing: -0.02em;">
                    {{ $order->order_code }}
                </h2>
                <span class="badge {{ $order->shipping_status_badge['class'] }} fs-6 px-3 py-1.5">
                    {{ $order->shipping_status_badge['label'] }}
                </span>
                <span class="badge {{ $order->payment_status_badge['class'] }} fs-6 px-3 py-1.5">
                    {{ $order->payment_status_badge['label'] }}
                </span>
            </div>
            <p class="text-secondary mb-0" style="font-size: 0.92rem;">
                Thời gian đặt: <strong>{{ $order->created_at->format('d/m/Y H:i:s') }}</strong>
                &bull; Cập nhật: <strong>{{ $order->updated_at->format('d/m/Y H:i:s') }}</strong>
            </p>
        </div>

        <!-- Action Buttons Group -->
        <div class="d-flex flex-wrap align-items-center gap-2">
            <a href="{{ route('admin.orders.index') }}" class="btn-surface">
                <i data-lucide="arrow-left" style="width: 16px; height: 16px; margin-right: 0.35rem;"></i>
                <span>Quay lại</span>
            </a>

            <!-- Confirm Order Button (pending -> processing) -->
            @if ($order->canBeConfirmed())
                <form action="{{ route('admin.orders.update_status', $order) }}" method="POST" class="d-inline">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="action" value="confirm">
                    <button type="submit" class="btn btn-primary d-inline-flex align-items-center">
                        <i data-lucide="check" style="width: 16px; height: 16px; margin-right: 0.4rem;"></i>
                        <span>Xác nhận đơn (Chuẩn bị hàng)</span>
                    </button>
                </form>
            @endif

            <!-- Send to GHN Button (processing -> shipping) -->
            @if ($order->canBeSentToGhn())
                <form action="{{ route('admin.orders.send_ghn', $order) }}" method="POST" class="d-inline" onsubmit="return confirm('Xác nhận gửi thông tin kiện hàng lên GHN để tài xế đến lấy hàng?');">
                    @csrf
                    <button type="submit" class="btn btn-success d-inline-flex align-items-center">
                        <i data-lucide="send" style="width: 16px; height: 16px; margin-right: 0.4rem;"></i>
                        <span>Gửi đơn sang GHN</span>
                    </button>
                </form>
            @endif

            <!-- Print GHN Label Button -->
            @if ($order->isGhnOrder())
                <a href="{{ route('admin.orders.print_label', $order) }}" target="_blank" class="btn btn-brand-primary d-inline-flex align-items-center">
                    <i data-lucide="printer" style="width: 16px; height: 16px; margin-right: 0.4rem;"></i>
                    <span>In phiếu giao GHN (A5)</span>
                </a>
            @endif

            <!-- Mark Delivered Button (shipping -> delivered) -->
            @if ($order->shipping_status === 'shipping')
                <form action="{{ route('admin.orders.update_status', $order) }}" method="POST" class="d-inline" onsubmit="return confirm('Xác nhận đơn hàng này đã được giao thành công đến khách hàng?');">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="action" value="mark_delivered">
                    <button type="submit" class="btn btn-outline-success d-inline-flex align-items-center">
                        <i data-lucide="check-circle" style="width: 16px; height: 16px; margin-right: 0.4rem;"></i>
                        <span>Đánh dấu Đã Giao</span>
                    </button>
                </form>
            @endif

            <!-- Mark Payment as Paid Button (if still pending) -->
            @if ($order->payment_status !== 'paid')
                <form action="{{ route('admin.orders.update_status', $order) }}" method="POST" class="d-inline" onsubmit="return confirm('Xác nhận đơn hàng đã thanh toán thành công?');">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="action" value="mark_paid">
                    <button type="submit" class="btn btn-outline-primary d-inline-flex align-items-center" title="Xác nhận thanh toán">
                        <i data-lucide="credit-card" style="width: 16px; height: 16px; margin-right: 0.4rem;"></i>
                        <span>Xác nhận Đã Thu Tiền</span>
                    </button>
                </form>
            @endif

            <!-- Cancel Order Button -->
            @if ($order->canBeCancelledByAdmin())
                <button type="button" class="btn btn-outline-danger d-inline-flex align-items-center" data-bs-toggle="modal" data-bs-target="#cancelOrderModal">
                    <i data-lucide="x" style="width: 16px; height: 16px; margin-right: 0.4rem;"></i>
                    <span>Hủy đơn hàng</span>
                </button>
            @endif
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Left Column: Shipping details, GHN tracking & Items -->
    <div class="col-lg-8">
        <!-- GHN Shipping & Delivery Tracking Card -->
        <div class="card-modern p-4 mb-4 shadow-sm border position-relative overflow-hidden">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3 pb-2 border-bottom">
                <div class="d-flex align-items-center gap-2">
                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 34px; height: 34px; background: var(--brand-50); color: var(--brand-600);">
                        <i data-lucide="truck" style="width: 18px; height: 18px;"></i>
                    </div>
                    <h5 class="fw-bold text-dark mb-0 fs-6">Trạng Thái Vận Chuyển GHN</h5>
                </div>

                @if ($order->ghn_order_code)
                    <div class="d-flex align-items-center gap-2">
                        <a href="https://donhang.ghn.vn/?order_code={{ $order->ghn_order_code }}" target="_blank" class="btn btn-sm btn-outline-primary d-inline-flex align-items-center gap-1">
                            <i data-lucide="external-link" style="width: 14px; height: 14px;"></i>
                            <span>Tra cứu trên GHN Portal</span>
                        </a>
                    </div>
                @endif
            </div>

            @if ($order->isGhnOrder())
                <!-- GHN Order Connected State -->
                <div class="p-3 rounded-3 mb-3" style="background: rgba(30, 77, 233, 0.05); border: 1px solid rgba(30, 77, 233, 0.2);">
                    <div class="row g-3 align-items-center">
                        <div class="col-sm-4">
                            <span class="text-secondary small">Mã vận đơn GHN:</span>
                            <div class="fw-extrabold text-primary font-monospace fs-5">
                                {{ $order->ghn_order_code }}
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <span class="text-secondary small">Trạng thái hiện tại từ GHN:</span>
                            <div class="fw-bold text-dark">
                                {{ $order->ghn_status_name ?: $order->ghn_status }}
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <span class="text-secondary small">Dự kiến giao hàng:</span>
                            <div class="fw-bold text-success">
                                {{ $order->expected_delivery_at ? $order->expected_delivery_at->format('d/m/Y') : 'Chưa có thông tin' }}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- GHN Details Data if returned -->
                @if (!empty($ghnDetail))
                    <div class="p-3 bg-light rounded-3 small">
                        <div class="row g-2">
                            <div class="col-6 col-md-3">
                                <span class="text-secondary">Dịch vụ:</span>
                                <div class="fw-semibold text-dark">{{ $ghnDetail['service_type_name'] ?? 'Tiêu chuẩn' }}</div>
                            </div>
                            <div class="col-6 col-md-3">
                                <span class="text-secondary">Trọng lượng GHN:</span>
                                <div class="fw-semibold text-dark">{{ number_format($ghnDetail['converted_weight'] ?? $order->total_weight) }}g</div>
                            </div>
                            <div class="col-6 col-md-3">
                                <span class="text-secondary">Tiền thu hộ COD:</span>
                                <div class="fw-semibold text-dark">{{ number_format($ghnDetail['cod_amount'] ?? ($order->payment_method === 'cod' ? $order->total_amount : 0)) }} ₫</div>
                            </div>
                            <div class="col-6 col-md-3">
                                <span class="text-secondary">Bưu cục hiện tại:</span>
                                <div class="fw-semibold text-dark">{{ $ghnDetail['station_name'] ?? 'Kho trung chuyển GHN' }}</div>
                            </div>
                        </div>
                    </div>
                @endif
            @elseif ($order->shipping_status === 'pending')
                <div class="alert alert-warning d-flex align-items-center gap-2 mb-0" role="alert">
                    <i data-lucide="alert-triangle" style="width: 20px; height: 20px; flex-shrink: 0;"></i>
                    <div class="small">
                        Đơn hàng đang ở trạng thái <strong>Chờ xác nhận</strong>. Vui lòng xác nhận đơn hàng sau khi liên hệ hoặc kiểm tra thông tin khách hàng, sau đó gói hàng và gửi sang GHN.
                    </div>
                </div>
            @elseif ($order->shipping_status === 'processing')
                <div class="alert alert-info d-flex align-items-center justify-content-between flex-wrap gap-2 mb-0" role="alert">
                    <div class="d-flex align-items-center gap-2 small">
                        <i data-lucide="package-check" style="width: 20px; height: 20px; flex-shrink: 0;"></i>
                        <div>
                            Đơn hàng đã được xác nhận và đang đóng gói. Nhấn <strong>"Gửi đơn sang GHN"</strong> để tạo mã vận đơn và điều phối shipper lấy hàng!
                        </div>
                    </div>
                    <form action="{{ route('admin.orders.send_ghn', $order) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-success">
                            <i data-lucide="send" style="width: 14px; height: 14px; margin-right: 0.3rem;"></i>
                            <span>Gửi GHN ngay</span>
                        </button>
                    </form>
                </div>
            @elseif ($order->shipping_status === 'cancelled')
                <div class="alert alert-danger d-flex align-items-center gap-2 mb-0" role="alert">
                    <i data-lucide="x-circle" style="width: 20px; height: 20px; flex-shrink: 0;"></i>
                    <div class="small">
                        Đơn hàng này đã bị hủy vào lúc <strong>{{ $order->cancelled_at ? $order->cancelled_at->format('d/m/Y H:i') : '' }}</strong>.
                        @if ($order->cancel_reason)
                            <br>Lý do hủy: <em>{{ $order->cancel_reason }}</em>
                        @endif
                    </div>
                </div>
            @endif
        </div>

        <!-- Recipient & Delivery Information Card -->
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
                    <div class="fw-semibold text-dark fs-6">{{ $order->shipping_address }}</div>
                    @if ($order->to_district_id || $order->to_ward_code)
                        <div class="text-secondary mt-1" style="font-size: 0.75rem;">
                            Mã địa bàn GHN: District ID: <strong>{{ $order->to_district_id ?? 'N/A' }}</strong> &bull; Ward Code: <strong>{{ $order->to_ward_code ?? 'N/A' }}</strong>
                        </div>
                    @endif
                </div>
                <div class="col-sm-6">
                    <span class="text-secondary">Gói vận chuyển:</span>
                    <div class="fw-semibold text-dark">{{ $order->shipping_method_label }}</div>
                </div>
                <div class="col-sm-6">
                    <span class="text-secondary">Tổng trọng lượng ước tính:</span>
                    <div class="fw-semibold text-dark">{{ number_format($order->total_weight ?? 600) }} gram</div>
                </div>
                @if ($order->notes)
                    <div class="col-12">
                        <span class="text-secondary">Ghi chú của khách:</span>
                        <div class="p-2.5 rounded-2 bg-light fst-italic text-dark border">
                            {{ $order->notes }}
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Order Items Card -->
        <div class="card-modern p-4 shadow-sm border">
            <h6 class="fw-bold text-dark mb-3 pb-2 border-bottom d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-2">
                    <i data-lucide="package" class="text-primary" style="width: 18px; height: 18px;"></i>
                    <span>Danh Sách Sản Phẩm ({{ $order->items->count() }} món)</span>
                </div>
                <span class="badge bg-secondary-subtle text-secondary small">
                    Tổng: {{ $order->items->sum('quantity') }} sản phẩm
                </span>
            </h6>

            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead class="bg-light-subtle text-secondary small">
                        <tr>
                            <th class="ps-2 py-2">Sản phẩm</th>
                            <th class="py-2 text-center" style="width: 110px;">Đơn giá</th>
                            <th class="py-2 text-center" style="width: 90px;">Số lượng</th>
                            <th class="pe-2 py-2 text-end" style="width: 130px;">Thành tiền</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($order->items as $item)
                            <tr>
                                <td class="ps-2 py-3">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="rounded-3 border overflow-hidden flex-shrink-0" style="width: 54px; height: 54px;">
                                            @if ($item->product_image)
                                                <img src="{{ $item->product_image }}" alt="{{ $item->product_name }}" class="w-100 h-100 object-fit-cover">
                                            @else
                                                <div class="w-100 h-100 d-flex align-items-center justify-content-center bg-light">
                                                    <i data-lucide="shopping-bag" style="width: 18px; height: 18px;"></i>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="min-w-0">
                                            <div class="fw-bold text-dark small text-truncate" style="max-width: 280px;">
                                                {{ $item->product_name }}
                                            </div>
                                            @if ($item->variant_title)
                                                <div class="text-primary" style="font-size: 0.75rem; font-weight: 500;">
                                                    Phân loại: {{ $item->variant_title }}
                                                </div>
                                            @endif
                                            @if ($item->product)
                                                <a href="{{ route('shop.show', $item->product) }}" target="_blank" class="text-secondary small text-decoration-none" style="font-size: 0.72rem;">
                                                    <span>Xem link sản phẩm</span>
                                                    <i data-lucide="external-link" style="width: 11px; height: 11px;"></i>
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="py-3 text-center small text-secondary">
                                    {{ $item->formatted_price }}
                                </td>
                                <td class="py-3 text-center">
                                    <span class="badge bg-light text-dark border px-2.5 py-1 font-monospace fw-bold">
                                        {{ $item->quantity }}
                                    </span>
                                </td>
                                <td class="pe-2 py-3 text-end fw-extrabold text-dark fs-6">
                                    {{ $item->formatted_subtotal }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Right Column: Payment summary & Customer info -->
    <div class="col-lg-4">
        <!-- Payment Breakdown Card -->
        <div class="card-modern p-4 mb-4 shadow-sm border">
            <h6 class="fw-bold text-dark mb-3 pb-2 border-bottom d-flex align-items-center justify-content-between">
                <span>Tóm Tắt Thanh Toán</span>
                <span class="badge {{ $order->payment_status_badge['class'] }}">
                    {{ $order->payment_status_badge['label'] }}
                </span>
            </h6>

            <div class="d-flex flex-column gap-2 text-secondary small mb-3">
                <div class="d-flex justify-content-between">
                    <span>Tiền hàng (Tạm tính):</span>
                    <span class="fw-bold text-dark">{{ $order->formatted_subtotal }}</span>
                </div>
                <div class="d-flex justify-content-between">
                    <span>Phí vận chuyển GHN:</span>
                    <span class="fw-bold text-dark">{{ $order->formatted_shipping_fee }}</span>
                </div>
                @if ($order->discount_amount > 0)
                    <div class="d-flex justify-content-between text-success">
                        <span>Giảm giá:</span>
                        <span class="fw-bold">-{{ number_format($order->discount_amount, 0, ',', '.') }} ₫</span>
                    </div>
                @endif
                <div class="d-flex justify-content-between align-items-baseline pt-2 border-top">
                    <span class="fw-bold text-dark fs-6">Tổng thu của khách:</span>
                    <span class="fw-extrabold text-primary fs-4">{{ $order->formatted_total_amount }}</span>
                </div>
            </div>

            <div class="p-3 bg-light rounded-3 small">
                <div class="mb-1.5">
                    <span class="text-secondary">Phương thức:</span>
                    <strong class="text-dark ms-1">{{ $order->payment_method_label }}</strong>
                </div>
                @if ($order->paid_at)
                    <div>
                        <span class="text-secondary">Thời điểm thanh toán:</span>
                        <strong class="text-success ms-1">{{ $order->paid_at->format('d/m/Y H:i') }}</strong>
                    </div>
                @else
                    <div>
                        <span class="text-secondary">Thu tiền:</span>
                        @if ($order->payment_method === 'cod')
                            <strong class="text-warning ms-1">Tài xế GHN thu tiền mặt khi giao</strong>
                        @else
                            <strong class="text-warning ms-1">Khách chuyển khoản online</strong>
                        @endif
                    </div>
                @endif
            </div>
        </div>

        <!-- Customer Profile Card -->
        <div class="card-modern p-4 mb-4 shadow-sm border">
            <h6 class="fw-bold text-dark mb-3 pb-2 border-bottom d-flex align-items-center gap-2">
                <i data-lucide="user" class="text-primary" style="width: 18px; height: 18px;"></i>
                <span>Tài Khoản Đặt Hàng</span>
            </h6>

            @if ($order->user)
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div class="sidebar-user-avatar" style="width: 44px; height: 44px; font-size: 1rem;">
                        {{ strtoupper(substr($order->user->name, 0, 2)) }}
                    </div>
                    <div class="min-w-0">
                        <div class="fw-bold text-dark text-truncate">{{ $order->user->name }}</div>
                        <div class="text-secondary small text-truncate">{{ $order->user->email }}</div>
                    </div>
                </div>
                <div class="small text-secondary">
                    <div>Thành viên từ: <strong class="text-dark">{{ $order->user->created_at->format('d/m/Y') }}</strong></div>
                    <div>Vai trò: <span class="badge bg-secondary-subtle text-secondary">{{ ucfirst($order->user->role ?? 'Khách') }}</span></div>
                </div>
            @else
                <div class="text-secondary small">
                    <div class="p-2.5 rounded-2 bg-light border text-center">
                        <i data-lucide="user-x" class="text-secondary mb-1" style="width: 24px; height: 24px;"></i>
                        <div>Khách mua hàng vãng lai (Chưa tạo tài khoản)</div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Cancellation Details Card (if cancelled) -->
        @if ($order->shipping_status === 'cancelled')
            <div class="card-modern p-4 shadow-sm border border-danger-subtle bg-danger-subtle bg-opacity-25">
                <h6 class="fw-bold text-danger mb-2 d-flex align-items-center gap-2">
                    <i data-lucide="alert-circle" style="width: 18px; height: 18px;"></i>
                    <span>Thông Tin Hủy Đơn</span>
                </h6>
                <div class="small text-secondary">
                    <div>Thời điểm hủy: <strong class="text-dark">{{ $order->cancelled_at ? $order->cancelled_at->format('d/m/Y H:i') : 'N/A' }}</strong></div>
                    <div class="mt-1">Lý do: <strong class="text-danger">{{ $order->cancel_reason ?? 'Không nêu lý do' }}</strong></div>
                    <div class="mt-2 text-success">
                        <i data-lucide="check" style="width: 14px; height: 14px;"></i>
                        <span>Tồn kho sản phẩm đã được tự động hoàn lại vào kho.</span>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

<!-- Cancel Order Modal -->
@if ($order->canBeCancelledByAdmin())
    <div class="modal fade" id="cancelOrderModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" style="max-width: 480px;">
            <div class="modal-content modal-content-modern border-0">
                <form action="{{ route('admin.orders.cancel', $order) }}" method="POST">
                    @csrf
                    <div class="modal-header p-4 border-bottom">
                        <h5 class="fw-bold text-danger mb-0 d-flex align-items-center gap-2">
                            <i data-lucide="alert-triangle" style="width: 20px; height: 20px;"></i>
                            <span>Xác Nhận Hủy Đơn Hàng</span>
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <p class="text-secondary small mb-3">
                            Khi bạn hủy đơn hàng <strong class="font-monospace text-dark">{{ $order->order_code }}</strong>:
                        </p>
                        <ul class="text-secondary small mb-3 ps-3">
                            <li>Toàn bộ số lượng sản phẩm trong đơn sẽ được <strong>hoàn trả lại vào kho</strong> tự động.</li>
                            @if ($order->isGhnOrder())
                                <li class="text-warning fw-semibold">Đơn hàng trên hệ thống GHN sẽ được yêu cầu hủy tự động.</li>
                            @endif
                            @if ($order->payment_status === 'paid')
                                <li class="text-danger fw-semibold">Đơn hàng này đã thanh toán, trạng thái sẽ đổi thành 'Đang hoàn tiền' để kế toán xử lý.</li>
                            @endif
                        </ul>

                        <div class="mb-2">
                            <label class="form-label small fw-semibold text-secondary">Lý do hủy đơn hàng <span class="text-danger">*</span></label>
                            <textarea 
                                name="cancel_reason" 
                                rows="3" 
                                class="form-control" 
                                placeholder="Nhập lý do hủy (VD: Khách gọi điện yêu cầu hủy, hết hàng trong kho, v.v.)..." 
                                required
                            ></textarea>
                        </div>
                    </div>
                    <div class="modal-footer p-3 border-top">
                        <button type="button" class="btn-surface" data-bs-dismiss="modal">Đóng</button>
                        <button type="submit" class="btn btn-danger d-inline-flex align-items-center gap-1">
                            <i data-lucide="trash-2" style="width: 16px; height: 16px;"></i>
                            <span>Xác Nhận Hủy Đơn</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endif
@endsection
