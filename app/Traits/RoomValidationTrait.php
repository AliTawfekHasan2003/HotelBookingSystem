<?php

namespace App\Traits;

use App\Models\Room;
use Illuminate\Validation\Rule;

trait RoomValidationTrait
{
    public function roomTypeIdRule($isRequired)
    {
        $required = $isRequired ? ['required'] : ['nullable'];

        return array_merge($required, ['integer', Rule::exists('room_types', 'id')->whereNull('deleted_at')]);
    }

    public function floorAndNumberRule($isRequired)
    {
        $required = $isRequired ? ['required'] : ['nullable'];

        return array_merge($required, ['integer', 'min:1', 'max:50']);
    }

    public function isDuplicateRoom($floor, $number, $roomId, $isRequired)
    {
        $query = Room::query()->withTrashed();

        if ($floor && $number) {
            $query->where('floor', $floor)->where('number', $number);
        } elseif ($roomId) {
            $room = Room::find($roomId);
            if ($room) {
                $query->where('floor', $floor ?? $room->floor)->where('number', $number ?? $room->number);
            } else {
                return false;
            }
        }

        if (!$isRequired) {
            $query->where('id', '!=', $roomId);
        }

        return $query->exists();
    }

    public function roomTypeIdMessages()
    {
        return [
            'room_type_id.integer' => __('validation.integer'),
            'room_type_id.exists' => __('validation.exists.room_type_id'),
        ];
    }

    public function floorMessages()
    {
        return [
            'floor.integer' => __('validation.integer'),
            'floor.min' => __('validation.min.floor', ['min' => 1]),
            'floor.max' => __('validation.max.floor', ['max' => 50]),
        ];
    }

    public function numberMessages()
    {
        return [
            'number.integer' => __('validation.integer'),
            'number.min' => __('validation.min.number', ['min' => 1]),
            'number.max' => __('validation.max.number', ['max' => 50]),
        ];
    }

    public function translationMessages()
    {
        return [
            'view_en.string' =>  __('validation.string'),
            'view_en.min' => __('validation.min.string', ['min' => 3]),
            'view_en.regex' => __('validation.regex.translation_en'),

            'view_ar.string' =>  __('validation.string'),
            'view_ar.min' => __('validation.min.string', ['min' => 3]),
            'view_ar.regex' => __('validation.regex.translation_ar'),

            'description_en.string' =>  __('validation.string'),
            'description_en.min' => __('validation.min.string', ['min' => 10]),
            'description_en.regex' => __('validation.regex.translation_en'),

            'description_ar.string' =>  __('validation.string'),
            'description_ar.min' => __('validation.min.string', ['min' => 10]),
            'description_ar.regex' => __('validation.regex.translation_ar'),
        ];
    }
}
