<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class TourResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $basePath = "tour/{$this->id}/";

        $images = $this->images->map(function ($img) use ($basePath) {
            if (!$img->image) {
                return null;
            }

            $imagePath = $basePath . $img->image;

            return [
                'image' => $imagePath,
                'image_url' => asset(Storage::url($imagePath)),
            ];
        })->filter()->values();

        $vehicles = $this->vehicles->map(function ($vehicle) {
            if (!$vehicle->code_vehicle) {
                return null;
            }
            return [
                'code_vehicle' => $vehicle->code_vehicle,
                'name_vehicle' => null,
            ];
        })->filter()->values();

        $guests = $this->guests->map(function ($guest) {
            if (!$guest->guest_code) {
                return null;
            }
            return [
                'guest_code' => $guest->guest_code,
                'guests_name' => null,
            ];
        })->filter()->values();
        return [
            'id' => $this->id,
            'tour_code' => $this->tour_code ?? null,
            'tour_name' => $this->tour_name ?? null,
            'slug' => $this->slug ?? null,
            'tour_group' => +$this->tour_group ?? 1,
            'area' => $this->area ?? null,
            'suggested_price' => +$this->price ?? 0,
            'is_sale' => $this->sale > 0,
            'sale' => $this->sale ?? 0,
            'price' => $this->price - ($this->price * ($this->sale / 100)) ?? 0,
            'trip' => $this->trip ?? null,
            'departure_schedule' => $this->departure_schedule ?? null,
            'time' => $this->time ?? null,
            'status' => $this->status ?? 1,
            'tour_summary' => $this->detail->tour_summary ?? null,
            'tour_program' => $this->detail->tour_program ?? null,
            'tour_policy' => $this->detail->tour_policy ?? null,
            'terms_conditions' => $this->detail->terms_conditions ?? null,
            'vehicles' => $vehicles->isEmpty() ? null : $vehicles,
            'guests' => $guests->isEmpty() ? null : $guests,
            'thumbnail' => $this->thumbnail
                ? $basePath . $this->thumbnail
                : null,
            'images' => $images->isEmpty() ? null : $images->pluck('image'),
            'thumbnail_url' => $this->thumbnail
                ? asset(Storage::url($basePath . $this->thumbnail))
                : null,
            'images_url' => $images->isEmpty() ? null : $images->pluck('image_url'),
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
