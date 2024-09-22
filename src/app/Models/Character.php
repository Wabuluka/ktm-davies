<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Http\UploadedFile;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Character extends Model implements HasMedia
{
    use HasFactory;
    use InteractsWithMedia;

    protected $guarded = [
        'id', 'created_at', 'updated_at',
    ];

    public function books(): BelongsToMany
    {
        return $this->belongsToMany(Book::class);
    }

    public function series(): BelongsTo
    {
        return $this->belongsTo(Series::class);
    }

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

    public function registerMediaCollections(): void
    {
        $this
            ->addMediaCollection('thumbnail')
            ->singleFile();
    }
}
