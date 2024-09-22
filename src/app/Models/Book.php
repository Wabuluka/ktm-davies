<?php

namespace App\Models;

use App\Enums\BookStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\UploadedFile;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Book extends Model implements HasMedia
{
    use HasFactory;
    use InteractsWithMedia;

    protected $guarded = ['id', 'created_by', 'updated_by'];

    protected $casts = [
        'volume' => 'string',
        'release_date' => 'datetime:Y-m-d',
        'published_at' => 'datetime:Y-m-d\TH:i',
        'is_primary' => 'boolean',
    ];

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
     * 発売予定の書籍のみに絞り込む
     */
    public function scopeUpcoming(Builder $query): Builder
    {
        return $query->where('release_date', '>', now());
    }

    /**
     * true が渡されたときに、 発売済みの書籍のみに絞り込む
     *
     * @param  Builder<Book>  $query
     * @param  bool  $released lighthouseのscopeディレクティブから渡される値
     *
     * @see https://lighthouse-php.com/6/api-reference/directives.html#scope
     */
    public function scopeReleased(Builder $query, bool $released = true): Builder
    {
        return $released
            ? $query->where('release_date', '<=', now())
            : $query;
    }

    /**
     * 公開サイトで絞り込む
     */
    public function scopePublishedAtSite(Builder $query, int|string $siteId): Builder
    {
        return $query->whereHas('sites', fn ($query) => $query->whereId($siteId));
    }

    /**
     * 書影画像の情報を返却する
     */
    protected function cover(): Attribute
    {
        return Attribute::make(
            fn () => $this->getFirstMedia('cover')
        );
    }

    /**
     * 一覧に表示する購入先を返却する
     */
    protected function primaryBookStore(): Attribute
    {
        return Attribute::make(
            fn () => $this->bookstores->firstWhere('pivot.is_primary', true),
        );
    }

    /**
     * 一覧に表示する電子書籍の購入先を返却する
     */
    protected function primaryEbookStore(): Attribute
    {
        return Attribute::make(
            fn () => $this->ebookstores->firstWhere('pivot.is_primary', true),
        );
    }

    /**
     * 書籍のステータスを返却する
     */
    protected function status(): Attribute
    {
        return Attribute::make(
            fn () => $this->is_draft
                ? BookStatus::Draft
                : ($this->published_at > now() ? BookStatus::WillBePublished : BookStatus::Published)
        );
    }

    /**
     * 書影を設定する
     */
    public function setCover(UploadedFile $cover, array $customProperties = []): static
    {
        $this
            ->addMedia($cover)
            ->withCustomProperties($customProperties)
            ->toMediaCollection('cover');

        return $this;
    }

    /**
     * 書影を削除する
     */
    public function deleteCover(): static
    {
        $this->clearMediaCollection('cover');

        return $this;
    }

    public function sites()
    {
        return $this->belongsToMany(Site::class);
    }

    public function label()
    {
        return $this->belongsTo(Label::class);
    }

    public function series()
    {
        return $this->belongsTo(Series::class);
    }

    public function genre()
    {
        return $this->belongsTo(Genre::class);
    }

    public function format()
    {
        return $this->belongsTo(BookFormat::class, 'format_id');
    }

    public function size()
    {
        return $this->belongsTo(BookSize::class, 'size_id');
    }

    public function creators()
    {
        return $this->belongsToMany(Creator::class, 'book_creations')
            ->as('creation')
            ->using(BookCreation::class)
            ->orderByPivot('sort')
            ->withPivot(['creation_type', 'displayed_type', 'sort']);
    }

    public function characters()
    {
        return $this->belongsToMany(Character::class);
    }

    public function blocks(): HasMany
    {
        return $this->hasMany(Block::class);
    }

    public function bookstores(): BelongsToMany
    {
        return $this->belongsToMany(BookStore::class)->withPivot('url', 'is_primary');
    }

    public function ebookstores(): BelongsToMany
    {
        return $this->belongsToMany(EbookStore::class)->withPivot('url', 'is_primary');
    }

    public function benefits(): BelongsToMany
    {
        return $this->belongsToMany(Benefit::class);
    }

    public function relatedItems(): HasMany
    {
        return $this->hasMany(RelatedItem::class);
    }

    public function stories(): BelongsToMany
    {
        return $this->belongsToMany(Story::class);
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * 書影は一つだけ登録できるようにする。
     */
    public function registerMediaCollections(): void
    {
        $this
            ->addMediaCollection('cover')
            ->singleFile();
    }
}
