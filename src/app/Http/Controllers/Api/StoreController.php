<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Store;
use Illuminate\Http\Request;

class StoreController extends Controller
{
    /**
     * @return \Inertia\Response
     */
    public function index(Request $request)
    {
        $name = $request->query('name');
        $query = Store::query();

        if ($name) {
            $query->where('name', 'LIKE', '%' . $name . '%');
        }

        $page = $request->query('page');
        $stores = $query->with('types')->paginate(10, ['*'], 'page', $page);

        return response()->json([$stores->items(), $stores->lastPage()]);
    }

    public function show(Store $store)
    {
        return response()->json($store);
    }
}
