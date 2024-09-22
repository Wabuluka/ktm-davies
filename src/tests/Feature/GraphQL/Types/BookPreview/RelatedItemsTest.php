<?php

namespace Tests\Feature\GraphQL\Types\BookPreview;

use App\GraphQL\Enums\AdultScopeType;
use App\Models\Book;
use App\Models\BookPreview;
use App\Models\ExternalLink;
use App\Models\RelatedItem;
use App\Models\Site;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Str;
use Illuminate\Testing\Fluent\AssertableJson;
use Nuwave\Lighthouse\Schema\Source\SchemaSourceProvider;
use Nuwave\Lighthouse\Testing\UsesTestSchema;
use Tests\TestCase;

class RelatedItemsTest extends TestCase
{
    use UsesTestSchema;

    private string $findBookPreviewQuery;

    protected function setUp(): void
    {
        parent::setUp();

        $this->artisan('lighthouse:clear-cache');

        $originalSchema = $this->app
            ->make(SchemaSourceProvider::class)
            ->getSchemaString();

        $testSchema = $originalSchema . /** @lang GraphQL */ '
        extend type Query {
            testBookPreview(
                siteId: ID!,
                token: String!
            ): BookPreview @field(resolver: "BookPreview")
        }
        ';

        $this->schema = $testSchema;

        $this->setUpTestSchema();

        $this->findBookPreviewQuery = /** @lang GraphQL */ '
            query findBookPreview(
                $token: String!,
                $siteId: ID!,
                $adult: AdultScopeType!
            ) {
                testBookPreview(siteId: $siteId, token: $token) {
                    relatedItems(scope: { siteId: $siteId, adult: $adult }) {
                        description
                        relatable {
                            __typename
                            ... on Book {
                                id
                                title
                            }
                            ... on ExternalLink {
                                id
                                title
                            }
                        }
                    }
                }
            }';
    }

    /** @test */
    public function 公開中の書籍のみ取得できること(): void
    {
        $factory = Book::factory()->published()->hasAttached($site = Site::factory()->create());
        $published = $factory->published()->create();
        $draft = $factory->draft()->create();
        $link = ExternalLink::factory()->create();
        $book = $factory
            ->has(RelatedItem::factory()->for($published, 'relatable')->state([
                'description' => '公開中の関連作品', 'sort' => 1,
            ]))
            ->has(RelatedItem::factory()->for($draft, 'relatable')->state([
                'description' => '非公開の関連作品', 'sort' => 2,
            ]))
            ->has(RelatedItem::factory()->for($link, 'relatable')->state([
                'description' => '外部リンク', 'sort' => 3,
            ]))
            ->create();
        BookPreview::create([
            'token' => $token = Str::uuid(),
            'model' => $book->load([
                'relatedItems.relatable' => fn (MorphTo $morphTo) => $morphTo->morphWith([
                    Book::class => ['sites'],
                    ExternalLink::class => [],
                ]),
            ]),
            'file_paths' => [],
        ]);

        $this
            ->graphQL($this->findBookPreviewQuery, [
                'token' => $token,
                'siteId' => $site->id,
                'adult' => AdultScopeType::INCLUDE->name,
            ])
            ->assertGraphQLErrorFree()
            ->assertJson(fn (AssertableJson $json) => $json
                ->has('data.testBookPreview', fn (AssertableJson $json) => $json
                    ->has('relatedItems', 2)
                    ->has('relatedItems.0', fn (AssertableJson $json) => $json
                        ->where('description', '公開中の関連作品')
                        ->where('relatable.__typename', 'Book')
                        ->where('relatable.title', $published->title)
                        ->etc()
                    )
                    ->has('relatedItems.1', fn (AssertableJson $json) => $json
                        ->where('description', '外部リンク')
                        ->where('relatable.__typename', 'ExternalLink')
                        ->where('relatable.title', $link->title)
                        ->etc()
                    )
                )
            );
    }

