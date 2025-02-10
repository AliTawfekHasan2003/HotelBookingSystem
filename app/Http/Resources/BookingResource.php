<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $booking = [
            'id' => $this->id,
            'invoice_id' => $this->invoice_id,
            'bookingable_type' => $this->bookingable_type,
            'bookingable_id' => $this->bookingable_id,
            'original_monthly_price' => $this->original_monthly_price,
            'original_daily_price' => $this->original_daily_price,
            'booking_cost' => $this->booking_cost,
        ];

        return $booking;
    }
}
