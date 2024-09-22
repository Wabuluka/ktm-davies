<?php

namespace App\Http\Controllers;

use App\Http\Requests\BannerRequest;
use App\Http\Resources\BannerResource;
use App\Models\Banner;
use App\Models\BannerPlacement;
use App\Models\Site;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class BannerController extends Controller
{
    /**
     * @return \Inertia\Response
     */
    public function index(BannerPlacement $bannerPlacement)
    {
        return Inertia::render('Banners/Index', [
            'placement' => $bannerPlacement,
            'creatable' => $bannerPlacement->creatable,
            'banners' => BannerResource::collection($bannerPlacement->banners),
        ]);
    }

    public function create(Site $site, BannerPlacement $bannerPlacement)
    {
        return Inertia::render('Banners/Create', [
            'site' => $site,
            'placement' => $bannerPlacement,
        ]);
    }

    public function edit(Banner $banner)
    {
        return Inertia::render('Banners/Edit', ['banner' => new BannerResource($banner)]);
    }

    public function store(BannerPlacement $bannerPlacement, BannerRequest $request)
    {
        if (! $bannerPlacement->creatable) {
            $message = "「{$bannerPlacement->name}」バナーは{$bannerPlacement->max_banner_count}件まで作成できます";

            return back()->withErrors([
                'banner_placement' => $message,
            ]);
        }

        DB::transaction(function () use (
            $request,
        ) {
            $banner = $request->toModel();
            $banner->save();

            $imageData = $request->safe()['image'];

            match ($imageData['operation']) {
                'set' => $banner->setImage($imageData['file'], $imageData['dimensions']),
                default => null,
            };
        });

        return to_route('banner-placements.banners.index', [
            'banner_placement' => $bannerPlacement->id,
        ]);
    }

    public function update(Banner $banner, BannerRequest $request)
    {
        DB::transaction(function () use (
            $banner,
            $request,
        ) {
            $newBanner = $request->toModel($banner);
            $newBanner->save();

            $imageData = $request->safe()['image'];
            match ($imageData['operation']) {
                'set' => $newBanner->setImage($imageData['file'], $imageData['dimensions']),
                default => null,
            };
        });

        return to_route('banner-placements.banners.index', [
            'banner_placement' => $banner->placement_id,
        ]);
    }

    public function destroy(Banner $banner)
    {
        $banner->delete();

        return to_route('banner-placements.banners.index', [
            'banner_placement' => $banner->placement_id,
        ]);
    }

    public function moveUp(Banner $banner)
    {
        $banner->moveUp();
    }

    public function moveDown(Banner $banner)
    {
        $banner->moveDown();
    }
}
