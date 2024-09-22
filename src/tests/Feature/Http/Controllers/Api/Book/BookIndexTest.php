<?php

namespace Tests\Feature\Http\Controllers\Api\Book;

use App\Models\Book;
use App\Models\Site;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class BookIndexTest extends TestCase
{
    /** @test */
    public function 未ログイン状態であればログイン画面にリダイレクトすること(): void
    {
        $response = $this->get('/api/books');

        $response->assertRedirect('/login');
    }

    /** @test */
    public function ログイン状態であれば書籍一覧ページを表示すること(): void
    {
        $response = $this->login()->get('/api/books');
        $response->assertOk();
    }

    /** @test*/
    public function クエリパラメータによる絞り込み検索が可能なこと(): void
    {
        [$site1, $site2] = Site::factory(2)->create();
        Book::factory()->hasAttached($site1)->published()
            ->create(['title' => 'Book 1']);
        Book::factory()->hasAttached($site1)->published()
            ->create(['title' => 'Book 2 (キーワードに引っかからない)']);
        Book::factory()->hasAttached($site2)->published()
            ->create(['title' => 'Book 100 (別サイト)']);
        Book::factory()->hasAttached($site1)->draft()
            ->create(['title' => 'Book 11 (ステータスが下書き)']);

        $this
            ->login()
            ->get("/api/books?keyword=Book 1&sites[]={$site1->id}&statuses[]=published")
            ->assertJson(fn (AssertableJson $json) => $json
                ->has('data', 1)
                ->where('data.0.title', 'Book 1')
                ->etc()
            );
    }

    /** @test */
    public function 不正な検索条件が与えらるとエラーになること(): void
    {
        $this->login()->get('/api/books?keyword=' . str_repeat('a', 256))->assertRedirect('/books');
    }
}
