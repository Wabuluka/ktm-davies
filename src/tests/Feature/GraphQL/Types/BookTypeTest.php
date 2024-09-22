<?php

namespace Tests\Feature\GraphQL\Types;

use App\Enums\BlockType;
use App\Models\Block;
use App\Models\Book;
use App\Models\ExternalLink;
use App\Models\RelatedItem;
use App\Models\Site;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Testing\Fluent\AssertableJson;
use Nuwave\Lighthouse\Schema\Source\SchemaSourceProvider;
use Nuwave\Lighthouse\Testing\UsesTestSchema;
use Tests\TestCase;

class BookTypeTest extends TestCase
{
    use UsesTestSchema;

    protected function setUp(): void
    {
        parent::setUp();

        $this->artisan('lighthouse:clear-cache');

        $originalSchema = $this->app
            ->make(SchemaSourceProvider::class)
            ->getSchemaString();

        $testSchema = $originalSchema . /** @lang GraphQL */ '
        extend type Query {
            testBook(id: ID! @eq): Book @find
        }
        ';

        $this->schema = $testSchema;

        $this->setUpTestSchema();
    }

    /** @test */
    public function blocks_表示フラグがONのブロックのみ返却すること(): void
    {
        $book = Book::factory()->published()->create();

        Block::factory(3)
            ->for($book)
            ->sequence(fn (Sequence $sequence) => [
                'custom_title' => [
                    'ブロック (1)',
                    'ブロック (2)',
                    'ブロック (3)',
                ][$sequence->index],
                'type_id' => [
                    BlockType::Benefit,
                    BlockType::BookStore,
                    BlockType::Character,
                ][$sequence->index],
                'displayed' => [
                    true,
                    true,
                    false,
                ][$sequence->index],
                'sort' => $sequence->index + 1,
            ])
            ->create();

        $findBookQuery = /** @lang GraphQL */ '
        query findBook($id: ID!) {
            testBook(id: $id) {
                blocks {
                    type
                    customTitle
                }
            }
        }
        ';

        $this
            ->graphQL($findBookQuery, ['id' => $book->id])
            ->assertGraphQLErrorFree()
            ->assertJson(fn (AssertableJson $json) => $json
                ->has('data.testBook.blocks', 2)
                ->has('data.testBook.blocks.0', fn (AssertableJson $json) => $json
                    ->where('type', 'Benefit')
                    ->where('customTitle', 'ブロック (1)')
                )
                ->has('data.testBook.blocks.1', fn (AssertableJson $json) => $json
                    ->where('type', 'BookStore')
                    ->where('customTitle', 'ブロック (2)')
                )
            );
    }

    /** @test */
    public function relatedItems_公開中の関連作品のみ返却すること(): void
    {
        $externalLink = ExternalLink::factory()->create();

        $bookFactory = Book::factory()->hasAttached($site = Site::factory()->create());

        $publishedBook = $bookFactory
            ->published()
            ->create();

        $draftBook = $bookFactory
            ->draft()
            ->create();

        $book = $bookFactory
            ->published()
            ->has(RelatedItem::factory()->for($externalLink, 'relatable'))
            ->has(RelatedItem::factory()->for($publishedBook, 'relatable'))
            ->has(RelatedItem::factory()->for($draftBook, 'relatable'))
            ->create();

        $findBookQuery = /** @lang GraphQL */ '
        query findBook($id: ID!, $siteId: ID!) {
            testBook(id: $id) {
                relatedItems(scope: { siteId: $siteId, adult: INCLUDE}) {
                    relatable {
                        __typename
                        ... on Book {
                            title
                        }
                        ... on ExternalLink {
                            url
                        }
                    }
                }
            }
        }
        ';

        $this
            ->graphQL($findBookQuery, ['id' => $book->id, 'siteId' => $site->id])
            ->assertGraphQLErrorFree()
            ->assertJson(fn (AssertableJson $json) => $json
                ->has('data.testBook.relatedItems', 2)
                ->has('data.testBook.relatedItems.0.relatable', fn (AssertableJson $json) => $json
                    ->where('__typename', 'ExternalLink')
                    ->where('url', $externalLink->url)
                )
                ->has('data.testBook.relatedItems.1.relatable', fn (AssertableJson $json) => $json
                    ->where('__typename', 'Book')
                    ->where('title', $publishedBook->title)
                )
            );
    }

