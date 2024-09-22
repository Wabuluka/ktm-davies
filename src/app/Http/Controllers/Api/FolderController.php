<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\FolderCreateRequest;
use App\Http\Requests\FolderUpdateRequest;
use App\Models\Folder;
use Illuminate\Http\Request;

class FolderController extends Controller
{
    public function index(Request $request)
    {
        $mimeType = $request->mimeType;
        $rootFolder = Folder::whereNull('parent_id')->firstOrFail();
        $fileSystemEntities = self::getFileSystemEntities($rootFolder, $mimeType);

        return $fileSystemEntities;
    }

    public function store(FolderCreateRequest $request)
    {
        $validatedData = $request->validated();

        Folder::create($validatedData);
    }

    public function show(Folder $folder, Request $request)
    {
        $mimeType = $request->mimeType;
        $fileSystemEntities = self::getFileSystemEntities($folder, $mimeType);

        return $fileSystemEntities;
    }

    public function update(Folder $folder, FolderUpdateRequest $request)
    {
        $folder->update(['name' => $request->name]);
    }

    public function destroyMany(Request $request)
    {
        $folderIds = $request->input('folderIds');

        Folder::destroy($folderIds);
    }

    private function getFileSystemEntities(Folder $targetFolder, string $mimeType = null)
    {
        $fileSystemEntities = [];

        $folders = $targetFolder->children;
        $folderIdPrefix = 'folder_';
        foreach ($folders as $folder) {
            array_push($fileSystemEntities, [
                'id' => $folderIdPrefix . $folder->id,
                'name' => $folder->name,
                'isDir' => true,
                'modDate' => $folder->updated_at,
            ]);
        }

        $files = $targetFolder->getmedia();
        if ($mimeType != null) {
            $files = $files->filter(function ($media) use ($mimeType) {
                return strpos($media->mime_type, $mimeType) === 0;
            });
        }

        $fileIdPrefix = 'files_';
        foreach ($files as $file) {
            array_push($fileSystemEntities, [
                'id' => $fileIdPrefix . $file->id,
                'name' => $file->name,
                'ext' => pathinfo($file->name, PATHINFO_EXTENSION),
                'isDir' => false,
                'size' => $file->size,
                'modDate' => $file->updated_at,
                'thumbnailUrl' => $file->hasGeneratedConversion('thumbnail') ? $file->getUrl('thumbnail') : '',
                'src' => $file->getUrl(),
            ]);
        }

        return $fileSystemEntities;
    }
}
