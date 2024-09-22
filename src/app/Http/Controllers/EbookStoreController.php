<?php

namespace App\Http\Controllers;

use App\Models\EbookStore;
use Inertia\Inertia;

class EbookStoreController extends Controller
{
    public function index()
    {
        $stores = EbookStore::all();

        return Inertia::render('EbookStores/Index', [
            'stores' => $stores,
        ]);
    }
}
