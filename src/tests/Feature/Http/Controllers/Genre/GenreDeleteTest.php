<?php

namespace Tests\Feature\Http\Controllers\Genre;

use App\Models\Genre;
use Tests\TestCase;

class GenreDeleteTest extends TestCase
{
    /** @test */
    public function ジャンルを削除できること(): void
    {
        $genre = Genre::factory()->create();
        $response = $this->login()->from(route('books.create'))->delete(route('genres.destroy', $genre));
        $response->assertRedirect('/books/create')->assertSessionHas(['message' => 'Deleted successfully', 'status' => 'success']);
        $this->assertModelMissing($genre);
    }

    /** @test */
    public function ログインしなければジャンルを削除できないこと(): void
    {
        $genre = Genre::factory()->create();
        $response = $this->from(route('books.create'))->delete(route('genres.destroy', $genre));
        $response->assertRedirect('/login');
        $this->assertModelExists($genre);
    }

    /** @test */
    public function 書籍に紐付いたジャンルを削除できないこと(): void
    {
        $genre = Genre::factory()->hasBooks()->create();
        $response = $this->login()->from(route('books.create'))->delete(route('genres.destroy', $genre));
        $response->assertRedirect('/books/create')->assertSessionHasErrors(['name' => 'このジャンルには関連する本が存在します。']);
        $this->assertModelExists($genre);
    }
}
