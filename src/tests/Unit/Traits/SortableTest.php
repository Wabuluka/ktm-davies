<?php

namespace Tests\Unit\Traits;

use Illuminate\Database\Schema\Blueprint;
use Tests\Supports\Models\TestScopedSortable;
use Tests\Supports\Models\TestSortable;
use Tests\TestCase;

class SortableTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $addColumns = function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->unsignedInteger('publisher_no');
            $table->unsignedInteger('sort')->index();
            $table->timestamps();
        };

        app()['db']->connection()->getSchemaBuilder()
            ->create('test_sortables', $addColumns);

        app()['db']->connection()->getSchemaBuilder()
            ->create('test_scoped_sortables', $addColumns);
    }

    protected function tearDown(): void
    {
        app()['db']->connection()->getSchemaBuilder()
            ->drop('test_sortables');

        app()['db']->connection()->getSchemaBuilder()
            ->drop('test_scoped_sortables');

        parent::tearDown();
    }

    /** @test */
    public function 並び順が最初のものにmove_upをした時並び順に変化がないこと(): void
    {
        $records = [
            ['title' => 'Book 1', 'publisher_no' => 1, 'sort' => 1],
            ['title' => 'Book 2', 'publisher_no' => 1, 'sort' => 2],
            ['title' => 'Book 3', 'publisher_no' => 2, 'sort' => 3],
        ];
        TestSortable::insert($records);
        TestSortable::whereSort(1)->first()->moveUp();

        collect($records)->each(fn ($record) => $this->assertDatabaseHas('test_sortables', $record));
    }

    /** @test */
    public function 並び順が最後のものにmove_downをした時並び順に変化がないこと(): void
    {
        $records = [
            ['title' => 'Book 1', 'publisher_no' => 1, 'sort' => 1],
            ['title' => 'Book 2', 'publisher_no' => 1, 'sort' => 2],
            ['title' => 'Book 3', 'publisher_no' => 2, 'sort' => 3],
        ];
        TestSortable::insert($records);
        TestSortable::whereSort(3)->first()->moveDown();

        collect($records)->each(fn ($record) => $this->assertDatabaseHas('test_sortables', $record));
    }

    /** @test */
    public function データが1件の時move_upでsortに変化がないこと(): void
    {
        $record = ['title' => 'Book 1', 'publisher_no' => 1, 'sort' => 1];
        TestSortable::insert($record);
        TestSortable::first()->moveUp();

        $this->assertDatabaseHas('test_sortables', $record);
    }

    /** @test */
    public function データが1件の時move_downでsortに変化がないこと(): void
    {
        $record = ['title' => 'Book 1', 'publisher_no' => 1, 'sort' => 1];
        TestSortable::insert($record);
        TestSortable::first()->moveDown();

        $this->assertDatabaseHas('test_sortables', $record);
    }

    /** @test */
    public function モデル作成時にsortのインクリメントが行われること(): void
    {
        TestSortable::create(['title' => 'Book 1', 'publisher_no' => 1, 'sort' => 1]);
        TestSortable::create(['title' => 'Book 2', 'publisher_no' => 2, 'sort' => 2]);
        TestScopedSortable::create(['title' => 'Scoped Book 1', 'publisher_no' => 1, 'sort' => 1]);
        TestScopedSortable::create(['title' => 'Scoped Book 2', 'publisher_no' => 2, 'sort' => 2]);

        $book = TestSortable::create(['title' => 'Book 3', 'publisher_no' => 1]);
        $scopedBook = TestScopedSortable::create(['title' => 'Scoped Book 3', 'publisher_no' => 1]);

        $this->assertSame(3, $book->sort);
        $this->assertSame(2, $scopedBook->sort);
    }

    /** @test */
    public function モデル削除時にsortのデクリメントが行われること(): void
    {
        TestSortable::create(['title' => 'Book 1', 'publisher_no' => 1]);
        TestSortable::create(['title' => 'Book 2', 'publisher_no' => 2]);
        TestSortable::create(['title' => 'Book 3', 'publisher_no' => 1]);
        TestSortable::create(['title' => 'Book 4', 'publisher_no' => 2]);
        TestScopedSortable::create(['title' => 'Scoped Book 1', 'publisher_no' => 1]);
        TestScopedSortable::create(['title' => 'Scoped Book 2', 'publisher_no' => 2]);
        TestScopedSortable::create(['title' => 'Scoped Book 3', 'publisher_no' => 1]);
        TestScopedSortable::create(['title' => 'Scoped Book 4', 'publisher_no' => 2]);

        $book = TestSortable::whereTitle('Book 1')->first();
        $scopedBook = TestScopedSortable::whereTitle('Scoped Book 1')->first();
        $book->delete();
        $scopedBook->delete();

        $this->assertDatabaseHas('test_sortables', ['title' => 'Book 2', 'sort' => 1]);
        $this->assertDatabaseHas('test_sortables', ['title' => 'Book 3', 'sort' => 2]);
        $this->assertDatabaseHas('test_sortables', ['title' => 'Book 4', 'sort' => 3]);
        $this->assertDatabaseHas('test_scoped_sortables', ['title' => 'Scoped Book 2', 'sort' => 1]);
        $this->assertDatabaseHas('test_scoped_sortables', ['title' => 'Scoped Book 3', 'sort' => 1]);
        $this->assertDatabaseHas('test_scoped_sortables', ['title' => 'Scoped Book 4', 'sort' => 2]);
    }

    /** @test */
    public function move_downで次の順番のモデルとsort値が入れ替わること(): void
    {
        TestSortable::create(['title' => 'Book 1', 'publisher_no' => 1]);
        TestSortable::create(['title' => 'Book 2', 'publisher_no' => 2]);
        TestSortable::create(['title' => 'Book 3', 'publisher_no' => 1]);
        TestSortable::create(['title' => 'Book 4', 'publisher_no' => 2]);
        TestScopedSortable::create(['title' => 'Scoped Book 1', 'publisher_no' => 1]);
        TestScopedSortable::create(['title' => 'Scoped Book 2', 'publisher_no' => 2]);
        TestScopedSortable::create(['title' => 'Scoped Book 3', 'publisher_no' => 1]);
        TestScopedSortable::create(['title' => 'Scoped Book 4', 'publisher_no' => 2]);

        $book = TestSortable::whereTitle('Book 1')->first();
        $scopedBook = TestScopedSortable::whereTitle('Scoped Book 1')->first();
        $book->moveDown();
        $scopedBook->moveDown();

        $this->assertDatabaseHas('test_sortables', ['title' => 'Book 1', 'sort' => 2]);
        $this->assertDatabaseHas('test_sortables', ['title' => 'Book 2', 'sort' => 1]);
        $this->assertDatabaseHas('test_scoped_sortables', ['title' => 'Scoped Book 1', 'sort' => 2]);
        $this->assertDatabaseHas('test_scoped_sortables', ['title' => 'Scoped Book 3', 'sort' => 1]);
    }

    /** @test */
    public function move_upで前の順番のモデルとsort値が入れ替わること(): void
    {
        TestSortable::create(['title' => 'Book 1', 'publisher_no' => 1]);
        TestSortable::create(['title' => 'Book 2', 'publisher_no' => 2]);
        TestSortable::create(['title' => 'Book 3', 'publisher_no' => 1]);
        TestSortable::create(['title' => 'Book 4', 'publisher_no' => 2]);
        TestScopedSortable::create(['title' => 'Scoped Book 1', 'publisher_no' => 1]);
        TestScopedSortable::create(['title' => 'Scoped Book 2', 'publisher_no' => 2]);
        TestScopedSortable::create(['title' => 'Scoped Book 3', 'publisher_no' => 1]);
        TestScopedSortable::create(['title' => 'Scoped Book 4', 'publisher_no' => 2]);

        $book = TestSortable::whereTitle('Book 4')->first();
        $scopedBook = TestScopedSortable::whereTitle('Scoped Book 4')->first();
        $book->moveUp();
        $scopedBook->moveUp();

        $this->assertDatabaseHas('test_sortables', ['title' => 'Book 4', 'sort' => 3]);
        $this->assertDatabaseHas('test_sortables', ['title' => 'Book 3', 'sort' => 4]);
        $this->assertDatabaseHas('test_scoped_sortables', ['title' => 'Scoped Book 4', 'sort' => 1]);
        $this->assertDatabaseHas('test_scoped_sortables', ['title' => 'Scoped Book 2', 'sort' => 2]);
    }
}
