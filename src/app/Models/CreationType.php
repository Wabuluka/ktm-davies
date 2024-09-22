<?php

namespace App\Models;

use App\Traits\Models\Sortable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CreationType extends Model
{
    use HasFactory;
    use Sortable;

    protected $primaryKey = 'name';

    protected $keyType = 'string';

    public $incrementing = false;

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

    public function getRouteKeyName()
    {
        return 'name';
    }

    public function creators()
    {
        return $this->belongsToMany(Creator::class, 'book_creations', 'creation_type');
    }
}
