<?php

namespace Tests\Feature\GraphQL\Queries;

use App\Enums\BlockType;
use App\Models\Benefit;
use App\Models\Block;
use App\Models\Book;
use App\Models\BookFormat;
use App\Models\BookPreview;
use App\Models\BookSize;
use App\Models\BookStore;
use App\Models\Character;
use App\Models\CreationType;
use App\Models\Creator;
use App\Models\EbookStore;
use App\Models\ExternalLink;
use App\Models\Genre;
use App\Models\Label;
use App\Models\RelatedItem;
use App\Models\Series;
use App\Models\Site;
use App\Models\Store;
use App\Models\Story;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Illuminate\Testing\Fluent\AssertableJson;
use Nuwave\Lighthouse\Testing\RefreshesSchemaCache;
use Tests\TestCase;

class BookPreviewQueryTest extends TestCase
{
    use RefreshesSchemaCache;

    private string $getAttributesQuery = /** @lang GraphQL */ '
        query findBookPreview(
            $token: String!
            $siteId: ID!,
        ) {
            bookPreview(token: $token, siteId: $siteId) {
                id
                title
                volume
                isbn13
                releaseDate
                price
                ebookOnly
                specialEdition
                limitedEdition
                adult
                caption
                description
                trialUrl
            }
        }';

    private string $getCoverQuery = /** @lang GraphQL */ '
        query findBookPreview(
            $token: String!
            $siteId: ID!,
        ) {
            bookPreview(token: $token, siteId: $siteId) {
                id
                cover {
                    url
                    customProperties {
                        width
                        height
                    }
                }
            }
        }';

    private string $getBelongsToRelationsQuery = /** @lang GraphQL */ '
        query findBookPreview(
            $token: String!
            $siteId: ID!,
        ) {
            bookPreview(token: $token, siteId: $siteId) {
                id
                title
                label {
                    id
                    name
                }
                genre {
                    id
                    name
                }
                series {
                    id
                    name
                }
                format {
                    id
                    name
                }
                size {
                    id
                    name
                }
            }
        }';

    private string $getCreatorsQuery = /** @lang GraphQL */ '
        query findBookPreview(
            $token: String!
            $siteId: ID!,
        ) {
            bookPreview(token: $token, siteId: $siteId) {
                id
                creators {
                    id
                    name
                    creation {
                        type
                    }
                }
            }
        }';

    private string $getBookStoresQuery = /** @lang GraphQL */ '
        query findBookPreview(
            $token: String!
            $siteId: ID!,
        ) {
            bookPreview(token: $token, siteId: $siteId) {
                id
                bookPurchaseInfo {
                    store {
                        id
                        name
                    }
                    banner {
                        url
                        customProperties {
                            width
                            height
                        }
                    }
                    purchaseUrl
                }
            }
        }';

    private string $getEbookStoresQuery = /** @lang GraphQL */ '
        query findBookPreview(
            $token: String!
            $siteId: ID!,
        ) {
            bookPreview(token: $token, siteId: $siteId) {
                id
                ebookPurchaseInfo {
                    store {
                        id
                        name
                    }
                    banner {
                        url
                        customProperties {
                            width
                            height
                        }
                    }
                    purchaseUrl
                }
            }
        }';

    private string $getBenefitsQuery = /** @lang GraphQL */ '
        query findBookPreview(
            $token: String!
            $siteId: ID!,
        ) {
            bookPreview(token: $token, siteId: $siteId) {
                id
                benefits {
                    id
                    name
                    paid
                    store {
                        name
                        url
                    }
                    thumbnail {
                        url
                        customProperties {
                            width
                            height
                        }
                    }
                }
            }
        }';

    private string $getStoriesQuery = /** @lang GraphQL */ '
        query findBookPreview(
            $token: String!
            $siteId: ID!,
        ) {
            bookPreview(token: $token, siteId: $siteId) {
                id
                stories {
                    id
                    title
                    trialUrl
                    creators {
                        id
                        name
                    }
                    thumbnail {
                        url
                        customProperties {
                            width
                            height
                        }
                    }
                }
            }
        }';

