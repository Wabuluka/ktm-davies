<?php

namespace Tests\Feature\Http\Controllers\Api\Book;

use App\Models\Book;
use Illuminate\Http\UploadedFile;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class BookShowTest extends TestCase
{
    /** @test */
    public function 未ログイン状態であればログイン画面にリダイレクトすること(): void
    {
        $response = $this->get(route('api.books.show', Book::factory()->create()));
        $response->assertRedirect('/login');
    }

    /** @test */
    public function ログイン状態であれば書籍作成ページを表示すること(): void
    {
        $this->login();
        $response = $this->get(route('api.books.show', Book::factory()->create()));
        $response->assertOk();
    }

    /** @test */
    public function Bookのタイトルと書影を返却すること(): void
    {
        $book = Book::factory()
            ->attachCover(UploadedFile::fake()->image('cover.jpg'))
            ->create();

        $response = $this
            ->login()
            ->get(route('api.books.show', $book));

        $response
            ->assertJson(fn (AssertableJson $json) => $json
                ->where('title', $book->title)
                ->has('cover', fn (AssertableJson $json) => $json
                    ->where('file_name', 'cover.jpg')
                    ->etc())
                ->etc());
    }
}
