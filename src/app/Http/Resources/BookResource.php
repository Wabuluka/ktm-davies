<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BookResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            ...$this->only([
                'id',
                'title',
                'title_kana',
                'volume',
                'published_at',
                'isbn13',
                'price',
                'format_id',
                'size_id',
                'ebook_only',
                'special_edition',
                'limited_edition',
                'adult',
                'caption',
                'description',
                'keywords',
                'trial_url',
                'survey_url',
            ]),
            'status' => $this->status,
            'label' => $this->whenLoaded('label'),
            'genre' => $this->whenLoaded('genre'),
            'series' => $this->whenLoaded('series'),
            'creators' => $this->whenLoaded('creators'),
            'bookstores' => $this->whenLoaded('bookstores'),
            'ebookstores' => $this->whenLoaded('ebookstores'),
            'release_date' => $this->release_date?->format('Y-m-d'),
            'published_at' => $this->published_at->toDateTimeLocalString('minutes'),
            'cover' => $this->cover,
            'sites' => SiteResource::collection($this->whenLoaded('sites')),
            'benefits' => BenefitResource::collection($this->whenLoaded('benefits')),
            'stories' => StoryResource::collection($this->whenLoaded('stories')),
            'related_items' => RelatedItemResource::collection($this->whenLoaded('relatedItems')),
            'characters' => CharacterResource::collection($this->whenLoaded('characters')),
            'blocks' => BlockResource::collection($this->whenLoaded('blocks')),
            'updated_at' => $this->updated_at->format('Y-m-d H:i'),
            'updatedBy' => $this->whenLoaded('updatedBy'),
        ];
    }
}
