@extends('layouts.app')

@section('title', 'Quản Lý Đánh Giá Sản Phẩm')

@section('content')
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
    <div>
        <h1 class="h3 fw-extrabold text-dark mb-1">Đánh Giá & Trải Nghiệm Khách Hàng</h1>
        <p class="text-secondary small mb-0">Theo dõi ý kiến khách hàng, kiểm duyệt nội dung và phản hồi đánh giá Shopee-style</p>
    </div>
</div>

<!-- 3 Stats Cards -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-4">
        <div class="card-modern p-4 shadow-sm border h-100">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="text-secondary small fw-semibold text-uppercase" style="letter-spacing: 0.5px;">Tổng Đánh Giá</span>
                <div class="rounded-circle d-flex align-items-center justify-content-center bg-primary-subtle text-primary" style="width: 40px; height: 40px;">
                    <i data-lucide="message-square" style="width: 20px; height: 20px;"></i>
                </div>
            </div>
            <div class="display-6 fw-extrabold text-dark mb-1">{{ number_format($totalReviews) }}</div>
            <div class="text-secondary small">Trên toàn bộ danh mục sản phẩm</div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-4">
        <div class="card-modern p-4 shadow-sm border h-100">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="text-secondary small fw-semibold text-uppercase" style="letter-spacing: 0.5px;">Điểm Trung Bình Shop</span>
                <div class="rounded-circle d-flex align-items-center justify-content-center bg-warning-subtle text-warning-emphasis" style="width: 40px; height: 40px;">
                    <i data-lucide="star" style="width: 20px; height: 20px;"></i>
                </div>
            </div>
            <div class="display-6 fw-extrabold text-warning-emphasis mb-1">
                {{ number_format($avgRating, 1) }} <span class="fs-5 text-muted fw-normal">/ 5.0</span>
            </div>
            <div class="text-secondary small">Chất lượng dịch vụ & độ hài lòng</div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-4">
        <div class="card-modern p-4 shadow-sm border h-100">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="text-secondary small fw-semibold text-uppercase" style="letter-spacing: 0.5px;">Chờ Shop Trả Lời</span>
                <div class="rounded-circle d-flex align-items-center justify-content-center bg-danger-subtle text-danger" style="width: 40px; height: 40px;">
                    <i data-lucide="reply" style="width: 20px; height: 20px;"></i>
                </div>
            </div>
            <div class="display-6 fw-extrabold text-danger mb-1">{{ number_format($pendingReplies) }}</div>
            <div class="text-secondary small">Khách hàng đang mong đợi phản hồi</div>
        </div>
    </div>
</div>

<!-- Filters Bar -->
<div class="card-modern p-4 mb-4 shadow-sm border">
    <form action="{{ route('admin.reviews.index') }}" method="GET" class="row g-3">
        <div class="col-md-4">
            <div class="input-group">
                <span class="input-group-text bg-light border-end-0">
                    <i data-lucide="search" style="width: 16px; height: 16px;" class="text-secondary"></i>
                </span>
                <input type="text" name="search" class="form-control border-start-0" placeholder="Tìm theo tên khách, sản phẩm, bình luận..." value="{{ request('search') }}">
            </div>
        </div>

        <div class="col-sm-6 col-md-2">
            <select name="rating" class="form-select">
                <option value="">-- Số sao --</option>
                <option value="5" {{ request('rating') == '5' ? 'selected' : '' }}>5 Sao ★★★★★</option>
                <option value="4" {{ request('rating') == '4' ? 'selected' : '' }}>4 Sao ★★★★☆</option>
                <option value="3" {{ request('rating') == '3' ? 'selected' : '' }}>3 Sao ★★★☆☆</option>
                <option value="2" {{ request('rating') == '2' ? 'selected' : '' }}>2 Sao ★★☆☆☆</option>
                <option value="1" {{ request('rating') == '1' ? 'selected' : '' }}>1 Sao ★☆☆☆☆</option>
            </select>
        </div>

        <div class="col-sm-6 col-md-2">
            <select name="reply_status" class="form-select">
                <option value="">-- Phản hồi Shop --</option>
                <option value="pending" {{ request('reply_status') == 'pending' ? 'selected' : '' }}>Chưa trả lời</option>
                <option value="replied" {{ request('reply_status') == 'replied' ? 'selected' : '' }}>Đã trả lời</option>
            </select>
        </div>

        <div class="col-sm-6 col-md-2">
            <select name="is_visible" class="form-select">
                <option value="all">-- Trạng thái hiển thị --</option>
                <option value="1" {{ request('is_visible') === '1' ? 'selected' : '' }}>Đang hiển thị</option>
                <option value="0" {{ request('is_visible') === '0' ? 'selected' : '' }}>Đang ẩn</option>
            </select>
        </div>

        <div class="col-md-2 d-flex gap-2">
            <button type="submit" class="btn btn-brand-primary w-100 d-inline-flex align-items-center justify-content-center gap-1">
                <i data-lucide="filter" style="width: 14px; height: 14px;"></i>
                <span>Lọc</span>
            </button>
            <a href="{{ route('admin.reviews.index') }}" class="btn btn-surface px-3" title="Đặt lại bộ lọc">
                <i data-lucide="rotate-ccw" style="width: 14px; height: 14px;"></i>
            </a>
        </div>
    </form>
