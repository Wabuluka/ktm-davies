<?php

namespace App\Models;

use App\Enums\BlockType;
use App\Traits\Models\Sortable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Block extends Model
{
    use HasFactory;
    use Sortable;

    public $timestamps = false;

    protected $fillable = [
        'type_id',
        'custom_title',
        'custom_content',
        'sort',
        'displayed',
    ];

    protected $casts = [
        'type_id' => BlockType::class,
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
     * ブロックの種別を返却する
     */
    protected function type(): Attribute
    {
        return Attribute::make(
            fn () => $this->type_id,
        );
    }

    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }

    public function scopeDisplayed(Builder $query): Builder
    {
        return $query->where('displayed', true);
    }
}
