<?php

namespace Tests\Feature\Http\Controllers\NewsCategory;

use App\Models\NewsCategory;
use App\Models\Site;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class NewsCategoryIndexTest extends TestCase
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
        $response = $this->get(route('sites.news-categories.index', $this->site));
        $response->assertRedirect('/login');
    }

    /** @test */
    public function ログイン状態であればページを表示できること(): void
    {
        $response = $this->login()->get(route('sites.news-categories.index', $this->site));
        $response->assertOk();
    }

    /** @test */
    public function viewにサイトのNewsカテゴリの一覧をidの昇順で返すこと(): void
    {
        NewsCategory::factory()->for($this->site)->create(['name' => 'お知らせ']);
        NewsCategory::factory()->for($this->site)->create(['name' => 'ブログ']);
        $response = $this->login()->get(route('sites.news-categories.index', $this->site));
        $response->assertInertia(fn (Assert $page) => $page
            ->component('NewsCategories/Index')
            ->has('site.news_categories', 2)
            ->has('site.news_categories', fn (Assert $page) => $page
                ->where('0.name', 'お知らせ')
                ->where('1.name', 'ブログ')
            )
        );
    }
}
