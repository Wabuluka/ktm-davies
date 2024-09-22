<?php

namespace App\Models;

use App\Traits\Models\IsPreviewModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookPreview extends Model
{
    use HasFactory;
    use IsPreviewModel;

    protected $hasPreviewFiles = true;

    protected $fillable = [
        'model', 'token', 'file_paths',
    ];

    protected $casts = [
        'file_paths' => 'array',
    ];
}