</div>

<!-- Reviews Table -->
<div class="card-modern shadow-sm border overflow-hidden">
    @if ($reviews->isEmpty())
        <div class="text-center py-5 text-secondary">
            <div class="rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 56px; height: 56px; background: #fef3c7; color: #d97706;">
                <i data-lucide="star-off" style="width: 28px; height: 28px;"></i>
            </div>
            <h6 class="fw-bold text-dark mb-1">Không tìm thấy đánh giá nào</h6>
            <p class="small text-secondary mb-0">Thử thay đổi bộ lọc hoặc từ khóa tìm kiếm</p>
        </div>
    @else
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr class="text-secondary small">
                        <th style="width: 220px;">Khách Hàng</th>
                        <th style="width: 250px;">Sản Phẩm</th>
                        <th>Đánh Giá & Nhận Xét</th>
                        <th style="width: 250px;">Phản Hồi Từ Shop</th>
                        <th class="text-center" style="width: 110px;">Trạng Thái</th>
                        <th class="text-end" style="width: 130px;">Thao Tác</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($reviews as $rev)
                        <tr>
                            <!-- Customer Info -->
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <img src="{{ $rev->user_avatar_url }}" alt="" class="rounded-circle border" style="width: 38px; height: 38px; object-fit: cover;">
                                    <div class="min-w-0">
                                        <div class="fw-bold text-dark small text-truncate">{{ $rev->user?->name ?? 'Vô danh' }}</div>
                                        <div class="text-secondary small font-monospace" style="font-size: 0.72rem;">{{ $rev->user?->email }}</div>
                                        @if($rev->is_verified_purchase)
                                            <span class="badge bg-success-subtle text-success py-0 px-1" style="font-size: 0.65rem;">
                                                <i class="bi bi-shield-check"></i> Đã mua hàng
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </td>

                            <!-- Product Info -->
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <img src="{{ $rev->product?->image ?? 'https://placehold.co/50' }}" alt="" class="rounded-2 border object-fit-cover" style="width: 44px; height: 44px;">
                                    <div class="min-w-0">
                                        <div class="fw-semibold text-dark small text-truncate" title="{{ $rev->product?->name }}">
                                            {{ $rev->product?->name }}
                                        </div>
                                        @if($rev->product_variant_title)
                                            <span class="badge bg-light text-secondary border py-0 px-1" style="font-size: 0.68rem;">
                                                {{ $rev->product_variant_title }}
                                            </span>
                                        @endif
                                        <div class="text-secondary small mt-0.5" style="font-size: 0.72rem;">
                                            Đơn #{{ $rev->order?->order_code }}
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <!-- Rating & Comment & Images -->
                            <td>
                                <div class="mb-1 d-flex align-items-center gap-1">
                                    @for($i = 1; $i <= 5; $i++)
                                        @if($i <= $rev->rating)
                                            <i class="bi bi-star-fill text-warning small"></i>
                                        @else
                                            <i class="bi bi-star text-muted opacity-25 small"></i>
                                        @endif
                                    @endfor
                                    <span class="fw-bold ms-1 small text-dark">{{ $rev->rating }}/5</span>
                                    <span class="text-muted ms-2 small" style="font-size: 0.72rem;">{{ $rev->created_at->format('d/m/Y H:i') }}</span>
                                    @if($rev->coins_rewarded > 0)
                                        <span class="badge bg-warning-subtle text-dark ms-1" style="font-size: 0.68rem;">
                                            +{{ number_format($rev->coins_rewarded) }} Xu
                                        </span>
                                    @endif
                                </div>

                                @if($rev->comment)
                                    <p class="mb-2 small text-secondary lh-sm">{{ $rev->comment }}</p>
                                @endif

                                @if($rev->images->isNotEmpty())
                                    <div class="d-flex flex-wrap gap-1.5 mt-1">
                                        @foreach($rev->images as $img)
                                            <a href="{{ $img->url }}" target="_blank">
                                                <img src="{{ $img->url }}" class="rounded border object-fit-cover shadow-xs" style="width: 42px; height: 42px;">
                                            </a>
                                        @endforeach
                                    </div>
                                @endif
                            </td>

                            <!-- Shop Reply Box -->
                            <td>
                                @if($rev->admin_reply)
                                    <div class="p-2.5 rounded-2 bg-light border border-warning-subtle small">
                                        <div class="d-flex align-items-center justify-content-between mb-1">
                                            <span class="fw-bold text-dark" style="font-size: 0.75rem;">Aurelia Shop:</span>
                                            <span class="text-muted" style="font-size: 0.7rem;">{{ $rev->admin_replied_at?->format('d/m/Y') }}</span>
                                        </div>
                                        <div class="text-secondary fst-italic" style="font-size: 0.78rem;">
                                            "{{ $rev->admin_reply }}"
                                        </div>
                                    </div>
                                @else
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle">
                                        Chưa có phản hồi
                                    </span>
                                @endif
                            </td>

                            <!-- Visibility Status -->
                            <td class="text-center">
                                @if($rev->is_visible)
                                    <span class="badge bg-success-subtle text-success border border-success-subtle">
                                        Hiển thị
                                    </span>
                                @else
                                    <span class="badge bg-secondary text-white">
                                        Đang ẩn
                                    </span>
                                @endif
                            </td>

                            <!-- Action Buttons -->
                            <td class="text-end">
                                <div class="d-inline-flex gap-1">
                                    <!-- Reply Button -->
                                    <button type="button" class="btn btn-sm btn-outline-primary p-1.5 px-2" 
                                            onclick="openAdminReplyModal({{ $rev->id }}, '{{ addslashes($rev->user?->name ?? 'Khách hàng') }}', '{{ addslashes($rev->comment ?? '') }}', '{{ addslashes($rev->admin_reply ?? '') }}')"
                                            title="Trả lời đánh giá">
                                        <i data-lucide="message-circle" style="width: 14px; height: 14px;"></i>
                                    </button>

                                    <!-- Toggle Visibility -->
                                    <form action="{{ route('admin.reviews.toggle', $rev) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm {{ $rev->is_visible ? 'btn-outline-secondary' : 'btn-outline-success' }} p-1.5 px-2" 
                                                title="{{ $rev->is_visible ? 'Ẩn đánh giá này' : 'Hiển thị công khai đánh giá' }}">
                                            <i data-lucide="{{ $rev->is_visible ? 'eye-off' : 'eye' }}" style="width: 14px; height: 14px;"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if ($reviews->hasPages())
            <div class="p-3 border-top d-flex justify-content-center">
                {{ $reviews->links() }}
            </div>
        @endif
    @endif