    private string $getCharactersQuery = /** @lang GraphQL */ '
        query findBookPreview(
            $token: String!
            $siteId: ID!,
        ) {
            bookPreview(token: $token, siteId: $siteId) {
                id
                characters {
                    id
                    name
                    description
                    thumbnail {
                        url
                        customProperties {
                            width
                            height
                        }
                    }
                }
            }
        }';

    private string $getRelatedItemsQuery = /** @lang GraphQL */ '
        query findBookPreview(
            $token: String!
            $siteId: ID!,
        ) {
            bookPreview(token: $token, siteId: $siteId) {
                id
                relatedItems(scope: { siteId: $siteId, adult: INCLUDE }) {
                    id
                    description
                    relatable {
                        __typename
                        ... on Book {
                            id
                            title
                            cover {
                                url
                                customProperties {
                                    width
                                    height
                                }
                            }
                        }
                        ... on ExternalLink {
                            title
                            url
                            thumbnail {
                                url
                                customProperties {
                                    width
                                    height
                                }
                            }
                        }
                    }
                }
            }
        }';

    private string $getBlocksQuery = /** @lang GraphQL */ '
        query findBookPreview(
            $token: String!
            $siteId: ID!,
        ) {
            bookPreview(token: $token, siteId: $siteId) {
                id
                blocks {
                    id
                    type
                    customTitle
                    customContent
                }
            }
        }';

    /** @test */
    public function ログインしたユーザのみデータを取得できること(): void
    {
        $this
            ->graphQL($this->getAttributesQuery, ['token' => Str::uuid(), 'siteId' => 0])
            ->assertGraphQLErrorMessage('Unauthenticated.');

        $this->login()
            ->graphQL($this->getAttributesQuery, ['token' => Str::uuid(), 'siteId' => 0])
            ->assertGraphQLErrorFree()
            ->assertJson(fn (AssertableJson $json) => $json->has('data'));
    }

    /** @test */
    public function Bookのアトリビュートがプレビューに含まれること(): void
    {
        $book = Book::factory()->create($attributes = [
            'title' => 'コミックシャイニー',
            'volume' => 'vol.100',
            'isbn13' => '9781234567890',
            'release_date' => '2021-01-01',
            'price' => 1000,
            'ebook_only' => true,
            'special_edition' => true,
            'limited_edition' => true,
            'adult' => false,
            'caption' => 'キャプション',
            'description' => '<p>説明文</p>',
            'trial_url' => 'https://example.com/trial',
        ]);
        $preview = BookPreview::create([
            'token' => Str::uuid(),
            'model' => $book,
            'file_paths' => [],
        ]);

        $this->login()
            ->graphQL($this->getAttributesQuery, ['token' => $preview->token, 'siteId' => 0])
            ->assertGraphQLErrorFree()
            ->assertJsonPath('data.bookPreview', [
                'id' => "{$book->id}",
                'title' => 'コミックシャイニー',
                'volume' => 'vol.100',
                'isbn13' => '9781234567890',
                'releaseDate' => '2021-01-01 00:00:00',
                'price' => 1000,
                'ebookOnly' => true,
                'specialEdition' => true,
                'limitedEdition' => true,
                'adult' => false,
                'caption' => 'キャプション',
                'description' => '<p>説明文</p>',
                'trialUrl' => 'https://example.com/trial',
            ]);
    }

    /** @test */
    public function プレビュー用の書影情報のみがプレビューに含まれること(): void
    {
        $originalCover = UploadedFile::fake()->image('original-cover.jpg', 10, 20);
        $book = Book::factory()->attachCover($originalCover)->create();
        $book->preview_cover = [
            'original_url' => 'https;//cdn.example.com/images/preview-cover.jpg',
            'custom_properties' => [
                'width' => 100,
                'height' => 200,
            ],
        ];
        $preview = BookPreview::create([
            'token' => Str::uuid(),
            'model' => $book,
            'file_paths' => [],
        ]);

        $this->login()
            ->graphQL($this->getCoverQuery, ['token' => $preview->token, 'siteId' => 0])
            ->assertGraphQLErrorFree()
            ->assertJsonPath('data.bookPreview', [
                'id' => "{$book->id}",
                'cover' => [
                    'url' => 'https;//cdn.example.com/images/preview-cover.jpg',
                    'customProperties' => [
                        'width' => 100,
                        'height' => 200,
                    ],
                ],
            ]);
    }

