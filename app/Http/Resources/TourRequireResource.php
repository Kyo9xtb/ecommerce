<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TourRequireResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'full_name' => $this->full_name ?? null,
            'nationality' => $this->nationality ?? null,
            'email' => +$this->email ?? null,
            'phone' => $this->phone ?? null,
            'expected_destination' => $this->expected_destination ?? null,
            'departure_date' => $this->departure_date ?? null,
            'end_date' => $this->end_date ?? null,
            'expected_month' => $this->expected_month ?? null,
            'expected_year' => $this->expected_year ?? null,
            'number_days' => $this->number_days ?? null,
            'vehicle' => $this->vehicle ?? null,
            'adult' => $this->adult ?? null,
            'children' => $this->children ?? null,
            'baby' => $this->baby ?? null,
            'number_rooms' => $this->number_rooms ?? null,
            'hotel_standards' => $this->hotel_standards ?? null,
            'expected_destination' => $this->expected_destination ?? null,
            'note' => $this->note ?? null,
            'feedback' => $this->feedback ?? null,
            'status' => $this->status ?? 1,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
