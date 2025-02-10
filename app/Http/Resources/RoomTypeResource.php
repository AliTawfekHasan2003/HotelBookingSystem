<?php

namespace App\Http\Resources;

use App\Traits\TranslationTrait;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class RoomTypeResource extends JsonResource
{
    use TranslationTrait;

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $roomType = [
            'id' => $this->id,
            'name' => $this->getAttributeTranslation('name') ?? null,
            'category' => $this->getAttributeTranslation('category') ?? null,
            'capacity' => $this->capacity,
            'image' => Storage::url($this->image),
            'description' => $this->getAttributeTranslation('description') ?? null,
            'count_rooms' => $this->rooms->count(),
            'daily_price' => $this->daily_price,
            'monthly_price' => $this->monthly_price,
        ];

        return $roomType;
    }
}
