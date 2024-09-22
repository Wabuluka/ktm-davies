<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class NewsResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            ...$this->only([
                'id',
                'title',
                'slug',
                'content',
                'published_at',
            ]),
            'status' => $this->status,
            'category' => $this->whenLoaded('category'),
        ];
    }
}
