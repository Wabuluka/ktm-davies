<?php

namespace Tests\Supports\Models;

use App\Traits\Models\Sortable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class TestScopedSortable extends Model
{
    use Sortable;

    protected $guarded = [];

    protected function sortable(): Builder
    {
        return static::query()->where('publisher_no', $this->publisher_no);
    }
}