    /** @test */
    public function BelongsToリレーション情報がプレビューに含まれること(): void
    {
        $label = Label::factory()->create(['name' => 'レーベル']);
        $genre = Genre::factory()->create(['name' => 'ジャンル']);
        $series = Series::factory()->create(['name' => 'シリーズ']);
        $format = BookFormat::first();
        $size = BookSize::first();
        $book = Book::factory()
            ->for($label)->for($genre)->for($series)
            ->for($format, 'format')->for($size, 'size')
            ->create();
        $preview = BookPreview::create([
            'token' => Str::uuid(),
            'model' => $book,
            'file_paths' => [],
        ]);

        $this->login()
            ->graphQL($this->getBelongsToRelationsQuery, ['token' => $preview->token, 'siteId' => 0])
            ->assertGraphQLErrorFree()
            ->assertJsonPath('data.bookPreview', [
                'id' => "{$book->id}",
                'title' => $book->title,
                'label' => [
                    'id' => "{$label->id}",
                    'name' => $label->name,
                ],
                'genre' => [
                    'id' => "{$genre->id}",
                    'name' => $genre->name,
                ],
                'series' => [
                    'id' => "{$series->id}",
                    'name' => $series->name,
                ],
                'format' => [
                    'id' => "{$format->id}",
                    'name' => $format->name,
                ],
                'size' => [
                    'id' => "{$size->id}",
                    'name' => $size->name,
                ],
            ]);
    }

    /** @test */
    public function 作家情報ががプレビューに反映されること(): void
    {
        $creator1 = Creator::factory()->create(['name' => '原作者']);
        $creator2 = Creator::factory()->create(['name' => 'イラスト担当']);
        $book = Book::factory()
            ->hasAttached($creator1, [
                'sort' => 1,
                'creation_type' => CreationType::factory()->create(['name' => '原作'])->name,
                'displayed_type' => null,
            ])
            ->hasAttached($creator2, [
                'sort' => 2,
                'creation_type' => CreationType::factory()->create(['name' => 'イラスト'])->name,
                'displayed_type' => 'スペシャルサンクス',
            ])
            ->create();
        $preview = BookPreview::create([
            'token' => Str::uuid(),
            'model' => $book,
            'file_paths' => [],
        ]);

        $this->login()
            ->graphQL($this->getCreatorsQuery, ['token' => $preview->token, 'siteId' => 0])
            ->assertGraphQLErrorFree()
            ->assertJson(fn (AssertableJson $json) => $json
                ->where('data.bookPreview.id', "{$book->id}")
                ->has('data.bookPreview.creators', 2)
                ->has('data.bookPreview.creators.0', fn (AssertableJson $json) => $json
                    ->where('id', "{$creator1->id}")
                    ->where('name', '原作者')
                    ->where('creation.type', '原作'))
                ->has('data.bookPreview.creators.1', fn (AssertableJson $json) => $json
                    ->where('id', "{$creator2->id}")
                    ->where('name', 'イラスト担当')
                    ->where('creation.type', 'スペシャルサンクス')));
    }

