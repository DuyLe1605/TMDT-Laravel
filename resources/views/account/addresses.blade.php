@extends('layouts.storefront')

@section('title', 'Sổ Địa Chỉ Nhận Hàng - Tài Khoản Của Tôi')

@section('content')
<div class="container py-4 py-lg-5">
    <!-- Breadcrumb -->
    <div class="breadcrumb-modern mb-3">
        <a href="{{ route('home') }}">Trang chủ</a>
        <i data-lucide="chevron-right" style="width: 14px; height: 14px;"></i>
        <span>Tài khoản</span>
        <i data-lucide="chevron-right" style="width: 14px; height: 14px;"></i>
        <span class="text-primary fw-medium">Sổ địa chỉ</span>
    </div>

    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4 pb-3 border-bottom">
        <div>
            <h1 class="fw-extrabold text-dark mb-1" style="font-size: 1.85rem; letter-spacing: -0.02em;">
                Sổ Địa Chỉ Của Tôi
            </h1>
            <p class="text-secondary small mb-0">
                Quản lý các địa chỉ nhận hàng để thanh toán nhanh chóng hơn khi mua sắm tại Aurelia
            </p>
        </div>
        <button type="button" class="btn-brand-primary py-2 px-3.5 d-inline-flex align-items-center gap-1.5" onclick="openCreateAddressModal()">
            <i data-lucide="plus" style="width: 16px; height: 16px;"></i>
            <span>+ Thêm địa chỉ mới</span>
        </button>
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
                    <a href="{{ route('account.orders') }}" class="btn-surface w-100 text-start py-2 px-3 text-decoration-none d-flex align-items-center gap-2">
                        <i data-lucide="package" style="width: 16px; height: 16px;"></i>
                        <span>Đơn hàng của tôi</span>
                    </a>
                    <a href="{{ route('account.addresses') }}" class="btn-brand-primary w-100 text-start py-2 px-3 text-decoration-none d-flex align-items-center gap-2">
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

        <!-- Main Addresses Content -->
        <div class="col-lg-9">
            @if ($addresses->isEmpty())
                <div class="card-modern text-center py-5 px-4 shadow-sm border">
                    <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 68px; height: 68px; background: var(--brand-50); color: var(--brand-600);">
                        <i data-lucide="map-pin-off" style="width: 34px; height: 34px;"></i>
                    </div>
                    <h5 class="fw-bold text-dark mb-2">Chưa có địa chỉ nhận hàng nào</h5>
                    <p class="text-secondary small mb-4">
                        Bạn hãy thêm địa chỉ nhận hàng để không phải nhập lại thông tin mỗi lần mua sắm.
                    </p>
                    <button type="button" class="btn-brand-primary py-2 px-4" onclick="openCreateAddressModal()">
                        <i data-lucide="plus" style="width: 16px; height: 16px; margin-right: 0.4rem;"></i>
                        <span>Thêm Địa Chỉ Đầu Tiên</span>
                    </button>
                </div>
            @else
                <div class="d-flex flex-column gap-3">
                    @foreach ($addresses as $address)
                        <div class="card-modern p-4 shadow-sm border position-relative">
                            <div class="d-flex flex-wrap align-items-start justify-content-between gap-3">
                                <div>
                                    <div class="d-flex flex-wrap align-items-center gap-2 mb-1.5">
                                        <span class="fw-bold text-dark fs-6">{{ $address->recipient_name }}</span>
                                        <span class="text-secondary small">| {{ $address->phone }}</span>
                                        <span class="badge bg-secondary-subtle text-secondary" style="font-size: 0.7rem;">{{ $address->type_label }}</span>
                                        @if ($address->is_default)
                                            <span class="badge bg-danger-subtle text-danger" style="font-size: 0.7rem;">Mặc định</span>
                                        @endif
                                    </div>
                                    <div class="text-secondary small mb-1">
                                        {{ $address->full_address }}
                                    </div>
                                </div>

                                <div class="d-flex align-items-center gap-2">
                                    <button type="button" class="btn btn-sm btn-surface" onclick="openEditAddressModal({{ json_encode($address) }})">
                                        <i data-lucide="edit" style="width: 14px; height: 14px; margin-right: 0.25rem;"></i>
                                        <span>Cập nhật</span>
                                    </button>

                                    @if (!$address->is_default)
                                        <form action="{{ route('addresses.set_default', $address) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-secondary">
                                                Đặt mặc định
                                            </button>
                                        </form>

                                        <form action="{{ route('addresses.destroy', $address) }}" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc chắn muốn xóa địa chỉ này?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                                <i data-lucide="trash-2" style="width: 14px; height: 14px;"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal Thêm / Chỉnh Sửa Địa Chỉ -->
