<?php

namespace App\Http\Requests;

use App\Traits\ResponseTrait;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class ConfirmPaymentRequest extends FormRequest
{
    use ResponseTrait;

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
            'payment_id' => 'required|string|regex:/^pi_[a-zA-Z0-9_]+$/|min:10',
            'payment_status' => 'required|string|in:succeeded,failed',
        ];
    }

    public function messages()
    {
        return [
            'payment_id.required' => __('validation.required'),
            'payment_id.string' => __('validation.string'),
            'payment_id.regex' => __('validation.regex.payment_id'),
            'payment_id.min' => __('validation.min.string', ['min' => 10]),
            'payment_status.required' => __('validation.required'),
            'payment_status.string' => __('validation.string'),
            'payment_status.in' => __('validation.in.payment_status'),
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        $response = $this->returnError($validator->errors(), 422);

        throw new HttpResponseException($response);
    }
}
