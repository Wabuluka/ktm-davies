<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CharacterRequest;
use App\Http\Resources\CharacterResource;
use App\Models\Character;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CharacterController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Inertia\Response
     */
    public function index(Request $request)
    {
        $name = $request->query('name');
        $series_id = $request->query('seriesId');
        $query = Character::query();

        if ($name) {
            $query->where('name', 'LIKE', '%' . $name . '%');
        }
        if ($series_id) {
            $query->where('series_id', $series_id);
        }

        $page = $request->query('page');
        $characters = $query->with('series')->paginate(10, ['*'], 'page', $page);

        return response()->json([CharacterResource::collection($characters), $characters->lastPage()]);

    }

    public function show(Character $character)
    {
        $character->load('series');

        return new CharacterResource($character);
    }

    public function store(CharacterRequest $request)
    {
        DB::transaction(function () use (
            $request,
        ) {
            $character = $request->toModel();
            $character->save();
            $thumbnailData = $request->safe()['thumbnail'];
            match ($thumbnailData['operation']) {
                'set' => $character->setThumbnail($thumbnailData['file'], $thumbnailData['dimensions']),
                default => null,
            };
        });
    }

    public function update(CharacterRequest $request, Character $character)
    {
        DB::transaction(function () use (
            $character,
            $request,
        ) {
            $newCharacter = $request->toModel($character);
            $newCharacter->save();

            $thumbnailData = $request->safe()['thumbnail'];
            match ($thumbnailData['operation']) {
                'set' => $newCharacter->setThumbnail($thumbnailData['file'], $thumbnailData['dimensions']),
                'delete' => $newCharacter->deleteThumbnail(),
                default => null,
            };
        });
    }

    public function destroy(Character $character)
    {
        if ($character->books()->exists()) {
            return response()->json([
                'message' => __('validation.not_in_use', ['attribute' => 'キャラクター']),
                'errors' => ['character' => [__('validation.not_in_use', ['attribute' => 'キャラクター'])]],
            ], 422);
        }

        $character->delete();
    }
}
