<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class RelatedItemResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'relatable' => match ($this->relatable_type) {
                'book' => new BookResource($this->whenLoaded('relatable')),
                'externalLink' => new ExternalLinkResource($this->whenLoaded('relatable')),
            },
            'relatable_type' => $this->relatable_type,
            'relatable_id' => $this->relatable_id,
            'description' => $this->description,
            'sort' => $this->sort,
        ];
    }
}
