<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Http\Resources\ApiResponseResource;

class MerchantRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $merchantId = $this->route('merchant');

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                Rule::unique('merchants', 'email')->ignore($merchantId),
            ],
            'contact_number' => ['required', 'string', 'max:20'],
            'address' => ['nullable', 'string'],
            'status' => ['in:active,inactive'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Merchant name is required.',
            'email.required' => 'Email address is required.',
            'email.unique' => 'This email is already registered.',
            'contact_number.required' => 'Contact number is required.',
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
