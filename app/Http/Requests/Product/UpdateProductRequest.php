<?php

namespace App\Http\Requests\Product;

use App\Constants\AppConstants;
use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'brand_id' => ['nullable', 'integer', 'exists:brands,id'],
            'name' => ['required', 'string', 'max:' . AppConstants::MAX_STRING_LENGTH],
            'price' => ['required', 'numeric', 'min:0'],
            'sale_price' => ['nullable', 'numeric', 'min:0'],
            'stock' => ['nullable', 'integer', 'min:0'],
            'has_variants' => ['nullable', 'boolean'],
            'material' => ['nullable', 'string', 'max:' . AppConstants::MAX_STRING_LENGTH],
            'dimensions' => ['nullable', 'string', 'max:100'],
            'color' => ['nullable', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:' . AppConstants::MAX_TEXT_LENGTH],
            'image' => ['nullable', 'string', 'max:500'],
            'is_featured' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],

            // Attributes & Variants matrix validation
            'attributes' => ['nullable', 'array'],
            'attributes.*.name' => ['required_with:attributes', 'string', 'max:100'],
            'attributes.*.values' => ['required_with:attributes'],
            
            'variants' => ['nullable', 'array'],
            'variants.*.variant_title' => ['required_with:variants', 'string'],
            'variants.*.sku' => ['nullable', 'string', 'max:100'],
            'variants.*.price' => ['required_with:variants', 'numeric', 'min:0'],
            'variants.*.sale_price' => ['nullable', 'numeric', 'min:0'],
            'variants.*.stock' => ['required_with:variants', 'integer', 'min:0'],
            'variants.*.image' => ['nullable', 'string', 'max:500'],
            'variants.*.option1_value' => ['nullable', 'string', 'max:100'],
            'variants.*.option2_value' => ['nullable', 'string', 'max:100'],
            'variants.*.option3_value' => ['nullable', 'string', 'max:100'],
            'variants.*.is_active' => ['nullable'],
        ];
    }

    public function messages(): array
    {
        return [
            'category_id.required' => 'Vui lòng chọn dòng danh mục cho túi xách.',
            'category_id.exists' => 'Dòng túi xách đã chọn không tồn tại trong hệ thống.',
            'brand_id.exists' => 'Thương hiệu đã chọn không tồn tại.',
            'name.required' => 'Tên sản phẩm túi xách không được để trống.',
            'name.max' => 'Tên sản phẩm không được vượt quá ' . AppConstants::MAX_STRING_LENGTH . ' ký tự.',
            'price.required' => 'Giá bán sản phẩm không được để trống.',
            'price.numeric' => 'Giá bán phải là định dạng số hợp lệ.',
            'price.min' => 'Giá bán không được nhỏ hơn 0đ.',
            'sale_price.numeric' => 'Giá khuyến mãi phải là số hợp lệ.',
            'stock.integer' => 'Số lượng tồn kho phải là số nguyên.',
            'stock.min' => 'Số lượng tồn kho không được âm.',
            'variants.*.price.required_with' => 'Giá bán của từng biến thể là bắt buộc.',
            'variants.*.price.min' => 'Giá biến thể không được nhỏ hơn 0đ.',
            'variants.*.stock.required_with' => 'Tồn kho của từng biến thể là bắt buộc.',
        ];
    }
}
