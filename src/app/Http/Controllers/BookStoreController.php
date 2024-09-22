<?php

namespace App\Http\Controllers;

use App\Models\BookStore;
use Inertia\Inertia;

class BookStoreController extends Controller
{
    public function index()
    {
        $stores = BookStore::all();

        return Inertia::render('BookStores/Index', [
            'stores' => $stores,
        ]);
    }
}
