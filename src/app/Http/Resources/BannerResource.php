<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BannerResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $image = $this->getFirstMedia('image');

        return [
            ...$this->only([
                'id',
                'name',
                'url',
                'placement_id',
            ]),
            'new_tab' => $this->new_tab === 1 ? true : false,
            'displayed' => $this->displayed === 1 ? true : false,
            'image' => [
                'id' => $image?->id,
                'original_url' => $image?->getFullUrl(),
            ],
        ];
    }
}
