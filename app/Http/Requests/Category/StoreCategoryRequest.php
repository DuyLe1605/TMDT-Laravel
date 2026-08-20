<?php

namespace App\Http\Requests\Category;

use App\Constants\AppConstants;
use Illuminate\Foundation\Http\FormRequest;

class StoreCategoryRequest extends FormRequest
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
            'name' => [
                'required',
                'string',
                'min:2',
                'max:' . AppConstants::MAX_STRING_LENGTH,
                'unique:categories,name',
            ],
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'name' => 'Tên danh mục',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Vui lòng nhập tên danh mục.',
            'name.string' => 'Tên danh mục phải là chuỗi ký tự hợp lệ.',
            'name.min' => 'Tên danh mục phải có tối thiểu :min ký tự.',
            'name.max' => 'Tên danh mục không được vượt quá :max ký tự.',
            'name.unique' => 'Tên danh mục này đã tồn tại trong hệ thống.',
        ];
    }
}
