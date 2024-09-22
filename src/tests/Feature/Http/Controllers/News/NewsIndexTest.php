<?php

namespace Tests\Feature\Http\Controllers\News;

use App\Models\News;
use App\Models\NewsCategory;
use App\Models\Site;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class NewsIndexTest extends TestCase
{
    private Site $site;

    protected function setUp(): void
    {
        parent::setUp();

        $this->site = Site::factory()->create();
    }

    /** @test */
    public function 未ログイン状態であればログイン画面にリダイレクトすること(): void
    {
        $response = $this->get(route('sites.news.index', $this->site));
        $response->assertRedirect('/login');
    }

    /** @test */
    public function ログイン状態であれば書籍一覧ページを表示すること(): void
    {
        $response = $this->login()->get(route('sites.news.index', $this->site));
        $response->assertOk();
    }

    /** @test */
    public function viewにサイトのNews一覧を公開日の降順で返すこと(): void
    {
        News::factory()->create(['title' => '別サイトの NEWS']);
        $factory = News::factory()->for(NewsCategory::factory()->for($this->site), 'category');
        $factory->create(['published_at' => '2023-01-01 00:00:00', 'title' => 'サイトリニューアルのお知らせ']);
        $factory->create(['published_at' => '2023-01-02 00:00:00', 'title' => 'ブログ更新']);
        $factory->create(['published_at' => '2023-01-03 00:00:00', 'title' => '新商品発売のお知らせ']);

        $response = $this->login()->get(route('sites.news.index', $this->site));

        $response->assertInertia(fn (Assert $page) => $page
            ->component('News/Index')
            ->count('newsPaginator.data', 3)
            ->has('newsPaginator.data', fn (Assert $page) => $page
                ->where('0.title', '新商品発売のお知らせ')
                ->where('1.title', 'ブログ更新')
                ->where('2.title', 'サイトリニューアルのお知らせ')));
    }

    /** @test */
    public function バリデーションエラーが発生すると、検索な検索条件がクリアされた状態でリダイレクトされること(): void
    {
        $response = $this->login()->get(route('sites.news.index', [
            'site' => $this->site,
            'keyword' => str_repeat('a', 256),
        ]));
        $response->assertRedirect(route('sites.news.index', $this->site));
    }

    /**
     * @test
     *
     * @dataProvider titleProvider
     */
    public function キーワードに合致するタイトルを持つNEWSのみが表示されること(string $keyword, array $expected): void
    {
        News::factory(3)
            ->sequence(
                ['published_at' => now()->addMonths(3), 'title' => 'サイトリニューアルのお知らせ'],
                ['published_at' => now()->addMonths(2), 'title' => '新規サイト公開予定'],
                ['published_at' => now()->addMonths(1), 'title' => '投稿テスト'],
            )->for(
                NewsCategory::factory()->for($this->site)->create(['name' => 'カテゴリ']),
                'category'
            )->create();

        $this->login();
        $this->get(route('sites.news.index', [
            'site' => $this->site,
            'keyword' => $keyword,
        ]))
            ->assertInertia(fn (Assert $page) => $page
                ->count('newsPaginator.data', count($expected))
                ->has('newsPaginator.data', fn (Assert $page) => collect($expected)
                    ->each(fn ($title, $i) => $page->where("{$i}.title", $title))
                )
            );
    }

    public static function titleProvider(): iterable
    {
        yield '完全一致' => [
            'keyword' => 'サイトリニューアルのお知らせ',
            'expected' => ['サイトリニューアルのお知らせ'],
        ];
        yield '部分一致' => [
            'keyword' => 'サイト',
            'expected' => ['サイトリニューアルのお知らせ', '新規サイト公開予定'],
        ];
        yield '複数' => [
            'keyword' => 'サイト　お知らせ',
            'expected' => ['サイトリニューアルのお知らせ'],
        ];
    }

    /**
     * @test
     *
     * @dataProvider slugProvider
     */
    public function キーワードに合致するスラッグを持つNEWSのみが表示されること(string $keyword, array $expected): void
    {
        News::factory(3)
            ->sequence(
                ['published_at' => now()->addMonths(3), 'title' => 'サイトリニューアルのお知らせ', 'slug' => 'renewal'],
                ['published_at' => now()->addMonths(2), 'title' => '新規サイト公開予定', 'slug' => 'new-site'],
                ['published_at' => now()->addMonths(1), 'title' => '投稿テスト', 'slug' => 'test-post'],
            )->for(
                NewsCategory::factory()->for($this->site)->create(['name' => 'カテゴリ']),
                'category'
            )->create();

        $this->login();
        $this->get(route('sites.news.index', [
            'site' => $this->site,
            'keyword' => $keyword,
        ]))
            ->assertInertia(fn (Assert $page) => $page
                ->count('newsPaginator.data', count($expected))
                ->has('newsPaginator.data', fn (Assert $page) => collect($expected)
                    ->each(fn ($title, $i) => $page->where("{$i}.title", $title))
                )
            );
    }

    public static function slugProvider(): iterable
    {
        yield '完全一致' => [
            'keyword' => 'renewal',
            'expected' => ['サイトリニューアルのお知らせ'],
        ];
        yield '部分一致' => [
            'keyword' => 'new',
            'expected' => ['新規サイト公開予定'], // スラッグは先頭一致のみ
        ];
        yield '複数' => [
            'keyword' => 'new　site',
            'expected' => [],
        ];
    }

    /**
     * @test
     *
     * @dataProvider categoryProvider
     */
    public function キーワードに合致するカテゴリを持つNEWSのみが表示されること(string $keyword, array $expected): void
    {
        $categoryFactory = NewsCategory::factory()->for($this->site);
        News::factory()
            ->for($categoryFactory->create(['name' => 'メディアミックス']), 'category')
            ->create([
                'title' => '〇〇のアニメ化が決定！',
                'published_at' => now()->addMonths(3),
            ]);
        News::factory()
            ->for($categoryFactory->create(['name' => 'お知らせ']), 'category')
            ->create([
                'title' => 'サイトリニューアル！',
                'published_at' => now()->addMonths(2),
            ]);
        News::factory()
            ->for($categoryFactory->create(['name' => 'お知らせ (書店向け)']), 'category')
            ->create([
                'title' => 'XXに関するお詫び',
                'published_at' => now()->addMonths(1),
            ]);

        $this->login();
        $this->get(route('sites.news.index', [
            'site' => $this->site,
            'keyword' => $keyword,
        ]))
            ->assertInertia(fn (Assert $page) => $page
                ->count('newsPaginator.data', count($expected))
                ->has('newsPaginator.data', fn (Assert $page) => collect($expected)
                    ->each(fn ($title, $i) => $page->where("{$i}.title", $title))
                )
            );
    }

    public static function categoryProvider(): iterable
    {
        yield '完全一致' => [
            'keyword' => 'メディアミックス',
            'expected' => ['〇〇のアニメ化が決定！'],
        ];
        yield '部分一致' => [
            'keyword' => 'お知らせ',
            'expected' => ['サイトリニューアル！', 'XXに関するお詫び'],
        ];
        yield '複数' => [
            'keyword' => 'お知らせ　書店',
            'expected' => [],  // カテゴリは先頭一致のみなので、この検索条件にはマッチしない
        ];
    }

    /**
     * @test
     *
     * @dataProvider statusesProvider
     */
    public function 指定ステータスを持つNEWSのみが表示されること(array $statuses, array $expected): void
    {
        $category = NewsCategory::factory()->for($this->site)->create(['name' => 'カテゴリ']);
        $factory = News::factory()->for($category, 'category');
        $factory->willBePublished()->create([
            'title' => '公開予定のNEWS',
            'published_at' => now()->addMonths(2),
        ]);
        $factory->draft()->create([
            'title' => '下書きのNEWS',
            'published_at' => now()->addMonths(1),
        ]);
        $factory->published()->create([
            'title' => '公開中のNEWS',
        ]);

        $this->login();
        $this->get(route('sites.news.index', [
            'site' => $this->site,
            'statuses' => $statuses,
        ]))
            ->assertInertia(fn (Assert $page) => $page
                ->count('newsPaginator.data', count($expected))
                ->has('newsPaginator.data', fn (Assert $page) => collect($expected)
                    ->each(fn ($title, $i) => $page->where("{$i}.title", $title))
                )
            );
    }

    public static function statusesProvider(): iterable
    {
        yield '単一' => [
            'statuses' => ['draft'],
            'expected' => ['下書きのNEWS'],
        ];
        yield '複数' => [
            'statuses' => ['draft', 'published'],
            'expected' => ['下書きのNEWS', '公開中のNEWS'],
        ];
    }
}
