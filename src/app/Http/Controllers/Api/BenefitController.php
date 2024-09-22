<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\BenefitRequest;
use App\Http\Resources\BenefitResource;
use App\Models\Benefit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BenefitController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Inertia\Response
     */
    public function index(Request $request)
    {
        $name = $request->query('name');
        $query = Benefit::query();

        if ($name) {
            $query->where('name', 'LIKE', '%' . $name . '%');
        }

        $page = $request->query('page');
        $benefits = $query->with('goodsStore')->paginate(10, ['*'], 'page', $page);

        return response()->json([BenefitResource::collection($benefits), $benefits->lastPage()]);
    }

    public function show(Benefit $benefit)
    {
        $benefit->load('goodsStore');

        return new BenefitResource($benefit);
    }

    public function store(BenefitRequest $request)
    {
        DB::transaction(function () use (
            $request,
        ) {
            $benefit = $request->toModel();
            $benefit->save();

            $thumbnailData = $request->safe()['thumbnail'];
            match ($thumbnailData['operation']) {
                'set' => $benefit->setThumbnail($thumbnailData['file'], $thumbnailData['dimensions']),
                default => null,
            };
        });
    }

    public function update(BenefitRequest $request, Benefit $benefit)
    {
        DB::transaction(function () use (
            $benefit,
            $request,
        ) {
            $newBenefit = $request->toModel($benefit);
            $newBenefit->save();

            $thumbnailData = $request->safe()['thumbnail'];
            match ($thumbnailData['operation']) {
                'set' => $newBenefit->setThumbnail($thumbnailData['file'], $thumbnailData['dimensions']),
                'delete' => $newBenefit->deleteThumbnail(),
                default => null,
            };
        });
    }

    public function destroy(Benefit $benefit)
    {
        if ($benefit->books()->exists()) {
            return response()->json([
                'message' => __('validation.not_in_use', ['attribute' => '店舗特典']),
                'errors' => ['benefit' => [__('validation.not_in_use', ['attribute' => '店舗特典'])]],
            ], 422);
        }

        $benefit->delete();
    }
}
