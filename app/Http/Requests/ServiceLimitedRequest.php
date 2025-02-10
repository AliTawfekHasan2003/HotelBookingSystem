<?php

namespace App\Http\Requests;

use App\Traits\BookingValidationTrait;
use App\Traits\ResponseTrait;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class ServiceLimitedRequest extends FormRequest
{
    use ResponseTrait, BookingValidationTrait;

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
            'start_date' => $this->datesRule(true),
            'end_date' => $this->datesRule(false),
        ];
    }

    public function messages()
    {
        return array_merge(
            $this->startDateMessages(),
            $this->endDateMessages(),
        );
    }

    protected function failedValidation(Validator $validator)
    {
        $response = $this->returnError($validator->errors(), 422);

        throw new HttpResponseException($response);
    }
}
