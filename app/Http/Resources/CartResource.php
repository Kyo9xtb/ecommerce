<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class CartResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $details = $this->details->map(function ($detail) {
            if (!$detail) {
                return null;
            }
            // dd($detail->tour);
            $basePath = "tour/{$detail->tour_id}/";
            $imagePath = $basePath . $detail->tour->thumbnail;
            return [
                'id' => $detail->id,
                'tour_id' => $detail->tour_id,
                'tour_name' => $detail->tour->tour_name ?? null,
                'slug' => $detail->tour->slug ?? null,
                'thumbnail_url' => $detail->tour->thumbnail ?  asset(Storage::url($imagePath)) : null,
                'guest_id' => $detail->guest_id,
                'quantity' => (int) ($detail->quantity ?? 1),
                'unit_price' => (float) ($detail->unit_price ?? 0),
                'departure_date' => $detail->departure_date
                    ? (new \DateTime($detail->departure_date))->format('Y-m-d')
                    : null,
            ];
        })->filter()->values();

        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'total_amount' => (float) ($this->total_amount ?? 0),
            'status' => (int) ($this->status ?? 1),
            'details' => $details->isEmpty() ? null : $details,
            'created_at' => optional($this->created_at)->format('Y-m-d H:i:s'),
            'updated_at' => optional($this->updated_at)->format('Y-m-d H:i:s'),
        ];
    }
}
