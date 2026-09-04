@forelse($reviews as $review)
    <div class="review-item py-4 border-bottom border-secondary border-opacity-10">
        <div class="d-flex align-items-start gap-3">
            <!-- User Avatar -->
            <img src="{{ $review->user_avatar_url }}" alt="Avatar" class="rounded-circle shadow-sm" style="width: 44px; height: 44px; object-fit: cover; border: 2px solid #d4af37;">
            
            <div class="flex-grow-1">
                <!-- User Name & Verified Badge -->
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-1">
                    <div class="d-flex align-items-center gap-2">
                        <span class="fw-semibold text-dark">{{ $review->masked_user_name }}</span>
                        @if($review->is_verified_purchase)
                            <span class="badge bg-success-subtle text-success border border-success-subtle d-inline-flex align-items-center gap-1 font-monospace" style="font-size: 0.72rem;">
                                <i class="bi bi-shield-check"></i> Đã mua hàng tại Aurelia
                            </span>
                        @endif
                    </div>
                    <small class="text-muted" style="font-size: 0.8rem;">
                        <i class="bi bi-clock me-1"></i>{{ $review->created_at->format('d/m/Y H:i') }}
                    </small>
                </div>

                <!-- Star Rating -->
                <div class="mb-2 d-flex align-items-center gap-1">
                    @for($i = 1; $i <= 5; $i++)
                        @if($i <= $review->rating)
                            <i class="bi bi-star-fill text-warning" style="font-size: 0.95rem;"></i>
                        @else
                            <i class="bi bi-star text-muted opacity-25" style="font-size: 0.95rem;"></i>
                        @endif
                    @endfor
                    <span class="text-muted ms-1 small">({{ $review->rating }}/5)</span>
                </div>

                <!-- Product Variant Info Snapshot -->
                @if($review->product_variant_title)
                    <div class="text-muted small mb-2 d-flex align-items-center gap-2">
                        <span class="badge bg-light text-secondary border px-2 py-1">
                            <i class="bi bi-tags me-1"></i>Phân loại: {{ $review->product_variant_title }}
                        </span>
                    </div>
                @endif

                <!-- Review Comment -->
                @if($review->comment)
                    <div class="review-comment text-secondary mb-3 lh-base" style="font-size: 0.95rem;">
                        {{ $review->comment }}
                    </div>
                @endif

                <!-- Review Attached Images -->
                @if($review->images->isNotEmpty())
                    <div class="d-flex flex-wrap gap-2 mb-3">
                        @foreach($review->images as $img)
                            <div class="position-relative overflow-hidden rounded-3 border review-img-wrapper" 
                                 style="width: 80px; height: 80px; cursor: pointer; transition: transform 0.2s;"
                                 onclick="showReviewImage('{{ $img->url }}')"
                                 title="Bấm để xem ảnh phóng to">
                                <img src="{{ $img->url }}" alt="Review photo" class="w-100 h-100 object-fit-cover">
                                <div class="overlay-zoom position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center bg-dark bg-opacity-25 opacity-0 hover-opacity-100 text-white">
                                    <i class="bi bi-zoom-in"></i>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif

                <!-- Admin / Shop Response -->
                @if($review->admin_reply)
                    <div class="p-3 rounded-3 bg-light border border-warning-subtle position-relative ms-2 mt-2">
                        <div class="d-flex align-items-center justify-content-between mb-1">
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge bg-dark text-warning px-2 py-1" style="letter-spacing: 0.5px;">
                                    <i class="bi bi-patch-check-fill text-warning me-1"></i>Phản hồi từ Aurelia Luxury
                                </span>
                            </div>
                            @if($review->admin_replied_at)
                                <small class="text-muted" style="font-size: 0.75rem;">
                                    {{ $review->admin_replied_at->format('d/m/Y') }}
                                </small>
                            @endif
                        </div>
                        <p class="text-secondary small mb-0 lh-base ps-1 fst-italic">
                            "{{ $review->admin_reply }}"
                        </p>
                    </div>
                @endif
            </div>
        </div>
    </div>
@empty
    <div class="text-center py-5 text-muted">
        <div class="mb-3">
            <i class="bi bi-chat-square-dots display-6 text-muted opacity-50"></i>
        </div>
        <p class="mb-1 fw-medium">Chưa có đánh giá nào phù hợp với bộ lọc đã chọn</p>
        <small class="text-muted">Hãy là người đầu tiên trải nghiệm và để lại đánh giá cho sản phẩm tuyệt vời này!</small>
    </div>
@endforelse
