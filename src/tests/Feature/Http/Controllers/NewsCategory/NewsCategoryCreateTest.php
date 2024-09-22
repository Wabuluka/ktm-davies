<?php

namespace Tests\Feature\Http\Controllers\NewsCategory;

use App\Models\Site;
use Tests\TestCase;

class NewsCategoryCreateTest extends TestCase
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
        $response = $this->get(route('sites.news-categories.create', $this->site));
        $response->assertRedirect('/login');
    }

    /** @test */
    public function ログイン状態であればページを表示できること(): void
    {
        $response = $this->login()->get(route('sites.news-categories.create', $this->site));
        $response->assertOk();
    }
}
