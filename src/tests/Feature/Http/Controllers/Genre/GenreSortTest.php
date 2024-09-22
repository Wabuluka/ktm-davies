<?php

namespace Tests\Feature\Http\Controllers\Genre;

use App\Models\Genre;
use Tests\TestCase;

class GenreSortTest extends TestCase
{
    /** @test */
    public function 指定したジャンルの並び順を一つ上げること(): void
    {
        $genre1 = Genre::create(['name' => 'genre1']);
        $genre2 = Genre::create(['name' => 'genre2']);

        $response = $this->login()->from(route('books.create'))->patch(route('genres.sort.move-up', $genre2));
        $response->assertRedirect('/books/create')->assertSessionHas(['message' => '表示順をSaved successfully', 'status' => 'success']);

        $this->assertDatabaseHas('genres', ['name' => 'genre1', 'sort' => 2]);
        $this->assertDatabaseHas('genres', ['name' => 'genre2', 'sort' => 1]);
    }

    /** @test */
    public function 指定したジャンルの並び順を一つ下げること(): void
    {
        $genre1 = Genre::create(['name' => 'genre1']);
        $genre2 = Genre::create(['name' => 'genre2']);

        $response = $this->login()->from(route('books.create'))->patch(route('genres.sort.move-down', $genre1));
        $response->assertRedirect('/books/create')->assertSessionHas(['message' => '表示順をSaved successfully', 'status' => 'success']);

        $this->assertDatabaseHas('genres', ['name' => 'genre1', 'sort' => 2]);
        $this->assertDatabaseHas('genres', ['name' => 'genre2', 'sort' => 1]);
    }
}
