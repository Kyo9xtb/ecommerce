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
                'tour_id' => $detail->tour_id,
                'tour_name' => $detail->tour->tour_name ?? null,
                'guest_id' => $detail->guest_id,
                'price' => $detail->price ?? 0,
                'quantity' => $detail->quantity ?? 0,
                'total_price' => $detail->price * $detail->quantity ?? 0,
                'thumbnail_url' => $detail->tour->thumbnail
                    ? asset(Storage::url($basePath . $detail->tour->thumbnail))
                    : null,
                'departure_date' => $detail->departure_date
                    ? (new \DateTime($detail->departure_date))->format('Y-m-d')
                    : null,
            ];
        })->filter()->values();

        return [
            'id' => $this->id,
            'user_id' => $this->user_id ?? null,
            'full_name' => $this->full_name ?? null,
            'email' => $this->email ?? null,
            'phone' => $this->phone ?? null,
            'address' => $this->address ?? 0,
            'total_price' => $this->total_price ?? 0,
            'deposit' => $this->deposit ?? 0,
            'unpaid' => $this->total_price - $this->deposit ?? 0,
            'discount_code' => $this->discount_code ?? 0,
            'currency' => $this->currency ?? null,
            'payment_method' => $this->payment_method ?? null,
            'status' => $this->status ?? 1,
            'details' => $details->isEmpty() ? null : $details,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
