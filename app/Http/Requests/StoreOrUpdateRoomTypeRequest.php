<?php

namespace App\Http\Requests;

use App\Traits\ImageTrait;
use App\Traits\ResponseTrait;
use App\Traits\RoomTypeValidationTrait;
use App\Traits\TranslationTrait;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreOrUpdateRoomTypeRequest extends FormRequest
{
    use ResponseTrait, RoomTypeValidationTrait, TranslationTrait, ImageTrait;

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
            'name_en' => $this->translationRule($this->isRequired, 'en', 3),
            'name_ar' => $this->translationRule($this->isRequired, 'ar', 3),
            'category_en' => $this->translationRule($this->isRequired, 'en', 3),
            'category_ar' =>  $this->translationRule($this->isRequired, 'ar', 3),
            'description_en' => $this->translationRule($this->isRequired, 'en', 10),
            'description_ar' => $this->translationRule($this->isRequired, 'ar', 10),
            'capacity' => $this->capacityRule($this->isRequired),
            'daily_price' => $this->priceRule($this->isRequired),
            'monthly_price' => $this->priceRule($this->isRequired),
            'image' => $this->imageRule($this->isRequired),
        ];
    }

    public function messages()
    {
        $messages = array_merge(
            $this->translationMessages(),
            $this->capacityMessages(),
            $this->dailyPriceMessages(),
            $this->monthlyPriceMessages(),
            $this->imageMessages(),
        );

        if ($this->isRequired) {
            return array_merge(
                [
                    'name_en.required' =>  __('validation.required'),
                    'name_ar.required' =>  __('validation.required'),
                    'category_en.required' =>  __('validation.required'),
                    'category_ar.required' =>  __('validation.required'),
                    'description_en.required' =>  __('validation.required'),
                    'description_ar.required' =>  __('validation.required'),
                    'capacity.required' =>  __('validation.required'),
                    'daily_price.required' =>  __('validation.required'),
                    'monthly_price.required' => __('validation.required'),
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
