<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class ExternalLink extends Model implements HasMedia
{
    use HasFactory;
    use InteractsWithMedia;

    protected $fillable = [
        'title',
        'url',
    ];

    /**
     * サムネイルを返却する
     */
    protected function thumbnail(): Attribute
    {
        return Attribute::make(
            fn () => $this->getFirstMedia('thumbnail')
        );
    }

    /**
     * サムネイルを設定する
     */
    public function setThumbnail(UploadedFile $thumbnail, array $customProperties = []): static
    {
        $this
            ->addMedia($thumbnail)
            ->withCustomProperties($customProperties)
            ->toMediaCollection('thumbnail');

        return $this;
    }

    /**
     * サムネイルを削除する
     */
    public function deleteThumbnail(): static
    {
        $this->clearMediaCollection('thumbnail');

        return $this;
    }

    public function relatedItems()
    {
        return $this->morphMany(RelatedItem::class, 'relatable');
    }

    public function registerMediaCollections(): void
    {
        $this
            ->addMediaCollection('thumbnail')
            ->singleFile();
    }
}
