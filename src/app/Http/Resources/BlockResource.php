<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BlockResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'type_id' => $this->type_id,
            'custom_title' => $this->custom_title,
            'custom_content' => $this->custom_content,
            'sort' => $this->sort,
            'displayed' => $this->displayed,
        ];
    }
}
