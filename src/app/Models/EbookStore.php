<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class EbookStore extends Model implements HasMedia
{
    use HasFactory;
    use InteractsWithMedia;

    protected $with = ['store'];

    public static function boot()
    {
        parent::boot();

        static::addGlobalScope('default_order', fn ($builder) => $builder->orderBy('store_id'));
    }

    public function books(): BelongsToMany
    {
        return $this->belongsToMany(Book::class)->withPivot('url', 'is_primary');
    }

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    public function registerMediaCollections(): void
    {
        $this
            ->addMediaCollection('banner')
            ->singleFile();
    }

    protected function banner(): Attribute
    {
        return Attribute::make(
            fn () => $this->getFirstMedia('banner')
        );
    }
}
