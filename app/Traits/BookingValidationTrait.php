<?php

namespace App\Traits;

use App\Models\Service;
use Illuminate\Validation\Rule;

trait BookingValidationTrait
{
    public function roomIdRule()
    {
        return ['required', 'integer', Rule::exists('rooms', 'id')->whereNull('deleted_at')];
    }

    public function servicesIdRule()
    {
        return ['nullable', 'array'];
    }

    public function serviceIdRule()
    {
        return ['nullable', 'integer', Rule::exists('services', 'id')->whereNull('deleted_at'), 'distinct'];
    }

    public function datesRule($isStart)
    {
        $rules = ['required', 'date', 'date_format:Y-m-d'];

        $rules[] = $isStart ? 'after:today' : 'after:start_date';

        return $rules;
    }

    public function paymentMethodRule($isRequired)
    {
        $required = $isRequired ? ['required'] : ['nullable'];

        return array_merge($required, ['string', 'regex:/^pm_[a-zA-Z0-9_]+$/', 'min:10']);
    }

    public function validateRoomAvailability($validator, $room, $startDate, $endDate)
    {
        if (!$room->bookings()->isAvailable($startDate, $endDate)) {
            $validator->errors()->add('room_id', __('errors.room.unavailable'));
        }
    }

    public function validateServiceRoomType($validator, $service, $room)
    {
        if (!$service->roomTypes->contains('id', $room->room_type_id)) {
            $validator->errors()->add('service_id => ' . $service->id . '', __('errors.room_type_service.not_assign'));
        }
    }

    public function validateServiceUnits($validator, $service, $startDate, $endDate)
    {
        if ($service->bookings()->countAvailableServiceUnits($startDate, $endDate, $service->total_units) === 0) {
            $validator->errors()->add('service_id => ' . $service->id . '', __('errors.service.units'));
        }
    }

    public function validateServices($validator, $servicesId, $room, $startDate, $endDate)
    {
        $services = Service::with('roomTypes')->whereIn('id', $servicesId)->get();

        foreach ($services as $service) {
            $this->validateServiceRoomType($validator, $service, $room);
            if ($service->is_limited) {
                $this->validateServiceUnits($validator, $service, $startDate, $endDate);
            }
        }
    }

    public function roomIdMessages()
    {
        return [
            'room_id.required' => __('validation.required'),
            'room_id.integer' => __('validation.integer'),
            'room_id.exists' => __('validation.exists.room_id'),
        ];
    }

    public function servicesIdMessages()
    {
        return [
            'services.array' => __('validation.array'),
        ];
    }

    public function serviceIdMessages()
    {
        return [
            'services.*.integer' => __('validation.integer'),
            'services.*.exists' => __('validation.exists.service_id'),
            'services.*.distinct' => __('validation.distinct.service'),
        ];
    }

    public function startDateMessages()
    {
        return [
            'start_date.required' => __('validation.required'),
            'start_date.date' => __('validation.date'),
            'start_date.date_format' => __('validation.date_format'),
            'start_date.after' => __('validation.date_after.start'),
        ];
    }

    public function endDateMessages()
    {
        return [
            'end_date.required' => __('validation.required'),
            'end_date.date' => __('validation.date'),
            'end_date.date_format' => __('validation.date_format'),
            'end_date.after' => __('validation.date_after.end'),
        ];
    }

    public function paymentMethodMessages()
    {
        return [
            'payment_method_id.required' => __('validation.required'),
            'payment_method_id.string' => __('validation.string'),
            'payment_method_id.regex' => __('validation.regex.payment_method'),
            'payment_method_id.min' => __('validation.min.string', ['min' => 10]),
        ];
    }
}
