<?php

namespace App\Http\Requests;

use App\Traits\ImageTrait;
use App\Traits\ResponseTrait;
use App\Traits\RoomValidationTrait;
use App\Traits\TranslationTrait;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreOrUpdateRoomRequest extends FormRequest
{
    use ResponseTrait, RoomValidationTrait, TranslationTrait, ImageTrait;

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
        $this->isRequired  = $this->isMethod('post');

        return [
            'room_type_id' => $this->roomTypeIdRule($this->isRequired),
            'view_en' => $this->translationRule($this->isRequired, 'en', 3),
            'view_ar' => $this->translationRule($this->isRequired, 'ar', 3),
            'description_en' => $this->translationRule($this->isRequired, 'en', 10),
            'description_ar' => $this->translationRule($this->isRequired, 'ar', 10),
            'floor' => $this->floorAndNumberRule($this->isRequired),
            'number' => $this->floorAndNumberRule($this->isRequired),
            'image' => $this->imageRule($this->isRequired),
        ];
    }

    public function withValidator($validator)
    {
        if ($validator->fails()) {
            return;
        }

        $validator->after(function ($validator) {
            $floor = $this->input('floor');
            $number = $this->input('number');
            $roomId = $this->route('room');

            if ($floor || $number) {
                if ($this->isDuplicateRoom($floor, $number, $roomId, $this->isRequired)) {
                    $validator->errors()->add('floor_number', __('validation.unique.floor_number'));
                }
            }
        });
    }
    public function messages()
    {
        $messages = array_merge(
            $this->roomTypeIdMessages(),
            $this->translationMessages(),
            $this->floorMessages(),
            $this->numberMessages(),
            $this->imageMessages(),
        );

        if ($this->isRequired) {
            return array_merge(
                [
                    'room_type_id.required' => __('validation.required'),
                    'view_en.required' =>  __('validation.required'),
                    'view_ar.required' =>  __('validation.required'),
                    'description_en.required' =>  __('validation.required'),
                    'description_ar.required' =>  __('validation.required'),
                    'floor.required' =>  __('validation.required'),
                    'number.required' =>  __('validation.required'),
                    'image.required' =>  __('validation.required'),
                ],
                $messages,
            );
        }
        return $messages;
    }


    protected function failedValidation(Validator $validator)
    {
        $response = $this->returnError($validator->errors(), 422);

        throw new HttpResponseException($response);
    }
}
