<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SiteResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $this->load('pages');

        return [
            ...$this->only([
                'id',
                'name',
            ]),
            'logo' => $this->whenLoaded('media', $this->logo),
            'bannerPlacements' => $this->whenLoaded('bannerPlacements'),
            'pages' => PageResource::collection($this->pages),
        ];
    }
}
