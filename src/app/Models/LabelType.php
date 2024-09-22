<?php

namespace App\Models;

use App\Enums\LabelType as EnumsLabelType;
use App\Traits\Models\Sortable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LabelType extends Model
{
    use HasFactory;
    use Sortable;

    public $timestamps = false;

    protected $fillable = [
        'name', 'sort', 'is_default',
    ];

    public static function boot()
    {
        parent::boot();

        static::addGlobalScope('default_order', fn ($builder) => $builder->orderBy('sort'));
    }

    public function labels()
    {
        return $this->belongsToMany(Label::class);
    }

    public function toEnum(): EnumsLabelType
    {
        return EnumsLabelType::fromModel($this);
    }
}
