<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\SeriesRequest;
use App\Models\Series;
use Illuminate\Http\Request;

class SeriesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Inertia\Response
     */
    public function index(Request $request)
    {
        $name = $request->query('name');
        $query = Series::query();

        if ($name) {
            $query->where('name', 'LIKE', '%' . $name . '%');
        }

        $series = $query->paginate(10);

        return response()->json($series);
    }

    public function show(Series $series)
    {
        return response()->json($series);
    }

    public function store(SeriesRequest $request)
    {
        Series::create($request->validated());
    }

    public function update(SeriesRequest $request, Series $series)
    {
        $series->fill($request->validated())->save();
    }

    public function destroy(Series $series)
    {
        if ($series->books()->exists()) {
            return response()->json([
                'message' => __('validation.not_in_use', ['attribute' => 'シリーズ']),
                'errors' => ['series' => [__('validation.not_in_use', ['attribute' => 'シリーズ'])]],
            ], 422);
        }

        $series->delete();
    }

    public function moveUp(Series $series)
    {
        $series->moveUp();
    }

    public function moveDown(Series $series)
    {
        $series->moveDown();
    }
}
