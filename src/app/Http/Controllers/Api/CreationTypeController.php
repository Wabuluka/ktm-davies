<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreationTypeRequest;
use App\Models\CreationType;

class CreationTypeController extends Controller
{
    public function index()
    {
        return CreationType::all();
    }

    public function store(CreationTypeRequest $request)
    {
        $creationType = CreationType::create($request->validated());

        return $creationType;
    }

    public function update(CreationTypeRequest $request, CreationType $creationType)
    {
        $creationType->update($request->validated());

        return $creationType;
    }

    public function destroy(CreationType $creationType)
    {
        if ($creationType->creators()->exists()) {
            return response()->json([
                'message' => __('validation.not_in_use', ['attribute' => '作家区分']),
                'errors' => ['creationType' => [__('validation.not_in_use', ['attribute' => '作家区分'])]],
            ], 422);
        }

        $creationType->delete();
    }

    public function moveUp(CreationType $creationType)
    {
        $creationType->moveUp();

        return $creationType;
    }

    public function moveDown(CreationType $creationType)
    {
        $creationType->moveDown();

        return $creationType;
    }
}
