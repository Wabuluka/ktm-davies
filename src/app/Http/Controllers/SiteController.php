<?php

namespace App\Http\Controllers;

use Inertia\Inertia;

class SiteController extends Controller
{
    /**
     * @return \Inertia\Response
     */
    public function index()
    {
        return Inertia::render('Sites/Index');
    }
}
