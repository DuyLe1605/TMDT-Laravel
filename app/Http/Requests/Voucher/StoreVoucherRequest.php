<?php

namespace App\Http\Requests\Voucher;

use App\Models\Voucher;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreVoucherRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        if ($this->has('code')) {
            $this->merge([
                'code' => strtoupper(trim((string) $this->code)),
            ]);
        }

        if (!$this->has('applicable_payment_methods')) {
            $this->merge([
                'applicable_payment_methods' => ['all'],
            ]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'code'                       => ['required', 'string', 'max:50', 'regex:/^[A-Z0-9_-]+$/', 'unique:vouchers,code'],
            'name'                       => ['required', 'string', 'max:255'],
            'description'                => ['nullable', 'string', 'max:1000'],
            'discount_type'              => ['required', Rule::in([Voucher::TYPE_PERCENTAGE, Voucher::TYPE_FIXED, Voucher::TYPE_SHIPPING])],
            'discount_value'             => [
                'required',
                'numeric',
                'min:0.01',
                function ($attribute, $value, $fail) {
                    if ($this->input('discount_type') === Voucher::TYPE_PERCENTAGE && $value > 100) {
                        $fail('Mức giảm theo phần trăm không được vượt quá 100%.');
                    }
                },
            ],
            'max_discount_amount'        => ['nullable', 'numeric', 'min:0'],
            'min_order_amount'           => ['nullable', 'numeric', 'min:0'],
            'applicable_payment_methods' => ['required', 'array', 'min:1'],
            'applicable_payment_methods.*' => ['string', Rule::in(['all', 'cod', 'bank_transfer', 'momo'])],
            'usage_limit'                => ['nullable', 'integer', 'min:1'],
            'usage_limit_per_user'       => ['required', 'integer', 'min:1'],
            'starts_at'                  => ['nullable', 'date'],
            'expires_at'                 => ['nullable', 'date', 'after_or_equal:starts_at'],
            'is_active'                  => ['sometimes', 'boolean'],
        ];
    }

    /**
     * Custom messages for validation errors.
     */
    public function messages(): array
    {
        return [
            'code.required'                      => 'Mã voucher không được để trống.',
            'code.unique'                        => 'Mã voucher này đã tồn tại trong hệ thống.',
            'code.regex'                         => 'Mã voucher chỉ được chứa chữ cái in hoa, chữ số, gạch ngang và gạch dưới (VD: AURELIA20).',
            'name.required'                      => 'Tên chương trình ưu đãi không được để trống.',
            'discount_type.required'             => 'Vui lòng chọn loại chiết khấu.',
            'discount_value.required'            => 'Vui lòng nhập giá trị chiết khấu.',
            'discount_value.min'                 => 'Giá trị chiết khấu phải lớn hơn 0.',
            'usage_limit_per_user.required'      => 'Vui lòng chỉ định số lượt dùng tối đa cho mỗi khách hàng.',
            'usage_limit_per_user.min'           => 'Số lượt dùng mỗi khách tối thiểu là 1.',
            'expires_at.after_or_equal'          => 'Ngày hết hạn phải sau hoặc bằng ngày bắt đầu.',
            'applicable_payment_methods.required'=> 'Vui lòng chọn ít nhất 1 phương thức thanh toán áp dụng.',
        ];
    }
}
