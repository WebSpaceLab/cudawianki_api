<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InboxResource extends JsonResource
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
            'subject' => $this->subject,
            'sender' => $this->sender,
            'email' => $this->email,
            'phone' => $this->phone,
            'content' => $this->content,
            'is_read' => $this->is_read,
            'createdAt' => $this->created_at->format('d/m/Y'),
        ];
    }
}