    /** @test */
    public function 購入先情報がプレビューに含まれること(): void
    {
        $site = Site::factory()->create();
        $storeFactory = BookStore::factory()
            ->attachBanner(
                fn () => UploadedFile::fake()->image(fake()->slug . '.jpg')
            );
        $store1 = $storeFactory
            ->for(Store::factory()->create(['name' => '購入先1', 'url' => 'https://store1.example.com']))
            ->create();
        $store2 = $storeFactory
            ->for(Store::factory()->create(['name' => '購入先2', 'url' => 'https://store2.example.com']))
            ->create();
        $book = Book::factory()
            ->hasAttached($site)
            ->hasAttached($store1, ['url' => null])
            ->hasAttached($store2, ['url' => 'https://example.com/explicit-purchase-url'])
            ->create();
        $preview = BookPreview::create([
            'token' => Str::uuid(),
            'model' => $book,
            'file_paths' => [],
        ]);

        $this->login()
            ->graphQL($this->getBookStoresQuery, ['token' => $preview->token, 'siteId' => $site->id])
            ->assertGraphQLErrorFree()
            ->assertJson(fn (AssertableJson $json) => $json
                ->where('data.bookPreview.id', "{$book->id}")
                ->has('data.bookPreview.bookPurchaseInfo', 2)
                ->has('data.bookPreview.bookPurchaseInfo.0', fn (AssertableJson $json) => $json
                    ->has('store', fn (AssertableJson $json) => $json
                        ->where('id', "{$store1->store->id}")
                        ->where('name', '購入先1'))
                    ->has('banner', fn (AssertableJson $json) => $json
                        ->whereType('url', 'string')
                        ->has('customProperties.width')
                        ->has('customProperties.height'))
                    ->where('purchaseUrl', 'https://store1.example.com'))
                ->has('data.bookPreview.bookPurchaseInfo.1', fn (AssertableJson $json) => $json
                    ->has('store', fn (AssertableJson $json) => $json
                        ->where('id', "{$store2->store->id}")
                        ->where('name', '購入先2'))
                    ->has('banner', fn (AssertableJson $json) => $json
                        ->whereType('url', 'string')
                        ->has('customProperties.width')
                        ->has('customProperties.height'))
                    ->where('purchaseUrl', 'https://example.com/explicit-purchase-url')));
    }

    /** @test */
    public function 電子書籍の購入先情報がプレビューに含まれること(): void
    {
        $site = Site::factory()->create();
        $storeFactory = EbookStore::factory()
            ->attachBanner(
                fn () => UploadedFile::fake()->image(fake()->slug . '.jpg')
            );
        $store1 = $storeFactory
            ->for(Store::factory()->create(['name' => '購入先1', 'url' => 'https://store1.example.com']))
            ->create();
        $store2 = $storeFactory
            ->for(Store::factory()->create(['name' => '購入先2', 'url' => 'https://store2.example.com']))
            ->create();
        $book = Book::factory()
            ->hasAttached($site)
            ->hasAttached($store1, ['url' => null])
            ->hasAttached($store2, ['url' => 'https://example.com/explicit-purchase-url'])
            ->create();
        $preview = BookPreview::create([
            'token' => Str::uuid(),
            'model' => $book,
            'file_paths' => [],
        ]);

        $this->login()
            ->graphQL($this->getEbookStoresQuery, ['token' => $preview->token, 'siteId' => $site->id])
            ->assertGraphQLErrorFree()
            ->assertJson(fn (AssertableJson $json) => $json
                ->where('data.bookPreview.id', "{$book->id}")
                ->has('data.bookPreview.ebookPurchaseInfo', 2)
                ->has('data.bookPreview.ebookPurchaseInfo.0', fn (AssertableJson $json) => $json
                    ->has('store', fn (AssertableJson $json) => $json
                        ->where('id', "{$store1->store->id}")
                        ->where('name', '購入先1'))
                    ->has('banner', fn (AssertableJson $json) => $json
                        ->whereType('url', 'string')
                        ->has('customProperties.width')
                        ->has('customProperties.height'))
                    ->where('purchaseUrl', 'https://store1.example.com'))
                ->has('data.bookPreview.ebookPurchaseInfo.1', fn (AssertableJson $json) => $json
                    ->has('store', fn (AssertableJson $json) => $json
                        ->where('id', "{$store2->store->id}")
                        ->where('name', '購入先2'))
                    ->has('banner', fn (AssertableJson $json) => $json
                        ->whereType('url', 'string')
                        ->has('customProperties.width')
                        ->has('customProperties.height'))
                    ->where('purchaseUrl', 'https://example.com/explicit-purchase-url')));
    }

