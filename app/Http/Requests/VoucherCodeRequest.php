<?php

namespace App\Http\Requests;

use App\Models\VoucherCode;
use Illuminate\Validation\Rule;
use App\Http\Resources\ApiResponseResource;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class VoucherCodeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $voucherCodeId = $this->route('voucher_code');

        return [
            'code' => [
                'required',
                'string',
                'max:50',
                Rule::unique('voucher_codes', 'code')->ignore($voucherCodeId),
            ],
            'offer_id' => ['required', 'exists:offers,id'],
            'valid_date' => [
                'required',
                'date',
                'after_or_equal:today',
                function ($attribute, $value, $fail) {
                    $offerValidUntil = $this->offer->valid_until ?? null;
                    if ($offerValidUntil && $value > $offerValidUntil) {
                        $fail('The valid date cannot be after the offer expiration date.');
                    }
                }
            ],
            'status' => ['required', Rule::in(VoucherCode::getStatuses())],
        ];
    }

    public function messages(): array
    {
        return [
            'code.required' => 'Voucher code is required.',
            'code.unique' => 'This voucher code already exists.',
            'offer_id.required' => 'Offer ID is required.',
            'offer_id.exists' => 'The selected offer does not exist.',
            'valid_date.required' => 'Valid date is required.',
            'valid_date.after_or_equal' => 'Valid date must be today or in the future.',
            'status.required' => 'Status is required.',
            'status.in' => 'Invalid status value.',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            ApiResponseResource::failureResponse(
                'Validation failed.',
                422,
                $validator->errors()
            )
        );
    }
}
