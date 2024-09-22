<?php

namespace App\Http\Controllers;

use App\Enums\NewsStatus;
use App\Http\Requests\News\IndexNewsRequest;
use App\Http\Requests\News\StoreNewsRequest;
use App\Http\Requests\News\UpdateNewsRequest;
use App\Http\Resources\NewsResource;
use App\Models\News;
use App\Models\Site;
use App\Traits\Http\HandleRequestParameters;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class NewsController extends Controller
{
    use HandleRequestParameters;

    public function index(IndexNewsRequest $request, Site $site): Response
    {
        $params = $request->makeParameters();
        $paginator = $site->news()
            ->with('category')
            ->when($params->get('keywords'), fn ($query, $keywords) => $query
                ->where(function ($query) use ($keywords) {
                    foreach ($keywords as $keyword) {
                        $escaped = addcslashes($keyword, '%_\\');
                        $query->where(fn ($query) => $query
                            ->where(
                                'news.id', 'like', "{$escaped}%"
                            )->orwhere(
                                'title', 'like', "%{$escaped}%"
                            )->orWhere(
                                'slug', 'like', "{$escaped}%"
                            )->orWhereHas('category', fn ($query) => $query->where(
                                'name', 'like', "{$escaped}%"
                            ))
                        );
                    }
                })
            )
            ->when($params->get('statuses'), fn ($query, $statuses) => $query
                ->where(function ($query) use ($statuses) {
                    foreach ($statuses as $status) {
                        match ($status) {
                            NewsStatus::Draft => $query->orWhere(fn ($query) => $query->draft()),
                            NewsStatus::WillBePublished => $query->orWhere(fn ($query) => $query->willBePublished()),
                            NewsStatus::Published => $query->orWhere(fn ($query) => $query->published()),
                        };
                    }
                })
            )
            ->orderByDesc('published_at')
            ->paginate(10);

        return Inertia::render('News/Index', [
            'newsPaginator' => NewsResource::collection($paginator),
            'site' => $site,
        ]);
    }

    public function create(Site $site): Response
    {
        return Inertia::render('News/Create', [
            'site' => $site->load('newsCategories'),
        ]);
    }

    public function store(StoreNewsRequest $request, Site $site): RedirectResponse
    {
        DB::transaction(function () use ($request) {
            $news = $request->makeNews();
            $news->save();
            if ($request->safe()->has('eyecatch')) {
                $eyecatch = $request->safe()->eyecatch;
                $customProperties = $this->getImageDimensions($eyecatch);
                $news->setEyecatch($eyecatch, $customProperties);
            }
        });

        return to_route('sites.news.index', $site);
    }

    public function edit(News $news): Response
    {
        return Inertia::render('News/Edit', [
            'news' => $news
                ->load(['category.site.newsCategories', 'media'])
                ->append(['status', 'eyecatch']),
        ]);
    }

    public function update(UpdateNewsRequest $request, News $news): RedirectResponse
    {
        DB::transaction(function () use ($request, $news) {
            $request->fillNews($news)->save();
            if ($request->safe()->has('eyecatch')) {
                if ($eyecatch = $request->safe()->eyecatch) {
                    $customProperties = $this->getImageDimensions($eyecatch);
                    $news->setEyecatch($eyecatch, $customProperties);
                } else {
                    $news->deleteEyecatch();
                }
            }
        });

        return to_route('sites.news.index', $request->site);
    }

    public function destroy(News $news): RedirectResponse
    {
        $site = $news->category->site;
        $news->delete();

        return to_route('sites.news.index', $site);
    }
}
