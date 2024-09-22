<?php

namespace App\Http\Controllers;

use Inertia\Inertia;

class BookTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Inertia\Response
     */
    public function index()
    {
        return Inertia::render('BookTypes/Index');
    }
}
