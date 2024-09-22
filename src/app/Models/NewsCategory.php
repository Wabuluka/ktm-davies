<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NewsCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope('default_order', fn (Builder $builder) => $builder->orderBy('id'));
    }

    public function news()
    {
        return $this->hasMany(News::class, 'category_id');
    }

    public function site()
    {
        return $this->belongsTo(Site::class);
    }
}