</div>

<!-- Admin Reply Modal -->
<div class="modal fade" id="adminReplyModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <form id="adminReplyForm" action="" method="POST">
                @csrf
                <div class="modal-header border-bottom p-3.5">
                    <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                        <i data-lucide="reply" class="text-primary" style="width: 18px; height: 18px;"></i>
                        <span>Phản Hồi Đánh Giá Khách Hàng</span>
                    </h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body p-4">
                    <div class="p-3 rounded-3 bg-light border mb-3 small">
                        <div class="fw-bold text-dark mb-1" id="replyCustomerName">Khách hàng:</div>
                        <div class="text-secondary fst-italic" id="replyCustomerComment">Nội dung đánh giá...</div>
                    </div>

                    <div>
                        <label class="form-label small fw-bold text-dark">Nội dung phản hồi của Shop: <span class="text-danger">*</span></label>
                        <textarea name="admin_reply" id="adminReplyInput" rows="4" class="form-control" placeholder="Cảm ơn bạn đã tin tưởng ủng hộ sản phẩm của Aurelia Luxury Bags..." required></textarea>
                    </div>
                </div>

                <div class="modal-footer p-3 border-top bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                    <button type="submit" class="btn btn-brand-primary">Lưu Phản Hồi</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    let replyModalInstance = null;

    function openAdminReplyModal(reviewId, customerName, customerComment, existingReply) {
        document.getElementById('adminReplyForm').action = `/admin/reviews/${reviewId}/reply`;
        document.getElementById('replyCustomerName').textContent = `Khách hàng: ${customerName}`;
        document.getElementById('replyCustomerComment').textContent = customerComment ? `"${customerComment}"` : '(Không có nhận xét văn bản)';
        document.getElementById('adminReplyInput').value = existingReply || '';

        const modalEl = document.getElementById('adminReplyModal');
        if (!replyModalInstance) {
            replyModalInstance = new bootstrap.Modal(modalEl);
        }
        replyModalInstance.show();
    }
</script>
@endsection
