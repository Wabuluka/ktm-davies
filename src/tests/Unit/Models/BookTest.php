<?php

namespace Tests\Unit\Models;

use App\Enums\BookStatus;
use App\Models\Book;
use Closure;
use Illuminate\Http\UploadedFile;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Tests\TestCase;

class BookTest extends TestCase
{
    /**
     * @test
     *
     * @dataProvider statusDataProvider
     */
    public function status_書籍のステータスを返却すること(BookStatus $expected, bool $is_draft, Closure $published_at): void
    {
        $this->freezeTime();
        $this->assertSame(
            Book::factory()->create(['is_draft' => $is_draft, 'published_at' => $published_at])->status,
            $expected
        );
    }

    /**
     * @test
     *
     * @dataProvider statusDataProvider
     */
    public function scopeDraft_ステータスが「下書き」の書籍に絞り込むこと(BookStatus $expected, bool $is_draft, Closure $published_at): void
    {
        $this->freezeTime();
        Book::factory()->create(['is_draft' => $is_draft, 'published_at' => $published_at]);
        $expectedCount = $expected === BookStatus::Draft ? 1 : 0;
        $this->assertCount($expectedCount, Book::draft()->get());
    }

    /**
     * @test
     *
     * @dataProvider statusDataProvider
     */
    public function scopeWillBePublished_ステータスが「公開予定」の書籍に絞り込むこと(BookStatus $expected, bool $is_draft, Closure $published_at): void
    {
        $this->freezeTime();
        Book::factory()->create(['is_draft' => $is_draft, 'published_at' => $published_at]);
        $expectedCount = $expected === BookStatus::WillBePublished ? 1 : 0;
        $this->assertCount($expectedCount, Book::willBePublished()->get());
    }

    /**
     * @test
     *
     * @dataProvider statusDataProvider
     */
    public function scopePublished_ステータスが「下書き」の書籍に絞り込むこと(BookStatus $expected, bool $is_draft, Closure $published_at): void
    {
        $this->freezeTime();
        Book::factory()->create(['is_draft' => $is_draft, 'published_at' => $published_at]);
        $expectedCount = $expected === BookStatus::Published ? 1 : 0;
        $this->assertCount($expectedCount, Book::published()->get());
    }

    public static function statusDataProvider()
    {
        return [
            '下書き ON & 現在日時 < 公開日時の場合' => [
                'expected' => BookStatus::Draft, 'is_draft' => true, 'published_at' => fn () => now()->addMinute(),
            ],
            '下書き ON & 現在日時 >= 公開日時の場合' => [
                'expected' => BookStatus::Draft, 'is_draft' => true, 'published_at' => fn () => now(),
            ],
            '下書き OFF & 現在日時 < 公開日時の場合' => [
                'expected' => BookStatus::WillBePublished, 'is_draft' => false, 'published_at' => fn () => now()->addMinute(),
            ],
            '下書き OFF & 現在日時 >= 公開日時の場合' => [
                'expected' => BookStatus::Published, 'is_draft' => false, 'published_at' => fn () => now(),
            ],
        ];
    }

    /** @test */
    public function addCover_書籍に書影が設定されること(): void
    {
        $book = Book::factory()->create();
        $cover = UploadedFile::fake()->image('cover.jpg');
        $book->setCover($cover, ['width' => 200, 'height' => 300]);

        $this->assertDatabaseHas('media', [
            'model_type' => 'book',
            'model_id' => $book->id,
            'file_name' => $cover->name,
            'custom_properties->width' => 200,
            'custom_properties->height' => 300,
        ]);
        $this->assertInstanceOf(Media::class, $book->cover);

        $newCover = UploadedFile::fake()->image('cover-updated.jpg');
        $book->setCover($newCover, ['width' => 300, 'height' => 400]);

        $this->assertDatabaseHas('media', [
            'model_type' => 'book',
            'model_id' => $book->id,
            'file_name' => $newCover->name,
            'custom_properties->width' => 300,
            'custom_properties->height' => 400,
        ]);
        $this->assertInstanceOf(Media::class, $book->cover);
    }

    /** @test */
    public function deleteCover_書籍の書影を更新できること(): void
    {
        $book = Book::factory()->create();
        $book
            ->addMedia(UploadedFile::fake()->image('cover.jpg'))
            ->toMediaCollection('cover');

        $book->deleteCover();

        $this->assertDatabaseEmpty('media');
        $this->assertNull($book->cover);
    }
}
