<?php

namespace Tests\Feature\GraphQL\Types\Book;

use App\Models\Book;
use App\Models\BookStore;
use App\Models\Site;
use App\Models\Store;
use Database\Seeders\Helpers\SeedBookStoreHelper;
use Database\Seeders\Helpers\SeedSiteHelper;
use Database\Seeders\Helpers\SeedStoreHelper;
use Database\Seeders\StoreSeeder;
use Illuminate\Testing\Fluent\AssertableJson;
use Nuwave\Lighthouse\Schema\Source\SchemaSourceProvider;
use Nuwave\Lighthouse\Testing\UsesTestSchema;
use Tests\TestCase;

class BookPurchaseInfoTest extends TestCase
{
    use UsesTestSchema;

    private string $primaryQuery;

    private string $allQuery;

    protected function setUp(): void
    {
        parent::setUp();

        $this->artisan('lighthouse:clear-cache');

        $originalSchema = $this->app
            ->make(SchemaSourceProvider::class)
            ->getSchemaString();

        $testSchema = $originalSchema . /** @lang GraphQL */ '
        extend type Query {
            testBook(id: ID! @eq, siteId: ID!): Book @find
        }
        ';

        $this->schema = $testSchema;

        $this->setUpTestSchema();

        $this->primaryQuery = /** @lang GraphQL */ '
            query findBook($id: ID!, $siteId: ID!) {
                testBook(id: $id, siteId: $siteId) {
                    primaryBookPurchaseInfo {
                        store {
                            id
                            name
                        }
                        purchaseUrl
                    }
                }
            }';

        $this->allQuery = /** @lang GraphQL */ '
            query findBook($id: ID!, $siteId: ID!) {
                testBook(id: $id, siteId: $siteId) {
                    bookPurchaseInfo {
                        store {
                            id
                            name
                        }
                        purchaseUrl
                    }
                }
            }';
    }

    /** @test */
    public function primary_一覧に表示するフラグがONの購入先情報のみ取得できること(): void
    {
        $book1 = Book::factory()
            ->hasAttached($site = Site::factory()->create())
            ->hasAttached(
                BookStore::factory()->for(Store::factory()->create(['name' => 'Primary Store'])),
                ['is_primary' => true]
            )
            ->create(['title' => 'Book 1']);

        $book2 = Book::factory()
            ->hasAttached($site)
            ->hasAttached(
                BookStore::factory()->for(Store::factory()->create(['name' => 'Common Store'])),
                ['is_primary' => false]
            )
            ->create(['title' => 'Book 2']);

        $this
            ->graphQL($this->primaryQuery, ['id' => $book1->id, 'siteId' => $site->id])
            ->assertGraphQLErrorFree()
            ->assertJson(
                fn (AssertableJson $json) => $json
                    ->has(
                        'data.testBook.primaryBookPurchaseInfo',
                        fn (AssertableJson $json) => $json
                            ->where('store.name', 'Primary Store')
                            ->etc()
                    )
            );

        $this
            ->graphQL($this->primaryQuery, ['id' => $book2->id, 'siteId' => $site->id])
            ->assertGraphQLErrorFree()
            ->assertJson(
                fn (AssertableJson $json) => $json
                    ->where('data.testBook.primaryBookPurchaseInfo', null)
            );
    }

    /** @test */
    public function primary_purchaseUrl_一覧に表示するフラグがONの購入先情報のみ取得できること(): void
    {
        $ktcom = SeedSiteHelper::createKtcom();
        SeedStoreHelper::createAmazon();
        $amazon = SeedBookStoreHelper::createAmazon();

        $book = Book::factory()
            ->hasAttached($ktcom)
            ->hasAttached($amazon, ['is_primary' => true])
            ->create([
                'title' => 'Book 1',
                'volume' => '1',
                'isbn13' => '9784799218020',
            ]);

        $this
            ->graphQL($this->primaryQuery, ['id' => $book->id, 'siteId' => $ktcom->id])
            ->assertGraphQLErrorFree()
            ->assertJson(
                fn (AssertableJson $json) => $json
                    ->has(
                        'data.testBook.primaryBookPurchaseInfo',
                        fn (AssertableJson $json) => $json
                            ->where('store.name', 'Amazon')
                            ->where('purchaseUrl', 'https://www.amazon.co.jp/exec/obidos/ASIN/4799218026/k1040041-22')
                            ->etc()
                    )
            );
    }

