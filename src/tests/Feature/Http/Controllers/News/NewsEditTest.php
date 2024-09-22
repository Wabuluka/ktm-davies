<?php

namespace Tests\Feature\Http\Controllers\News;

use App\Models\News;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class NewsEditTest extends TestCase
{
    private News $news;

    protected function setUp(): void
    {
        parent::setUp();

        $this->news = News::factory()->published()->create();
    }

    /** @test */
    public function 未ログイン状態であればログイン画面にリダイレクトすること(): void
    {
        $response = $this->get(route('news.edit', $this->news));
        $response->assertRedirect('/login');
    }

    /** @test */
    public function viewに現在のNewsの属性を渡すこと(): void
    {
        $response = $this->login()->get(route('news.edit', $this->news));
        $response
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('News/Edit')
                ->has('news', fn (Assert $page) => $page
                    ->where('id', $this->news->id)
                    ->where('status', $this->news->status->value)
                    ->where('title', $this->news->title)
                    ->where('slug', $this->news->slug)
                    ->where('content', $this->news->content)
                    ->where('category_id', $this->news->category_id)
                    ->where('published_at', $this->news->published_at->format('Y-m-d H:i:s'))
                    ->has('category', fn (Assert $page) => $page
                        ->whereType('site.news_categories', 'array')
                        ->etc())
                    ->etc()));
    }
}
