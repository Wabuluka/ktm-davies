<?php

namespace App\Http\Controllers;

use App\Http\Requests\PageRequest;
use App\Models\Page;
use App\Models\Site;
use Inertia\Inertia;

class PageController extends Controller
{
    /**
     * Show the form for editing the specified resource.
     *
     * @return \Inertia\Response
     */
    public function edit(Site $site, Page $page)
    {
        return Inertia::render('Pages/Edit', [
            'site' => $site,
            'page' => $page,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(PageRequest $request, Site $site, Page $page)
    {
        $request->makePage($page)->save();
    }
}
