<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\GenreRequest;
use App\Models\Genre;
use Illuminate\Http\Request;

class GenreController extends Controller
{
    public function index(Request $request)
    {
        $name = $request->query('name');
        $query = Genre::query();

        if ($name) {
            $query->where('name', 'LIKE', '%' . $name . '%');
        }

        $genre = $query->get();

        return response()->json($genre);
    }

    public function show(Genre $genre)
    {
        return response()->json($genre);
    }

    public function store(GenreRequest $request)
    {
        Genre::create($request->validated());

        return back()->with(['message' => 'Saved successfully', 'status' => 'success']);
    }

    public function update(GenreRequest $request, Genre $genre)
    {
        $genre->update([...$request->validated()]);
    }

    public function destroy(Genre $genre)
    {
        if ($genre->books()->exists()) {
            return response()->json([
                'message' => __('validation.not_in_use', ['attribute' => 'ジャンル']),
                'errors' => ['genre' => [__('validation.not_in_use', ['attribute' => 'ジャンル'])]],
            ], 422);
        }

        $genre->delete();
    }

    public function moveUp(Genre $genre)
    {
        $genre->moveUp();
    }

    public function moveDown(Genre $genre)
    {
        $genre->moveDown();
    }

    public function dragAndDrop(Request $request)
    {
        $ids = $request->input('ids');
        $sort = 1;
        foreach ($ids as $id) {
            Genre::where('id', $id)->update(['sort' => $sort]);
            $sort++;
        }
    }
}
