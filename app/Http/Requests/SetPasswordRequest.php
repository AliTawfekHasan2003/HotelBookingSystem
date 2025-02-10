<?php

namespace App\Http\Requests;

use App\Traits\ResponseTrait;
use App\Traits\UserValidationTrait;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class SetPasswordRequest extends FormRequest
{
    use ResponseTrait, UserValidationTrait;

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
            'password' => $this->passwordRule(true,true, true),
        ];
    }

    public function messages()
    {
        return [
            'password.required' => __('validation.required'),
            'password.string' => __('validation.string'),
            'password.min' => __('validation.min.password', ['min' => 8]),
            'password.max' => __('validation.max.password', ['max' => 20]),
            'password.confirmed' => __('validation.confirmed.password'),
            'password.regex' => __('validation.regex.password'),
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        $response = $this->returnError($validator->errors(), 422);

        throw new HttpResponseException($response);
    }
}
