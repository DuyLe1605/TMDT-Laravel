@extends('layouts.app')

@section('title', 'Chỉnh Sửa Voucher ' . $voucher->code . ' - Admin Portal')

@section('content')
<div class="mb-4">
    <div class="breadcrumb-modern">
        <span>Admin Portal</span>
        <i data-lucide="chevron-right" style="width: 14px; height: 14px;"></i>
        <span>Quản lý E-Commerce</span>
        <i data-lucide="chevron-right" style="width: 14px; height: 14px;"></i>
        <a href="{{ route('admin.vouchers.index') }}" class="text-decoration-none text-secondary">Vouchers</a>
        <i data-lucide="chevron-right" style="width: 14px; height: 14px;"></i>
        <span class="text-primary fw-medium">{{ $voucher->code }}</span>
    </div>

    <div class="d-flex justify-content-between align-items-center mt-2">
        <div>
            <h2 class="fw-bold mb-1 text-dark" style="letter-spacing: -0.03em;">Chỉnh Sửa Voucher: {{ $voucher->code }}</h2>
            <p class="text-secondary mb-0" style="font-size: 0.94rem;">
                Cập nhật thông số kỹ thuật, hạn mức và quy tắc áp dụng mã ưu đãi
            </p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.vouchers.show', $voucher) }}" class="btn btn-outline-primary d-flex align-items-center gap-1">
                <i data-lucide="eye" style="width: 16px; height: 16px;"></i>
                <span>Xem Thống Kê</span>
            </a>
            <a href="{{ route('admin.vouchers.index') }}" class="btn btn-outline-secondary d-flex align-items-center gap-1">
                <i data-lucide="arrow-left" style="width: 16px; height: 16px;"></i>
                <span>Quay lại</span>
            </a>
        </div>
    </div>
</div>

