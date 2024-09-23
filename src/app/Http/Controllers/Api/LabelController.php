<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LabelRequest;
use App\Models\Label;
use App\Traits\Models\Sortable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LabelController extends Controller
{
    use Sortable;
    /**
     * Display a listing of the resource.
     *
     * @return \Inertia\Response
     */
    public function index(Request $request)
    {
        $labels = Label::with('types')
            ->when($request->query('name'), function ($query, $name) {
                return $query->where('name', 'LIKE', '%' . $name . '%');
            })
            ->get();

        return response()->json($labels);
    }

    public function show(Label $label)
    {
        return response()->json($label->load('types'));
    }

    public function store(LabelRequest $request)
    {
        DB::transaction(function () use ($request) {
            $label = Label::create($request->safe()->except('type_ids'));
            if ($request->hasTypeIds) {
                $label->types()->sync($request->typeIds);
            }
        });
    }

    public function update(LabelRequest $request, Label $label)
    {
        DB::transaction(function () use ($request, $label) {
            $label->fill($request->safe()->except('type_ids'))->save();
            if ($request->hasTypeIds) {
                $label->types()->sync($request->typeIds);
            }
        });
    }

    public function destroy(Label $label)
    {
        if ($label->books()->exists()) {
            return response()->json([
                'message' => __('validation.not_in_use', ['attribute' => 'レーベル']),
                'errors' => ['label' => [__('validation.not_in_use', ['attribute' => 'レーベル'])]],
            ], 422);
        }

        $label->delete();
    }

    public function moveUp(Label $label)
    {
        $label->moveUp();
    }

    public function moveDown(Label $label)
    {
        $label->moveDown();
    }

    public function sort(Label $label, Request $request)
    {
        // dd($request->all());
        $label->updateSortingOrder($request);
    }
}
