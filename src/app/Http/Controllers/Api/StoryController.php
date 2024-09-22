<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoryRequest;
use App\Http\Resources\StoryResource;
use App\Models\Story;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Inertia\Response
     */
    public function index(Request $request)
    {
        $title = $request->query('title');
        $query = Story::with('creators');

        if ($title) {
            $query->where('title', 'LIKE', '%' . $title . '%');
        }

        $page = $request->query('page');
        $stories = $query->paginate(10, ['*'], 'page', $page);

        return response()->json([StoryResource::collection($stories), $stories->lastPage()]);
    }

    public function show(Story $story)
    {
        return new StoryResource($story->load('creators'));
    }

    public function store(StoryRequest $request)
    {
        DB::transaction(function () use (
            $request,
        ) {
            $story = $request->toModel();
            $story->save();

            if ($request->shouldSyncCreators()) {
                $story->creators()->sync($request->makeCreatorsSyncData());
            }

            $thumbnailData = $request->safe()['thumbnail'];
            match ($thumbnailData['operation']) {
                'set' => $story->setThumbnail($thumbnailData['file'], $thumbnailData['dimensions']),
                default => null,
            };
        });
    }

    public function update(StoryRequest $request, Story $story)
    {
        DB::transaction(function () use (
            $story,
            $request,
        ) {
            $newStory = $request->toModel($story);
            $newStory->save();

            if ($request->shouldSyncCreators()) {
                $story->creators()->sync($request->makeCreatorsSyncData());
            }

            $thumbnailData = $request->safe()['thumbnail'];
            match ($thumbnailData['operation']) {
                'set' => $newStory->setThumbnail($thumbnailData['file'], $thumbnailData['dimensions']),
                'delete' => $newStory->deleteThumbnail(),
                default => null,
            };
        });
    }

    public function destroy(Story $story)
    {
        if ($story->books()->exists()) {
            return response()->json([
                'message' => __('validation.not_in_use', ['attribute' => '収録作品']),
                'errors' => ['story' => [__('validation.not_in_use', ['attribute' => '収録作品'])]],
            ], 422);
        }

        $story->delete();
    }
}
