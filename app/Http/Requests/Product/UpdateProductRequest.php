<?php

namespace App\Http\Requests\Product;

use App\Constants\AppConstants;
use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
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
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:' . AppConstants::MAX_STRING_LENGTH],
            'price' => ['required', 'numeric', 'min:0'],
            'sale_price' => ['nullable', 'numeric', 'min:0', 'lte:price'],
            'stock' => ['required', 'integer', 'min:0'],
            'material' => ['nullable', 'string', 'max:' . AppConstants::MAX_STRING_LENGTH],
            'dimensions' => ['nullable', 'string', 'max:100'],
            'color' => ['nullable', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:' . AppConstants::MAX_TEXT_LENGTH],
            'image' => ['nullable', 'string', 'max:500'],
            'is_featured' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }

    /**
     * Custom validation messages in Vietnamese.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'category_id.required' => 'Vui lòng chọn danh mục cho túi xách.',
            'category_id.exists' => 'Danh mục đã chọn không tồn tại trong hệ thống.',
            'name.required' => 'Tên sản phẩm túi xách không được để trống.',
            'name.max' => 'Tên sản phẩm không được vượt quá ' . AppConstants::MAX_STRING_LENGTH . ' ký tự.',
            'price.required' => 'Giá bán sản phẩm không được để trống.',
            'price.numeric' => 'Giá bán phải là định dạng số hợp lệ.',
            'price.min' => 'Giá bán không được nhỏ hơn 0đ.',
            'sale_price.numeric' => 'Giá khuyến mãi phải là số hợp lệ.',
            'sale_price.lte' => 'Giá khuyến mãi phải nhỏ hơn hoặc bằng giá gốc.',
            'stock.required' => 'Số lượng tồn kho không được để trống.',
            'stock.integer' => 'Số lượng tồn kho phải là số nguyên.',
            'stock.min' => 'Số lượng tồn kho không được âm.',
            'material.max' => 'Thông tin chất liệu không được vượt quá ' . AppConstants::MAX_STRING_LENGTH . ' ký tự.',
            'dimensions.max' => 'Thông tin kích thước không được vượt quá 100 ký tự.',
            'description.max' => 'Mô tả sản phẩm không được vượt quá ' . AppConstants::MAX_TEXT_LENGTH . ' ký tự.',
        ];
    }
}
