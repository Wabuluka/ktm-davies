<?php

namespace Database\Seeders;

use App\Models\Benefit;
use App\Models\Book;
use App\Models\Character;
use App\Models\CreationType;
use App\Models\Creator;
use App\Models\EbookStore;
use App\Models\ExternalLink;
use App\Models\GoodsStore;
use App\Models\Series;
use App\Models\Story;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Database\Seeder;

class BookResourceSeeder extends Seeder
{
    public function run(): void
    {
        /** @var \Database\Factories\BookFactory $bookFactory */
        $bookFactory = Book::factory()
            ->published()
            ->sequence(fn (Sequence $sequence) => [
                'title' => 'Book ' . $sequence->index + 1,
                'title_kana' => 'book',
            ])
            ->attachCover(fn () => $this->getDummyImagePath('cover'))
            ->attachBlocks(randomOrder: true) // Display Setting
            ->for(User::first(), 'updatedBy');

        /////////////////////////////
        // Series
        /////////////////////////////

        $seriesFactory = Series::factory()->sequence(fn (Sequence $sequence) => [
            'name' => 'Series ' . $sequence->index + 1,
        ]);

        // Series not linked to any book
        $seriesFactory->create();

        /////////////////////////////
        // Creation
        /////////////////////////////

        CreationType::factory(5)
            ->sequence(
                ['name' => 'Original Work'],
                ['name' => 'Novel'],
                ['name' => 'Illustration'],
                ['name' => 'Character Design'],
                ['name' => 'Page cover'],
                ['name' => 'Other'],
            )
            ->create();

        $creatorFactory = Creator::factory();

        /////////////////////////////
        // BookStore
        /////////////////////////////

        $ebookStores = EbookStore::all();

        $generateEbookStorePivot = fn () => [
            'url' => 'https://example.com/' . fake()->slug(),
        ];

        // Online Bookstore linked to multiple books
        $bookFactory
            ->hasAttached($ebookStores->random(), $generateEbookStorePivot())
            ->count(2)
            ->create();
        // Online bookstore linnked to a single book
        $bookFactory
            ->hasAttached($ebookStores->random(), $generateEbookStorePivot())
            ->create();
        $bookFactory
            ->hasAttached($ebookStores->random(), $generateEbookStorePivot())
            ->create();
        $bookFactory
            ->hasAttached($ebookStores->random(), $generateEbookStorePivot())
            ->create();

        /////////////////////////////
        // Store Benefit
        /////////////////////////////

        $goodsStores = GoodsStore::all();

        $benefitFactory = Benefit::factory()
            ->sequence(fn () => ['store_id' => $goodsStores->random()])
            ->attachThumbnail(fn () => $this->getDummyImagePath('benefit'));

        // Create a store benefit that isn't linked to any book.
        $benefitFactory->create();

        /////////////////////////////
        // Related Works
        /////////////////////////////

        // Create an external link that isn't linked to any book.
        ExternalLink::factory()->create();

        /////////////////////////////
        // Related Works
        /////////////////////////////

        $storyFactory = Story::factory()->sequence(fn (Sequence $sequence) => [
            'title' => 'Story ' . $sequence->index + 1,
        ])
            ->hasAttached($creatorFactory, [
                'sort' => 1,
            ])
            ->hasAttached($creatorFactory, [
                'sort' => 2,
            ])
            ->afterCreating(fn (Story $story) => $story
                ->addMedia($this->getDummyImagePath('story'))
                ->preservingOriginal()
                ->toMediaCollection('thumbnail'));

        // 書籍に紐付かない収録作品を作成 Create a story that doesn't linked to any book.
        $storyFactory->create();

        /////////////////////////////
        // Character
        /////////////////////////////

        $createCharacterFactory = fn () => Character::factory()
            ->for($seriesFactory)
            ->sequence(fn (Sequence $sequence) => [
                'name' => 'Character ' . $sequence->index + 1,
            ])
            ->afterCreating(fn (Character $character) => $character
                ->addMedia($this->getDummyImagePath('character'))
                ->preservingOriginal()
                ->toMediaCollection('thumbnail'));

        // A character that doesn't linked to any book.
        $createCharacterFactory()->create();
    }

    /**
     * Get path of dummy image
     */
    private function getDummyImagePath(string $imageType): string
    {
        return database_path("seeders/assets/images/{$imageType}/" . match ($imageType) {
            'cover' => collect([
                '01.jpeg',
                '02.jpeg',
            ])->random(),

            'character' => collect([
                '01.jpeg',
                '02.jpeg',
                '03.jpeg',
                '04.jpeg',
            ])->random(),

            'story' => collect([
                '01.jpeg',
                '02.jpeg',
                '03.jpeg',
                '04.jpeg',
            ])->random(),

            'benefit' => collect([
                '01.jpeg',
                '02.jpeg',
                '03.jpeg',
                '04.jpeg',
            ])->random(),
        });
    }
}
