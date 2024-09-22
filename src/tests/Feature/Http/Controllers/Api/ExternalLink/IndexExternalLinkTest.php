<?php

namespace Tests\Feature\Http\Controllers\Api\ExternalLink;

use App\Models\ExternalLink;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class IndexExternalLinkTest extends TestCase
{
    /** @test */
    public function 未ログイン状態であればログイン画面にリダイレクトすること(): void
    {
        $response = $this->get('/api/external-links');
        $response->assertRedirect('/login');
    }

    /** @test */
    public function ログイン状態であれば書籍一覧ページを表示すること(): void
    {
        $response = $this->login()->get('/api/external-links');
        $response->assertOk();
    }

    /** @test */
    public function 作成日の降順で表示されること(): void
    {
        $this->travelTo(now()->subDays(2), fn () => ExternalLink::factory()->create(['title' => 'Link 1']));
        $this->travelTo(now()->subDays(1), fn () => ExternalLink::factory()->create(['title' => 'Link 2']));
        $this->travelTo(now()->subDays(0), fn () => ExternalLink::factory()->create(['title' => 'Link 3']));

        $this->login();
        $this
            ->get('/api/external-links')
            ->assertJson(fn (AssertableJson $json) => $json
                ->has('data', 3)
                ->where('data.0.title', 'Link 3')
                ->where('data.1.title', 'Link 2')
                ->where('data.2.title', 'Link 1')
                ->etc()
            );
    }

    /** @test */
    public function キーワードによる絞り込み検索が可能なこと(): void
    {
        $this->freezeTime();
        ExternalLink::factory()
            ->create(['title' => 'Link 1', 'url' => 'https://cdn.example.com/']);
        ExternalLink::factory()
            ->create(['title' => 'Link 2', 'url' => 'https://www.example.com/']);
        ExternalLink::factory()
            ->create(['title' => 'Link 3', 'url' => 'https://shop.example.com/']);

        $this->login();
        $this
            ->get('/api/external-links?keyword=Link')
            ->assertJson(fn (AssertableJson $json) => $json
                ->has('data', 3)
                ->where('data.0.title', 'Link 1')
                ->where('data.1.title', 'Link 2')
                ->where('data.2.title', 'Link 3')
                ->etc()
            );
        $this
            ->get('/api/external-links?keyword=Link shop')
            ->assertJson(fn (AssertableJson $json) => $json
                ->has('data', 1)
                ->where('data.0.title', 'Link 3')
                ->etc()
            );
    }

    /** @test */
    public function 不正なキーワードが与えられた場合、エラーになること(): void
    {
        $this->login()->get('/api/external-links?keyword=' . str_repeat('a', 256))
            ->assertSessionHasErrors('keyword');
    }
}
