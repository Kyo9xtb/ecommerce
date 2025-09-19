<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class NewsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $basePath = "news/{$this->id}/";

        return [
            'id' => $this->id,
            'title' => $this->title ?? null,
            'meta_title' => $this->meta_title ?? null,
            'slug' => $this->slug ?? null,
            'meta_description' => $this->meta_description ?? null,
            'description' => $this->description ?? null,
            'content' => $this->content ?? null,
            'author' => $this->author ?? null,
           
            'thumbnail' => $this->thumbnail
            ? $basePath . $this->thumbnail
            : null,
            'thumbnail_url' => $this->thumbnail
            ? asset(Storage::url($basePath . $this->thumbnail))
            : null,
            'status' => $this->status ?? 1,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
