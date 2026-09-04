<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreReviewRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'order_item_id' => ['required', 'integer', 'exists:order_items,id'],
            'rating'        => ['required', 'integer', 'min:1', 'max:5'],
            'comment'       => ['nullable', 'string', 'max:2000'],
            'images'        => ['nullable', 'array', 'max:5'],
            'images.*'      => ['image', 'mimes:jpg,jpeg,png,webp', 'max:5120'], // 5MB
        ];
    }

    public function messages(): array
    {
        return [
            'order_item_id.required' => 'Thông tin sản phẩm đánh giá không hợp lệ.',
            'order_item_id.exists'   => 'Sản phẩm trong đơn hàng không tồn tại.',
            'rating.required'        => 'Vui lòng chọn số sao đánh giá (1-5 sao).',
            'rating.min'             => 'Điểm đánh giá tối thiểu là 1 sao.',
            'rating.max'             => 'Điểm đánh giá tối đa là 5 sao.',
            'comment.max'            => 'Bình luận tối đa 2.000 ký tự.',
            'images.max'             => 'Bạn chỉ có thể tải lên tối đa 5 hình ảnh.',
            'images.*.image'         => 'Tập tin tải lên phải là hình ảnh.',
            'images.*.mimes'         => 'Hình ảnh phải có định dạng jpg, jpeg, png hoặc webp.',
            'images.*.max'           => 'Mỗi hình ảnh không được vượt quá 5MB.',
        ];
    }
}
