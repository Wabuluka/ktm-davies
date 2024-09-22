<?php

namespace App\Models;

use App\Traits\Models\IsPreviewModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PagePreview extends Model
{
    use HasFactory;
    use IsPreviewModel;

    protected $fillable = [
        'model', 'token',
    ];
}
