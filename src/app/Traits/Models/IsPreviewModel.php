<?php

namespace App\Traits\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Facades\Storage;

trait IsPreviewModel
{
    public static function bootIsPreviewModel()
    {
        static::deleting(function ($model) {
            if ($model->hasPreviewFiles ?? false) {
                $model->deleteAllFiles();
            }
        });
    }

    public function deleteAllFiles(): void
    {
        Storage::disk('public')->delete($this->file_paths ?? []);
    }

    protected function model(): Attribute
    {
        return new Attribute(
            get: fn ($value) => unserialize($value),
            set: fn ($value) => serialize($value),
        );
    }
}
