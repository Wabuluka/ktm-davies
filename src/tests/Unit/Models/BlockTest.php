<?php

namespace Tests\Unit\Models;

use App\Enums\BlockType;
use App\Models\Block;
use App\Models\Book;
use Tests\TestCase;

class BlockTest extends TestCase
{
    /** @test */
    public function Book毎にsortの値が一意になること(): void
    {
        [$book1, $book2] = Book::factory(2)->create();
        Block::factory()->for($book1)->create(['type_id' => BlockType::Common]);
        Block::factory()->for($book1)->create(['type_id' => BlockType::Series]);
        Block::factory()->for($book2)->create(['type_id' => BlockType::BookStore]);
        Block::factory()->for($book2)->create(['type_id' => BlockType::Custom]);

        $this->assertDatabaseHas('blocks', [
            'book_id' => $book1->id,
            'type_id' => BlockType::Common,
            'sort' => 1,
        ]);
        $this->assertDatabaseHas('blocks', [
            'book_id' => $book1->id,
            'type_id' => BlockType::Series,
            'sort' => 2,
        ]);
        $this->assertDatabaseHas('blocks', [
            'book_id' => $book2->id,
            'type_id' => BlockType::BookStore,
            'sort' => 1,
        ]);
        $this->assertDatabaseHas('blocks', [
            'book_id' => $book2->id,
            'type_id' => BlockType::Custom,
            'sort' => 2,
        ]);
    }
}