    /** @test */
    public function 店舗特典情報がプレビューに含まれること(): void
    {
        $benefit1 = Benefit::factory()
            ->attachThumbnail(UploadedFile::fake()->image('thumb1.jpeg'))
            ->create(['name' => 'タペストリー', 'paid' => true]);
        $benefit2 = Benefit::factory()
            ->attachThumbnail(UploadedFile::fake()->image('thumb2.jpeg'))
            ->create(['name' => 'ポストカード', 'paid' => false]);
        $book = Book::factory()
            ->hasAttached($benefit1)
            ->hasAttached($benefit2)
            ->create();
        $preview = BookPreview::create([
            'token' => Str::uuid(),
            'model' => $book,
            'file_paths' => [],
        ]);

        $this->login()
            ->graphQL($this->getBenefitsQuery, ['token' => $preview->token, 'siteId' => 0])
            ->assertGraphQLErrorFree()
            ->assertJson(fn (AssertableJson $json) => $json
                ->where('data.bookPreview.id', "{$book->id}")
                ->has('data.bookPreview.benefits', 2)
                ->has('data.bookPreview.benefits.0', fn (AssertableJson $json) => $json
                    ->where('id', "{$benefit1->id}")
                    ->where('name', 'タペストリー')
                    ->where('paid', true)
                    ->has('store', fn (AssertableJson $json) => $json
                        ->whereType('name', 'string')
                        ->whereType('url', 'string'))
                    ->has('thumbnail', fn (AssertableJson $json) => $json
                        ->where('url', fn ($value) => str_contains($value, 'thumb1.jpeg'))
                        ->has('customProperties.width')
                        ->has('customProperties.height')))
                ->has('data.bookPreview.benefits.1', fn (AssertableJson $json) => $json
                    ->where('id', "{$benefit2->id}")
                    ->where('name', 'ポストカード')
                    ->where('paid', false)
                    ->has('store', fn (AssertableJson $json) => $json
                        ->whereType('name', 'string')
                        ->whereType('url', 'string'))
                    ->has('thumbnail', fn (AssertableJson $json) => $json
                        ->where('url', fn ($value) => str_contains($value, 'thumb2.jpeg'))
                        ->has('customProperties.width')
                        ->has('customProperties.height'))));
    }

    /** @test */
    public function 収録作品情報がプレビューに含まれること(): void
    {
        $creator1 = Creator::factory()->create(['name' => '原作者']);
        $creator2 = Creator::factory()->create(['name' => '挿絵担当']);
        $story1 = Story::factory()
            ->attachThumbnail(UploadedFile::fake()->image('thumb1.jpeg'))
            ->hasAttached($creator1, ['sort' => 1])
            ->create([
                'title' => '第1話',
                'trial_url' => 'https://example.com/trial/ep1',
            ]);
        $story2 = Story::factory()
            ->attachThumbnail(UploadedFile::fake()->image('thumb2.jpeg'))
            ->hasAttached($creator2, ['sort' => 1])
            ->hasAttached($creator1, ['sort' => 2])
            ->create([
                'title' => '第2話',
                'trial_url' => 'https://example.com/trial/ep2',
            ]);
        $book = Book::factory()
            ->hasAttached($story1)
            ->hasAttached($story2)
            ->create();
        $preview = BookPreview::create([
            'token' => Str::uuid(),
            'model' => $book,
            'file_paths' => [],
        ]);

        $this->login()
            ->graphQL($this->getStoriesQuery, ['token' => $preview->token, 'siteId' => 0])
            ->assertGraphQLErrorFree()
            ->assertJson(fn (AssertableJson $json) => $json
                ->where('data.bookPreview.id', "{$book->id}")
                ->has('data.bookPreview.stories', 2)
                ->has('data.bookPreview.stories.0', fn (AssertableJson $json) => $json
                    ->where('id', "{$story1->id}")
                    ->where('title', '第1話')
                    ->where('trialUrl', 'https://example.com/trial/ep1')
                    ->has('creators', 1)
                    ->has('creators.0', fn (AssertableJson $json) => $json
                        ->where('id', "{$creator1->id}")
                        ->where('name', '原作者'))
                    ->has('thumbnail', fn (AssertableJson $json) => $json
                        ->where('url', fn ($value) => str_contains($value, 'thumb1.jpeg'))
                        ->has('customProperties.width')
                        ->has('customProperties.height')))
                ->has('data.bookPreview.stories.1', fn (AssertableJson $json) => $json
                    ->where('id', "{$story2->id}")
                    ->where('title', '第2話')
                    ->where('trialUrl', 'https://example.com/trial/ep2')
                    ->has('creators', 2)
                    ->has('creators.0', fn (AssertableJson $json) => $json
                        ->where('id', "{$creator2->id}")
                        ->where('name', '挿絵担当'))
                    ->has('creators.1', fn (AssertableJson $json) => $json
                        ->where('id', "{$creator1->id}")
                        ->where('name', '原作者'))
                    ->has('thumbnail', fn (AssertableJson $json) => $json
                        ->where('url', fn ($value) => str_contains($value, 'thumb2.jpeg'))
                        ->has('customProperties.width')
                        ->has('customProperties.height'))));
    }