@if ($errors->any())
    <div class="alert alert-danger shadow-sm border-0 mb-4">
        <div class="fw-bold mb-1 d-flex align-items-center gap-2">
            <i data-lucide="alert-circle" style="width: 18px; height: 18px;"></i>
            <span>Vui lòng kiểm tra lại các thông tin sau:</span>
        </div>
        <ul class="mb-0 ps-3 small">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form action="{{ route('admin.vouchers.update', $voucher) }}" method="POST" id="voucherForm">
    @csrf
    @method('PUT')

    <div class="row g-4">
        <!-- Left Column: Form Fields -->
        <div class="col-lg-8">
            <!-- Basic Information Card -->
            <div class="card-modern p-4 mb-4 shadow-sm border">
                <h5 class="fw-bold text-dark mb-3 pb-2 border-bottom d-flex align-items-center gap-2">
                    <i data-lucide="info" class="text-primary" style="width: 18px; height: 18px;"></i>
                    <span>Thông Tin Cơ Bản</span>
                </h5>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label text-dark fw-semibold small required">Mã Voucher (Coupon Code)</label>
                        <input type="text" name="code" id="voucherCodeInput" class="form-control font-monospace fw-bold text-uppercase @error('code') is-invalid @enderror" value="{{ old('code', $voucher->code) }}" required maxlength="50">
                        <div class="form-text text-secondary" style="font-size: 0.75rem;">Chữ in hoa, số và gạch ngang (không dấu cách).</div>
                        @error('code')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label text-dark fw-semibold small required">Tên chương trình ưu đãi</label>
                        <input type="text" name="name" id="voucherNameInput" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $voucher->name) }}" required maxlength="255">
                        @error('name')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12">
                        <label class="form-label text-dark fw-semibold small">Mô tả chi tiết / Điều khoản</label>
                        <textarea name="description" id="voucherDescInput" class="form-control @error('description') is-invalid @enderror" rows="2">{{ old('description', $voucher->description) }}</textarea>
                        @error('description')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Discount Configuration Card -->
            <div class="card-modern p-4 mb-4 shadow-sm border">
                <h5 class="fw-bold text-dark mb-3 pb-2 border-bottom d-flex align-items-center gap-2">
                    <i data-lucide="percent" class="text-primary" style="width: 18px; height: 18px;"></i>
                    <span>Cấu Hình Chiết Khấu</span>
                </h5>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label text-dark fw-semibold small required">Loại chiết khấu</label>
                        <select name="discount_type" id="discountTypeSelect" class="form-select @error('discount_type') is-invalid @enderror" required onchange="handleDiscountTypeChange()">
                            <option value="percentage" {{ old('discount_type', $voucher->discount_type) === 'percentage' ? 'selected' : '' }}>Giảm theo phần trăm (%)</option>
                            <option value="fixed_amount" {{ old('discount_type', $voucher->discount_type) === 'fixed_amount' ? 'selected' : '' }}>Giảm số tiền cố định (₫)</option>
                            <option value="shipping_discount" {{ old('discount_type', $voucher->discount_type) === 'shipping_discount' ? 'selected' : '' }}>Giảm phí vận chuyển Freeship (₫)</option>
                        </select>
                        @error('discount_type')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label text-dark fw-semibold small required" id="discountValueLabel">Mức giảm (%)</label>
                        <div class="input-group">
                            <input type="number" step="any" name="discount_value" id="discountValueInput" class="form-control @error('discount_value') is-invalid @enderror" value="{{ old('discount_value', (float) $voucher->discount_value) }}" required min="0.01">
                            <span class="input-group-text fw-bold bg-light" id="discountValueUnit">%</span>
                        </div>
                        @error('discount_value')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6" id="maxDiscountCol">
                        <label class="form-label text-dark fw-semibold small">Mức giảm tối đa (₫)</label>
                        <div class="input-group">
                            <input type="number" step="any" name="max_discount_amount" id="maxDiscountInput" class="form-control @error('max_discount_amount') is-invalid @enderror" value="{{ old('max_discount_amount', $voucher->max_discount_amount ? (float) $voucher->max_discount_amount : '') }}" min="0">
                            <span class="input-group-text bg-light">₫</span>
                        </div>
                        <div class="form-text text-secondary" style="font-size: 0.75rem;">Bỏ trống nếu không áp dụng mức chặn tối đa.</div>
                        @error('max_discount_amount')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label text-dark fw-semibold small">Giá trị đơn hàng tối thiểu (₫)</label>
                        <div class="input-group">
                            <input type="number" step="any" name="min_order_amount" id="minOrderInput" class="form-control @error('min_order_amount') is-invalid @enderror" value="{{ old('min_order_amount', (float) $voucher->min_order_amount) }}" min="0">
                            <span class="input-group-text bg-light">₫</span>
                        </div>
                        <div class="form-text text-secondary" style="font-size: 0.75rem;">Đơn hàng phải đạt mức này mới được áp dụng.</div>
                        @error('min_order_amount')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Usage Limits & Payment Conditions Card -->
            <div class="card-modern p-4 mb-4 shadow-sm border">
                <h5 class="fw-bold text-dark mb-3 pb-2 border-bottom d-flex align-items-center gap-2">
                    <i data-lucide="shield-check" class="text-primary" style="width: 18px; height: 18px;"></i>
                    <span>Giới Hạn Lượt Dùng & Phương Thức Thanh Toán</span>
                </h5>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label text-dark fw-semibold small">Tổng số lượt dùng toàn hệ thống</label>
                        <input type="number" name="usage_limit" class="form-control @error('usage_limit') is-invalid @enderror" value="{{ old('usage_limit', $voucher->usage_limit) }}" min="1">
                        <div class="form-text text-secondary" style="font-size: 0.75rem;">Đã sử dụng: <strong>{{ $voucher->used_count }}</strong> lượt.</div>
                        @error('usage_limit')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label text-dark fw-semibold small required">Số lượt dùng tối đa / Khách hàng</label>
                        <input type="number" name="usage_limit_per_user" class="form-control @error('usage_limit_per_user') is-invalid @enderror" value="{{ old('usage_limit_per_user', $voucher->usage_limit_per_user) }}" required min="1">
                        @error('usage_limit_per_user')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12">
                        @php
                            $currentPms = old('applicable_payment_methods', $voucher->applicable_payment_methods ?? ['all']);
                        @endphp
                        <label class="form-label text-dark fw-semibold small required">Phương thức thanh toán áp dụng</label>
                        <div class="d-flex flex-wrap gap-3 p-3 bg-light rounded-3 border">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="applicable_payment_methods[]" value="all" id="pm_all" {{ in_array('all', $currentPms) ? 'checked' : '' }} onchange="handlePaymentMethodChange(this)">
                                <label class="form-check-label fw-bold text-dark" for="pm_all">
                                    Tất cả phương thức thanh toán
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input pm-specific" type="checkbox" name="applicable_payment_methods[]" value="cod" id="pm_cod" {{ in_array('cod', $currentPms) ? 'checked' : '' }}>
                                <label class="form-check-label text-dark" for="pm_cod">
                                    Thanh toán khi nhận hàng (COD)
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input pm-specific" type="checkbox" name="applicable_payment_methods[]" value="bank_transfer" id="pm_bank" {{ in_array('bank_transfer', $currentPms) ? 'checked' : '' }}>
                                <label class="form-check-label text-dark" for="pm_bank">
                                    Chuyển khoản ngân hàng (QR Code)
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input pm-specific" type="checkbox" name="applicable_payment_methods[]" value="momo" id="pm_momo" {{ in_array('momo', $currentPms) ? 'checked' : '' }}>
                                <label class="form-check-label text-dark" for="pm_momo">
                                    Ví điện tử MoMo
                                </label>
                            </div>
                        </div>
                        @error('applicable_payment_methods')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column: Settings & Preview -->
        <div class="col-lg-4">
            <!-- Validity & Status Card -->
            <div class="card-modern p-4 mb-4 shadow-sm border">
                <h5 class="fw-bold text-dark mb-3 pb-2 border-bottom d-flex align-items-center gap-2">
                    <i data-lucide="calendar" class="text-primary" style="width: 18px; height: 18px;"></i>
                    <span>Thời Gian & Kích Hoạt</span>
                </h5>

                <div class="mb-3">
                    <label class="form-label text-dark fw-semibold small">Ngày bắt đầu áp dụng</label>
                    <input type="datetime-local" name="starts_at" class="form-control" value="{{ old('starts_at', $voucher->starts_at ? $voucher->starts_at->format('Y-m-d\TH:i') : '') }}">
                </div>

                <div class="mb-3">
                    <label class="form-label text-dark fw-semibold small">Ngày hết hạn (Hạn chót)</label>
                    <input type="datetime-local" name="expires_at" class="form-control" value="{{ old('expires_at', $voucher->expires_at ? $voucher->expires_at->format('Y-m-d\TH:i') : '') }}">
                    <div class="form-text text-secondary" style="font-size: 0.75rem;">Để trống nếu không giới hạn ngày hết hạn.</div>
                </div>

                <div class="p-3 bg-light rounded-3 border">
                    <div class="form-check form-switch mb-0">
                        <input class="form-check-input" type="checkbox" name="is_active" id="isActiveSwitch" value="1" {{ old('is_active', $voucher->is_active) ? 'checked' : '' }}>
                        <label class="form-check-label fw-bold text-dark" for="isActiveSwitch">
                            Kích hoạt sử dụng voucher
                        </label>
                    </div>
                </div>
            </div>

            <!-- Live Coupon Preview Card -->
            <div class="card-modern p-4 mb-4 shadow-sm border bg-light">
                <h6 class="fw-bold text-secondary mb-3 text-uppercase small d-flex align-items-center gap-1" style="letter-spacing: 0.05em;">
                    <i data-lucide="eye" style="width: 15px; height: 15px;"></i>
                    <span>Xem Trước Thẻ Voucher</span>
                </h6>

                <div class="voucher-preview-card p-3 bg-white rounded-3 border position-relative overflow-hidden shadow-sm" style="border-left: 5px solid #d4af37 !important;">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <span class="badge bg-primary text-white font-monospace fw-bold" id="prevCode">{{ $voucher->code }}</span>
                            <span class="badge bg-light text-secondary border small ms-1" id="prevType">{{ $voucher->formatted_discount }}</span>
                        </div>
                        <span class="badge {{ $voucher->status_badge['class'] }} small" id="prevStatus">{{ $voucher->status_badge['label'] }}</span>
                    </div>

                    <h6 class="fw-bold text-dark mb-1" id="prevName">{{ $voucher->name }}</h6>
                    <p class="text-secondary small mb-2" id="prevDesc" style="font-size: 0.8rem;">
                        {{ $voucher->description ?? 'Không có mô tả chi tiết' }}
                    </p>

                    <div class="pt-2 border-top d-flex justify-content-between align-items-center small text-secondary">
                        <span id="prevMinOrder">{{ $voucher->formatted_min_order }}</span>
                        <span class="text-danger fw-semibold" id="prevDiscount">{{ $voucher->formatted_discount }}</span>
                    </div>
                </div>
            </div>

            <!-- Submit Buttons -->
            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-primary py-2.5 fw-bold d-flex align-items-center justify-content-center gap-2">
                    <i data-lucide="save" style="width: 18px; height: 18px;"></i>
                    <span>Cập Nhật Voucher</span>
                </button>
                <a href="{{ route('admin.vouchers.index') }}" class="btn btn-outline-secondary">
                    Hủy bỏ
                </a>
            </div>
        </div>
    </div>
