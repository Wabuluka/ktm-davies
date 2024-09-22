<?php

namespace App\Http\Controllers\Api\Preview;

use App\Http\Controllers\Controller;
use App\Http\Requests\News\PreviewNewsRequest;
use App\Models\News;
use App\Models\NewsPreview;
use App\Models\Site;
use App\Traits\Http\HandleRequestParameters;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PreviewNewsController extends Controller
{
    use HandleRequestParameters;

    public function __invoke(PreviewNewsRequest $request, Site $site, ?News $news)
    {
        if (! $site->news_preview_path) {
            throw new \Exception('サイトに News プレビュー用のパスが設定されていません。');
        }

        $news->id ??= 0;
        $news = $request->makeNews($news)->load('category');
        $token = Str::uuid();
        $eyecatchPath = $this->setAndStorePreviewEyecatch($news, $request);

        NewsPreview::create([
            'token' => $token,
            'model' => $news,
            'file_paths' => Arr::wrap($eyecatchPath),
        ]);

        return response()->json([
            'preview' => [
                'url' => $this->generatePreviewUrl($site, $token),
            ],
        ]);
    }

    private function setAndStorePreviewEyecatch(News $news, PreviewNewsRequest $request): ?string
    {
        if (! $request->safe()->has('eyecatch')) {
            $news->preview_eyecatch = $news->eyecatch;

            return null;
        }

        if (! ($eyecatch = $request->safe()->eyecatch)) {
            $news->preview_eyecatch = null;

            return null;
        }

        $path = $eyecatch->store('tmp/preview/news/eyecatch', 'public');
        $news->preview_eyecatch = [
            'original_url' => Storage::disk('public')->url($path),
            'custom_properties' => $this->getImageDimensions($eyecatch),
        ];

        return $path;
    }

    private function generatePreviewUrl(Site $site, string $token): string
    {
        $baseUrl
            = config("preview.{$site->code}.base_url") ?: $site->url;
        $path
            = str($site->news_preview_path)->replace('[token]', $token);
        $previewUrl
            = str($baseUrl)->finish('/')->append($path);

        return $previewUrl;
    }
}
