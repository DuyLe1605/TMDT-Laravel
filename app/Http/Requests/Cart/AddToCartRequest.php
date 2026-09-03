<?php

namespace App\Http\Requests\Cart;

use Illuminate\Foundation\Http\FormRequest;

class AddToCartRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'product_variant_id' => ['nullable', 'integer', 'exists:product_variants,id'],
            'quantity' => ['required', 'integer', 'min:1', 'max:100'],
        ];
    }

    /**
     * Custom messages for validation errors.
     */
    public function messages(): array
    {
        return [
            'product_id.required' => 'Vui lòng chọn sản phẩm cần thêm.',
            'product_id.exists' => 'Sản phẩm được chọn không tồn tại trong hệ thống.',
            'product_variant_id.exists' => 'Phân loại hàng đã chọn không tồn tại trong hệ thống.',
            'quantity.required' => 'Vui lòng nhập số lượng.',
            'quantity.integer' => 'Số lượng phải là số nguyên hợp lệ.',
            'quantity.min' => 'Số lượng tối thiểu là 1.',
            'quantity.max' => 'Số lượng mỗi lần thêm không được vượt quá 100.',
        ];
    }
}
