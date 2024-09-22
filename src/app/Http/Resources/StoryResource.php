<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class StoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $thumbnail = $this->getFirstMedia('thumbnail');

        return [
            ...$this->only([
                'id',
                'title',
                'trial_url',
            ]),
            'thumbnail' => [
                'id' => $thumbnail?->id,
                'original_url' => $thumbnail?->getFullUrl(),
            ],
            'creators' => $this->whenLoaded('creators'),
        ];
    }
}
