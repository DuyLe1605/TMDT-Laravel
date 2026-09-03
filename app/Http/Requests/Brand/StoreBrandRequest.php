<?php

namespace App\Http\Requests\Brand;

use App\Constants\AppConstants;
use Illuminate\Foundation\Http\FormRequest;

class StoreBrandRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:' . AppConstants::MAX_STRING_LENGTH],
            'slug' => ['nullable', 'string', 'max:' . AppConstants::MAX_STRING_LENGTH, 'unique:brands,slug'],
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
