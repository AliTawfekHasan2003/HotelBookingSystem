<?php

namespace App\Http\Requests;

use App\Models\Room;
use App\Traits\BookingValidationTrait;
use App\Traits\ResponseTrait;
use Carbon\Carbon;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class BookingRequest extends FormRequest
{
    use ResponseTrait, BookingValidationTrait;

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
        $this->isRequired = $this->routeIs('payment_intent');

        return [
            'room_id'  => $this->roomIdRule(),
            'services' => $this->servicesIdRule(),
            'services.*' => $this->serviceIdRule(),
            'start_date' =>  $this->datesRule(true),
            'end_date' => $this->datesRule(false),
            'payment_method_id' => $this->paymentMethodRule($this->isRequired),
        ];
    }

    public function withValidator($validator)
    {
        if ($validator->fails()) {
            return;
        }

        $validator->after(function ($validator) {

            $roomId = $this->input('room_id');
            $servicesId = $this->input('services');
            $startDate = Carbon::parse($this->input('start_date'));
            $endDate = Carbon::parse($this->input('end_date'));
            $room = Room::find($roomId);

            $this->validateRoomAvailability($validator, $room, $startDate, $endDate);
            $this->validateServices($validator, $servicesId, $room, $startDate, $endDate);
        });
    }

    public function messages()
    {
        $messages = array_merge(
            $this->roomIdMessages(),
            $this->servicesIdMessages(),
            $this->serviceIdMessages(),
            $this->startDateMessages(),
            $this->endDateMessages(),
        );

        if ($this->isRequired) {
            return array_merge(
                $this->paymentMethodMessages(),
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
