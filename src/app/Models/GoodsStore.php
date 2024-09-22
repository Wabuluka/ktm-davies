<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GoodsStore extends Model
{
    use HasFactory;

    protected $with = ['store'];

    public static function boot()
    {
        parent::boot();

        static::addGlobalScope('default_order', fn ($builder) => $builder->orderBy('store_id'));
    }

    public function benefits(): HasMany
    {
        return $this->hasMany(Benefit::class, 'store_id');
    }

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }
}
