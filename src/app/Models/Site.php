<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Site extends Model implements HasMedia
{
    use HasFactory;
    use InteractsWithMedia;

    protected $fillable = [
        'name',
        'url',
        'book_preview_path',
        'news_preview_path',
        'page_preview_path',
    ];

    protected function logo(): Attribute
    {
        return Attribute::make(
            fn () => $this->getFirstMedia()
        );
    }

    public function bannerPlacements()
    {
        return $this->hasMany(BannerPlacement::class);
    }

    public function banners()
    {
        return $this->through('bannerPlacements')->has('banners');
    }

    public function books()
    {
        return $this->belongsToMany(Book::class);
    }

    public function news()
    {
        return $this->through('newsCategories')->has('news');
    }

    public function newsCategories()
    {
        return $this->hasMany(NewsCategory::class);
    }

    public function pages()
    {
        return $this->hasMany(Page::class);
    }
}
