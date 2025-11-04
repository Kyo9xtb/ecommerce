<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class BookingTourResource extends JsonResource
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
            $basePath = "tour/{$detail->tour_id}/";

            return [
                'id' => $detail->id,
                'tour_id' => $detail->tour_id,
                'tour_code' => $detail->tour->tour_code ?? null,
                'tour_name' => $detail->tour->tour_name ?? null,
                'guest_id' => $detail->guest_id,
                'price' => (float) ($detail->price ?? 0),
                'quantity' => (int) ($detail->quantity ?? 0),
                'total_price' => (float) (($detail->price ?? 0) * ($detail->quantity ?? 0)),
                'thumbnail_url' => data_get($detail->tour, 'thumbnail')
                    ? asset(Storage::url($basePath . data_get($detail->tour, 'thumbnail')))
                    : null,
                'departure_date' => $detail->departure_date
                    ? (new \DateTime($detail->departure_date))->format('Y-m-d')
                    : null,
            ];
        })->filter()->values();

        return [
            'id' => $this->id,
            'booking_code' => $this->booking_code,
            'user_id' => $this->user_id,
            'full_name' => $this->full_name,
            'email' => $this->email,
            'phone' => $this->phone,
            'address' => $this->address,
            'total_price' => (float) ($this->total_price ?? 0),
            'deposit' => (float) ($this->deposit ?? 0),
            'unpaid' => (float)(($this->total_price) - ($this->deposit ?? 0)),
            'discount_code' => $this->discount_code ?? 0,
            'currency' => $this->currency ?? null,
            'payment_method' => (int) ($this->payment_method ?? 0),
            'status' => (int) ($this->status ?? 1),
            'details' => $details->isEmpty() ? null : $details,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
