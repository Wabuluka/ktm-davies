<?php

namespace Tests\Feature\Http\Controllers\Book;

use App\Enums\BookStatus;
use App\Models\Book;
use Tests\TestCase;

class BookPublishTest extends TestCase
{
    /** @test */
    public function 書籍を公開できること(): void
    {
        $book = Book::factory()->draft()->create();
        $response = $this->login()->patch("/books/publish/{$book->id}");
        $response->assertRedirect('/books');
        $book->refresh();
        $this->assertEquals(BookStatus::Published, $book->status);
    }

    /** @test */
    public function ログインしなければ書籍を公開できないこと(): void
    {
        $book = Book::factory()->draft()->create();
        $response = $this->patch("/books/publish/{$book->id}");
        $response->assertRedirect('/login');
        $book->refresh();
        $this->assertEquals(BookStatus::Draft, $book->status);
    }

    /** @test */
    public function 書籍を一括で公開できること(): void
    {
        $books = Book::factory(3)->draft()->create();
        $ids = $books->pluck('id')->toArray();
        $response = $this->login()->patch('/books/publish-many', ['ids' => $ids]);
        $response->assertRedirect('/books');
        $books->each(function ($book) {
            $book->refresh();
            $this->assertEquals(BookStatus::Published, $book->status);
        });
    }

    /** @test */
    public function ログインしなければ書籍を一括で公開できないこと(): void
    {
        $books = Book::factory(3)->draft()->create();
        $ids = $books->pluck('id')->toArray();
        $response = $this->patch('/books/publish-many', ['ids' => $ids]);
        $response->assertRedirect('/login');
        $books->each(function ($book) {
            $book->refresh();
            $this->assertEquals(BookStatus::Draft, $book->status);
        });
    }
}
