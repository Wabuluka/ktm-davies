<?php

namespace Tests\Feature\Http\Controllers\NewsCategory;

use App\Models\NewsCategory;
use App\Models\Site;
use Tests\TestCase;

class NewsCategoryStoreTest extends TestCase
{
    private Site $site;

    private string $from;

    private string $to;

    protected function setUp(): void
    {
        parent::setUp();

        $this->site = Site::factory()->create();
        $this->from = route('sites.news-categories.index', $this->site);
        $this->to = route('sites.news-categories.store', $this->site);
    }

    /** @test */
    public function 未ログイン状態であればログイン画面にリダイレクトすること(): void
    {
        $this
            ->from($this->from)
            ->post($this->to, ['name' => 'お知らせ'])
            ->assertRedirect('/login');
    }

    /** @test */
    public function ログイン状態であればページを表示できること(): void
    {
        $this->login()
            ->from($this->from)
            ->post($this->to, ['name' => 'お知らせ'])
            ->assertRedirectToRoute('sites.news-categories.index', $this->site)
            ->assertSessionHasNoErrors();
        $this->assertDatabaseHas(NewsCategory::class, ['name' => 'お知らせ']);
    }

    /** @test */
    public function サイト内に同名のカテゴリが存在する場合、バリデーションエラーが発生すること(): void
    {
        NewsCategory::factory()->for($this->site)->create(['name' => 'お知らせ']);
        $count = NewsCategory::count();
        $this->login()
            ->from($this->from)
            ->post($this->to, ['name' => 'お知らせ'])
            ->assertRedirect($this->from)
            ->assertSessionHasErrors('name');
        $this->assertDatabaseCount(NewsCategory::class, $count);
    }

    /** @test */
    public function 別サイトに同名のカテゴリが存在していてもバリデーションエラーが発生しないこと(): void
    {
        NewsCategory::factory()->create(['name' => 'お知らせ']);
        $this->login()
            ->from($this->from)
            ->post($this->to, ['name' => 'お知らせ'])
            ->assertRedirect($this->from)
            ->assertSessionHasNoErrors();
        $this->assertDatabaseHas(NewsCategory::class, [
            'name' => 'お知らせ',
            'site_id' => $this->site->id,
        ]);
    }
}
