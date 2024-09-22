<?php

namespace Tests\Feature\Http\Controllers\Genre;

use Tests\TestCase;

class GenreStoreTest extends TestCase
{
    /** @test */
    public function ジャンルを作成できること(): void
    {
        $data = ['name' => 'genre'];

        $response = $this->login()->from(route('books.create'))->post(route('genres.store'), $data);
        $response->assertRedirect('/books/create')->assertSessionHas(['message' => 'Saved successfully', 'status' => 'success']);
        $this->assertDatabaseHas('genres', $data);
    }

    /** @test */
    public function ログインしなければジャンルを作成できないこと(): void
    {
        $data = ['name' => 'genre'];

        $response = $this->from(route('books.create'))->post(route('genres.store'), $data);
        $response->assertRedirect('/login');
        $this->assertDatabaseMissing('genres', $data);
    }

    /** @test */
    public function sortに連番が自動的に設定されること(): void
    {
        $genresData = [
            ['name' => 'genre1'],
            ['name' => 'genre2'],
            ['name' => 'genre3'],
        ];

        foreach ($genresData as $data) {
            $response = $this->login()->from(route('books.create'))->post(route('genres.store'), $data);
            $response->assertRedirect('/books/create');
        }
        $this->assertDatabaseHas('genres', ['name' => 'genre1', 'sort' => 1]);
        $this->assertDatabaseHas('genres', ['name' => 'genre2', 'sort' => 2]);
        $this->assertDatabaseHas('genres', ['name' => 'genre3', 'sort' => 3]);
    }
}