    /**
     * @test
     *
     * @dataProvider purchaseUrlProvider
     */
    public function purchaseUrl_購入先URLの登録がない場合、紙書籍の購入先URLを自動生成して返却すること(\Closure $createSite, array $bookAttributes, array $expectations): void
    {
        $this->seed(StoreSeeder::class);
        $book = Book::factory()
            ->hasAttached(BookStore::all())
            ->hasAttached($site = $createSite())
            ->published()
            ->create($bookAttributes);

        $response = $this
            ->graphQL($this->allQuery, ['id' => $book->id, 'siteId' => $site->id])
            ->assertGraphQLErrorFree()
            ->assertJson(
                fn (AssertableJson $json) => $json
                    ->has('data.testBook.bookPurchaseInfo', count($expectations))
            );
        foreach ($expectations as $i => $expected) {
            $response->assertJson(
                fn (AssertableJson $json) => $json
                    ->has("data.testBook.bookPurchaseInfo.{$i}", fn (AssertableJson $json) => $json
                        ->where('store.name', $expected['store.name'])
                        ->where('purchaseUrl', $expected['purchaseUrl'])
                        ->etc())
            );
        }
    }

    public static function purchaseUrlProvider(): iterable
    {
        yield 'KTC本体サイト' => [
            'createSite' => fn () => SeedSiteHelper::createKtcom(),
            'bookAttributes' => [
                'title' => 'Book 1',
                'volume' => '1',
                'isbn13' => '9784799218020',
            ],
            'expected' => [
                [
                    'store.name' => 'Amazon',
                    'purchaseUrl' => 'https://www.amazon.co.jp/exec/obidos/ASIN/4799218026/k1040041-22',
                ],
                [
                    'store.name' => '楽天ブックス',
                    'purchaseUrl' => 'https://books.rakuten.co.jp/rdt/item/?sid=213310&sno=ISBN%3A9784799218020',
                ],
                [
                    'store.name' => 'セブンネットショッピング',
                    'purchaseUrl' => 'https://7net.omni7.jp/detail_isbn/9784799218020',
                ],
                [
                    'store.name' => 'e-hon',
                    'purchaseUrl' => 'https://www.e-hon.ne.jp/bec/SA/Forward?isbn=9784799218020&mode=kodawari&button=btnKodawari',
                ],
                [
                    'store.name' => 'ヨドバシ・ドット・コム',
                    'purchaseUrl' => 'https://www.yodobashi.com/category/81001/?word=9784799218020',
                ],
                [
                    'store.name' => 'TSUTAYA オンラインショッピング',
                    'purchaseUrl' => 'https://shop.tsutaya.co.jp/book/product/9784799218020/',
                ],
                [
                    'store.name' => '紀伊国屋書店',
                    'purchaseUrl' => 'https://www.kinokuniya.co.jp/f/dsg-01-9784799218020',
                ],
                [
                    'store.name' => 'メロンブックス',
                    'purchaseUrl' => 'https://www.melonbooks.co.jp/maker/index.php?maker_id=899&pageno=1&search_target%5B0%5D=1&name=Book+1',
                ],
            ],
        ];

        yield 'スリーズロゼ' => [
            'createSite' => fn () => SeedSiteHelper::createCeriseRose(),
            'bookAttributes' => [
                'title' => 'Book 1',
                'volume' => '1',
                'isbn13' => '9784799218020',
            ],
            'expected' => [
                [
                    'store.name' => 'Amazon',
                    'purchaseUrl' => 'https://www.amazon.co.jp/exec/obidos/ASIN/4799218026/k1040041-22',
                ],
                [
                    'store.name' => '楽天ブックス',
                    'purchaseUrl' => 'https://hb.afl.rakuten.co.jp/hgc/34df531b.2f0a5b2c.34df531c.15b50105/_RTLink23219?pc=https%3A%2F%2Fbooks.rakuten.co.jp%2Frdt%2Fitem%2F%3Fsid%3D213310%26sno%3DISBN%253A9784799218020',
                ],
                [
                    'store.name' => 'セブンネットショッピング',
                    'purchaseUrl' => 'https://ck.jp.ap.valuecommerce.com/servlet/referral?sid=3701759&pid=889550804&vc_url=https%3A%2F%2F7net.omni7.jp%2Fdetail_isbn%2F9784799218020',
                ],
                [
                    'store.name' => 'e-hon',
                    'purchaseUrl' => 'https://www.e-hon.ne.jp/bec/SA/Forward?isbn=9784799218020&mode=kodawari&button=btnKodawari',
                ],
                [
                    'store.name' => 'ヨドバシ・ドット・コム',
                    'purchaseUrl' => 'https://www.yodobashi.com/category/81001/?word=9784799218020',
                ],
                [
                    'store.name' => 'TSUTAYA オンラインショッピング',
                    'purchaseUrl' => 'https://ck.jp.ap.valuecommerce.com/servlet/referral?sid=3701759&pid=889559320&vc_url=https%3A%2F%2Fshop.tsutaya.co.jp%2Fbook%2Fproduct%2F9784799218020%2F',
                ],
                [
                    'store.name' => '紀伊国屋書店',
                    'purchaseUrl' => 'https://ck.jp.ap.valuecommerce.com/servlet/referral?sid=3701759&pid=889559322&vc_url=https%3A%2F%2Fwww.kinokuniya.co.jp%2Ff%2Fdsg-01-9784799218020',
                ],
                [
                    'store.name' => 'メロンブックス',
                    'purchaseUrl' => 'https://www.melonbooks.co.jp/maker/index.php?maker_id=899&pageno=1&search_target%5B0%5D=1&name=Book+1',
                ],
            ],
        ];

        yield 'ショコラシュクレ' => [
            'createSite' => fn () => SeedSiteHelper::createChocolatSucre(),
            'bookAttributes' => [
                'title' => 'Book 1',
                'volume' => '1',
                'isbn13' => '9784799218020',
            ],
            'expected' => [
                [
                    'store.name' => 'Amazon',
                    'purchaseUrl' => 'https://www.amazon.co.jp/exec/obidos/ASIN/4799218026/k1040041-22',
                ],
                [
                    'store.name' => '楽天ブックス',
                    'purchaseUrl' => 'https://hb.afl.rakuten.co.jp/hgc/34df531b.2f0a5b2c.34df531c.15b50105/_RTLink23120?pc=https%3A%2F%2Fbooks.rakuten.co.jp%2Frdt%2Fitem%2F%3Fsid%3D213310%26sno%3DISBN%253A9784799218020',
                ],
                [
                    'store.name' => 'セブンネットショッピング',
                    'purchaseUrl' => 'https://ck.jp.ap.valuecommerce.com/servlet/referral?sid=3585678&pid=889532036&vc_url=https%3A%2F%2F7net.omni7.jp%2Fdetail_isbn%2F9784799218020',
                ],
                [
                    'store.name' => 'e-hon',
                    'purchaseUrl' => 'https://www.e-hon.ne.jp/bec/SA/Forward?isbn=9784799218020&mode=kodawari&button=btnKodawari',
                ],
                [
                    'store.name' => 'ヨドバシ・ドット・コム',
                    'purchaseUrl' => 'https://www.yodobashi.com/category/81001/?word=9784799218020',
                ],
                [
                    'store.name' => 'TSUTAYA オンラインショッピング',
                    'purchaseUrl' => 'https://ck.jp.ap.valuecommerce.com/servlet/referral?sid=3585678&pid=889559314&vc_url=https%3A%2F%2Fshop.tsutaya.co.jp%2Fbook%2Fproduct%2F9784799218020%2F',
                ],
                [
                    'store.name' => '紀伊国屋書店',
                    'purchaseUrl' => 'https://ck.jp.ap.valuecommerce.com/servlet/referral?sid=3585678&pid=889559316&vc_url=https%3A%2F%2Fwww.kinokuniya.co.jp%2Ff%2Fdsg-01-9784799218020',
                ],
                [
                    'store.name' => 'メロンブックス',
                    'purchaseUrl' => 'https://www.melonbooks.co.jp/maker/index.php?maker_id=899&pageno=1&search_target%5B0%5D=1&name=Book+1',
                ],
            ],
        ];

        yield 'ブラックチェリー' => [
            'createSite' => fn () => SeedSiteHelper::createBlackCherry(),
            'bookAttributes' => [
                'title' => 'Book 1',
                'volume' => '1',
                'isbn13' => '9784799218020',
            ],
            'expected' => [
                [
                    'store.name' => 'Amazon',
                    'purchaseUrl' => 'https://www.amazon.co.jp/exec/obidos/ASIN/4799218026/k1040041-22',
                ],
                [
                    'store.name' => '楽天ブックス',
                    'purchaseUrl' => 'https://books.rakuten.co.jp/rdt/item/?sid=213310&sno=ISBN%3A9784799218020',
                ],
                [
                    'store.name' => 'セブンネットショッピング',
                    'purchaseUrl' => 'https://7net.omni7.jp/detail_isbn/9784799218020',
                ],
                [
                    'store.name' => 'e-hon',
                    'purchaseUrl' => 'https://www.e-hon.ne.jp/bec/SA/Forward?isbn=9784799218020&mode=kodawari&button=btnKodawari',
                ],
                [
                    'store.name' => 'ヨドバシ・ドット・コム',
                    'purchaseUrl' => 'https://www.yodobashi.com/category/81001/?word=9784799218020',
                ],
                [
                    'store.name' => 'TSUTAYA オンラインショッピング',
                    'purchaseUrl' => 'https://shop.tsutaya.co.jp/book/product/9784799218020/',
                ],
                [
                    'store.name' => '紀伊国屋書店',
                    'purchaseUrl' => 'https://www.kinokuniya.co.jp/f/dsg-01-9784799218020',
                ],
                [
                    'store.name' => 'メロンブックス',
                    'purchaseUrl' => 'https://www.melonbooks.co.jp/maker/index.php?maker_id=899&pageno=1&search_target%5B0%5D=1&name=Book+1',
                ],
            ],
        ];
    }
}
