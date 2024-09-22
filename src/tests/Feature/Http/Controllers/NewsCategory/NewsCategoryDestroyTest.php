<?php

namespace Tests\Feature\Http\Controllers\NewsCategory;

use App\Models\News;
use App\Models\NewsCategory;
use App\Models\Site;
use Tests\TestCase;

class NewsCategoryDestroyTest extends TestCase
{
    private Site $site;

    private NewsCategory $category;

    private string $from;

    private string $to;

    private string $redirectTo;

    protected function setUp(): void
    {
        parent::setUp();

        $this->site
            = Site::factory()->create();
        $this->category
            = NewsCategory::factory()->for($this->site)->create(['name' => 'カテゴリ']);
        $this->from
            = route('news-categories.edit', $this->category);
        $this->to
            = route('news-categories.destroy', $this->category);
        $this->redirectTo
            = route('sites.news-categories.index', $this->site);
    }

    /** @test */
    public function 未ログイン状態であればログイン画面にリダイレクトすること(): void
    {
        $this
            ->from($this->from)
            ->delete($this->to)
            ->assertRedirect('/login');
    }

    /** @test */
    public function ログイン状態であればNewsカテゴリを削除できること(): void
    {
        $this->login()
            ->from($this->from)
            ->delete($this->to)
            ->assertRedirect($this->redirectTo)
            ->assertSessionHasNoErrors();
        $this->assertModelMissing($this->category);
    }

    /** @test */
    public function Newsが紐付いているカテゴリの削除を試みると、バリデーションエラーが発生すること(): void
    {
        $this->category->news()->create(
            News::factory()->make()->toArray(),
        );
        $this->login()
            ->from($this->from)
            ->delete($this->to)
            ->assertRedirect($this->from)
            ->assertSessionHasErrors('name');
        $this->assertModelExists($this->category);
    }
}