</form>

<script>
function handleDiscountTypeChange() {
    const type = document.getElementById('discountTypeSelect').value;
    const valueLabel = document.getElementById('discountValueLabel');
    const valueUnit = document.getElementById('discountValueUnit');
    const maxDiscountCol = document.getElementById('maxDiscountCol');

    if (type === 'percentage') {
        valueLabel.innerText = 'Mức giảm (%)';
        valueUnit.innerText = '%';
        maxDiscountCol.style.display = 'block';
    } else if (type === 'fixed_amount') {
        valueLabel.innerText = 'Số tiền giảm (₫)';
        valueUnit.innerText = '₫';
        maxDiscountCol.style.display = 'none';
    } else if (type === 'shipping_discount') {
        valueLabel.innerText = 'Mức giảm phí ship (₫)';
        valueUnit.innerText = '₫';
        maxDiscountCol.style.display = 'none';
    }
    updatePreview();
}

function handlePaymentMethodChange(allCheckbox) {
    const specificCheckboxes = document.querySelectorAll('.pm-specific');
    if (allCheckbox.checked) {
        specificCheckboxes.forEach(cb => cb.checked = false);
    }
}

document.querySelectorAll('.pm-specific').forEach(cb => {
    cb.addEventListener('change', function() {
        if (this.checked) {
            document.getElementById('pm_all').checked = false;
        }
    });
});

