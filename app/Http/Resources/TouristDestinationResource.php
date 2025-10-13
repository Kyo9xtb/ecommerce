<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class TouristDestinationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $basePath = "tourist_destinations/{$this->id}/";
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

        $thumbnailPath = $this->thumbnail ? $basePath . $this->thumbnail : null;

        return [
            'id' => $this->id,
            'place_name' => $this->place_name,
            'meta_title' => $this->meta_title,
            'slug' => $this->slug,
            'area' => $this->area,
            'description' => $this->description,
            'tour_group' => $this->tour_group,
            'details' => $this->details,
            'status' => $this->status ?? 1,

            'thumbnail' => $thumbnailPath,
            'images' => $images->isEmpty() ? null : $images->pluck('image'),
            'thumbnail_url' => $thumbnailPath
                ? asset(Storage::url($thumbnailPath))
                : null,
            'images_url' => $images->isEmpty() ? null : $images->pluck('image_url'),
            'created_at' => optional($this->created_at)->format('Y-m-d H:i:s'),
            'updated_at' => optional($this->updated_at)->format('Y-m-d H:i:s'),
        ];
    }
}
