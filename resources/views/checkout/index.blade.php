@extends('layouts.storefront')

@section('title', 'Thanh Toán Đơn Hàng - Aurelia Luxury Bags')

@section('content')
<div class="container py-4 py-lg-5">
    <!-- Breadcrumb -->
    <div class="breadcrumb-modern mb-3">
        <a href="{{ route('home') }}">Trang chủ</a>
        <i data-lucide="chevron-right" style="width: 14px; height: 14px;"></i>
        <a href="{{ route('cart.index') }}">Giỏ hàng</a>
        <i data-lucide="chevron-right" style="width: 14px; height: 14px;"></i>
        <span class="text-primary fw-medium">Thanh toán đơn hàng</span>
    </div>

    <div class="d-flex align-items-center justify-content-between mb-4 pb-2 border-bottom">
        <div>
            <h1 class="fw-extrabold text-dark mb-1" style="font-size: 1.85rem; letter-spacing: -0.02em;">
                Thanh Toán Đơn Hàng
            </h1>
            <p class="text-secondary small mb-0">
                Vui lòng kiểm tra kỹ thông tin nhận hàng và danh sách sản phẩm trước khi hoàn tất đặt hàng
            </p>
        </div>
    </div>

    <form id="checkoutProcessForm" action="{{ route('checkout.process') }}" method="POST">
        @csrf

        <!-- Hidden input for selected cart items -->
        @foreach ($selectedItems as $item)
            <input type="hidden" name="selected_items[]" value="{{ $item->id }}">
        @endforeach

        <!-- Hidden shipping parameters -->
        <input type="hidden" name="shipping_fee" id="hiddenShippingFee" value="{{ $shippingFee }}">
        <input type="hidden" name="to_district_id" id="hiddenToDistrictId" value="">
        <input type="hidden" name="to_ward_code" id="hiddenToWardCode" value="">
        <input type="hidden" name="expected_delivery_at" id="hiddenExpectedDeliveryAt" value="">

        <div class="row g-4">
            <!-- Left Column: Shipping Address & Order Review -->
            <div class="col-lg-8">
                <!-- 1. Shopee-Style Shipping Address Section -->
                <div class="card-modern p-4 mb-4 shadow-sm border position-relative overflow-hidden">
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3 pb-2 border-bottom">
                        <div class="d-flex align-items-center gap-2">
                            <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; background: var(--brand-50); color: var(--brand-600);">
                                <i data-lucide="map-pin" style="width: 18px; height: 18px;"></i>
                            </div>
                            <h5 class="fw-bold text-dark mb-0 fs-6">Địa Chỉ Nhận Hàng</h5>
                        </div>

                        <div class="d-flex align-items-center gap-2">
                            @auth
                                @if ($addresses->count() > 1)
                                    <button type="button" class="btn btn-sm btn-surface fw-semibold d-inline-flex align-items-center gap-1" onclick="openAddressModal()">
                                        <i data-lucide="list" style="width: 14px; height: 14px;"></i>
                                        <span>Chọn địa chỉ khác ({{ $addresses->count() }})</span>
                                    </button>
                                @endif
                            @endauth

                            <button type="button" class="btn btn-sm btn-brand-primary fw-semibold d-inline-flex align-items-center gap-1 shadow-sm" onclick="openNewAddressModal()">
                                <i data-lucide="plus" style="width: 14px; height: 14px;"></i>
                                <span>Tạo địa chỉ mới nhanh</span>
                            </button>
                        </div>
                    </div>

                    @auth
                        @if ($defaultAddress)
                            <!-- Active Selected Address Display (Shopee style card) -->
                            <div id="activeAddressDisplay" class="p-3.5 rounded-3" style="background: var(--bg-surface-subtle); border: 1px solid var(--border-default); border-left: 4px solid var(--brand-500) !important;">
                                <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-1.5">
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="fw-bold text-dark fs-6" id="dispRecipientName">{{ $defaultAddress->recipient_name }}</span>
                                        <span class="fw-bold text-secondary small" id="dispPhone">({{ $defaultAddress->phone }})</span>
                                        <span class="badge bg-primary-subtle text-primary" id="dispTypeBadge" style="font-size: 0.7rem;">
                                            {{ $defaultAddress->type_label }}
                                        </span>
                                        @if ($defaultAddress->is_default)
                                            <span class="badge bg-danger-subtle text-danger" id="dispDefaultBadge" style="font-size: 0.7rem;">Mặc định</span>
                                        @endif
                                    </div>

                                    @if ($addresses->count() > 1)
                                        <button type="button" class="btn btn-sm btn-link text-primary p-0 text-decoration-none fw-semibold" onclick="openAddressModal()">
                                            Thay đổi
                                        </button>
                                    @endif
                                </div>
                                <div class="text-dark small leading-relaxed" id="dispFullAddress">
                                    <i data-lucide="map-pin" class="text-danger inline-block me-1" style="width: 13px; height: 13px;"></i>
                                    {{ $defaultAddress->full_address }}
                                </div>
                            </div>

                            <!-- Hidden inputs submitted with form -->
                            <input type="hidden" name="recipient_name" id="hiddenRecipientName" value="{{ $defaultAddress->recipient_name }}">
                            <input type="hidden" name="phone" id="hiddenPhone" value="{{ $defaultAddress->phone }}">
                            <input type="hidden" name="shipping_address" id="hiddenShippingAddress" value="{{ $defaultAddress->full_address }}">
                        @else
                            <!-- No address saved yet -> Prompt to click create new -->
                            <div class="p-4 text-center rounded-3 border" style="background: var(--bg-surface-subtle);" id="noAddressBox">
                                <i data-lucide="map-pin-off" class="text-secondary mb-2" style="width: 32px; height: 32px;"></i>
                                <h6 class="fw-bold text-dark mb-1">Bạn chưa có địa chỉ nhận hàng nào</h6>
                                <p class="text-secondary small mb-3">Vui lòng bấm nút bên dưới để chọn Tỉnh/Thành phố, Quận/Huyện, Phường/Xã và thêm địa chỉ nhận hàng.</p>
                                <button type="button" class="btn btn-brand-primary px-4 py-2" onclick="openNewAddressModal()">
                                    <i data-lucide="plus-circle" style="width: 16px; height: 16px; margin-right: 0.4rem;"></i>
                                    <span>Tạo Địa Chỉ Mới Ngay</span>
                                </button>
                            </div>

                            <!-- Active Address Box (Hidden initially until created via dialog) -->
                            <div id="activeAddressDisplay" class="p-3.5 rounded-3 d-none" style="background: var(--bg-surface-subtle); border: 1px solid var(--border-default); border-left: 4px solid var(--brand-500) !important;">
                                <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-1.5">
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="fw-bold text-dark fs-6" id="dispRecipientName"></span>
                                        <span class="fw-bold text-secondary small" id="dispPhone"></span>
                                        <span class="badge bg-primary-subtle text-primary" id="dispTypeBadge" style="font-size: 0.7rem;">Nhà riêng</span>
                                    </div>
                                </div>
                                <div class="text-dark small leading-relaxed" id="dispFullAddress"></div>
                            </div>

                            <input type="hidden" name="recipient_name" id="hiddenRecipientName" value="">
                            <input type="hidden" name="phone" id="hiddenPhone" value="">
                            <input type="hidden" name="shipping_address" id="hiddenShippingAddress" value="">
                        @endif
                    @else
                        <!-- Guest User Cascading Address Form & Quick Dialog Trigger -->
                        <div class="alert alert-info py-2 px-3 small d-flex align-items-center justify-content-between mb-3">
                            <div class="d-flex align-items-center gap-2">
                                <i data-lucide="info" style="width: 16px; height: 16px;"></i>
                                <span>Bạn đang đặt hàng với tư cách <strong>Khách vãng lai</strong>.</span>
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="openNewAddressModal()">
                                Mở bảng chọn nhanh
                            </button>
                        </div>

                        <!-- Active Selected Guest Address Display if created via Modal -->
                        <div id="guestActiveAddressBox" class="p-3 rounded-3 mb-3 d-none" style="background: var(--bg-surface-subtle); border: 1px solid var(--border-default); border-left: 4px solid var(--brand-500) !important;">
                            <div class="d-flex align-items-center justify-content-between mb-1">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="fw-bold text-dark" id="guestDispName"></span>
                                    <span class="text-secondary small" id="guestDispPhone"></span>
                                    <span class="badge bg-primary-subtle text-primary" style="font-size: 0.68rem;">Đã chọn</span>
                                </div>
                                <button type="button" class="btn btn-sm btn-link text-primary p-0 text-decoration-none" onclick="openNewAddressModal()">
                                    Thay đổi
                                </button>
                            </div>
                            <div class="text-dark small" id="guestDispAddress"></div>
                        </div>

                        <!-- Cascading Inline Form for Guests -->
                        <div id="guestInlineForm" class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label-modern small fw-semibold">Họ và tên người nhận *</label>
                                <input type="text" name="recipient_name" id="guestRecipientName" class="form-control form-control-modern" placeholder="Ví dụ: Nguyễn Thị Mai" required value="{{ old('recipient_name') }}" oninput="updateGuestShippingAddress()">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label-modern small fw-semibold">Số điện thoại di động *</label>
                                <input type="tel" name="phone" id="guestPhone" class="form-control form-control-modern" placeholder="Ví dụ: 0988123456" required value="{{ old('phone') }}" oninput="updateGuestShippingAddress()">
                            </div>

                            <!-- Cascading Dropdowns for Province / District / Ward -->
                            <div class="col-md-4">
                                <label class="form-label-modern small fw-semibold">Tỉnh / Thành phố *</label>
                                <select name="province" id="guestProvinceSelect" class="form-select form-select-modern" required>
                                    <option value="">-- Chọn Tỉnh / Thành phố --</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label-modern small fw-semibold">Quận / Huyện *</label>
                                <select name="district" id="guestDistrictSelect" class="form-select form-select-modern" required disabled>
                                    <option value="">-- Chọn Quận / Huyện --</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label-modern small fw-semibold">Phường / Xã *</label>
                                <select name="ward" id="guestWardSelect" class="form-select form-select-modern" required disabled>
                                    <option value="">-- Chọn Phường / Xã --</option>
                                </select>
                            </div>

                            <div class="col-12">
                                <label class="form-label-modern small fw-semibold">Địa chỉ cụ thể (Số nhà, tên đường, tòa nhà...) *</label>
                                <input type="text" name="specific_address" id="guestSpecific" class="form-control form-control-modern" placeholder="Ví dụ: Số 123 Đường Lê Lợi" required value="{{ old('specific_address') }}" oninput="updateGuestShippingAddress()">
                            </div>
                        </div>

                        <input type="hidden" name="shipping_address" id="hiddenShippingAddress" value="{{ old('shipping_address') }}">
                    @endauth
                </div>

                <!-- 2. Ordered Products Review Section -->
                <div class="card-modern p-4 mb-4 shadow-sm border">
                    <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom">
                        <div class="d-flex align-items-center gap-2">
                            <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; background: var(--brand-50); color: var(--brand-600);">
                                <i data-lucide="package-check" style="width: 18px; height: 18px;"></i>
                            </div>
                            <h5 class="fw-bold text-dark mb-0 fs-6">Sản Phẩm Thanh Toán ({{ $selectedItems->count() }} món)</h5>
                        </div>
                        <a href="{{ route('cart.index') }}" class="btn-link text-primary small text-decoration-none fw-semibold">
                            Chỉnh sửa giỏ hàng
                        </a>
                    </div>

                    <div class="d-flex flex-column gap-3">
                        @foreach ($selectedItems as $item)
                            <div class="d-flex align-items-center justify-content-between gap-3 p-2.5 rounded-3" style="background: var(--bg-surface-subtle); border: 1px solid var(--border-default);">
                                <div class="d-flex align-items-center gap-3 min-w-0">
                                    <div class="rounded-3 border overflow-hidden flex-shrink-0" style="width: 56px; height: 56px;">
                                        @if ($item->product->image)
                                            <img src="{{ $item->product->image }}" alt="{{ $item->product->name }}" class="w-100 h-100 object-fit-cover">
                                        @else
                                            <div class="w-100 h-100 d-flex align-items-center justify-content-center" style="background: var(--bg-surface);">
                                                <i data-lucide="shopping-bag" class="text-secondary" style="width: 20px; height: 20px;"></i>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="min-w-0">
                                        <h6 class="fw-bold text-dark small mb-1 text-truncate">{{ $item->product->name }}</h6>
                                        <div class="text-secondary small" style="font-size: 0.78rem;">
                                            Đơn giá: {{ $item->formatted_unit_price }} &times; <span class="fw-bold text-dark">{{ $item->quantity }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="text-end flex-shrink-0">
                                    <span class="fw-extrabold text-primary small">{{ $item->formatted_subtotal }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- 3. Shipping & Payment Methods -->
                <div class="card-modern p-4 mb-4 shadow-sm border">
                    <!-- Shipping Method -->
                    <div class="mb-4 pb-3 border-bottom">
                        <label class="form-label-modern small fw-bold mb-2.5 d-flex align-items-center gap-2">
                            <i data-lucide="truck" class="text-primary" style="width: 16px; height: 16px;"></i>
                            <span>Phương thức vận chuyển</span>
                        </label>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="shipping-method-card p-3 rounded-3 border d-flex align-items-center justify-content-between cursor-pointer active" id="labelStandardShipping">
                                    <div class="d-flex align-items-center gap-2.5">
                                        <input type="radio" name="shipping_method" value="standard" class="form-check-input mt-0" checked onchange="onShippingMethodChanged('standard')">
                                        <div>
                                            <div class="fw-bold text-dark small d-flex align-items-center gap-1.5">
                                                <span>Giao tiêu chuẩn (GHN)</span>
                                                <span class="badge bg-success-subtle text-success py-0 px-1" style="font-size: 0.65rem;">Tiết kiệm</span>
                                            </div>
                                            <div class="text-secondary" id="standardLeadtimeText" style="font-size: 0.72rem;">Nhận hàng sau 2 - 3 ngày</div>
                                        </div>
                                    </div>
                                    <span class="fw-bold text-primary small" id="standardShippingFeeDisplay">
                                        {{ $summary['total_amount'] >= 500000 ? 'Miễn phí' : ($shippingFee > 0 ? number_format($shippingFee, 0, ',', '.') . ' ₫' : '30.000 ₫') }}
                                    </span>
                                </label>
                            </div>
                            <div class="col-md-6">
                                <label class="shipping-method-card p-3 rounded-3 border d-flex align-items-center justify-content-between cursor-pointer" id="labelExpressShipping">
                                    <div class="d-flex align-items-center gap-2.5">
                                        <input type="radio" name="shipping_method" value="express" class="form-check-input mt-0" onchange="onShippingMethodChanged('express')">
                                        <div>
                                            <div class="fw-bold text-dark small d-flex align-items-center gap-1.5">
                                                <span>Giao Hỏa tốc 24h</span>
                                                <span class="badge bg-warning-subtle text-warning-emphasis py-0 px-1" style="font-size: 0.65rem;">Hỏa tốc</span>
                                            </div>
                                            <div class="text-secondary" style="font-size: 0.72rem;">Nhận ngay trong 24 giờ</div>
                                        </div>
                                    </div>
                                    <span class="fw-bold text-primary small" id="expressShippingFeeDisplay">50.000 ₫</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Method -->
                    <div class="mb-4 pb-3 border-bottom">
                        <label class="form-label-modern small fw-bold mb-2.5 d-flex align-items-center gap-2">
                            <i data-lucide="wallet" class="text-primary" style="width: 16px; height: 16px;"></i>
                            <span>Phương thức thanh toán</span>
                        </label>

                        <div class="d-flex flex-column gap-2.5">
                            <!-- 1. COD -->
                            <label class="payment-method-card p-3 rounded-3 border d-flex align-items-center justify-content-between cursor-pointer active" id="labelPayCod">
                                <div class="d-flex align-items-center gap-3">
                                    <input type="radio" name="payment_method" value="cod" class="form-check-input mt-0" checked onchange="togglePaymentDetail('cod')">
                                    <div class="d-flex align-items-center gap-2.5">
                                        <div class="rounded-2 p-1.5 border d-flex align-items-center justify-content-center" style="background: var(--bg-surface-subtle); width: 32px; height: 32px;">
                                            <i data-lucide="banknote" class="text-success" style="width: 20px; height: 20px;"></i>
                                        </div>
                                        <div>
                                            <div class="fw-bold text-dark small">Thanh toán khi nhận hàng (COD)</div>
                                            <div class="text-secondary" style="font-size: 0.72rem;">Kiểm tra hàng rồi thanh toán tiền mặt cho shipper</div>
                                        </div>
                                    </div>
                                </div>
                            </label>

                            <!-- 2. Bank Transfer (VietQR) -->
                            <label class="payment-method-card p-3 rounded-3 border d-flex align-items-center justify-content-between cursor-pointer" id="labelPayBank">
                                <div class="d-flex align-items-center gap-3">
                                    <input type="radio" name="payment_method" value="bank_transfer" class="form-check-input mt-0" onchange="togglePaymentDetail('bank')">
                                    <div class="d-flex align-items-center gap-2.5">
                                        <div class="rounded-2 p-1.5 border d-flex align-items-center justify-content-center" style="background: var(--bg-surface-subtle); width: 32px; height: 32px;">
                                            <i data-lucide="qr-code" class="text-primary" style="width: 20px; height: 20px;"></i>
                                        </div>
                                        <div>
                                            <div class="fw-bold text-dark small">Chuyển khoản Ngân hàng (QR Code VietQR)</div>
                                            <div class="text-secondary" style="font-size: 0.72rem;">Quét mã QR qua app ngân hàng tiện lợi và bảo mật</div>
                                        </div>
                                    </div>
                                </div>
                            </label>

                            <!-- 3. MoMo E-Wallet -->
                            <label class="payment-method-card p-3 rounded-3 border d-flex align-items-center justify-content-between cursor-pointer" id="labelPayMomo">
                                <div class="d-flex align-items-center gap-3">
                                    <input type="radio" name="payment_method" value="momo" class="form-check-input mt-0" onchange="togglePaymentDetail('momo')">
                                    <div class="d-flex align-items-center gap-2.5">
                                        <div class="rounded-2 border d-flex align-items-center justify-content-center flex-shrink-0" style="background: #a50064; color: #fff; width: 32px; height: 32px; font-weight: 800; font-size: 0.68rem; letter-spacing: -0.02em;">
                                            MoMo
                                        </div>
                                        <div>
                                            <div class="fw-bold text-dark small">Ví Điện Tử MoMo (QR MoMo / App)</div>
                                            <div class="text-secondary" style="font-size: 0.72rem;">Thanh toán siêu tốc qua ứng dụng MoMo hoàn toàn miễn phí</div>
                                        </div>
                                    </div>
                                </div>
                            </label>
                        </div>

                        <!-- Bank Transfer Instruction Box -->
                        <div id="bankTransferBox" class="p-3 mt-3 rounded-3 border d-none" style="background: var(--bg-surface-subtle); border-color: var(--brand-400) !important;">
                            <div class="d-flex align-items-center gap-2 text-primary fw-bold small mb-2">
                                <i data-lucide="info" style="width: 16px; height: 16px;"></i>
                                <span>Thông tin chuyển khoản Ngân hàng:</span>
                            </div>
                            <div class="row g-2 small text-secondary">
                                <div class="col-sm-6"><strong>Ngân hàng:</strong> MB Bank (Quân Đội)</div>
                                <div class="col-sm-6"><strong>Số tài khoản:</strong> <span class="text-primary fw-bold">999988886666</span></div>
                                <div class="col-sm-6"><strong>Chủ tài khoản:</strong> CTY TNHH AURELIA BAGS</div>
                                <div class="col-sm-6"><strong>Nội dung CK:</strong> <span class="badge bg-dark-subtle text-dark">Hệ thống sẽ tạo mã đơn tự động</span></div>
                            </div>
                        </div>

                        <!-- MoMo Instruction Box -->
                        <div id="momoInstructionBox" class="p-3 mt-3 rounded-3 border d-none" style="background: rgba(165, 0, 100, 0.05); border-color: rgba(165, 0, 100, 0.3) !important;">
                            <div class="d-flex align-items-center gap-2 fw-bold small mb-2" style="color: #a50064;">
                                <i data-lucide="smartphone" style="width: 16px; height: 16px;"></i>
                                <span>Thông tin thanh toán qua Ví MoMo:</span>
                            </div>
                            <div class="row g-2 small text-secondary">
                                <div class="col-sm-6"><strong>Số ví MoMo:</strong> <span class="fw-bold" style="color: #a50064;">0988 889 999</span></div>
                                <div class="col-sm-6"><strong>Chủ ví:</strong> <span class="fw-bold text-dark">AURELIA LUXURY STORE</span></div>
                                <div class="col-12"><strong>Nội dung CK:</strong> <span class="badge bg-dark-subtle text-dark">Hệ thống tạo mã đơn hàng sau khi xác nhận</span></div>
                            </div>
                        </div>
                    </div>

                    <!-- Order Notes -->
                    <div>
                        <label class="form-label-modern small fw-bold mb-1.5 d-flex align-items-center gap-2">
                            <i data-lucide="message-square" class="text-primary" style="width: 16px; height: 16px;"></i>
                            <span>Ghi chú cho đơn hàng (Tùy chọn)</span>
                        </label>
                        <textarea name="notes" rows="2" class="form-control form-control-modern" placeholder="Ví dụ: Giao giờ hành chính, gọi trước khi giao..."></textarea>
                    </div>
                </div>
            </div>

            <!-- Right Column: Final Order Summary & Order Button -->
            <div class="col-lg-4">
                <div class="card-modern p-4 sticky-top shadow-sm border" style="top: 85px;">
                    <h5 class="fw-bold text-dark mb-3 pb-2 border-bottom d-flex align-items-center justify-content-between">
                        <span>Chi Tiết Thanh Toán</span>
                        <i data-lucide="receipt-check" class="text-primary" style="width: 18px; height: 18px;"></i>
                    </h5>

                    <!-- Selected Items Preview List -->
                    <div class="mb-3 pb-3 border-bottom">
                        <div class="fw-bold text-dark small mb-2 d-flex justify-content-between">
                            <span>Sản phẩm thanh toán</span>
                            <span class="text-secondary">{{ $selectedItems->count() }} mặt hàng</span>
                        </div>
                        <div class="d-flex flex-column gap-2" style="max-height: 220px; overflow-y: auto;">
                            @foreach ($selectedItems as $item)
                                @php
                                    $itemImg = $item->variant && $item->variant->image ? $item->variant->image : $item->product->image;
                                @endphp
                                <div class="d-flex align-items-center gap-2">
                                    <div class="rounded-2 border overflow-hidden flex-shrink-0" style="width: 44px; height: 44px; background: var(--bg-surface-subtle);">
                                        @if ($itemImg)
                                            <img src="{{ $itemImg }}" alt="" class="w-100 h-100 object-fit-cover">
                                        @else
                                            <div class="w-100 h-100 d-flex align-items-center justify-content-center">
                                                <i data-lucide="shopping-bag" style="width: 18px; height: 18px;" class="text-secondary"></i>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="min-w-0 flex-grow-1">
                                        <div class="fw-bold text-dark text-truncate small" style="font-size: 0.8rem;">{{ $item->product->name }}</div>
                                        @if ($item->variant)
                                            <div class="text-primary font-monospace" style="font-size: 0.72rem;">{{ $item->variant->variant_title }} &times; {{ $item->quantity }}</div>
                                        @else
                                            <div class="text-secondary" style="font-size: 0.72rem;">SL: {{ $item->quantity }}</div>
                                        @endif
                                    </div>
                                    <div class="text-end fw-bold text-primary small" style="font-size: 0.82rem;">
                                        {{ $item->formatted_subtotal }}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Aurelia Voucher Section (Shopee Style) -->
                    <div class="p-3 rounded-3 mb-3" style="background: var(--bg-surface-subtle); border: 1px dashed var(--brand-500);">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <div class="d-flex align-items-center gap-2">
                                <div class="rounded-2 d-flex align-items-center justify-content-center" style="width: 28px; height: 28px; background: #fff3e0; color: #e65100;">
                                    <i data-lucide="ticket" style="width: 16px; height: 16px;"></i>
                                </div>
                                <span class="fw-bold text-dark small">Aurelia Voucher</span>
                            </div>
                            <button type="button" class="btn btn-sm btn-link text-primary p-0 text-decoration-none fw-semibold small d-flex align-items-center gap-1" onclick="openVoucherModal()">
                                <span>Chọn mã ưu đãi</span>
                                <i data-lucide="chevron-right" style="width: 14px; height: 14px;"></i>
                            </button>
                        </div>

                        <!-- Quick input field -->
                        <div class="input-group input-group-sm mb-1" id="voucherQuickInputContainer">
                            <input type="text" id="quickVoucherInput" class="form-control text-uppercase font-monospace" placeholder="Nhập mã ưu đãi..." style="letter-spacing: 0.05em;">
                            <button type="button" class="btn btn-dark fw-bold px-3" onclick="applyVoucherFromInput()">
                                Áp Dụng
                            </button>
                        </div>

                        <!-- Applied voucher tag banner -->
                        <div id="appliedVoucherBanner" class="p-2.5 rounded-2 d-none mt-2" style="background: #e8f5e9; border: 1px solid #c8e6c9;">
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center gap-2 min-w-0">
                                    <span class="badge bg-success font-monospace" id="appliedVoucherCodeBadge">AURELIA20</span>
                                    <span class="text-success fw-bold small text-truncate" id="appliedVoucherText">-15.000 ₫</span>
                                </div>
                                <button type="button" class="btn btn-sm btn-link text-danger p-0 text-decoration-none" onclick="removeVoucher()" title="Gỡ bỏ mã">
                                    <i data-lucide="x" style="width: 16px; height: 16px;"></i>
                                </button>
                            </div>
                        </div>

                        <div id="voucherErrorMessage" class="text-danger small mt-1 d-none" style="font-size: 0.76rem;"></div>
                    </div>

                    <!-- Hidden voucher code field for backend order processing -->
                    <input type="hidden" name="voucher_code" id="hiddenVoucherCode" value="">

                    <div class="d-flex flex-column gap-2.5 mb-3 text-secondary small">
                        <div class="d-flex justify-content-between">
                            <span>Tổng tiền hàng ({{ $summary['total_quantity'] }} chiếc):</span>
                            <span class="fw-bold text-dark">{{ $summary['formatted_total_amount'] }}</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-start">
                            <span>Phí vận chuyển:</span>
                            <div class="text-end">
                                <span class="fw-bold text-dark" id="displayShippingFee">
                                    {{ $shippingFee > 0 ? number_format($shippingFee, 0, ',', '.') . ' ₫' : 'Miễn phí' }}
                                </span>
                                <div id="shippingLeadtimeDisplay" class="text-success small fw-medium" style="font-size: 0.72rem;"></div>
                            </div>
                        </div>
                        <div class="d-flex justify-content-between text-success {{ $summary['total_amount'] >= 500000 ? '' : 'd-none' }}" id="freeshipDiscountRow">
                            <span class="d-inline-flex align-items-center gap-1">
                                <i data-lucide="sparkles" style="width: 14px; height: 14px;"></i>
                                <span>Ưu đãi Freeship:</span>
                            </span>
                            <span id="freeshipDiscountAmount">-30.000 ₫</span>
                        </div>
                        <!-- Dynamic Voucher Discount Row -->
                        <div class="d-flex justify-content-between text-success d-none" id="voucherDiscountRow">
                            <span class="d-inline-flex align-items-center gap-1">
                                <i data-lucide="ticket" style="width: 14px; height: 14px;"></i>
                                <span>Giảm giá Voucher (<strong id="summaryVoucherCode" class="font-monospace"></strong>):</span>
                            </span>
                            <span class="fw-bold" id="summaryVoucherDiscount">-0 ₫</span>
                        </div>
                    </div>

                    <div class="p-3 rounded-3 mb-4" style="background: var(--bg-surface-subtle); border: 1px solid var(--border-default);">
                        <div class="d-flex justify-content-between align-items-baseline mb-1">
                            <span class="fw-bold text-dark">Tổng cộng:</span>
                            <span class="fw-extrabold text-primary fs-3" id="displayGrandTotal">
                                {{ number_format($grandTotal, 0, ',', '.') }} ₫
                            </span>
                        </div>
                        <div class="text-secondary" style="font-size: 0.75rem;">
                            Đã bao gồm thuế và chi phí giao nhận
                        </div>
                    </div>

                    <!-- Place Order Button -->
                    <button type="submit" id="placeOrderBtn" class="btn-brand-primary w-100 py-3 justify-content-center fw-bold fs-6 shadow-sm">
                        <i data-lucide="check-circle" style="width: 18px; height: 18px; margin-right: 0.45rem;"></i>
                        <span>Xác Nhận Đặt Hàng</span>
                    </button>

                    <div class="mt-3 text-center text-secondary small" style="font-size: 0.75rem; line-height: 1.5;">
                        Nhấn "Xác Nhận Đặt Hàng" đồng nghĩa với việc bạn đồng ý tuân theo <a href="#" class="text-primary text-decoration-none">Điều khoản dịch vụ</a> của Aurelia Luxury.
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- ========================================================================= -->
<!-- SHOPEE-STYLE ADDRESS SELECTION MODAL -->
<!-- ========================================================================= -->
@auth
<div class="modal fade" id="addressSelectModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" style="max-width: 600px;">
        <div class="modal-content modal-content-modern border-0">
            <div class="modal-header border-bottom p-3.5">
                <div class="d-flex align-items-center gap-2">
                    <i data-lucide="map-pin" class="text-primary" style="width: 18px; height: 18px;"></i>
                    <h5 class="modal-title fw-bold text-dark fs-6">Địa Chỉ Của Tôi</h5>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body p-4" style="max-height: 60vh; overflow-y: auto;">
                <div class="d-flex flex-column gap-3" id="addressListContainer">
                    @foreach ($addresses as $addr)
                        <label class="address-item-card p-3 rounded-3 border d-flex gap-3 cursor-pointer position-relative {{ $defaultAddress && $defaultAddress->id === $addr->id ? 'border-primary bg-primary-subtle bg-opacity-10' : '' }}" id="addr-card-{{ $addr->id }}">
                            <input 
                                type="radio" 
                                name="selected_address_radio" 
                                value="{{ $addr->id }}" 
                                class="form-check-input mt-1" 
                                {{ $defaultAddress && $defaultAddress->id === $addr->id ? 'checked' : '' }}
                                onchange="selectAddressItem({{ json_encode($addr) }})"
                            >
                            <div class="flex-grow-1 min-w-0">
                                <div class="d-flex flex-wrap align-items-center gap-2 mb-1">
                                    <span class="fw-bold text-dark">{{ $addr->recipient_name }}</span>
                                    <span class="text-secondary small">| {{ $addr->phone }}</span>
                                    <span class="badge bg-secondary-subtle text-secondary" style="font-size: 0.68rem;">{{ $addr->type_label }}</span>
                                    @if ($addr->is_default)
                                        <span class="badge bg-danger-subtle text-danger" style="font-size: 0.68rem;">Mặc định</span>
                                    @endif
                                </div>
                                <div class="text-secondary small line-clamp-2">
                                    {{ $addr->full_address }}
                                </div>
                            </div>
                        </label>
                    @endforeach
                </div>
            </div>

            <div class="modal-footer border-top p-3 bg-light-subtle d-flex justify-content-between">
                <button type="button" class="btn btn-outline-primary d-inline-flex align-items-center gap-1.5" onclick="openNewAddressModal()">
                    <i data-lucide="plus" style="width: 16px; height: 16px;"></i>
                    <span>+ Thêm Địa Chỉ Mới</span>
                </button>
                <button type="button" class="btn-brand-primary px-4 py-2" data-bs-dismiss="modal">
                    Hoàn thành
                </button>
            </div>
        </div>
    </div>
</div>
@endauth

<!-- ========================================================================= -->
<!-- ADD NEW ADDRESS MODAL (AJAX Cascading Vietnam Administrative Selector) -->
<!-- ========================================================================= -->
<div class="modal fade" id="addNewAddressModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 540px;">
        <div class="modal-content modal-content-modern border-0">
            <div class="modal-header border-bottom p-3.5">
                <div class="d-flex align-items-center gap-2">
                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; background: var(--brand-50); color: var(--brand-600);">
                        <i data-lucide="map-pin" style="width: 18px; height: 18px;"></i>
                    </div>
                    <h5 class="modal-title fw-bold text-dark fs-6">Thêm Địa Chỉ Nhận Hàng Mới</h5>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form id="ajaxAddAddressForm" onsubmit="submitAjaxAddress(event)">
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label-modern small fw-semibold">Họ và tên người nhận *</label>
                            <input type="text" id="newRecipientName" name="recipient_name" class="form-control form-control-modern" placeholder="Ví dụ: Nguyễn Văn A" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label-modern small fw-semibold">Số điện thoại *</label>
                            <input type="tel" id="newPhone" name="phone" class="form-control form-control-modern" placeholder="0988123456" required>
                        </div>

                        <!-- Vietnam Administrative Cascading Selects -->
                        <div class="col-12">
                            <label class="form-label-modern small fw-semibold">Tỉnh / Thành phố *</label>
                            <select id="newProvince" name="province" class="form-select form-select-modern" required>
                                <option value="">-- Chọn Tỉnh / Thành phố --</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label-modern small fw-semibold">Quận / Huyện *</label>
                            <select id="newDistrict" name="district" class="form-select form-select-modern" required disabled>
                                <option value="">-- Chọn Quận / Huyện --</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label-modern small fw-semibold">Phường / Xã *</label>
                            <select id="newWard" name="ward" class="form-select form-select-modern" required disabled>
                                <option value="">-- Chọn Phường / Xã --</option>
                            </select>
                        </div>

                        <div class="col-12">
                            <label class="form-label-modern small fw-semibold">Địa chỉ cụ thể (Số nhà, tên đường, tòa nhà...) *</label>
                            <input type="text" id="newSpecific" name="specific_address" class="form-control form-control-modern" placeholder="Ví dụ: Số 25 Ngõ 12 Đường Cầu Giấy" required>
                        </div>

                        <div class="col-12">
                            <label class="form-label-modern small fw-semibold">Loại địa chỉ</label>
                            <div class="d-flex gap-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="modal_address_type" id="typeHome" value="home" checked>
                                    <label class="form-check-label small" for="typeHome">Nhà riêng</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="modal_address_type" id="typeOffice" value="office">
                                    <label class="form-check-label small" for="typeOffice">Văn phòng</label>
                                </div>
                            </div>
                        </div>

                        @auth
                            <div class="col-12">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="is_default" id="newIsDefault" value="1" checked>
                                    <label class="form-check-label small fw-medium" for="newIsDefault">
                                        Đặt làm địa chỉ mặc định
                                    </label>
                                </div>
                            </div>
                        @endauth
                    </div>

                    <div id="newAddressErrorAlert" class="alert alert-danger py-2 px-3 small d-none mt-3 mb-0"></div>
                </div>

                <div class="modal-footer border-top p-3 bg-light-subtle">
                    <button type="button" class="btn btn-surface px-3 py-2" data-bs-dismiss="modal">Trở lại</button>
                    <button type="submit" id="newAddressSubmitBtn" class="btn-brand-primary px-4 py-2">
                        <span>Lưu & Sử Dụng Địa Chỉ Này</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
<!-- ========================================================================= -->
<!-- SHOPEE-STYLE VOUCHER SELECTION MODAL -->
<!-- ========================================================================= -->
<div class="modal fade" id="voucherSelectModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg" style="max-width: 620px;">
        <div class="modal-content modal-content-modern border-0 shadow-lg">
            <div class="modal-header border-bottom p-3.5 bg-white">
                <div class="d-flex align-items-center gap-2">
                    <div class="p-1.5 rounded-2 bg-warning-subtle text-warning">
                        <i data-lucide="ticket" style="width: 22px; height: 22px;"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold text-dark fs-6 mb-0">Chọn Aurelia Voucher</h5>
                        <div class="text-secondary" style="font-size: 0.75rem;">Chọn 1 mã giảm giá để nhận ưu đãi tốt nhất cho đơn hàng</div>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body p-3.5 bg-light-subtle">
                <!-- Manual Code Input Bar -->
                <div class="card p-3 mb-3 border shadow-sm rounded-3 bg-white">
                    <label class="form-label text-dark fw-bold small mb-1.5 d-flex align-items-center gap-1">
                        <i data-lucide="tag" style="width: 14px; height: 14px;" class="text-primary"></i>
                        <span>Nhập mã ưu đãi của bạn</span>
                    </label>
                    <div class="input-group">
                        <input type="text" id="modalVoucherInput" class="form-control font-monospace text-uppercase" placeholder="VD: AURELIA20, FREESHIP, MOMO50K...">
                        <button type="button" class="btn btn-dark fw-bold px-3" onclick="applyModalVoucherInput()">
                            Áp Dụng
                        </button>
                    </div>
                    <div id="modalVoucherInputError" class="text-danger small mt-1.5 d-none" style="font-size: 0.78rem;"></div>
                </div>

                <!-- Section 1: Eligible Vouchers -->
                <div class="mb-4">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <h6 class="fw-bold text-dark small mb-0 text-uppercase d-flex align-items-center gap-1.5" style="letter-spacing: 0.05em;">
                            <i data-lucide="check-circle-2" class="text-success" style="width: 16px; height: 16px;"></i>
                            <span>Mã Giảm Giá Khả Dụng</span>
                        </h6>
                        <span class="badge bg-success-subtle text-success small fw-bold" id="eligibleVouchersCountBadge">0 mã</span>
                    </div>
                    <div class="d-flex flex-column gap-2.5" id="eligibleVouchersList">
                        <div class="text-center py-4 text-secondary small">
                            <span class="spinner-border spinner-border-sm text-primary"></span>
                            <span class="ms-1">Đang tải danh sách voucher...</span>
                        </div>
                    </div>
                </div>

                <!-- Section 2: Ineligible Vouchers -->
                <div>
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <h6 class="fw-bold text-secondary small mb-0 text-uppercase d-flex align-items-center gap-1.5" style="letter-spacing: 0.05em;">
                            <i data-lucide="alert-circle" class="text-secondary" style="width: 16px; height: 16px;"></i>
                            <span>Chưa Đủ Điều Kiện Áp Dụng</span>
                        </h6>
                        <span class="badge bg-secondary-subtle text-secondary small" id="ineligibleVouchersCountBadge">0 mã</span>
                    </div>
                    <div class="d-flex flex-column gap-2.5" id="ineligibleVouchersList">
                        <!-- Populated dynamically -->
                    </div>
                </div>
            </div>

            <div class="modal-footer border-top p-3 d-flex justify-content-between align-items-center bg-white">
                <div class="text-secondary small">
                    Đang chọn: <strong class="text-primary font-monospace fs-6" id="modalSelectedVoucherCode">Chưa chọn mã nào</strong>
                </div>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-surface px-3" data-bs-dismiss="modal">Đóng</button>
                    <button type="button" class="btn btn-brand-primary px-4 fw-bold shadow-sm" onclick="confirmModalVoucherSelection()">
                        Đồng Ý Áp Dụng
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Shopee Style Voucher Ticket Styles */
.shopee-ticket-card {
    display: flex;
    background: #ffffff;
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    overflow: hidden;
    position: relative;
    box-shadow: 0 1px 3px rgba(0,0,0,0.04);
    transition: all 0.2s ease;
    cursor: pointer;
}
.shopee-ticket-card:hover {
    border-color: #d4af37;
    box-shadow: 0 3px 10px rgba(212, 175, 55, 0.15);
}
.shopee-ticket-card.selected {
    border-color: #d4af37;
    background: #fffdf5;
}
.shopee-ticket-left {
    width: 115px;
    min-width: 115px;
    background: linear-gradient(135deg, #2b2b2b, #1a1a1a);
    color: #ffffff;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 12px 8px;
    text-align: center;
    position: relative;
    border-right: 1px dashed #d4af37;
}
.shopee-ticket-left.shipping-ticket {
    background: linear-gradient(135deg, #00897b, #004d40);
    border-right-color: #80cbc4;
}
.shopee-ticket-right {
    flex: 1;
    padding: 12px 14px;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}
.shopee-ticket-card.ineligible {
    opacity: 0.65;
    background: #fdfdfd;
    cursor: not-allowed;
}
.shopee-ticket-card.ineligible .shopee-ticket-left {
    background: #757575;
    border-right-color: #bdbdbd;
}
.shopee-ticket-card.ineligible:hover {
    border-color: #e0e0e0;
    box-shadow: none;
}
</style>
@endsection

@section('scripts')
<script>
    let addressSelectModalInstance = null;
    let addNewAddressModalInstance = null;
    let voucherSelectModalInstance = null;

    const subtotalAmount = {{ (float) $summary['total_amount'] }};
    const selectedItemIds = [{{ $selectedItems->pluck('id')->implode(',') }}];
    let currentShippingFee = {{ (float) $shippingFee }};
    let currentShippingMethod = 'standard';
    let currentPaymentMethod = 'cod';
    let currentLocationData = {
        province: '{{ $defaultAddress->province ?? "" }}',
        district: '{{ $defaultAddress->district ?? "" }}',
        ward: '{{ $defaultAddress->ward ?? "" }}',
        district_id: null,
        ward_code: null
    };
    let currentVoucher = null;
    let currentVoucherDiscount = 0;
    let tempSelectedVoucher = null;
    let availableVouchersData = null;

    function formatCurrency(amount) {
        return new Intl.NumberFormat('vi-VN').format(Math.max(0, amount)) + ' ₫';
    }

    function recalculateGrandTotal() {
        const finalTotal = Math.max(0, subtotalAmount + currentShippingFee - currentVoucherDiscount);
        const grandTotalEl = document.getElementById('displayGrandTotal');
        if (grandTotalEl) {
            grandTotalEl.textContent = formatCurrency(finalTotal);
        }
    }

    /**
     * Cascading Location Selects using GHN API with local fallback
     */
    async function initGhnCascadingSelects(provId, distId, wardId, options = {}) {
        const provEl = document.getElementById(provId);
        const distEl = document.getElementById(distId);
        const wardEl = document.getElementById(wardId);
        if (!provEl || !distEl || !wardEl) return;

        try {
            provEl.innerHTML = '<option value="">Đang tải danh sách Tỉnh/Thành...</option>';
            const res = await fetch('/api/shipping/provinces');
            const json = await res.json();

            if (json.success && json.data && json.data.length > 0) {
                provEl.innerHTML = '<option value="">-- Chọn Tỉnh / Thành phố --</option>';
                json.data.forEach(p => {
                    const opt = document.createElement('option');
                    opt.value = p.ProvinceName;
                    opt.dataset.id = p.ProvinceID;
                    opt.textContent = p.ProvinceName;
                    provEl.appendChild(opt);
                });
            } else {
                fallbackLocalProvinces(provEl);
            }
        } catch (e) {
            fallbackLocalProvinces(provEl);
        }

        provEl.onchange = async function () {
            const selectedOpt = provEl.options[provEl.selectedIndex];
            const pId = selectedOpt?.dataset?.id;
            distEl.innerHTML = '<option value="">-- Chọn Quận / Huyện --</option>';
            distEl.disabled = true;
            wardEl.innerHTML = '<option value="">-- Chọn Phường / Xã --</option>';
            wardEl.disabled = true;

            if (!pId) {
                if (typeof VN_LOCATIONS_DATA !== 'undefined') {
                    const found = VN_LOCATIONS_DATA.find(p => p.name === provEl.value);
                    if (found) {
                        found.districts.forEach(d => {
                            const opt = document.createElement('option');
                            opt.value = d.name;
                            opt.textContent = d.name;
                            distEl.appendChild(opt);
                        });
                        distEl.disabled = false;
                    }
                }
                if (options.onChange) options.onChange();
                return;
            }

            try {
                distEl.innerHTML = '<option value="">Đang tải Quận / Huyện...</option>';
                const res = await fetch(`/api/shipping/districts?province_id=${pId}`);
                const json = await res.json();
                distEl.innerHTML = '<option value="">-- Chọn Quận / Huyện --</option>';
                if (json.success && json.data && json.data.length > 0) {
                    json.data.forEach(d => {
                        const opt = document.createElement('option');
                        opt.value = d.DistrictName;
                        opt.dataset.id = d.DistrictID;
                        opt.textContent = d.DistrictName;
                        distEl.appendChild(opt);
                    });
                    distEl.disabled = false;
                }
            } catch (err) {
                distEl.disabled = false;
            }

            if (options.onChange) options.onChange();
        };

        distEl.onchange = async function () {
            const selectedOpt = distEl.options[distEl.selectedIndex];
            const dId = selectedOpt?.dataset?.id;
            wardEl.innerHTML = '<option value="">-- Chọn Phường / Xã --</option>';
            wardEl.disabled = true;

            if (!dId) {
                if (typeof VN_LOCATIONS_DATA !== 'undefined') {
                    const provFound = VN_LOCATIONS_DATA.find(p => p.name === provEl.value);
                    const distFound = provFound?.districts?.find(d => d.name === distEl.value);
                    if (distFound) {
                        distFound.wards.forEach(w => {
                            const opt = document.createElement('option');
                            opt.value = w;
                            opt.textContent = w;
                            wardEl.appendChild(opt);
                        });
                        wardEl.disabled = false;
                    }
                }
                if (options.onChange) options.onChange();
                return;
            }

            try {
                wardEl.innerHTML = '<option value="">Đang tải Phường / Xã...</option>';
                const res = await fetch(`/api/shipping/wards?district_id=${dId}`);
                const json = await res.json();
                wardEl.innerHTML = '<option value="">-- Chọn Phường / Xã --</option>';
                if (json.success && json.data && json.data.length > 0) {
                    json.data.forEach(w => {
                        const opt = document.createElement('option');
                        opt.value = w.WardName;
                        opt.dataset.code = w.WardCode;
                        opt.textContent = w.WardName;
                        wardEl.appendChild(opt);
                    });
                    wardEl.disabled = false;
                }
            } catch (err) {
                wardEl.disabled = false;
            }

            if (options.onChange) options.onChange();
        };

        wardEl.onchange = function () {
            if (options.onChange) options.onChange();
        };
    }

    function fallbackLocalProvinces(selectEl) {
        selectEl.innerHTML = '<option value="">-- Chọn Tỉnh / Thành phố --</option>';
        if (typeof VN_LOCATIONS_DATA !== 'undefined') {
            VN_LOCATIONS_DATA.forEach(p => {
                const opt = document.createElement('option');
                opt.value = p.name;
                opt.textContent = p.name;
                selectEl.appendChild(opt);
            });
        }
    }

    /**
     * Calculate GHN shipping fee dynamically
     */
    async function calculateRealShippingFee(locData) {
        if (!locData || (!locData.district && !locData.district_id)) {
            return;
        }

        const feeEl = document.getElementById('displayShippingFee');
        if (feeEl) {
            feeEl.innerHTML = '<span class="spinner-border spinner-border-sm text-primary"></span> <span class="text-secondary small">Đang tính cước GHN...</span>';
        }

        try {
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            const res = await fetch('/api/shipping/calculate-fee', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({
                    province: locData.province,
                    district: locData.district,
                    ward: locData.ward,
                    district_id: locData.district_id,
                    ward_code: locData.ward_code,
                    service_type_id: currentShippingMethod === 'express' ? 1 : 2,
                    items: selectedItemIds
                })
            });

            const data = await res.json();
            if (data.success) {
                currentShippingFee = data.shipping_fee;
                
                // Update hidden inputs
                const hiddenFee = document.getElementById('hiddenShippingFee');
                if (hiddenFee) hiddenFee.value = currentShippingFee;

                const hiddenDist = document.getElementById('hiddenToDistrictId');
                if (hiddenDist && data.district_id) hiddenDist.value = data.district_id;

                const hiddenWard = document.getElementById('hiddenToWardCode');
                if (hiddenWard && data.ward_code) hiddenWard.value = data.ward_code;

                const hiddenExp = document.getElementById('hiddenExpectedDeliveryAt');
                if (hiddenExp && data.leadtime_date) hiddenExp.value = data.leadtime_date;

                // Update summary displays
                if (feeEl) {
                    feeEl.textContent = data.formatted_shipping_fee;
                }

                // Update grand total with voucher discount factored in
                recalculateGrandTotal();

                // If currently applied voucher is a shipping discount, recalculate it
                if (currentVoucher && currentVoucher.discount_type === 'shipping_discount') {
                    applyVoucherAjax(currentVoucher.code);
                }

                // Update leadtime
                const leadtimeEl = document.getElementById('shippingLeadtimeDisplay');
                const standardLeadEl = document.getElementById('standardLeadtimeText');
                if (data.leadtime_text) {
                    if (leadtimeEl) leadtimeEl.textContent = data.leadtime_text;
                    if (standardLeadEl && currentShippingMethod === 'standard') standardLeadEl.textContent = data.leadtime_text;
                }

                // Update freeship badge row
                const freeshipRow = document.getElementById('freeshipDiscountRow');
                const freeshipAmount = document.getElementById('freeshipDiscountAmount');
                if (freeshipRow) {
                    if (data.is_freeship) {
                        freeshipRow.classList.remove('d-none');
                        if (freeshipAmount) freeshipAmount.textContent = '-' + data.formatted_original_fee;
                    } else {
                        freeshipRow.classList.add('d-none');
                    }
                }

                // Update shipping method card display
                const stdDisplay = document.getElementById('standardShippingFeeDisplay');
                if (stdDisplay) {
                    stdDisplay.textContent = (subtotalAmount >= 500000) ? 'Miễn phí' : data.formatted_original_fee;
                }
            }
        } catch (e) {
            console.warn('Lỗi tính phí GHN:', e);
            if (feeEl) feeEl.textContent = formatCurrency(currentShippingFee);
        }
    }

    function updateGuestShippingAddress() {
        const provEl = document.getElementById('guestProvinceSelect');
        const distEl = document.getElementById('guestDistrictSelect');
        const wardEl = document.getElementById('guestWardSelect');
        const spec = document.getElementById('guestSpecific')?.value || '';

        const prov = provEl?.value || '';
        const dist = distEl?.value || '';
        const ward = wardEl?.value || '';

        const distOpt = distEl?.options[distEl.selectedIndex];
        const wardOpt = wardEl?.options[wardEl.selectedIndex];

        currentLocationData = {
            province: prov,
            district: dist,
            ward: ward,
            district_id: distOpt?.dataset?.id || null,
            ward_code: wardOpt?.dataset?.code || null
        };

        const full = [spec, ward, dist, prov].filter(Boolean).join(', ');
        const hiddenAddr = document.getElementById('hiddenShippingAddress');
        if (hiddenAddr) hiddenAddr.value = full;

        const name = document.getElementById('guestRecipientName')?.value || '';
        const phone = document.getElementById('guestPhone')?.value || '';
        
        const hiddenName = document.getElementById('hiddenRecipientName');
        if (hiddenName) hiddenName.value = name;
        const hiddenPhone = document.getElementById('hiddenPhone');
        if (hiddenPhone) hiddenPhone.value = phone;

        // Auto trigger GHN fee calculation if district is selected
        if (dist) {
            calculateRealShippingFee(currentLocationData);
        }
    }

    function onShippingMethodChanged(method) {
        currentShippingMethod = method;

        // Toggle card active states
        const stdCard = document.getElementById('labelStandardShipping');
        const expCard = document.getElementById('labelExpressShipping');
        if (method === 'standard') {
            stdCard?.classList.add('active');
            expCard?.classList.remove('active');
        } else {
            expCard?.classList.add('active');
            stdCard?.classList.remove('active');
        }

        // Re-calculate shipping fee with chosen method
        if (currentLocationData.district || currentLocationData.district_id) {
            calculateRealShippingFee(currentLocationData);
        } else {
            const fee = (method === 'express') ? 50000 : (subtotalAmount >= 500000 ? 0 : 30000);
            currentShippingFee = fee;
            document.getElementById('hiddenShippingFee').value = fee;
            document.getElementById('displayShippingFee').textContent = fee > 0 ? formatCurrency(fee) : 'Miễn phí';
            recalculateGrandTotal();
            if (currentVoucher && currentVoucher.discount_type === 'shipping_discount') {
                applyVoucherAjax(currentVoucher.code);
            }
        }
    }

    function openAddressModal() {
        const modalEl = document.getElementById('addressSelectModal');
        if (modalEl) {
            if (!addressSelectModalInstance) {
                addressSelectModalInstance = new bootstrap.Modal(modalEl);
            }
            addressSelectModalInstance.show();
            setTimeout(() => { if (typeof lucide !== 'undefined') lucide.createIcons(); }, 100);
        }
    }

    function openNewAddressModal() {
        if (addressSelectModalInstance) addressSelectModalInstance.hide();
        const modalEl = document.getElementById('addNewAddressModal');
        if (!addNewAddressModalInstance) {
            addNewAddressModalInstance = new bootstrap.Modal(modalEl);
        }

        // Initialize cascading selects for modal with GHN API
        initGhnCascadingSelects('newProvince', 'newDistrict', 'newWard');

        addNewAddressModalInstance.show();
        setTimeout(() => { if (typeof lucide !== 'undefined') lucide.createIcons(); }, 100);
    }

    function selectAddressItem(addr) {
        document.getElementById('dispRecipientName').textContent = addr.recipient_name;
        document.getElementById('dispPhone').textContent = `(${addr.phone})`;
        
        const fullAddr = [addr.specific_address, addr.ward, addr.district, addr.province].filter(Boolean).join(', ');
        document.getElementById('dispFullAddress').textContent = fullAddr;
        
        document.getElementById('hiddenRecipientName').value = addr.recipient_name;
        document.getElementById('hiddenPhone').value = addr.phone;
        document.getElementById('hiddenShippingAddress').value = fullAddr;

        const typeBadge = document.getElementById('dispTypeBadge');
        if (typeBadge) {
            typeBadge.textContent = addr.address_type === 'office' ? 'Văn phòng' : 'Nhà riêng';
        }

        // Highlight card
        document.querySelectorAll('.address-item-card').forEach(c => {
            c.classList.remove('border-primary', 'bg-primary-subtle', 'bg-opacity-10');
        });
        const card = document.getElementById(`addr-card-${addr.id}`);
        if (card) {
            card.classList.add('border-primary', 'bg-primary-subtle', 'bg-opacity-10');
        }

        // Update current location and trigger GHN fee calculation
        currentLocationData = {
            province: addr.province,
            district: addr.district,
            ward: addr.ward,
            district_id: addr.district_id || null,
            ward_code: addr.ward_code || null
        };
        calculateRealShippingFee(currentLocationData);
    }

    async function submitAjaxAddress(event) {
        event.preventDefault();
        const submitBtn = document.getElementById('newAddressSubmitBtn');
        submitBtn.disabled = true;

        const recipientName = document.getElementById('newRecipientName').value;
        const phone = document.getElementById('newPhone').value;
        const provEl = document.getElementById('newProvince');
        const distEl = document.getElementById('newDistrict');
        const wardEl = document.getElementById('newWard');

        const province = provEl.value;
        const district = distEl.value;
        const ward = wardEl.value;
        const specificAddress = document.getElementById('newSpecific').value;
        const addressType = document.querySelector('input[name="modal_address_type"]:checked')?.value || 'home';
        const isDefault = document.getElementById('newIsDefault') ? (document.getElementById('newIsDefault').checked ? 1 : 0) : 1;

        if (!province || !district || !ward) {
            alert('Vui lòng chọn đầy đủ Tỉnh/Thành phố, Quận/Huyện và Phường/Xã.');
            submitBtn.disabled = false;
            return;
        }

        const provinceId = provEl.options[provEl.selectedIndex]?.dataset?.id || null;
        const districtId = distEl.options[distEl.selectedIndex]?.dataset?.id || null;
        const wardCode = wardEl.options[wardEl.selectedIndex]?.dataset?.code || null;

        const data = {
            recipient_name: recipientName,
            phone: phone,
            province: province,
            province_id: provinceId,
            district: district,
            district_id: districtId,
            ward: ward,
            ward_code: wardCode,
            specific_address: specificAddress,
            address_type: addressType,
            is_default: isDefault
        };

        const isAuth = {{ Auth::check() ? 'true' : 'false' }};

        if (isAuth) {
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            try {
                const res = await fetch('{{ route("addresses.store") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify(data)
                });

                const result = await res.json();
                if (res.ok && result.success) {
                    if (addNewAddressModalInstance) addNewAddressModalInstance.hide();
                    window.location.reload();
                } else {
                    const errEl = document.getElementById('newAddressErrorAlert');
                    errEl.textContent = result.message || 'Vui lòng kiểm tra lại thông tin.';
                    errEl.classList.remove('d-none');
                }
            } catch (e) {
                alert('Lỗi kết nối. Vui lòng thử lại.');
            } finally {
                submitBtn.disabled = false;
            }
        } else {
            // Guest address handling
            const fullAddress = [specificAddress, ward, district, province].filter(Boolean).join(', ');
            
            document.getElementById('guestDispName').textContent = recipientName;
            document.getElementById('guestDispPhone').textContent = `(${phone})`;
            document.getElementById('guestDispAddress').textContent = fullAddress;

            document.getElementById('hiddenRecipientName').value = recipientName;
            document.getElementById('hiddenPhone').value = phone;
            document.getElementById('hiddenShippingAddress').value = fullAddress;

            // Populate guest inline fields
            document.getElementById('guestRecipientName').value = recipientName;
            document.getElementById('guestPhone').value = phone;
            document.getElementById('guestSpecific').value = specificAddress;

            document.getElementById('guestActiveAddressBox').classList.remove('d-none');
            document.getElementById('guestInlineForm').classList.add('d-none');

            if (addNewAddressModalInstance) addNewAddressModalInstance.hide();
            submitBtn.disabled = false;

            currentLocationData = {
                province: province,
                district: district,
                ward: ward,
                district_id: distEl.options[distEl.selectedIndex]?.dataset?.id || null,
                ward_code: wardEl.options[wardEl.selectedIndex]?.dataset?.code || null
            };
            calculateRealShippingFee(currentLocationData);

            if (window.showToast) window.showToast('Đã lưu địa chỉ nhận hàng!', 'success');
        }
    }

    function togglePaymentDetail(type) {
        currentPaymentMethod = type;
        const bankBox = document.getElementById('bankTransferBox');
        const momoBox = document.getElementById('momoInstructionBox');
        
        if (bankBox) {
            if (type === 'bank' || type === 'bank_transfer') {
                bankBox.classList.remove('d-none');
            } else {
                bankBox.classList.add('d-none');
            }
        }

        if (momoBox) {
            if (type === 'momo') {
                momoBox.classList.remove('d-none');
            } else {
                momoBox.classList.add('d-none');
            }
        }

        // Validate applied voucher compatibility with newly selected payment method
        if (currentVoucher) {
            checkVoucherCompatibilityWithPaymentMethod(type);
        }
    }

    // =========================================================================
    // SHOPEE-STYLE VOUCHER LOGIC & MODAL
    // =========================================================================

    function openVoucherModal() {
        const modalEl = document.getElementById('voucherSelectModal');
        if (modalEl) {
            if (!voucherSelectModalInstance) {
                voucherSelectModalInstance = new bootstrap.Modal(modalEl);
            }
            voucherSelectModalInstance.show();
            loadAvailableVouchers();
            setTimeout(() => { if (typeof lucide !== 'undefined') lucide.createIcons(); }, 150);
        }
    }

    async function loadAvailableVouchers() {
        const eligibleList = document.getElementById('eligibleVouchersList');
        const ineligibleList = document.getElementById('ineligibleVouchersList');
        const eligibleBadge = document.getElementById('eligibleVouchersCountBadge');
        const ineligibleBadge = document.getElementById('ineligibleVouchersCountBadge');

        eligibleList.innerHTML = `
            <div class="text-center py-4 text-secondary small">
                <span class="spinner-border spinner-border-sm text-primary"></span>
                <span class="ms-1">Đang tải danh sách ưu đãi...</span>
            </div>
        `;
        ineligibleList.innerHTML = '';

        try {
            const res = await fetch(`/api/vouchers/available?subtotal=${subtotalAmount}&shipping_fee=${currentShippingFee}&payment_method=${currentPaymentMethod}`);
            const json = await res.json();

            if (json.success) {
                availableVouchersData = json;
                renderVouchersModal(json);
            }
        } catch (e) {
            eligibleList.innerHTML = '<div class="alert alert-danger py-2 small">Không thể tải danh sách voucher. Vui lòng thử lại.</div>';
        }
    }

    function renderVouchersModal(data) {
        const eligibleList = document.getElementById('eligibleVouchersList');
        const ineligibleList = document.getElementById('ineligibleVouchersList');
        const eligibleBadge = document.getElementById('eligibleVouchersCountBadge');
        const ineligibleBadge = document.getElementById('ineligibleVouchersCountBadge');

        const eligible = data.eligible || [];
        const ineligible = data.ineligible || [];

        eligibleBadge.textContent = `${eligible.length} mã khả dụng`;
        ineligibleBadge.textContent = `${ineligible.length} mã`;

        // Render eligible
        if (eligible.length === 0) {
            eligibleList.innerHTML = `
                <div class="p-3 bg-white rounded-3 border text-center text-secondary small">
                    Hiện chưa có mã ưu đãi phù hợp với giỏ hàng hiện tại.
                </div>
            `;
        } else {
            eligibleList.innerHTML = eligible.map(v => {
                const isCurrent = currentVoucher && currentVoucher.code === v.code;
                const isSelected = tempSelectedVoucher ? tempSelectedVoucher.code === v.code : isCurrent;
                const isShipping = v.discount_type === 'shipping_discount';

                let discountBadgeText = v.discount_type === 'percentage' 
                    ? `GIẢM ${v.discount_value}%` 
                    : (isShipping ? 'FREESHIP' : 'GIẢM TIỀN');

                return `
                    <div class="shopee-ticket-card ${isSelected ? 'selected' : ''}" onclick="selectModalVoucher('${v.code}')" id="ticket-${v.code}">
                        <div class="shopee-ticket-left ${isShipping ? 'shipping-ticket' : ''}">
                            <i data-lucide="${isShipping ? 'truck' : 'ticket'}" style="width: 22px; height: 22px;" class="mb-1"></i>
                            <div class="fw-extrabold" style="font-size: 0.72rem; line-height: 1.2;">${discountBadgeText}</div>
                            <div style="font-size: 0.65rem; opacity: 0.85;">AURELIA</div>
                        </div>
                        <div class="shopee-ticket-right">
                            <div class="d-flex justify-content-between align-items-start gap-2">
                                <div class="min-w-0">
                                    <div class="fw-bold text-dark small mb-0.5">${v.name}</div>
                                    <div class="text-secondary small" style="font-size: 0.76rem;">${v.formatted_min_order}</div>
                                </div>
                                <input type="radio" name="modal_voucher_choice" value="${v.code}" class="form-check-input mt-1 flex-shrink-0" ${isSelected ? 'checked' : ''}>
                            </div>
                            <div class="d-flex justify-content-between align-items-center pt-2 mt-2 border-top" style="font-size: 0.72rem;">
                                <span class="text-secondary">${v.expires_at ? 'HSD: ' + v.expires_at : 'Hạn dùng: Dài hạn'}</span>
                                <span class="text-success fw-bold">Tiết kiệm ${v.formatted_discount_amount}</span>
                            </div>
                        </div>
                    </div>
                `;
            }).join('');
        }

        // Render ineligible
        if (ineligible.length === 0) {
            ineligibleList.innerHTML = `
                <div class="p-2.5 bg-white rounded-3 border text-center text-secondary small" style="font-size: 0.78rem;">
                    Không có mã nào trong danh mục này.
                </div>
            `;
        } else {
            ineligibleList.innerHTML = ineligible.map(v => {
                const isShipping = v.discount_type === 'shipping_discount';
                return `
                    <div class="shopee-ticket-card ineligible">
                        <div class="shopee-ticket-left ${isShipping ? 'shipping-ticket' : ''}">
                            <i data-lucide="${isShipping ? 'truck' : 'ticket'}" style="width: 22px; height: 22px;" class="mb-1"></i>
                            <div class="fw-bold" style="font-size: 0.72rem;">${v.discount_type === 'percentage' ? 'GIẢM ' + v.discount_value + '%' : (isShipping ? 'FREESHIP' : 'GIẢM TIỀN')}</div>
                        </div>
                        <div class="shopee-ticket-right">
                            <div class="fw-bold text-dark small">${v.name}</div>
                            <div class="text-secondary small" style="font-size: 0.75rem;">${v.formatted_min_order}</div>
                            <div class="text-danger fw-medium pt-1 mt-1 border-top" style="font-size: 0.74rem;">
                                <i data-lucide="alert-circle" style="width: 12px; height: 12px;" class="inline-block me-1"></i>
                                ${v.reason}
                            </div>
                        </div>
                    </div>
                `;
            }).join('');
        }

        setTimeout(() => { if (typeof lucide !== 'undefined') lucide.createIcons(); }, 100);
    }

    function selectModalVoucher(code) {
        if (!availableVouchersData) return;
        const voucherItem = availableVouchersData.eligible.find(v => v.code === code);
        if (!voucherItem) return;

        tempSelectedVoucher = voucherItem;
        document.getElementById('modalSelectedVoucherCode').textContent = `${voucherItem.code} (${voucherItem.formatted_discount_amount})`;

        document.querySelectorAll('.shopee-ticket-card').forEach(c => c.classList.remove('selected'));
        const activeCard = document.getElementById(`ticket-${code}`);
        if (activeCard) {
            activeCard.classList.add('selected');
            const radio = activeCard.querySelector('input[type="radio"]');
            if (radio) radio.checked = true;
        }
    }

    function confirmModalVoucherSelection() {
        if (!tempSelectedVoucher) {
            alert('Vui lòng chọn một mã giảm giá khả dụng hoặc đóng cửa sổ.');
            return;
        }

        applyVoucherAjax(tempSelectedVoucher.code);
        if (voucherSelectModalInstance) voucherSelectModalInstance.hide();
    }

    function applyModalVoucherInput() {
        const input = document.getElementById('modalVoucherInput');
        const errEl = document.getElementById('modalVoucherInputError');
        const code = (input.value || '').trim();

        if (!code) {
            errEl.textContent = 'Vui lòng nhập mã ưu đãi.';
            errEl.classList.remove('d-none');
            return;
        }
        errEl.classList.add('d-none');

        applyVoucherAjax(code, {
            onSuccess: () => {
                if (voucherSelectModalInstance) voucherSelectModalInstance.hide();
            },
            onError: (msg) => {
                errEl.textContent = msg;
                errEl.classList.remove('d-none');
            }
        });
    }

    function applyVoucherFromInput() {
        const input = document.getElementById('quickVoucherInput');
        const code = (input.value || '').trim();
        if (!code) {
            showVoucherError('Vui lòng nhập mã giảm giá.');
            return;
        }
        applyVoucherAjax(code);
    }

    async function applyVoucherAjax(code, callbacks = {}) {
        hideVoucherError();
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

        try {
            const res = await fetch('/api/vouchers/apply', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({
                    code: code,
                    subtotal: subtotalAmount,
                    shipping_fee: currentShippingFee,
                    payment_method: currentPaymentMethod
                })
            });

            const data = await res.json();

            if (res.ok && data.success) {
                currentVoucher = data.voucher;
                currentVoucherDiscount = data.discount_amount;
                tempSelectedVoucher = data.voucher;

                // Update UI state
                document.getElementById('hiddenVoucherCode').value = data.voucher.code;
                document.getElementById('quickVoucherInput').value = data.voucher.code;

                // Show applied banner
                document.getElementById('appliedVoucherCodeBadge').textContent = data.voucher.code;
                document.getElementById('appliedVoucherText').textContent = data.voucher.formatted_discount;
                document.getElementById('appliedVoucherBanner').classList.remove('d-none');
                document.getElementById('voucherQuickInputContainer').classList.add('d-none');

                // Update summary discount line
                document.getElementById('summaryVoucherCode').textContent = data.voucher.code;
                document.getElementById('summaryVoucherDiscount').textContent = data.voucher.formatted_discount;
                document.getElementById('voucherDiscountRow').classList.remove('d-none');

                // Recalculate grand total
                recalculateGrandTotal();

                if (callbacks.onSuccess) callbacks.onSuccess();
                if (window.showToast) {
                    window.showToast(data.message, 'success');
                }
            } else {
                const msg = data.message || 'Mã giảm giá không hợp lệ hoặc không đủ điều kiện.';
                showVoucherError(msg);
                if (callbacks.onError) callbacks.onError(msg);
            }
        } catch (err) {
            const msg = 'Lỗi kết nối khi áp dụng voucher. Vui lòng thử lại.';
            showVoucherError(msg);
            if (callbacks.onError) callbacks.onError(msg);
        }
    }

    function removeVoucher() {
        currentVoucher = null;
        currentVoucherDiscount = 0;
        tempSelectedVoucher = null;

        document.getElementById('hiddenVoucherCode').value = '';
        document.getElementById('quickVoucherInput').value = '';
        document.getElementById('appliedVoucherBanner').classList.add('d-none');
        document.getElementById('voucherQuickInputContainer').classList.remove('d-none');
        document.getElementById('voucherDiscountRow').classList.add('d-none');
        hideVoucherError();

        recalculateGrandTotal();
        if (window.showToast) window.showToast('Đã gỡ bỏ mã giảm giá.', 'info');
    }

    function showVoucherError(msg) {
        const err = document.getElementById('voucherErrorMessage');
        if (err) {
            err.textContent = msg;
            err.classList.remove('d-none');
        }
    }

    function hideVoucherError() {
        const err = document.getElementById('voucherErrorMessage');
        if (err) {
            err.textContent = '';
            err.classList.add('d-none');
        }
    }

    function checkVoucherCompatibilityWithPaymentMethod(paymentMethod) {
        if (!currentVoucher) return;
        // Re-validate voucher with new payment method
        applyVoucherAjax(currentVoucher.code, {
            onError: (msg) => {
                alert(`Mã giảm giá ${currentVoucher.code} không áp dụng cho hình thức thanh toán vừa chọn (${msg}). Mã giảm giá đã được gỡ bỏ.`);
                removeVoucher();
            }
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        // Init guest cascading selects with GHN API
        const guestProv = document.getElementById('guestProvinceSelect');
        if (guestProv) {
            initGhnCascadingSelects('guestProvinceSelect', 'guestDistrictSelect', 'guestWardSelect', {
                onChange: updateGuestShippingAddress
            });
        }

        // If authenticated user already has a selected address, calculate real shipping fee
        @if (Auth::check() && $defaultAddress)
            calculateRealShippingFee({
                province: '{{ $defaultAddress->province }}',
                district: '{{ $defaultAddress->district }}',
                ward: '{{ $defaultAddress->ward }}'
            });
        @endif
    });
</script>
@endsection
