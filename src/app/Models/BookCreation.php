<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\Pivot;

class BookCreation extends Pivot
{
    public function type(): Attribute
    {
        return Attribute::make(
            fn () => $this->displayed_type ?? $this->creation_type
        );
    }
}
