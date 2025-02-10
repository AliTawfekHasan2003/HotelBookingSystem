<?php

namespace App\Traits;


trait RoomTypeValidationTrait
{
    public function capacityRule($isRequired)
    {
        $required = $isRequired ? ['required'] : ['nullable'];

        return array_merge($required, ['integer', 'min:1', 'max:10']);
    }

    public function priceRule($isRequired)
    {
        $required = $isRequired ? ['required'] : ['nullable'];

        return array_merge($required, ['decimal:2', 'min:1.00', 'max:999999.99']);
    }

    public function capacityMessages()
    {
        return [
            'capacity.integer' => __('validation.integer'),
            'capacity.min' => __('validation.min.capacity', ['min' => 1]),
            'capacity.max' => __('validation.max.capacity', ['max' => 10]),
        ];
    }

    public function dailyPriceMessages()
    {
        return [
            'daily_price.decimal' => __('validation.decimal', ['number' => 2]),
            'daily_price.min' => __('validation.min.price', ['min' => '1.00$']),
            'daily_price.max' => __('validation.max.price', ['max' => '999999.99$']),
        ];
    }

    public function monthlyPriceMessages()
    {
        return [
            'monthly_price.decimal' => __('validation.decimal', ['number' => 2]),
            'monthly_price.min' => __('validation.min.price', ['min' => '1.00$']),
            'monthly_price.max' => __('validation.max.price', ['max' => '999999.99$']),
        ];
    }

    public function translationMessages()
    {
        return [
            'name_en.string' =>  __('validation.string'),
            'name_en.min' => __('validation.min.string', ['min' => 3]),
            'name_en.regex' => __('validation.regex.translation_en'),

            'name_ar.string' =>  __('validation.string'),
            'name_ar.min' => __('validation.min.string', ['min' => 3]),
            'name_ar.regex' => __('validation.regex.translation_ar'),

            'category_en.string' =>  __('validation.string'),
            'category_en.min' => __('validation.min.string', ['min' => 3]),
            'category_en.regex' => __('validation.regex.translation_en'),

            'category_ar.string' =>  __('validation.string'),
            'category_ar.min' => __('validation.min.string', ['min' => 3]),
            'category_ar.regex' => __('validation.regex.translation_ar'),

            'description_en.string' =>  __('validation.string'),
            'description_en.min' => __('validation.min.string', ['min' => 10]),
            'description_en.regex' => __('validation.regex.translation_en'),

            'description_ar.string' =>  __('validation.string'),
            'description_ar.min' => __('validation.min.string', ['min' => 10]),
            'description_ar.regex' => __('validation.regex.translation_ar'),
        ];
    }
}
