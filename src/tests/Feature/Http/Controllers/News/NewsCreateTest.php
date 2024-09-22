<?php

namespace Tests\Feature\Http\Controllers\News;

use App\Models\NewsCategory;
use App\Models\Site;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class NewsCreateTest extends TestCase
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
        $response = $this->get(route('sites.news.create', $this->site));
        $response->assertRedirect('/login');
    }

    /** @test */
    public function ログイン状態であれば書籍作成ページを表示すること(): void
    {
        $response = $this->login()->get(route('sites.news.create', $this->site));
        $response->assertOk();
    }

    /** @test */
    public function viewにSiteを渡すこと(): void
    {
        NewsCategory::factory(1)->create(['name' => '別サイトのカテゴリ']);
        [$category1, $category2] = NewsCategory::factory(2)->for($this->site)->create();
        $response = $this->login()->get(route('sites.news.create', $this->site));
        $response->assertInertia(fn (Assert $page) => $page
            ->component('News/Create')
            ->has('site', fn (Assert $page) => $page
                ->where('id', $this->site->id)
                ->where('name', $this->site->name)
                ->count('news_categories', 2)
                ->has('news_categories.0', fn (Assert $page) => $page
                    ->where('id', $category1->id)
                    ->where('name', $category1->name)
                    ->etc())
                ->has('news_categories.1', fn (Assert $page) => $page
                    ->where('id', $category2->id)
                    ->where('name', $category2->name)
                    ->etc())
                ->etc()));
    }
}