    /** @test */
    public function キャラクター情報がプレビューに含まれること(): void
    {
        $character1 = Character::factory()
            ->attachThumbnail(UploadedFile::fake()->image('thumb1.jpeg'))
            ->create([
                'name' => '主人公',
                'description' => '毎回登場する',
            ]);
        $character2 = Character::factory()
            ->attachThumbnail(UploadedFile::fake()->image('thumb2.jpeg'))
            ->create([
                'name' => '脇役',
                'description' => '偶に登場する',
            ]);
        $book = Book::factory()
            ->hasAttached($character1)
            ->hasAttached($character2)
            ->create();
        $preview = BookPreview::create([
            'token' => Str::uuid(),
            'model' => $book,
            'file_paths' => [],
        ]);

        $this->login()
            ->graphQL($this->getCharactersQuery, ['token' => $preview->token, 'siteId' => 0])
            ->assertGraphQLErrorFree()
            ->assertJson(fn (AssertableJson $json) => $json
                ->where('data.bookPreview.id', "{$book->id}")
                ->has('data.bookPreview.characters', 2)
                ->has('data.bookPreview.characters.0', fn (AssertableJson $json) => $json
                    ->where('id', "{$character1->id}")
                    ->where('name', '主人公')
                    ->where('description', '毎回登場する')
                    ->has('thumbnail', fn (AssertableJson $json) => $json
                        ->where('url', fn ($value) => str_contains($value, 'thumb1.jpeg'))
                        ->has('customProperties.width')
                        ->has('customProperties.height')))
                ->has('data.bookPreview.characters.1', fn (AssertableJson $json) => $json
                    ->where('id', "{$character2->id}")
                    ->where('name', '脇役')
                    ->where('description', '偶に登場する')
                    ->has('thumbnail', fn (AssertableJson $json) => $json
                        ->where('url', fn ($value) => str_contains($value, 'thumb2.jpeg'))
                        ->has('customProperties.width')
                        ->has('customProperties.height'))));
    }

