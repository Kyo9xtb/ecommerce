<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ContactCustomerResource extends JsonResource
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
            'email' => $this->email ?? null,
            'phone' => $this->phone ?? null,
            'title' => $this->title ?? null,
            'contact_content' => $this->contact_content ?? null,
            'contact_result' => $this->contact_result ?? null,
            'status' => $this->status ?? 1,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
