<?php

namespace Tests\Supports\Models;

use App\Traits\Models\Sortable;
use Illuminate\Database\Eloquent\Model;

class TestSortable extends Model
{
    use Sortable;

    protected $guarded = [];
}
