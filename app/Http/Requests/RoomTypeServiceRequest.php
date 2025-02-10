<?php

namespace App\Http\Requests;

use App\Traits\ResponseTrait;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Exists;

class RoomTypeServiceRequest extends FormRequest
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
            'room_type_id' => ['required', Rule::exists('room_types','id')->whereNull('deleted_at')],
            'service_id' =>  ['required',  Rule::exists('services','id')->whereNull('deleted_at')],
        ];
    }

    public function messages()
    {
        return [
            'room_type_id.required' => __('validation.required'),
            'room_type_id.exists' => __('validation.exists.room_type_id'),
            'service_id.required' => __('validation.required'),
            'service_id.exists' => __('validation.exists.service_id'),
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        $response = $this->returnError($validator->errors(), 422);

        throw new HttpResponseException($response);
    }
}