function updatePreview() {
    const code = document.getElementById('voucherCodeInput').value || 'MÃ VOUCHER';
    const name = document.getElementById('voucherNameInput').value || 'Tên chương trình';
    const desc = document.getElementById('voucherDescInput').value || '';
    const type = document.getElementById('discountTypeSelect').value;
    const val = parseFloat(document.getElementById('discountValueInput').value) || 0;
    const maxVal = parseFloat(document.getElementById('maxDiscountInput').value) || 0;
    const minOrder = parseFloat(document.getElementById('minOrderInput').value) || 0;

    document.getElementById('prevCode').innerText = code.toUpperCase();
    document.getElementById('prevName').innerText = name;
    document.getElementById('prevDesc').innerText = desc;

    if (type === 'percentage') {
        document.getElementById('prevType').innerText = 'Giảm ' + val + '%';
        document.getElementById('prevDiscount').innerText = maxVal > 0 ? 'Tối đa -' + maxVal.toLocaleString('vi-VN') + '₫' : 'Giảm ' + val + '%';
    } else if (type === 'fixed_amount') {
        document.getElementById('prevType').innerText = 'Giảm ' + val.toLocaleString('vi-VN') + '₫';
        document.getElementById('prevDiscount').innerText = '-' + val.toLocaleString('vi-VN') + '₫';
    } else {
        document.getElementById('prevType').innerText = 'Freeship -' + val.toLocaleString('vi-VN') + '₫';
        document.getElementById('prevDiscount').innerText = 'Freeship -' + val.toLocaleString('vi-VN') + '₫';
    }

    document.getElementById('prevMinOrder').innerText = minOrder > 0 ? 'Đơn từ ' + minOrder.toLocaleString('vi-VN') + '₫' : 'Mọi đơn hàng';
}

document.addEventListener('DOMContentLoaded', function() {
    ['voucherCodeInput', 'voucherNameInput', 'voucherDescInput', 'discountValueInput', 'maxDiscountInput', 'minOrderInput'].forEach(id => {
        const el = document.getElementById(id);
        if (el) el.addEventListener('input', updatePreview);
    });
    handleDiscountTypeChange();
});
</script>
@endsection
