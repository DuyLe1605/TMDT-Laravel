<?php

namespace App\Http\Requests\Checkout;

use Illuminate\Foundation\Http\FormRequest;

class ProcessCheckoutRequest extends FormRequest
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
            'selected_items' => ['required', 'array', 'min:1'],
            'selected_items.*' => ['required', 'integer'],
            'recipient_name' => ['required', 'string', 'max:100'],
            'phone' => ['required', 'string', 'regex:/^(0[3|5|7|8|9])[0-9]{8}$/'],
            'shipping_address' => ['required', 'string', 'max:500'],
            'shipping_method' => ['required', 'in:standard,express'],
            'shipping_fee' => ['nullable', 'numeric', 'min:0'],
            'to_district_id' => ['nullable', 'integer'],
            'to_ward_code' => ['nullable', 'string'],
            'expected_delivery_at' => ['nullable', 'string'],
            'payment_method' => ['required', 'in:cod,bank_transfer,momo'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    /**
     * Custom messages for validation errors.
     */
    public function messages(): array
    {
        return [
            'selected_items.required' => 'Vui lòng chọn ít nhất 1 sản phẩm để thanh toán.',
            'selected_items.min' => 'Vui lòng chọn ít nhất 1 sản phẩm để thanh toán.',
            'recipient_name.required' => 'Họ và tên người nhận không được để trống.',
            'phone.required' => 'Số điện thoại nhận hàng không được để trống.',
            'phone.regex' => 'Số điện thoại không đúng định dạng số di động Việt Nam.',
            'shipping_address.required' => 'Địa chỉ giao hàng không được để trống.',
            'shipping_method.required' => 'Vui lòng chọn phương thức vận chuyển.',
            'payment_method.required' => 'Vui lòng chọn phương thức thanh toán.',
        ];
    }
}
