<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ExternalLink\ExternalLinkRequest;
use App\Http\Requests\ExternalLink\IndexExternalLinkRequest;
use App\Http\Resources\ExternalLinkResource;
use App\Models\ExternalLink;
use Illuminate\Support\Facades\DB;

class ExternalLinkController extends Controller
{
    public function index(IndexExternalLinkRequest $request)
    {
        $paginator = ExternalLink::with('media')
            ->when($request->get('keywords'), fn ($query, $keywords) => $query
                ->where(function ($query) use ($keywords) {
                    foreach ($keywords as $keyword) {
                        $query->where(fn ($query) => $query
                            ->where('title', 'like', "%{$keyword}%")
                            ->orWhere('url', 'like', "%{$keyword}%"));
                    }
                }))
            ->latest()
            ->paginate(10);

        return ExternalLinkResource::collection($paginator);
    }

    public function show(ExternalLink $externalLink)
    {
        return new ExternalLinkResource($externalLink);
    }

    public function store(ExternalLinkRequest $request)
    {
        DB::transaction(function () use (
            $request,
        ) {
            $link = $request->toModel();
            $link->save();

            $thumbnailData = $request->safe()['thumbnail'];
            match ($thumbnailData['operation']) {
                'set' => $link->setThumbnail($thumbnailData['file'], $thumbnailData['dimensions']),
                default => null,
            };
        });
    }

    public function update(ExternalLinkRequest $request, ExternalLink $externalLink)
    {
        DB::transaction(function () use (
            $externalLink,
            $request,
        ) {
            $link = $request->toModel($externalLink);
            $link->save();

            $thumbnailData = $request->safe()['thumbnail'];
            match ($thumbnailData['operation']) {
                'set' => $link->setThumbnail($thumbnailData['file'], $thumbnailData['dimensions']),
                'delete' => $link->deleteThumbnail(),
                default => null,
            };
        });
    }

    public function destroy(ExternalLink $externalLink)
    {
        if ($externalLink->relatedItems()->exists()) {
            return response()->json([
                'message' => __('validation.not_in_use', ['attribute' => '他社作品']),
                'errors' => ['externalLink' => [__('validation.not_in_use', ['attribute' => '他社作品'])]],
            ], 422);
        }
        $externalLink->delete();
    }
}