    /** @test */
    public function プレビュー中のサイトで公開される書籍のみ取得できること(): void
    {
        [$site, $anotherSite] = Site::factory(2)->create();
        $sameSiteBook = Book::factory()->published()->hasAttached($site)->create();
        $anotherSiteBook = Book::factory()->published()->hasAttached($anotherSite)->create();
        $book = Book::factory()->published()->hasAttached($site)
            ->has(RelatedItem::factory()->for($sameSiteBook, 'relatable')->state([
                'description' => '同じサイトの関連作品',
            ]))
            ->has(RelatedItem::factory()->for($anotherSiteBook, 'relatable')->state([
                'description' => '違うサイトの関連作品',
            ]))
            ->create();
        BookPreview::create([
            'token' => $token = Str::uuid(),
            'model' => $book->load([
                'relatedItems.relatable' => fn (MorphTo $morphTo) => $morphTo->morphWith([
                    Book::class => ['sites'],
                    ExternalLink::class => [],
                ]),
            ]),
            'file_paths' => [],
        ]);

        $this
            ->graphQL($this->findBookPreviewQuery, [
                'token' => $token,
                'siteId' => $site->id,
                'adult' => AdultScopeType::INCLUDE->name,
            ])
            ->assertGraphQLErrorFree()
            ->assertJson(fn (AssertableJson $json) => $json
                ->has('data.testBookPreview', fn (AssertableJson $json) => $json
                    ->has('relatedItems', 1)
                    ->has('relatedItems.0', fn (AssertableJson $json) => $json
                        ->where('description', '同じサイトの関連作品')
                        ->where('relatable.title', $sameSiteBook->title)
                        ->etc()
                    )
                )
            );
    }

    /** @test */
    public function 指定した成人向けフラグの条件に合致する書籍のみ取得できること(): void
    {
        $factory = Book::factory()->published()->hasAttached($site = Site::factory()->create());
        $adultBook = $factory->adult(true)->create();
        $allAgeBook = $factory->adult(false)->create();
        $book = $factory
            ->has(RelatedItem::factory()->for($adultBook, 'relatable')->state([
                'description' => '成人向けの関連作品',
            ]))
            ->has(RelatedItem::factory()->for($allAgeBook, 'relatable')->state([
                'description' => '全年齢向けの関連作品',
            ]))
            ->create();
        BookPreview::create([
            'token' => $token = Str::uuid(),
            'model' => $book->load([
                'relatedItems.relatable' => fn (MorphTo $morphTo) => $morphTo->morphWith([
                    Book::class => ['sites'],
                    ExternalLink::class => [],
                ]),
            ]),
            'file_paths' => [],
        ]);

        $this
            ->graphQL($this->findBookPreviewQuery, [
                'token' => $token,
                'siteId' => $site->id,
                'adult' => AdultScopeType::EXCLUDE->name,
            ])
            ->assertGraphQLErrorFree()
            ->assertJson(fn (AssertableJson $json) => $json
                ->has('data.testBookPreview', fn (AssertableJson $json) => $json
                    ->has('relatedItems', 1)
                    ->has('relatedItems.0', fn (AssertableJson $json) => $json
                        ->where('description', '全年齢向けの関連作品')
                        ->where('relatable.title', $allAgeBook->title)
                        ->etc()
                    )
                )
            );
        $this
            ->graphQL($this->findBookPreviewQuery, [
                'token' => $token,
                'siteId' => $site->id,
                'adult' => AdultScopeType::ONLY->name,
            ])
            ->assertGraphQLErrorFree()
            ->assertJson(fn (AssertableJson $json) => $json
                ->has('data.testBookPreview', fn (AssertableJson $json) => $json
                    ->has('relatedItems', 1)
                    ->has('relatedItems.0', fn (AssertableJson $json) => $json
                        ->where('description', '成人向けの関連作品')
                        ->where('relatable.title', $adultBook->title)
                        ->etc()
                    )
                )
            );
        $this
            ->graphQL($this->findBookPreviewQuery, [
                'token' => $token,
                'siteId' => $site->id,
                'adult' => AdultScopeType::INCLUDE->name,
            ])
            ->assertGraphQLErrorFree()
            ->assertJson(fn (AssertableJson $json) => $json
                ->has('data.testBookPreview', fn (AssertableJson $json) => $json
                    ->has('relatedItems', 2)
                    ->where('relatedItems.0.description', '成人向けの関連作品')
                    ->where('relatedItems.1.description', '全年齢向けの関連作品')
                )
            );
    }
}
