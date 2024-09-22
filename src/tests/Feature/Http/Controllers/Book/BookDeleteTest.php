<?php

namespace Tests\Feature\Http\Controllers\Book;

use App\Models\Book;
use Tests\TestCase;

class BookDeleteTest extends TestCase
{
    /** @test */
    public function 書籍を削除できること(): void
    {
        $book = Book::factory()->create();
        $response = $this->login()->delete("/books/{$book->id}");
        $response->assertRedirect('/books');
        $this->assertModelMissing($book);
    }

    /** @test */
    public function ログインしなければ書籍を削除できないこと(): void
    {
        $book = Book::factory()->create();
        $response = $this->delete("/books/{$book->id}");
        $response->assertRedirect('/login');
        $this->assertModelExists($book);
    }

    /** @test */
    public function 書籍を一括で削除できること(): void
    {
        $books = Book::factory(3)->create();
        $ids = $books->pluck('id')->toArray();
        $response = $this->login()->delete('/books/destroy-many', ['ids' => $ids]);
        $response->assertRedirect('/books');
        $this->assertDatabaseMissing('books', ['id' => $ids]);
    }

    /** @test */
    public function ログインしなければ書籍を一括で削除できないこと(): void
    {
        $books = Book::factory(3)->create();
        $ids = $books->pluck('id')->toArray();
        $response = $this->delete('/books/destroy-many', ['ids' => $ids]);
        $response->assertRedirect('/login');
        $this->assertDatabaseHas('books', ['id' => $ids]);
    }
}
