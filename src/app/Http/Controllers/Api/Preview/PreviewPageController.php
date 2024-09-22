<?php

namespace App\Http\Controllers\Api\Preview;

use App\Http\Controllers\Controller;
use App\Http\Requests\PageRequest;
use App\Models\Page;
use App\Models\PagePreview;
use App\Models\Site;
use App\Traits\Http\HandleRequestParameters;
use Illuminate\Support\Str;

class PreviewPageController extends Controller
{
    use HandleRequestParameters;

    public function __invoke(PageRequest $request, Site $site, ?Page $page)
    {
        if (! $site->page_preview_path) {
            throw new \Exception('サイトに固定ページプレビュー用のパスが設定されていません。');
        }

        $page = $request->makePage($page);
        $token = Str::uuid();

        PagePreview::create([
            'token' => $token,
            'model' => $page,
        ]);

        return response()->json([
            'preview' => [
                'url' => $this->generatePreviewUrl($site, $token, $page->slug),
            ],
        ]);
    }

    private function generatePreviewUrl(Site $site, string $token, string $slug): string
    {
        $baseUrl
            = config("preview.{$site->code}.base_url") ?: $site->url;
        $path
            = str($site->page_preview_path)->replace('[token]', $token)->replace('[slug]', $slug);
        $previewUrl
            = str($baseUrl)->finish('/')->append($path);

        return $previewUrl;
    }
}
