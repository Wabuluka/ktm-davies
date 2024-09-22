<?php

namespace Tests\Feature\Http\Controllers\NewsCategory;

use App\Models\NewsCategory;
use App\Models\Site;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class NewsCategoryEditTest extends TestCase
{
    private Site $site;

    private NewsCategory $category;

    protected function setUp(): void
    {
        parent::setUp();

        $this->site = Site::factory()->create();
        $this->category = NewsCategory::factory()->for($this->site)->create(['name' => 'カテゴリ']);
    }

    /** @test */
    public function 未ログイン状態であればログイン画面にリダイレクトすること(): void
    {
        $this
            ->get(route('news-categories.edit', $this->category))
            ->assertRedirect('/login');
    }

    /** @test */
    public function ログイン状態であればページを表示できること(): void
    {
        $this->login()
            ->get(route('news-categories.edit', $this->category))
            ->assertOk();
    }

    /** @test */
    public function viewに現在のNewsカテゴリの情報を渡すこと(): void
    {
        $this->login()
            ->get(route('news-categories.edit', $this->category))
            ->assertInertia(fn (Assert $page) => $page
                ->component('NewsCategories/Edit')
                ->has('category', fn (Assert $page) => $page
                    ->where('id', $this->category->id)
                    ->where('name', $this->category->name)
                    ->where('site_id', $this->category->site_id)
                    ->etc()
                )
            );
    }
}
