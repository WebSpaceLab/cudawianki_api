<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OfferCategoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'slug' => $this->slug,
            'path' => $this->offer->path .'/'. $this->slug,
            'status' => $this->status,
            'transition' => $this->transition,
            'images' => MediaResource::collection($this->galleries),
            'preview_image' => [
                'id' => $this->media->id,
                'name' => $this->media->name,
                'preview_url' => $this->media->preview_url
            ],
            'offer' => [
                'id' => $this->offer->id,
                'title' => $this->offer->title,
            ],
            'created_at' => $this->created_at->format('d/m/Y'),
            'updated_at' => $this->updated_at->format('d/m/Y'),
        ];
    }
}
