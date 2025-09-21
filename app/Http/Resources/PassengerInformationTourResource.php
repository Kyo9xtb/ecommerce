<?php

namespace App\Http\Resources;

use DateTime;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PassengerInformationTourResource extends JsonResource
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
            'booking_id' => $this->booking_id ?? null,
            'tour_id' => $this->tour_id ?? null,
            'customer' => $this->customer ?? null,
            'full_name' => $this->full_name ?? null,
            'birthday' => $this->birthday
                ? (new DateTime($this->birthday))->format('Y-m-d')
                : null,
            'gender' => $this->gender ?? null,
            'card_id' => $this->card_id ?? null,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
