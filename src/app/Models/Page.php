<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'slug',
        'content',
        'site_id',
    ];

    public function site()
    {
        return $this->belongsTo(Site::class);
    }

    /**
     * 公開サイトで絞り込む
     */
    public function scopePublishedAtSite(Builder $query, int|string $siteId): Builder
    {
        return $query->whereHas('site', fn ($query) => $query->whereId($siteId));
    }
}
