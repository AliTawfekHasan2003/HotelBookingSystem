<?php

namespace App\Traits;

use App\Models\Service;

trait ServiceValidationTrait
{
    public function limitedAndFreeRule($isRequired)
    {
        $required = $isRequired ? ['required'] : ['nullable'];

        return array_merge($required, ['boolean']);
    }

    public function unitsRule($filledLimited, $inputLimited, $serviceId)
    {
        $type = ['integer'];

        if ($filledLimited) {
            return array_merge($inputLimited ? ['required', 'min:1'] : ['nullable', 'in:0'], $type);
        } else {
            $service = Service::find($serviceId);
            if ($service) {
                $isLimited = $service->is_limited;
                return array_merge($isLimited ? ['nullable', 'min:1'] : ['nullable', 'in:0'], $type);
            }
        }
        return ['nullable'];
    }

    public function priceRule($filledFree, $inputFree, $serviceId)
    {
        $type = ['decimal:2'];

        if ($filledFree) {
            return array_merge($inputFree ? ['nullable', 'in:0.00'] : ['required', 'min:1.00', 'max:999999.99'], $type);
        } else {
            $service = Service::find($serviceId);
            if ($service) {
                $isFree = $service->is_free;
                return array_merge($isFree ? ['nullable', 'in:0.00'] : ['nullable', 'min:1.00', 'max:999999.99'], $type);
            }
        }
        return ['nullable'];
    }

    public function limitedMessages()
    {
        return [
            'is_limited.boolean' => __('validation.boolean'),
        ];
    }

    public function freeMessages()
    {
        return [
            'free.boolean' => __('validation.boolean'),
        ];
    }

    public function unitsMessages()
    {
        return [
            'total_units.integer' => __('validation.integer'),
            'total_units.min' => __('validation.min.units', ['min' => 1]),
            'total_units.in' => __('validation.in.units', ['value' => 0]),
        ];
    }

    public function dailyPriceMessages()
    {
        return [
            'daily_price.decimal' => __('validation.decimal', ['number' => 2]),
            'daily_price.min' => __('validation.min.price', ['min' => '1.00$']),
            'daily_price.max' => __('validation.max.price', ['max' => '999999.99$']),
            'daily_price.in' => __('validation.in.price', ['value' => '0.00$']),
        ];
    }

    public function monthlyPriceMessages()
    {
        return [
            'monthly_price.decimal' => __('validation.decimal', ['number' => 2]),
            'monthly_price.min' => __('validation.min.price', ['min' => '1.00$']),
            'monthly_price.max' => __('validation.max.price', ['max' => '999999.99$']),
            'monthly_price.in' => __('validation.in.price', ['value' => '0.00$']),
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
