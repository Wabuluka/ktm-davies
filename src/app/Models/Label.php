<?php

namespace App\Models;

use App\Traits\Models\Sortable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Label extends Model
{
    use HasFactory;
    use Sortable;

    protected $guarded = [
        'id', 'created_at', 'updated_at',
    ];

    public static function boot()
    {
        parent::boot();

        static::addGlobalScope('default_order', fn ($builder) => $builder->orderBy('sort'));
    }

    public function books()
    {
        return $this->hasMany(Book::class);
    }

    public function genre()
    {
        return $this->belongsTo(Genre::class);
    }

    public function types()
    {
        return $this->belongsToMany(LabelType::class);
    }

    public function scopeHasSite(Builder $query): Builder
    {
        return $query->whereNotNull('url')->whereNot('url', '');
    }

    /**
     * @return \Illuminate\Support\Collection<\App\Enums\Label>
     */
    public function typesAsEnum()
    {
        return $this->types->map->toEnum();
    }
}
