<?php

namespace App\Models;

use App\Traits\Models\Sortable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class RelatedItem extends Model
{
    use HasFactory;
    use Sortable;

    protected $fillable = [
        'book_id',
        'relatable_id',
        'relatable_type',
        'description',
        'sort',
    ];

    public static function boot()
    {
        parent::boot();

        static::addGlobalScope('default_order', fn ($builder) => $builder->orderBy('sort'));
    }

    protected function sortable(): Builder
    {
        return static::query()->where('book_id', $this->book_id);
    }

    /**
     * 関連作品のステータスが「公開済み」のものに絞り込む
     */
    public function scopePublished(Builder $query): Builder
    {
        $publishedOnly = fn (Builder $query, string $type) => $type === Book::class
            ? $query->published()
            : $query;

        return $query->whereHasMorph('relatable', [Book::class, ExternalLink::class], $publishedOnly);
    }

    /**
     * 公開サイトで絞り込む
     */
    public function scopePublishedAtSite(Builder $query, int|string $siteId): Builder
    {
        $publishedOnly = fn (Builder $query, string $type) => $type === Book::class
            ? $query->whereHas('sites', fn ($query) => $query->whereId($siteId))
            : $query;

        return $query->whereHasMorph('relatable', [Book::class, ExternalLink::class], $publishedOnly);
    }

    /**
     * 成人向けフラグで絞り込む
     */
    public function scopeAdult(Builder $query, $adult = true): Builder
    {
        $publishedOnly = fn (Builder $query, string $type) => $type === Book::class
            ? $query->where('adult', $adult)
            : $query;

        return $query->whereHasMorph('relatable', [Book::class, ExternalLink::class], $publishedOnly);
    }

    /**
     * サムネイルを返却する
     */
    protected function thumbnail(): Attribute
    {
        $thumbnail = match (get_class($this->relatable)) {
            Book::class => $this->relatable->cover,
            ExternalLink::class => $this->relatable->thumbnail,
        };

        return Attribute::make(
            fn () => $thumbnail
        );
    }

    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }

    public function relatable(): MorphTo
    {
        return $this->morphTo();
    }
}
