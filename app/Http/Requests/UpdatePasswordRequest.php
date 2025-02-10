<?php

namespace App\Http\Requests;

use App\Traits\ResponseTrait;
use App\Traits\UserValidationTrait;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdatePasswordRequest extends FormRequest
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
            'current_password' => $this->passwordRule(true,false, false),
            'new_password' => $this->passwordRule(true,true, true),
        ];
    }

    public function messages()
    {
        return [
            'current_password.required' => __('validation.required'),
            'current_password.string' => __('validation.string'),
            'current_password.min' => __('validation.min.password', ['min' => 8]),
            'current_password.max' => __('validation.max.password', ['max' => 20]),
            'new_password.required' => __('validation.required'),
            'new_password.string' => __('validation.string'),
            'new_password.min' => __('validation.min.password', ['min' => 8]),
            'new_password.max' => __('validation.max.password', ['max' => 20]),
            'new_password.confirmed' => __('validation.confirmed.password'),
            'new_password.regex' => __('validation.regex.password'),
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        $response = $this->returnError($validator->errors(), 422);

        throw new HttpResponseException($response);
    }
}
