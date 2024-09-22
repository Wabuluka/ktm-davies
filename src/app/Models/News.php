<?php

namespace App\Models;

use App\Enums\NewsStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class News extends Model implements HasMedia
{
    use HasFactory;
    use InteractsWithMedia;

    protected $fillable = [
        'title',
        'slug',
        'is_draft',
        'published_at',
        'content',
        'category_id',
    ];

    /**
     * 公開サイトで絞り込む
     */
    public function scopePublishedAtSite(Builder $query, int|string $siteId): Builder
    {
        return $query->whereHas('category', fn ($query) => $query->where('site_id', $siteId));
    }

    /**
     * スタータス「下書き」で絞り込む
     */
    public function scopeDraft($query): Builder
    {
        return $query->where('is_draft', true);
    }

    /**
     * スタータス「公開予定」で絞り込む
     */
    public function scopeWillBePublished(Builder $query): Builder
    {
        return $query->where('is_draft', false)->where('published_at', '>', now());
    }

    /**
     * スタータス「公開済み」で絞り込む
     */
    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_draft', false)->where('published_at', '<=', now());
    }

    /**
     * News のステータスを返却する
     */
    protected function status(): Attribute
    {
        return Attribute::make(
            fn () => $this->is_draft
                ? NewsStatus::Draft
                : ($this->published_at > now() ? NewsStatus::WillBePublished : NewsStatus::Published)
        );
    }

    /**
     * アイキャッチを設定する
     */
    public function setEyecatch(UploadedFile $eyecatch, array $customProperties = []): static
    {
        $this
            ->addMedia($eyecatch)
            ->withCustomProperties($customProperties)
            ->toMediaCollection('eyecatch');

        return $this;
    }

    /**
     * アイキャッチを削除する
     */
    public function deleteEyecatch(): static
    {
        $this->clearMediaCollection('eyecatch');

        return $this;
    }

    /**
     * アイキャッチ画像の情報を返却する
     */
    protected function eyecatch(): Attribute
    {
        return Attribute::make(
            fn () => $this->getFirstMedia('eyecatch')
        );
    }

    public function category()
    {
        return $this->belongsTo(NewsCategory::class, 'category_id');
    }

    public function registerMediaCollections(): void
    {
        $this
            ->addMediaCollection('eyecatch')
            ->singleFile();
    }
}
