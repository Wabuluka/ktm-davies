<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Creator extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'name_kana',
    ];

    public function books()
    {
        return $this->belongsToMany(Book::class, 'book_creations')
            ->as('creation')
            ->using(BookCreation::class)
            ->orderByPivot('sort')
            ->withPivot(['creation_type', 'displayed_type', 'sort']);
    }

    public function stories()
    {
        return $this->belongsToMany(Story::class);
    }

    public function creationTypes()
    {
        return $this->belongsToMany(CreationType::class, 'book_creations');
    }
}
