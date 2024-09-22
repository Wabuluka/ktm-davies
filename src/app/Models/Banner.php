<?php

namespace App\Models;

use App\Traits\Models\Sortable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Banner extends Model implements HasMedia
{
    use HasFactory;
    use InteractsWithMedia;
    use Sortable;

    protected $fillable = [
        'name',
        'url',
        'new_tab',
        'displayed',
        'placement_id',
    ];

    public static function boot()
    {
        parent::boot();

        static::addGlobalScope('default_order', fn ($builder) => $builder->orderBy('sort'));
    }

    protected function sortable(): Builder
    {
        return static::query()->where('placement_id', $this->placement_id);
    }

    public function placement()
    {
        return $this->belongsTo(BannerPlacement::class, 'placement_id');
    }

    /**
     * 表示 ON のバナーのみに絞り込む
     */
    public function scopeDisplayed(Builder $query): Builder
    {
        return $query->where('displayed', true);
    }

    /**
     * 画像を返却する
     */
    protected function image(): Attribute
    {
        return Attribute::make(
            fn () => $this->getFirstMedia('image')
        );
    }

    /**
     * 画像を設定する
     */
    public function setImage(UploadedFile $image, array $customProperties = []): static
    {
        $this
            ->addMedia($image)
            ->withCustomProperties($customProperties)
            ->toMediaCollection('image');

        return $this;
    }

    /**
     * 画像は一つだけ登録できるようにする。
     */
    public function registerMediaCollections(): void
    {
        $this
            ->addMediaCollection('image')
            ->singleFile();
    }
}
