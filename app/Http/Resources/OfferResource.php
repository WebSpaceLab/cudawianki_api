<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OfferResource extends JsonResource
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
            'title' => $this->title,
            'description' => $this->description,
            'slug' => $this->slug,
            'status' => $this->status,
            'transition' => $this->transition,
            'path' => $this->path,

            'preview_image' => [
                'id' => $this->media->id,
                'name' => $this->media->name,
                'preview_url' => $this->media->preview_url
            ],

            'images' => MediaResource::collection($this->galleries),
            'categories' => OfferCategoryResource::collection($this->categories),

            'created_at' => $this->created_at->format('d/m/Y'),
            'updated_at' => $this->updated_at->format('d/m/Y'),
        ];
    }
}
