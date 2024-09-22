<?php

namespace App\Models;

use App\Traits\Models\Sortable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Genre extends Model
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

    public function labels()
    {
        return $this->hasMany(Label::class);
    }
}
