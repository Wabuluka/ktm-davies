<?php

namespace App\Http\Controllers;

use App\Models\GoodsStore;
use Inertia\Inertia;

class GoodsStoreController extends Controller
{
    public function index()
    {
        $stores = GoodsStore::all();

        return Inertia::render('GoodsStores/Index', [
            'stores' => $stores,
        ]);
    }
}
