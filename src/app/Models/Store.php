<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Store extends Model implements HasMedia
{
    use HasFactory;
    use InteractsWithMedia;

    protected $fillable = [
        'name',
        'url',
    ];

    public function scopeBookstores(Builder $query): Builder
    {
        return $query->has('bookStore');
    }

    public function scopeEBookstores(Builder $query): Builder
    {
        return $query->has('ebookStore');
    }

    public function scopeGoodsStores(Builder $query): Builder
    {
        return $query->has('goodsStore');
    }

    public function bookStore(): HasOne
    {
        return $this->hasOne(BookStore::class);
    }

    public function ebookStore(): HasOne
    {
        return $this->hasOne(EbookStore::class);
    }

    public function goodsStore(): HasOne
    {
        return $this->hasOne(GoodsStore::class);
    }
}
