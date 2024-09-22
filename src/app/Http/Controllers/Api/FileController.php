<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\FileStoreRequest;
use App\Http\Requests\FileUpdateRequest;
use App\Models\Folder;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class FileController extends Controller
{
    public function store(Folder $folder, FileStoreRequest $request)
    {
        $file = $request->file('file');
        $fileName = $file->getClientOriginalName();
        $extension = $file->getClientOriginalExtension();
        $folder->addMediaFromRequest('file')->usingName($fileName)->usingFileName(Str::uuid() . '.' . $extension)->toMediaCollection();
    }

    public function update(Media $media, FileUpdateRequest $request)
    {
        $media->update(['name' => $request->name]);
    }

    public function destroyMany(Request $request)
    {
        $fileIds = $request->input('fileIds');

        Media::destroy($fileIds);
    }
}