    /** @test */
    public function relatedItems_同じサイトで公開されている関連作品のみ返却すること(): void
    {
        $externalLink = ExternalLink::factory()->create();
        $sameSiteBook = Book::factory()->hasAttached($site = Site::factory()->create())->create();
        $anotherSiteBook = Book::factory()->hasAttached($anotherSite = Site::factory()->create())->create();
        $book = Book::factory()->hasAttached($site)
            ->has(RelatedItem::factory()->for($externalLink, 'relatable'))
            ->has(RelatedItem::factory()->for($sameSiteBook, 'relatable'))
            ->has(RelatedItem::factory()->for($anotherSiteBook, 'relatable'))
            ->create();

        $findBookQuery = /** @lang GraphQL */ '
        query findBook($id: ID!, $siteId: ID!) {
            testBook(id: $id) {
                relatedItems(scope: { siteId: $siteId, adult: INCLUDE }) {
                    relatable {
                        __typename
                        ... on Book {
                            title
                        }
                        ... on ExternalLink {
                            url
                        }
                    }
                }
            }
        }
        ';

        $this
            ->graphQL($findBookQuery, ['id' => $book->id, 'siteId' => $site->id])
            ->assertGraphQLErrorFree()
            ->assertJson(fn (AssertableJson $json) => $json
                ->has('data.testBook.relatedItems', 2)
                ->has('data.testBook.relatedItems.0.relatable', fn (AssertableJson $json) => $json
                    ->where('__typename', 'ExternalLink')
                    ->where('url', $externalLink->url)
                )
                ->has('data.testBook.relatedItems.1.relatable', fn (AssertableJson $json) => $json
                    ->where('__typename', 'Book')
                    ->where('title', $sameSiteBook->title)
                )
            );
    }

    /** @test */
    public function relatedItems_指定した成人向けフラグの条件に合致する関連作品のみ返却すること(): void
    {
        $externalLink = ExternalLink::factory()->create();
        $adultBook = Book::factory()->adult(true)->hasAttached($site = Site::factory()->create())->create();
        $allAgeBook = Book::factory()->adult(false)->hasAttached($site)->create();
        $book = Book::factory()->hasAttached($site)
            ->has(RelatedItem::factory()->for($externalLink, 'relatable'))
            ->has(RelatedItem::factory()->for($adultBook, 'relatable'))
            ->has(RelatedItem::factory()->for($allAgeBook, 'relatable'))
            ->create();

        $includeAdultQuery = /** @lang GraphQL */ '
        query findBook($id: ID!, $siteId: ID!) {
            testBook(id: $id) {
                relatedItems(scope: { siteId: $siteId, adult: INCLUDE }) {
                    relatable {
                        __typename
                        ... on Book {
                            title
                        }
                        ... on ExternalLink {
                            url
                        }
                    }
                }
            }
        }
        ';
        $excludeAdultQuery = /** @lang GraphQL */ '
        query findBook($id: ID!, $siteId: ID!) {
            testBook(id: $id) {
                relatedItems(scope: { siteId: $siteId, adult: EXCLUDE }) {
                    relatable {
                        __typename
                        ... on Book {
                            title
                        }
                        ... on ExternalLink {
                            url
                        }
                    }
                }
            }
        }
        ';
        $onlyAdultQuery = /** @lang GraphQL */ '
        query findBook($id: ID!, $siteId: ID!) {
            testBook(id: $id) {
                relatedItems(scope: { siteId: $siteId, adult: ONLY }) {
                    relatable {
                        __typename
                        ... on Book {
                            title
                        }
                        ... on ExternalLink {
                            url
                        }
                    }
                }
            }
        }
        ';

        $this
            ->graphQL($excludeAdultQuery, ['id' => $book->id, 'siteId' => $site->id])
            ->assertGraphQLErrorFree()
            ->assertJson(fn (AssertableJson $json) => $json
                ->has('data.testBook.relatedItems', 2)
                ->has('data.testBook.relatedItems.0.relatable', fn (AssertableJson $json) => $json
                    ->where('__typename', 'ExternalLink')
                    ->where('url', $externalLink->url)
                )
                ->has('data.testBook.relatedItems.1.relatable', fn (AssertableJson $json) => $json
                    ->where('__typename', 'Book')
                    ->where('title', $allAgeBook->title)
                )
            );
        $this
            ->graphQL($onlyAdultQuery, ['id' => $book->id, 'siteId' => $site->id])
            ->assertGraphQLErrorFree()
            ->assertJson(fn (AssertableJson $json) => $json
                ->has('data.testBook.relatedItems', 2)
                ->has('data.testBook.relatedItems.0.relatable', fn (AssertableJson $json) => $json
                    ->where('__typename', 'ExternalLink')
                    ->where('url', $externalLink->url)
                )
                ->has('data.testBook.relatedItems.1.relatable', fn (AssertableJson $json) => $json
                    ->where('__typename', 'Book')
                    ->where('title', $adultBook->title)
                )
            );
        $this
            ->graphQL($includeAdultQuery, ['id' => $book->id, 'siteId' => $site->id])
            ->assertGraphQLErrorFree()
            ->assertJson(fn (AssertableJson $json) => $json
                ->has('data.testBook.relatedItems', 3)
                ->has('data.testBook.relatedItems.0.relatable', fn (AssertableJson $json) => $json
                    ->where('__typename', 'ExternalLink')
                    ->where('url', $externalLink->url)
                )
                ->has('data.testBook.relatedItems.1.relatable', fn (AssertableJson $json) => $json
                    ->where('__typename', 'Book')
                    ->where('title', $adultBook->title)
                )
                ->has('data.testBook.relatedItems.2.relatable', fn (AssertableJson $json) => $json
                    ->where('__typename', 'Book')
                    ->where('title', $allAgeBook->title)
                )
            );
    }
}
