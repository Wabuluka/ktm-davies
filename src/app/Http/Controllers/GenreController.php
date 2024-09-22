<?php

namespace App\Http\Controllers;

use App\Http\Requests\GenreRequest;
use App\Models\Genre;
use App\Traits\Models\Sortable;

class GenreController extends Controller
{
    use Sortable;

    public function store(GenreRequest $request)
    {
        Genre::create($request->validated());

        return back()->with(['message' => 'Saved successfully', 'status' => 'success']);
    }

    public function update(GenreRequest $request, Genre $genre)
    {
        $genre->update([...$request->validated()]);

        return back()->with(['message' => 'Saved successfully', 'status' => 'success']);
    }

    public function destroy(Genre $genre)
    {
        if ($genre->books()->exists()) {
            return back()->withErrors(['name' => 'このジャンルには関連する本が存在します。']);
        }

        $genre->delete();

        return back()->with(['message' => 'Deleted successfully', 'status' => 'success']);
    }

    public function moveUp(Genre $genre)
    {
        $genre->moveUp();

        return back()->with(['message' => '表示順をSaved successfully', 'status' => 'success']);
    }

    public function moveDown(Genre $genre)
    {
        $genre->moveDown();

        return back()->with(['message' => '表示順をSaved successfully', 'status' => 'success']);
    }

    public function sort(Genre $genre, GenreRequest $request)
    {
        $genre->updateSortingOrder($request);
    }
}
