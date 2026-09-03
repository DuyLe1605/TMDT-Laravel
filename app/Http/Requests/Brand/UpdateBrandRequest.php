<?php

namespace App\Http\Requests\Brand;

use App\Constants\AppConstants;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateBrandRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $brandId = $this->route('brand') ? $this->route('brand')->id : null;

        return [
            'name' => ['required', 'string', 'max:' . AppConstants::MAX_STRING_LENGTH],
            'slug' => ['nullable', 'string', 'max:' . AppConstants::MAX_STRING_LENGTH, Rule::unique('brands', 'slug')->ignore($brandId)],
            'logo' => ['nullable', 'string', 'max:500'],
            'description' => ['nullable', 'string', 'max:' . AppConstants::MAX_TEXT_LENGTH],
            'website' => ['nullable', 'string', 'max:' . AppConstants::MAX_STRING_LENGTH],
            'is_active' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Tên thương hiệu không được để trống.',
            'name.max' => 'Tên thương hiệu không được vượt quá ' . AppConstants::MAX_STRING_LENGTH . ' ký tự.',
            'slug.unique' => 'Đường dẫn (slug) của thương hiệu đã tồn tại.',
            'description.max' => 'Mô tả không được vượt quá ' . AppConstants::MAX_TEXT_LENGTH . ' ký tự.',
        ];
    }
}
