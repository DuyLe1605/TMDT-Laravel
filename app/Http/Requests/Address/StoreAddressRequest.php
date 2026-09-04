<?php

namespace App\Http\Requests\Address;

use Illuminate\Foundation\Http\FormRequest;

class StoreAddressRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'recipient_name' => ['required', 'string', 'max:100'],
            'phone' => ['required', 'string', 'regex:/^(0[3|5|7|8|9])[0-9]{8}$/'],
            'province' => ['required', 'string', 'max:100'],
            'province_id' => ['nullable', 'integer'],
            'district' => ['required', 'string', 'max:100'],
            'district_id' => ['nullable', 'integer'],
            'ward' => ['required', 'string', 'max:100'],
            'ward_code' => ['nullable', 'string', 'max:20'],
            'specific_address' => ['required', 'string', 'max:255'],
            'address_type' => ['required', 'in:home,office'],
            'is_default' => ['nullable', 'boolean'],
        ];
    }

    /**
     * Custom messages for validation errors.
     */
    public function messages(): array
    {
        return [
            'recipient_name.required' => 'Họ và tên người nhận không được để trống.',
            'recipient_name.max' => 'Họ và tên không được vượt quá 100 ký tự.',
            'phone.required' => 'Số điện thoại không được để trống.',
            'phone.regex' => 'Số điện thoại không đúng định dạng số di động Việt Nam (10 chữ số).',
            'province.required' => 'Vui lòng chọn hoặc nhập Tỉnh / Thành phố.',
            'district.required' => 'Vui lòng chọn hoặc nhập Quận / Huyện.',
            'ward.required' => 'Vui lòng chọn hoặc nhập Phường / Xã.',
            'specific_address.required' => 'Vui lòng nhập địa chỉ cụ thể (Số nhà, tên đường...).',
            'address_type.required' => 'Vui lòng chọn loại địa chỉ (Nhà riêng / Văn phòng).',
            'address_type.in' => 'Loại địa chỉ không hợp lệ.',
        ];
    }
}
