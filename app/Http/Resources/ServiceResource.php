<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Translation\Command\TranslationTrait;

class ServiceResource extends JsonResource
{
    use TranslationTrait;
    
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $service = [
            'id' => $this->id,
            'name' => $this->getAttributeTranslation('name') ?? null,
            'category' => $this->getAttributeTranslation('category') ?? null,
            'image' => Storage::url($this->image),
            'description' => $this->getAttributeTranslation('description') ?? null,
            'is_limited' => $this->is_limited,
            'total_units' => $this->total_units,
            'is_free' => $this->is_free,
            'daily_price' => $this->daily_price,
            'monthly_price' => $this->monthly_price,
        ];

        return $service;
    }
}
