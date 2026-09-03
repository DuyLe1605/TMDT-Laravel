<?php

namespace App\Http\Requests\Category;

use App\Constants\AppConstants;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $categoryId = $this->route('category') instanceof \App\Models\Category 
            ? $this->route('category')->id 
            : $this->route('category');

        return [
            'name' => [
                'required',
                'string',
                'min:2',
                'max:' . AppConstants::MAX_STRING_LENGTH,
                Rule::unique('categories', 'name')->ignore($categoryId),
            ],
            'parent_id' => ['nullable', 'integer', 'exists:categories,id'],
            'description' => ['nullable', 'string', 'max:' . AppConstants::MAX_TEXT_LENGTH],
            'image' => ['nullable', 'string', 'max:500'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Vui lòng nhập tên danh mục.',
            'name.min' => 'Tên danh mục phải có tối thiểu :min ký tự.',
            'name.max' => 'Tên danh mục không được vượt quá :max ký tự.',
            'name.unique' => 'Tên danh mục này đã tồn tại trong hệ thống.',
            'parent_id.exists' => 'Danh mục cha đã chọn không hợp lệ.',
        ];
    }
}
