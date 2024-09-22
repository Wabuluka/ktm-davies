<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Creator\IndexCreatorRequest;
use App\Http\Requests\CreatorRequest;
use App\Models\Creator;

class CreatorController extends Controller
{
    public function index(IndexCreatorRequest $request)
    {
        $params = $request->toParameteres();

        return Creator::when(
            $params->get('keywords'),
            fn ($query, $keywords) => $query
                ->where(function ($query) use ($keywords) {
                    foreach ($keywords as $keyword) {
                        $escaped = addcslashes($keyword, '%_\\');
                        $query->where(fn ($query) => $query
                            ->where('name', 'like', "%{$escaped}%")
                            ->orWhere('name_kana', 'like', "%{$escaped}%"));
                    }
                }))
            ->latest()
            ->paginate(10);
    }

    public function show(Creator $creator)
    {
        return $creator;
    }

    public function store(CreatorRequest $request)
    {
        $creator = Creator::create($request->validated());

        return $creator;
    }

    public function update(CreatorRequest $request, Creator $creator)
    {
        $creator->update($request->validated());

        return $creator;
    }

    public function destroy(Creator $creator)
    {
        if ($creator->books()->exists()) {
            return response()->json([
                'message' => __('validation.not_in_use', ['attribute' => '作家']),
                'errors' => ['creator' => [__('validation.not_in_use', ['attribute' => '作家'])]],
            ], 422);
        }
        $creator->delete();
    }
}
