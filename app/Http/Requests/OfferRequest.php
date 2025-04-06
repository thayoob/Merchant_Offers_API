<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Http\Resources\ApiResponseResource;

class OfferRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $offerId = $this->route('offer');

        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'discount_percentage' => ['required_without:offer_amount', 'numeric', 'between:0,100', 'nullable'],
            'offer_amount' => ['required_without:discount_percentage', 'numeric', 'min:0', 'nullable'],
            'valid_from' => ['required', 'date', 'after_or_equal:today'],
            'valid_until' => ['required', 'date', 'after:valid_from'],
            'merchant_id' => ['required', 'exists:merchants,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Offer title is required.',
            'discount_percentage.required_without' => 'Discount percentage is required when offer amount is not provided.',
            'offer_amount.required_without' => 'Offer amount is required when discount percentage is not provided.',
            'valid_from.required' => 'Start date is required.',
            'valid_from.after_or_equal' => 'Start date must be today or in the future.',
            'valid_until.required' => 'End date is required.',
            'valid_until.after' => 'End date must be after the start date.',
            'merchant_id.required' => 'Merchant ID is required.',
            'merchant_id.exists' => 'The selected merchant does not exist.',
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
