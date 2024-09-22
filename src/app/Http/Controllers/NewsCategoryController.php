<?php

namespace App\Http\Controllers;

use App\Models\NewsCategory;
use App\Models\Site;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class NewsCategoryController extends Controller
{
    public function index(Site $site): Response
    {
        return Inertia::render('NewsCategories/Index', [
            'site' => $site->load('newsCategories'),
        ]);
    }

    public function create(Site $site): Response
    {
        return Inertia::render('NewsCategories/Create', [
            'site' => $site,
        ]);
    }

    public function store(Request $request, Site $site): RedirectResponse
    {
        $unique = Rule::unique(NewsCategory::class)->where('site_id', $site->id);
        $attributes = $request->validate([
            'name' => ['required', 'string', 'max:255', $unique],
        ]);
        $site->newsCategories()->create($attributes);

        return to_route('sites.news-categories.index', $site);
    }

    public function edit(NewsCategory $newsCategory): Response
    {
        return Inertia::render('NewsCategories/Edit', [
            'category' => $newsCategory->load('site'),
        ]);
    }

    public function update(Request $request, NewsCategory $newsCategory): RedirectResponse
    {
        $unique = Rule::unique(NewsCategory::class)->where('site_id', $newsCategory->site_id);
        $attributes = $request->validate([
            'name' => ['required', 'string', 'max:255', $unique->ignore($newsCategory)],
        ]);
        $newsCategory->update($attributes);

        return to_route('sites.news-categories.index', $newsCategory->site);
    }

    public function destroy(NewsCategory $newsCategory): RedirectResponse
    {
        if ($newsCategory->news->count() > 0) {
            return back()->withErrors([
                'name' => __('validation.not_in_use', ['attribute' => 'カテゴリ']),
            ]);
        }
        $newsCategory->delete();

        return to_route('sites.news-categories.index', $newsCategory->site);
    }
}
