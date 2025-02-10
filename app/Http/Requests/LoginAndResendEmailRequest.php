<?php

namespace App\Http\Requests;

use App\Traits\ResponseTrait;
use App\Traits\UserValidationTrait;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class LoginAndResendEmailRequest extends FormRequest
{
    use ResponseTrait, UserValidationTrait;

    public $isRequired;

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
        $this->isRequired = $this->routeIs('login');

        return [
            'email' => $this->emailRule(true, false, false),
            'password' => $this->passwordRule($this->isRequired, false, false),
        ];
    }

    public function messages()
    {
        return [
            'email.required' => __('validation.required'),
            'email.string' => __('validation.string'),
            'email.email' => __('validation.email'),
            'email.min' => __('validation.min.string', ['min' => 11]),
            'email.max' => __('validation.max.string', ['max' => 64]),
            'email.exists' => __('validation.exists.email'),
            'password.required' => __('validation.required'),
            'password.string' => __('validation.string'),
            'password.min' => __('validation.min.password', ['min' => 8]),
            'password.max' => __('validation.max.password', ['max' => 20]),
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        $response = $this->returnError($validator->errors(), 422);

        throw new HttpResponseException($response);
    }
}
