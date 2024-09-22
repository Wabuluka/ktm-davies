<?php

namespace Tests\Feature\Http\Controllers\Genre;

use App\Models\Genre;
use Tests\TestCase;

class GenreUpdateTest extends TestCase
{
    /** @test */
    public function ジャンルを更新できること(): void
    {
        $initialData = ['name' => 'genre'];
        $genre = Genre::create($initialData);

        $newData = ['name' => 'genre (Updated)'];

        $response = $this->login()->from(route('books.create'))->put(route('genres.update', $genre), $newData);
        $response->assertRedirect('/books/create')->assertSessionHas(['message' => 'Saved successfully', 'status' => 'success']);
        $this->assertDatabaseHas('genres', $newData);
        $this->assertDatabaseMissing('genres', $initialData);
    }

    /** @test */
    public function ログインしなければジャンルを更新できないこと(): void
    {
        $initialData = ['name' => 'genre'];
        $genre = Genre::create($initialData);

        $newData = ['name' => 'genre (Updated)'];

        $response = $this->from(route('books.create'))->put(route('genres.update', $genre), $newData);
        $response->assertRedirect('/login');
        $this->assertDatabaseHas('genres', $initialData);
        $this->assertDatabaseMissing('genres', $newData);
    }

    /** @test */
    public function バリデーションエラーが発生すること(): void
    {
        $initialData = ['name' => 'genre'];
        $genre = Genre::create($initialData);

        $newData = [
            'name' => '',
        ];

        $response = $this->login()->from(route('books.create'))->put(route('genres.update', $genre), $newData);
        $response->assertRedirect('/books/create')->assertSessionHasErrors('name');
        $this->assertDatabaseHas('genres', $initialData);
        $this->assertDatabaseMissing('genres', $newData);
    }
}
