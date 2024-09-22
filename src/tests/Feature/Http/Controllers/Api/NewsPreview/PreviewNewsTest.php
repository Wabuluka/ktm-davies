<?php

namespace Tests\Feature\Http\Controllers\Api\NewsPreview;

use App\Enums\NewsStatus;
use App\Models\News;
use App\Models\NewsCategory;
use App\Models\NewsPreview;
use App\Models\Site;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PreviewNewsTest extends TestCase
{
    private NewsCategory $category;

    private array $requestData;

    protected function setUp(): void
    {
        parent::setUp();
        $this->category = NewsCategory::factory()
            ->for(Site::factory()->state([
                'url' => 'https://example.com',
                'news_preview_path' => 'news/preview/[token]',
            ]))
            ->create();
        $this->requestData = [
            'title' => 'サイトリニューアルのお知らせ',
            'status' => 'published',
            'slug' => 'site-renewal',
            'category_id' => $this->category->id,
            'content' => '<p>サイトをリニューアルしました。</p>',
        ];
    }

    private function postTo(News $news = null): string
    {
        return route('api.sites.news.preview', [
            'site' => $this->category->site,
            'news' => $news,
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
            ->postJson($this->postTo(), Arr::except($this->requestData, 'category_id'))
            ->assertUnprocessable();
    }

    /** @test */
    public function プレビューURLを返却すること(): void
    {
        $this->login()
            ->postJson($this->postTo(), $this->requestData)
            ->assertJsonPath('preview.url', function ($value) {
                $start = 'https://example.com/news/preview/';

                return str_starts_with($value, $start)
                    && str($value)->remove($start)->isUuid();
            });
    }

    /** @test */
    public function スタータスが「下書き」「公開予定」のNewsもプレビューできること(): void
    {
        $this->withoutExceptionHandling();
        $this->freezeTime();

        $this->login()
            ->postJson($this->postTo(), [
                'status' => 'draft',
            ] + $this->requestData)
            ->assertOk();
        $this->login()
            ->postJson($this->postTo(), [
                'status' => 'willBePublished',
                'published_at' => now()->addDay(),
            ] + $this->requestData)
            ->assertOk();

        [$draft, $willBePublished] = NewsPreview::latest()->take(2)->get();
        $this->assertSame(
            now()->toISOString(),
            $draft->model->published_at->toISOString(),
        );
        $this->assertSame(
            now()->addDay()->toISOString(),
            $willBePublished->model->published_at->toISOString(),
        );
    }

    /** @test */
    public function Newsのアトリビュートがプレビューに含まれること(): void
    {
        $this->login()
            ->postJson($this->postTo(), $attributes = [
                'title' => 'サイトリニューアルのお知らせ',
                'status' => 'published',
                'slug' => 'site-renewal',
                'category_id' => $this->category->id,
                'content' => '<p>サイトをリニューアルしました。</p>',
            ])
            ->assertOk();

        $preview = NewsPreview::latest()->first();
        $expected = ['status' => NewsStatus::Published] + $attributes;
        $actual = $preview->model->only(array_keys($expected));
        $this->assertSame($expected, $actual);
    }

    /** @test */
    public function Newsのアトリビュートの更新がプレビューに反映されること(): void
    {
        $this->freezeTime();
        $news = News::factory()->draft()->create(
            Arr::only($this->requestData, ['title', 'slug', 'content'])
        );

        $this->login()
            ->postJson($this->postTo($news), $attributes = [
                'title' => 'サイトリニューアルのお知らせ！！！',
                'slug' => 'renewal',
                'content' => '<p>サイトをリニューアルしました！！!</p>',
                'category_id' => $this->category->id,
                'status' => 'willBePublished',
                'published_at' => $publisheAt = now()->addDay(),
            ])
            ->assertOk();

        $preview = NewsPreview::latest()->first();
        $this->assertSame([
            'title' => $attributes['title'],
            'slug' => $attributes['slug'],
            'content' => $attributes['content'],
            'category_id' => $attributes['category_id'],
            'status' => NewsStatus::WillBePublished,
        ], $preview->model->only([
            'title',
            'slug',
            'content',
            'category_id',
            'status',
        ]));
        $this->assertSame(
            $publisheAt->toISOString(),
            $preview->model->published_at->toISOString(),
        );
    }

    /** @test */
    public function Newsカテゴリがプレビューに含まれること(): void
    {
        $this->login()
            ->postJson($this->postTo(), $this->requestData)
            ->assertOk();

        $preview = NewsPreview::latest()->first();
        $this->assertSame(
            $this->category->only(['id', 'name', 'site_id']),
            $preview->model->category->only(['id', 'name', 'site_id'])
        );
    }

    /** @test */
    public function Newsカテゴリの更新がプレビューに反映されること(): void
    {
        $news = News::factory()
            ->for(
                NewsCategory::factory()
                    ->for($this->category->site), 'category'
            )->create();

        $this->login()
            ->postJson($this->postTo($news), $this->requestData)
            ->assertOk();

        $preview = NewsPreview::latest()->first();
        $this->assertSame(
            $this->category->only(['id', 'name', 'site_id']),
            $preview->model->category->only(['id', 'name', 'site_id'])
        );
    }

    /** @test */
    public function アイキャッチ画像がプレビューに含まれること(): void
    {
        $this->login()
            ->postJson($this->postTo(), $this->requestData + [
                'eyecatch' => UploadedFile::fake()->image('eyecatch.jpg', 20, 10),
            ])
            ->assertOk();

        $preview = NewsPreview::latest()->first();
        $eyecatch = $preview->model->preview_eyecatch;
        $this->assertNotNull($eyecatch['original_url']);
        $this->assertSame(20, Arr::get($eyecatch, 'custom_properties.width'));
        $this->assertSame(10, Arr::get($eyecatch, 'custom_properties.height'));
    }

    /** @test */
    public function 選択したアイキャッチ画像がpublicストレージに保存されること(): void
    {
        $this->login()
            ->postJson($this->postTo(), $this->requestData + [
                'eyecatch' => UploadedFile::fake()->image('eyecatch.jpg', 20, 10),
            ])
            ->assertOk();

        $preview = NewsPreview::latest()->first();
        $eyecatch = $preview->model->preview_eyecatch;
        $path = 'tmp/preview/news/eyecatch/' . basename($eyecatch['original_url']);
        Storage::disk('public')->assertExists($path);
    }

    /** @test */
    public function アイキャッチ画像選択なしの既存Newsにアイキャッチ画像を選択した時、選択したアイキャッチ画像がプレビューに含まれること(): void
    {
        $news = News::factory()->create();

        $this->login()
            ->postJson($this->postTo($news), $this->requestData + [
                'eyecatch' => UploadedFile::fake()->image('eyecatch.jpg', 20, 10),
            ])
            ->assertOk();

        $preview = NewsPreview::latest()->first();
        $eyecatch = $preview->model->preview_eyecatch;
        $this->assertNotNull($eyecatch['original_url']);
        $this->assertSame(20, Arr::get($eyecatch, 'custom_properties.width'));
        $this->assertSame(10, Arr::get($eyecatch, 'custom_properties.height'));
    }

    /** @test */
    public function アイキャッチ画像選択ありの既存Newsをプレビューした時、選択済みのアイキャッチ画像がプレビューに含まれること(): void
    {
        $news = News::factory()->attachEyecatch(
            UploadedFile::fake()->image('eyecatch.jpg', 20, 10)
        )->create();

        $this->login()
            ->postJson($this->postTo($news), $this->requestData)
            ->assertOk();

        $preview = NewsPreview::latest()->first();
        $eyecatch = $preview->model->preview_eyecatch;
        $this->assertTrue(str_contains($eyecatch['original_url'], 'eyecatch.jpg'));
        $this->assertSame(20, Arr::get($eyecatch, 'custom_properties.width'));
        $this->assertSame(10, Arr::get($eyecatch, 'custom_properties.height'));
    }

    /** @test */
    public function アイキャッチ画像選択ありの既存Newsのアイキャッチ画像を変更した時、選択したアイキャッチ画像がプレビューに含まれること(): void
    {
        $news = News::factory()->attachEyecatch(
            UploadedFile::fake()->image('eyecatch.jpg', 20, 10)
        )->create();

        $this->login()
            ->postJson($this->postTo($news), $this->requestData + [
                'eyecatch' => UploadedFile::fake()->image('preview-eyecatch.jpg', 30, 40),
            ])
            ->assertOk();

        $preview = NewsPreview::latest()->first();
        $eyecatch = $preview->model->preview_eyecatch;
        $this->assertNotNull($eyecatch['original_url']);
        $this->assertSame(30, Arr::get($eyecatch, 'custom_properties.width'));
        $this->assertSame(40, Arr::get($eyecatch, 'custom_properties.height'));
    }

    /** @test */
    public function アイキャッチ画像選択ありの既存Newsのアイキャッチ画像を選択解除した時、アイキャッチ画像がプレビューに含まないこと(): void
    {
        $news = News::factory()->attachEyecatch(
            UploadedFile::fake()->image('eyecatch.jpg', 20, 10)
        )->create();

        $this->login()
            ->postJson($this->postTo($news), $this->requestData + [
                'eyecatch' => null,
            ])
            ->assertOk();

        $preview = NewsPreview::latest()->first();
        $this->assertNull($preview->model->preview_eyecatch);
    }
}
