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

        $images = collect($this->images)
            ->map(function ($img) use ($basePath) {
                if (!$img['image'] ?? !$img->image) {
                    return null;
                }

                $imagePath = $basePath . ($img['image'] ?? $img->image);

                return [
                    'image' => $imagePath,
                    'image_url' => asset(Storage::url($imagePath)),
                ];
            })
            ->filter()
            ->values();

        $vehicles = collect($this->vehicles)
            ->map(function ($vehicle) {
                $code = $vehicle['code_vehicle'] ?? $vehicle->code_vehicle ?? null;

                if (!$code) {
                    return null;
                }

                return [
                    'code_vehicle' => (int) $code,
                    'vehicle_name' => $vehicle['vehicle_name'] ?? $vehicle->vehicle_name ?? null,
                ];
            })
            ->filter()
            ->values();

        $guests = collect($this->guests)
            ->map(function ($guest) {
                $code = $guest['guest_code'] ?? $guest->guest_code ?? null;

                if (!$code) {
                    return null;
                }

                return [
                    'guest_code' => (int) $code,
                    'guest_name' => $guest['guest_name'] ?? $guest->guest_name ?? null,
                ];
            })
            ->filter()
            ->values();

        return [
            'id' => $this->id,
            'tour_code' => $this->tour_code,
            'tour_name' => $this->tour_name,
            'slug' => $this->slug,
            'tour_group' => (int) $this->tour_group ?: 1,
            'area' => $this->area,
            'suggested_price' => (float)$this->price ?: 0,
            'is_sale' => $this->sale > 0,
            'sale' => $this->sale ?? 0,
            'price' => round($this->price - ($this->price * ($this->sale / 100))),
            'trip' => $this->trip,
            'departure_schedule' => $this->departure_schedule,
            'time' => $this->time,
            'status' => $this->status ?: 1,
            'tour_summary' => data_get($this->detail, 'tour_summary'),
            'tour_program' =>  data_get($this->detail, 'tour_program'),
            'tour_policy' =>  data_get($this->detail, 'tour_policy'),
            'terms_conditions' =>  data_get($this->detail, 'terms_conditions'),
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