    /** @test */
    public function 関連作品がプレビューに含まれること(): void
    {
        $book = Book::factory()
            ->hasAttached($site = Site::factory()->create())
            ->create([
                'title' => '新・異世界転生 -外伝-',
            ]);
        $relatedBook = RelatedItem::factory()
            ->for($book)
            ->for(Book::factory()
                ->hasAttached($site)
                ->attachCover(UploadedFile::fake()->image('cover.jpg'))
                ->create([
                    'title' => '新・異世界転生',
                    'volume' => 'vol.1',
                ]), 'relatable')
            ->create([
                'description' => '原作小説 (1)',
                'sort' => 1,
            ]);
        $relatedLink = RelatedItem::factory()
            ->for($book)
            ->for(ExternalLink::factory()
                ->attachThumbnail(UploadedFile::fake()->image('thumbnail.jpg'))
                ->create([
                    'title' => '新・異世界転生 アンソロジーコミック',
                    'url' => 'https://example.com/anthology',
                ]), 'relatable')
            ->create([
                'description' => 'アンソロジー',
                'sort' => 2,
            ]);
        $preview = BookPreview::create([
            'token' => Str::uuid(),
            'model' => $book->load('relatedItems.relatable'),
            'file_paths' => [],
        ]);

        $this->login()
            ->graphQL($this->getRelatedItemsQuery, ['token' => $preview->token, 'siteId' => $site->id])
            ->assertGraphQLErrorFree()
            ->assertJson(fn (AssertableJson $json) => $json
                ->where('data.bookPreview.id', "{$book->id}")
                ->has('data.bookPreview.relatedItems', 2)
                ->has('data.bookPreview.relatedItems.0', fn (AssertableJson $json) => $json
                    ->where('id', "{$relatedBook->id}")
                    ->where('description', '原作小説 (1)')
                    ->has('relatable', fn (AssertableJson $json) => $json
                        ->where('__typename', 'Book')
                        ->where('id', "{$relatedBook->relatable->id}")
                        ->where('title', '新・異世界転生')
                        ->has('cover', fn (AssertableJson $json) => $json
                            ->whereType('url', 'string')
                            ->has('customProperties.width')
                            ->has('customProperties.height'))))
                ->has('data.bookPreview.relatedItems.1', fn (AssertableJson $json) => $json
                    ->where('id', "{$relatedLink->id}")
                    ->where('description', 'アンソロジー')
                    ->has('relatable', fn (AssertableJson $json) => $json
                        ->where('__typename', 'ExternalLink')
                        ->where('title', '新・異世界転生 アンソロジーコミック')
                        ->where('url', 'https://example.com/anthology')
                        ->has('thumbnail', fn (AssertableJson $json) => $json
                            ->whereType('url', 'string')
                            ->has('customProperties.width')
                            ->has('customProperties.height')))));
    }

    /** @test */
    public function 表示設定がプレビューに含まれること(): void
    {
        $book = Book::factory()->create();
        $block1 = Block::factory()->for($book)->create([
            'sort' => 4, 'displayed' => true, 'type_id' => BlockType::Common,
        ]);
        $block2 = Block::factory()->for($book)->create([
            'sort' => 3, 'displayed' => true, 'type_id' => BlockType::EbookStore,
            'custom_content' => '<p>販売が終了している商品リンクもございます。</p>',
        ]);
        $block3 = Block::factory()->for($book)->create([
            'sort' => 2, 'displayed' => true, 'type_id' => BlockType::Custom,
            'custom_content' => '<p>自由欄</p>', 'custom_title' => '自由欄',
        ]);
        $preview = BookPreview::create([
            'token' => Str::uuid(),
            'model' => $book,
            'file_paths' => [],
        ]);

        $this->login()
            ->graphQL($this->getBlocksQuery, ['token' => $preview->token, 'siteId' => 0])
            ->assertGraphQLErrorFree()
            ->assertJson(fn (AssertableJson $json) => $json
                ->where('data.bookPreview.id', "{$book->id}")
                ->has('data.bookPreview.blocks', 3)
                ->has('data.bookPreview.blocks.0', fn (AssertableJson $json) => $json
                    ->where('id', "{$block3->id}")
                    ->where('type', 'Custom')
                    ->where('customTitle', '自由欄')
                    ->where('customContent', '<p>自由欄</p>'))
                ->has('data.bookPreview.blocks.1', fn (AssertableJson $json) => $json
                    ->where('id', "{$block2->id}")
                    ->where('type', 'EbookStore')
                    ->where('customContent', '<p>販売が終了している商品リンクもございます。</p>')
                    ->etc())
                ->has('data.bookPreview.blocks.2', fn (AssertableJson $json) => $json
                    ->where('id', "{$block1->id}")
                    ->where('type', 'Common')
                    ->etc()));
    }
}
