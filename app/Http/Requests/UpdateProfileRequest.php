<?php

namespace App\Http\Requests;

use App\Traits\ResponseTrait;
use App\Traits\UserValidationTrait;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateProfileRequest extends FormRequest
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
            'first_name' => $this->nameRule(false),
            'last_name' => $this->nameRule(false),
            'email' => $this->emailRule(false, true, true),
        ];
    }

    public function messages()
    {
        return [
            'first_name.string' => __('validation.string'),
            'first_name.min' => __('validation.min.string', ['min' => 3]),
            'first_name.max' => __('validation.max.string', ['max' => 15]),
            'first_name.regex' => __('validation.regex.first_name'),
            'last_name.string' => __('validation.string'),
            'last_name.min' => __('validation.min.string', ['min' => 3]),
            'last_name.max' => __('validation.max.string', ['max' => 15]),
            'last_name.regex' => __('validation.regex.last_name'),
            'email.string' => __('validation.string'),
            'email.email' => __('validation.email'),
            'email.min' => __('validation.min.string', ['max' => 11]),
            'email.max' => __('validation.max.string', ['max' => 64]),
            'email.unique' => __('validation.unique.email'),
            'email.regex' => __('validation.regex.email'),
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        $response = $this->returnError($validator->errors(), 422);

        throw new HttpResponseException($response);
    }
}