<div class="modal fade" id="addressFormModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 520px;">
        <div class="modal-content modal-content-modern border-0">
            <div class="modal-header border-bottom p-3.5">
                <h5 class="modal-title fw-bold text-dark fs-6" id="addressModalTitle">Thêm Địa Chỉ Mới</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form id="addressForm" method="POST" action="{{ route('addresses.store') }}">
                @csrf
                <div id="methodSpoof"></div>

                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label-modern small fw-semibold">Họ và tên *</label>
                            <input type="text" id="modalRecipientName" name="recipient_name" class="form-control form-control-modern" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label-modern small fw-semibold">Số điện thoại *</label>
                            <input type="tel" id="modalPhone" name="phone" class="form-control form-control-modern" placeholder="0988123456" required>
                        </div>
                        <!-- Cascading Vietnam Administrative Selects -->
                        <div class="col-12">
                            <label class="form-label-modern small fw-semibold">Tỉnh / Thành phố *</label>
                            <select id="modalProvince" name="province" class="form-select form-select-modern" required>
                                <option value="">-- Chọn Tỉnh / Thành phố --</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label-modern small fw-semibold">Quận / Huyện *</label>
                            <select id="modalDistrict" name="district" class="form-select form-select-modern" required disabled>
                                <option value="">-- Chọn Quận / Huyện --</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label-modern small fw-semibold">Phường / Xã *</label>
                            <select id="modalWard" name="ward" class="form-select form-select-modern" required disabled>
                                <option value="">-- Chọn Phường / Xã --</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label-modern small fw-semibold">Địa chỉ cụ thể *</label>
                            <input type="text" id="modalSpecific" name="specific_address" class="form-control form-control-modern" placeholder="Số nhà, tên đường..." required>
                        </div>
                        <div class="col-12">
                            <label class="form-label-modern small fw-semibold">Loại địa chỉ</label>
                            <div class="d-flex gap-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="address_type" id="addrTypeHome" value="home" checked>
                                    <label class="form-check-label small" for="addrTypeHome">Nhà riêng</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="address_type" id="addrTypeOffice" value="office">
                                    <label class="form-check-label small" for="addrTypeOffice">Văn phòng</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="is_default" id="modalIsDefault" value="1">
                                <label class="form-check-label small fw-medium" for="modalIsDefault">
                                    Đặt làm địa chỉ mặc định
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer border-top p-3 bg-light-subtle">
                    <button type="button" class="btn btn-surface px-3 py-2" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn-brand-primary px-4 py-2">
                        Lưu địa chỉ
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    let addressModalInst = null;

    function openCreateAddressModal() {
        const modalEl = document.getElementById('addressFormModal');
        document.getElementById('addressModalTitle').textContent = 'Thêm Địa Chỉ Mới';
        
        const form = document.getElementById('addressForm');
        form.action = '{{ route("addresses.store") }}';
        document.getElementById('methodSpoof').innerHTML = '';

        document.getElementById('modalRecipientName').value = '';
        document.getElementById('modalPhone').value = '';
        document.getElementById('modalSpecific').value = '';
        document.getElementById('addrTypeHome').checked = true;
        document.getElementById('modalIsDefault').checked = false;

        initVnLocationSelects('modalProvince', 'modalDistrict', 'modalWard');

        if (!addressModalInst) addressModalInst = new bootstrap.Modal(modalEl);
        addressModalInst.show();
    }

    function openEditAddressModal(addr) {
        const modalEl = document.getElementById('addressFormModal');
        document.getElementById('addressModalTitle').textContent = 'Cập Nhật Địa Chỉ';

        const form = document.getElementById('addressForm');
        form.action = `/addresses/${addr.id}`;
        document.getElementById('methodSpoof').innerHTML = '@method("PUT")';

        document.getElementById('modalRecipientName').value = addr.recipient_name;
        document.getElementById('modalPhone').value = addr.phone;
        document.getElementById('modalSpecific').value = addr.specific_address;
        
        if (addr.address_type === 'office') {
            document.getElementById('addrTypeOffice').checked = true;
        } else {
            document.getElementById('addrTypeHome').checked = true;
        }
        
        document.getElementById('modalIsDefault').checked = Boolean(addr.is_default);

        initVnLocationSelects('modalProvince', 'modalDistrict', 'modalWard', {
            province: addr.province,
            district: addr.district,
            ward: addr.ward
        });

        if (!addressModalInst) addressModalInst = new bootstrap.Modal(modalEl);
        addressModalInst.show();
    }
</script>
@endsection
