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

class Benefit extends Model implements HasMedia
{
    use HasFactory;
    use InteractsWithMedia;

    protected $fillable = [
        'name',
        'paid',
        'store_id',
    ];

    public function books(): BelongsToMany
    {
        return $this->belongsToMany(Book::class);
    }

    public function goodsStore(): BelongsTo
    {
        return $this->belongsTo(GoodsStore::class, 'store_id');
    }

    protected function store(): Attribute
    {
        $get = function () {
            $this->relationLoaded('goodsStore') ?: throw new \Exception('Cannot access to store attribute before loading goodsStore relation');

            return $this->goodsStore->store;
        };

        return Attribute::make(get: $get);
    }

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

    /**
     * サムネイルは一つだけ登録できるようにする。
     */
    public function registerMediaCollections(): void
    {
        $this
            ->addMediaCollection('thumbnail')
            ->singleFile();
    }
}
