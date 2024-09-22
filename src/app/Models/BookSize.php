<?php

namespace App\Models;

use App\Traits\Models\Sortable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookSize extends Model
{
    use HasFactory;
    use Sortable;

    public $timestamps = false;

    protected $fillable = [
        'name',
        'sort',
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
}
