<?php

namespace Tests\Feature\Http\Controllers\Api\PagePreview;

use App\Models\Page;
use App\Models\PagePreview;
use App\Models\Site;
use Tests\TestCase;

class PagePreviewTest extends TestCase
{
    private Site $site;

    private array $requestData;

    protected function setUp(): void
    {
        parent::setUp();
        $this->site = Site::factory()->create([
            'url' => 'https://example.com',
            'page_preview_path' => 'page/preview/[token]?slug=[slug]',
        ]);
        $this->requestData = [
            'title' => 'プライバシーポリシー',
            'slug' => 'privacy',
            'content' => '<h1>プライバシーポリシー</h1>',
        ];
    }

    private function postTo(Page $page = null): string
    {
        return route('api.sites.pages.preview', [
            'site' => $this->site,
            'page' => $page,
        ]);
    }

    /** @test */
    public function ログインしたユーザーのみリクエストが成功すること(): void
    {
        $this
            ->postJson($this->postTo())
            ->assertUnauthorized();

        $this->login()
            ->postJson($this->postTo(), $this->requestData)
            ->assertOk();
    }

    /** @test */
    public function 不正なリクエストに対してバリデーションエラーが発生すること(): void
    {
        $this->login()
            ->postJson($this->postTo(), ['title' => str_repeat('a', 256)] + $this->requestData)
            ->assertUnprocessable();
    }

    /** @test */
    public function プレビューURLを返却すること(): void
    {
        $this->login()
            ->postJson($this->postTo(), $this->requestData)
            ->assertJsonPath('preview.url', fn ($value) => true
                && str_starts_with($value, $start = 'https://example.com/page/preview/')
                && str_ends_with($value, $end = '?slug=privacy')
                && str($value)->remove($start)->remove($end)->isUuid()
            );
    }

    /** @test */
    public function Pageのアトリビュートがプレビューに含まれること(): void
    {
        $this->login()
            ->postJson($this->postTo(), $attributes = [
                'title' => 'プライバシーに関する声明',
                'slug' => 'privacy',
                'content' => '<h1>プライバシーに関する声明</h1>',
            ])
            ->assertOk();

        $preview = PagePreview::latest()->first();
        $actual = $preview->model->only(array_keys($attributes));
        $this->assertSame($attributes, $actual);
    }

    /** @test */
    public function Pageのアトリビュートの更新がプレビューに反映されること(): void
    {
        $page = Page::factory()->for($this->site)->create();

        $this->login()
            ->postJson($this->postTo($page), $attributes = [
                'title' => 'プライバシーに関する生命',
                'slug' => 'privacy',
                'content' => '<h1>プライバシーに関する生命</h1>',
            ])
            ->assertOk();

        $preview = PagePreview::latest()->first();
        $this->assertSame([
            'title' => $attributes['title'],
            'slug' => $attributes['slug'],
            'content' => $attributes['content'],
        ], $preview->model->only([
            'title',
            'slug',
            'content',
        ]));
    }
}
