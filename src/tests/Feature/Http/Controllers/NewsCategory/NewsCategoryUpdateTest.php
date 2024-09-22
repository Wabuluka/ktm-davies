<?php

namespace Tests\Feature\Http\Controllers\NewsCategory;

use App\Models\NewsCategory;
use App\Models\Site;
use Tests\TestCase;

class NewsCategoryUpdateTest extends TestCase
{
    private Site $site;

    private NewsCategory $category;

    private string $from;

    private string $to;

    private string $redirectTo;

    protected function setUp(): void
    {
        parent::setUp();

        $this->site = Site::factory()->create();
        $this->category = NewsCategory::factory()->for($this->site)->create(['name' => 'カテゴリ']);
        $this->from = route('news-categories.edit', $this->category);
        $this->to = route('news-categories.update', $this->category);
        $this->redirectTo = route('sites.news-categories.index', $this->site);
    }

    /** @test */
    public function 未ログイン状態であればログイン画面にリダイレクトすること(): void
    {
        $this
            ->from($this->from)
            ->patch($this->to, ['name' => 'お知らせ'])
            ->assertRedirect('/login');
    }

    /** @test */
    public function ログイン状態であればNewsカテゴリを更新できること(): void
    {
        $this->login()
            ->from($this->from)
            ->patch($this->to, ['name' => 'お知らせ'])
            ->assertRedirect($this->redirectTo)
            ->assertSessionHasNoErrors();
        $this->assertDatabaseHas(NewsCategory::class, ['name' => 'お知らせ'])
            ->assertDatabaseMissing(NewsCategory::class, ['name' => $this->category->name]);
    }

    /** @test */
    public function サイト内に同名のカテゴリが存在する場合、バリデーションエラーが発生すること(): void
    {
        NewsCategory::factory()->for($this->site)->create(['name' => 'お知らせ']);
        $this->login()
            ->from($this->from)
            ->patch($this->to, ['name' => 'お知らせ'])
            ->assertRedirect($this->from)
            ->assertSessionHasErrors('name');
        $this->assertModelExists($this->category);
    }

    /** @test */
    public function 別サイトに同名のカテゴリが存在していてもバリデーションエラーが発生しないこと(): void
    {
        NewsCategory::factory()->create(['name' => 'お知らせ']);
        $this->login()
            ->from($this->from)
            ->patch($this->to, ['name' => 'お知らせ'])
            ->assertRedirect($this->redirectTo)
            ->assertSessionHasNoErrors();
        $this->assertDatabaseHas(NewsCategory::class, [
            'name' => 'お知らせ',
            'site_id' => $this->site->id,
        ]);
    }

    /** @test */
    public function カテゴリ名に変更がなくてもカテゴリを更新できること(): void
    {
        $this->login()
            ->from($this->from)
            ->patch($this->to, ['name' => $this->category->name])
            ->assertRedirect($this->redirectTo)
            ->assertSessionHasNoErrors();
        $this->assertModelExists($this->category);
    }
}
