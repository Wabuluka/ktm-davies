<?php

namespace Tests\Unit\Models;

use App\Models\Book;
use App\Models\RelatedItem;
use Tests\TestCase;

class RelatedItemTest extends TestCase
{
    /** @test */
    public function Book毎にsortの値が一意になること(): void
    {
        [$book1, $book2] = Book::factory(2)->create();
        RelatedItem::factory()->for($book1)->create();
        RelatedItem::factory()->for($book1)->create();
        RelatedItem::factory()->for($book2)->create();
        RelatedItem::factory()->for($book2)->create();

        $this->assertDatabaseHas('related_items', [
            'book_id' => $book1->id,
            'sort' => 1,
        ]);
        $this->assertDatabaseHas('related_items', [
            'book_id' => $book1->id,
            'sort' => 2,
        ]);
        $this->assertDatabaseHas('related_items', [
            'book_id' => $book2->id,
            'sort' => 1,
        ]);
        $this->assertDatabaseHas('related_items', [
            'book_id' => $book2->id,
            'sort' => 2,
        ]);
    }
}
