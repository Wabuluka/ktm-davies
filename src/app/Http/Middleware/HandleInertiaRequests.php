<?php

namespace App\Http\Middleware;

use App\Http\Resources\SiteResource;
use App\Models\BookFormat;
use App\Models\BookSize;
use App\Models\BookStore;
use App\Models\EbookStore;
use App\Models\GoodsStore;
use App\Models\LabelType;
use App\Models\Site;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Middleware;
use Tightenco\Ziggy\Ziggy;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     *
     * @return string|null
     */
    public function version(Request $request)
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return mixed[]
     */
    public function share(Request $request)
    {
        return array_merge(parent::share($request), [
            'auth' => [
                'user' => $request->user(),
            ],
            'ziggy' => function () use ($request) {
                return array_merge((new Ziggy)->toArray(), [
                    'location' => $request->url(),
                ]);
            },
            'flash' => [
                'message' => fn () => $request->session()->get('message'),
                'status' => fn () => $request->session()->get('status'),
            ],
            'master' => [
                'blockTypes' => fn () => DB::table('block_types')->get(),
                'bookFormats' => fn () => BookFormat::all(),
                'bookSizes' => fn () => BookSize::all(),
                'sites' => fn () => SiteResource::collection(Site::with('media', 'bannerPlacements')->get()),
                'bookStores' => fn () => BookStore::with('store')->get(),
                'ebookStores' => fn () => EbookStore::with('store')->get(),
                'goodsStores' => fn () => GoodsStore::with('store')->get(),
                'labelTypes' => fn () => LabelType::all(),
            ],
        ]);
    }
}
