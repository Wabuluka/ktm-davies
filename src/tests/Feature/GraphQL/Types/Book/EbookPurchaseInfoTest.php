<?php

namespace Tests\Feature\GraphQL\Types\Book;

use App\Models\Book;
use App\Models\EbookStore;
use App\Models\Site;
use App\Models\Store;
use Database\Seeders\Helpers\SeedEbookStoreHelper;
use Database\Seeders\Helpers\SeedSiteHelper;
use Database\Seeders\Helpers\SeedStoreHelper;
use Database\Seeders\StoreSeeder;
use Illuminate\Testing\Fluent\AssertableJson;
use Nuwave\Lighthouse\Schema\Source\SchemaSourceProvider;
use Nuwave\Lighthouse\Testing\UsesTestSchema;
use Tests\TestCase;

class EbookPurchaseInfoTest extends TestCase
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
                    primaryEbookPurchaseInfo {
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
                    ebookPurchaseInfo {
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
                EbookStore::factory()->for(Store::factory()->create(['name' => 'Primary Store'])),
                ['is_primary' => true]
            )
            ->create(['title' => 'Book 1']);

        $book2 = Book::factory()
            ->hasAttached($site)
            ->hasAttached(
                EbookStore::factory()->for(Store::factory()->create(['name' => 'Common Store'])),
                ['is_primary' => false]
            )
            ->create(['title' => 'Book 2']);

        $this
            ->graphQL($this->primaryQuery, ['id' => $book1->id, 'siteId' => $site->id])
            ->assertGraphQLErrorFree()
            ->assertJson(
                fn (AssertableJson $json) => $json
                    ->has(
                        'data.testBook.primaryEbookPurchaseInfo',
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
                    ->where('data.testBook.primaryEbookPurchaseInfo', null)
            );
    }

    /** @test */
    public function primary_purchaseUrl_一覧に表示するフラグがONの購入先情報のみ取得できること(): void
    {
        $ktcom = SeedSiteHelper::createKtcom();
        SeedStoreHelper::createAmazon();
        $kindle = SeedEbookStoreHelper::createAmazon();

        $book = Book::factory()
            ->hasAttached($ktcom)
            ->hasAttached($kindle, ['is_primary' => true])
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
                        'data.testBook.primaryEbookPurchaseInfo',
                        fn (AssertableJson $json) => $json
                            ->where('store.name', 'Amazon')
                            ->where('purchaseUrl', 'https://www.amazon.co.jp/s?k=4799218026&tag=k1040041-22')
                            ->etc()
                    )
            );
    }

    /**
     * @test
     *
     * @dataProvider purchaseUrlProvider
     */
    public function purchaseUrl_購入先URLの登録がない場合、電子書籍の購入先URLを自動生成して返却すること(\Closure $createSite, array $bookAttributes, array $expectations): void
    {
        $this->seed(StoreSeeder::class);
        $book = Book::factory()
            ->hasAttached(EbookStore::all())
            ->hasAttached($site = $createSite())
            ->published()
            ->create($bookAttributes);

        $response = $this
            ->graphQL($this->allQuery, ['id' => $book->id, 'siteId' => $site->id])
            ->assertGraphQLErrorFree()
            ->assertJson(
                fn (AssertableJson $json) => $json
                    ->has('data.testBook.ebookPurchaseInfo', count($expectations))
            );
        foreach ($expectations as $i => $expected) {
            $response->assertJson(
                fn (AssertableJson $json) => $json
                    ->has("data.testBook.ebookPurchaseInfo.{$i}", fn (AssertableJson $json) => $json
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
                    'purchaseUrl' => 'https://www.amazon.co.jp/s?k=4799218026&tag=k1040041-22',
                ],
                [
                    'store.name' => '楽天ブックス',
                    'purchaseUrl' => 'https://books.rakuten.co.jp/search?sitem=Book+1',
                ],
                [
                    'store.name' => 'DMMブックス',
                    'purchaseUrl' => 'https://www.dmm.com',
                ],
                [
                    'store.name' => 'FANZA',
                    'purchaseUrl' => 'https://www.dmm.co.jp',
                ],
                [
                    'store.name' => 'DLsite',
                    'purchaseUrl' => 'https://www.dlsite.com',
                ],
                [
                    'store.name' => 'ebookJapan',
                    'purchaseUrl' => 'https://bookstore.yahoo.co.jp/search?keyword=Book&publisher=400486',
                ],
                [
                    'store.name' => 'BOOK☆WALKER',
                    'purchaseUrl' => 'https://bookwalker.jp/company/116/?word=Book',
                ],
                [
                    'store.name' => 'BookLive!',
                    'purchaseUrl' => 'https://booklive.jp/search/keyword/keyword/Book/exfasc/1',
                ],
                [
                    'store.name' => 'コミックシーモア',
                    'purchaseUrl' => 'https://www.cmoa.jp/search/result/?header_word=Book&publisher_id=0000413',
                ],
                [
                    'store.name' => 'Renta!',
                    'purchaseUrl' => 'https://renta.papy.co.jp/renta/sc/frm/search?word=Book&publisher=%E3%82%AD%E3%83%AB%E3%82%BF%E3%82%A4%E3%83%A0%E3%82%B3%E3%83%9F%E3%83%A5%E3%83%8B%E3%82%B1%E3%83%BC%E3%82%B7%E3%83%A7%E3%83%B3',
                ],
                [
                    'store.name' => 'めちゃコミック',
                    'purchaseUrl' => 'https://mechacomic.jp/books?text=Book',
                ],
                [
                    'store.name' => 'デジケット',
                    'purchaseUrl' => 'https://www.digiket.com',
                ],
                [
                    'store.name' => 'KTC-Store',
                    'purchaseUrl' => 'https://ktc-store.com/products/list?name=Book+1',
                ],
                [
                    'store.name' => 'ピッコマ',
                    'purchaseUrl' => 'https://piccoma.com/web/search/result?word=Book',
                ],
                [
                    'store.name' => 'LINEマンガ',
                    'purchaseUrl' => 'https://manga.line.me/search_product/list?word=Book',
                ],
                [
                    'store.name' => 'pixivコミック',
                    'purchaseUrl' => 'https://comic.pixiv.net/search?q=Book+1&tab=store',
                ],
                [
                    'store.name' => 'メロンブックス',
                    'purchaseUrl' => 'https://www.melonbooks.co.jp/maker/index.php?maker_id=899&pageno=1&search_target%5B0%5D=2&name=Book+1',
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
                    'purchaseUrl' => 'https://www.amazon.co.jp/s?k=4799218026&tag=k1040041-22',
                ],
                [
                    'store.name' => '楽天ブックス',
                    'purchaseUrl' => 'https://hb.afl.rakuten.co.jp/hgc/34df531b.2f0a5b2c.34df531c.15b50105/_RTLink23219?pc=https%3A%2F%2Fbooks.rakuten.co.jp%2Fsearch%3Fsitem%3DBook%2B1',
                ],
                [
                    'store.name' => 'DMMブックス',
                    'purchaseUrl' => 'https://www.dmm.com',
                ],
                [
                    'store.name' => 'FANZA',
                    'purchaseUrl' => 'https://www.dmm.co.jp',
                ],
                [
                    'store.name' => 'DLsite',
                    'purchaseUrl' => 'https://www.dlsite.com',
                ],
                [
                    'store.name' => 'ebookJapan',
                    'purchaseUrl' => 'https://ck.jp.ap.valuecommerce.com/servlet/referral?sid=3701759&pid=889550798&vc_url=https%3A%2F%2Fbookstore.yahoo.co.jp%2Fsearch%3Fkeyword%3DBook%26genre%3Dbl%26publisher%3D400486%26dealerid%3D30064',
                ],
                [
                    'store.name' => 'BOOK☆WALKER',
                    'purchaseUrl' => 'https://ck.jp.ap.valuecommerce.com/servlet/referral?sid=3701759&pid=889550797&vc_url=https%3A%2F%2Fbookwalker.jp%2Fcompany%2F116%2F%3Fword%3DBook',
                ],
                [
                    'store.name' => 'BookLive!',
                    'purchaseUrl' => 'https://ck.jp.ap.valuecommerce.com/servlet/referral?sid=3701759&pid=889550802&vc_url=https%3A%2F%2Fbooklive.jp%2Fsearch%2Fkeyword%2Fg_ids%2F3%2Fk_ids%2F4833%2Fp_ids%2F740%2Fkeyword%2FBook%2Fexfasc%2F1%3Futm_source%3Dspad%26utm_medium%3Daffiliate%26utm_campaign%3D102%26utm_content%3Dnormal',
                ],
                [
                    'store.name' => 'コミックシーモア',
                    'purchaseUrl' => 'https://ck.jp.ap.valuecommerce.com/servlet/referral?sid=3701759&pid=889550803&vc_url=https%3A%2F%2Fwww.cmoa.jp%2Fsearch%2Fresult%2F%3Fheader_word%3DBook%26publisher_id%3D0000413',
                ],
                [
                    'store.name' => 'Renta!',
                    'purchaseUrl' => 'https://ck.jp.ap.valuecommerce.com/servlet/referral?sid=3701759&pid=889550795&vc_url=https%3A%2F%2Frenta.papy.co.jp%2Frenta%2Fsc%2Ffrm%2Fsearch%3Fword%3DBook%26publisher%3D%25E3%2582%25AD%25E3%2583%25AB%25E3%2582%25BF%25E3%2582%25A4%25E3%2583%25A0%25E3%2582%25B3%25E3%2583%259F%25E3%2583%25A5%25E3%2583%258B%25E3%2582%25B1%25E3%2583%25BC%25E3%2582%25B7%25E3%2583%25A7%25E3%2583%25B3',
                ],
                [
                    'store.name' => 'めちゃコミック',
                    'purchaseUrl' => 'https://mechacomic.jp/books?text=Book&genre=24',
                ],
                [
                    'store.name' => 'デジケット',
                    'purchaseUrl' => 'https://www.digiket.com',
                ],
                [
                    'store.name' => 'KTC-Store',
                    'purchaseUrl' => 'https://ktc-store.com/products/list?name=Book+1',
                ],
                [
                    'store.name' => 'ピッコマ',
                    'purchaseUrl' => 'https://piccoma.com/web/search/result?word=Book',
                ],
                [
                    'store.name' => 'LINEマンガ',
                    'purchaseUrl' => 'https://manga.line.me/search_product/list?word=Book',
                ],
                [
                    'store.name' => 'pixivコミック',
                    'purchaseUrl' => 'https://comic.pixiv.net/search?q=Book+1&tab=store',
                ],
                [
                    'store.name' => 'メロンブックス',
                    'purchaseUrl' => 'https://www.melonbooks.co.jp/maker/index.php?maker_id=899&pageno=1&search_target%5B0%5D=2&name=Book+1',
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
                    'purchaseUrl' => 'https://www.amazon.co.jp/s?k=4799218026&tag=k1040041-22',
                ],
                [
                    'store.name' => '楽天ブックス',
                    'purchaseUrl' => 'https://hb.afl.rakuten.co.jp/hgc/34df531b.2f0a5b2c.34df531c.15b50105/_RTLink23120?pc=https%3A%2F%2Fbooks.rakuten.co.jp%2Fsearch%3Fsitem%3DBook%2B1',
                ],
                [
                    'store.name' => 'DMMブックス',
                    'purchaseUrl' => 'https://www.dmm.com',
                ],
                [
                    'store.name' => 'FANZA',
                    'purchaseUrl' => 'https://www.dmm.co.jp',
                ],
                [
                    'store.name' => 'DLsite',
                    'purchaseUrl' => 'https://www.dlsite.com',
                ],
                [
                    'store.name' => 'ebookJapan',
                    'purchaseUrl' => 'https://ck.jp.ap.valuecommerce.com/servlet/referral?sid=3585678&pid=890588332&vc_url=https%3A%2F%2Fbookstore.yahoo.co.jp%2Fsearch%3Fkeyword%3DBook%26genre%3Dtl%26publisher%3D400486%26dealerid%3D30064',
                ],
                [
                    'store.name' => 'BOOK☆WALKER',
                    'purchaseUrl' => 'https://ck.jp.ap.valuecommerce.com/servlet/referral?sid=3585678&pid=889550792&vc_url=https%3A%2F%2Fbookwalker.jp%2Fcompany%2F116%2F%3Fword%3DBook',
                ],
                [
                    'store.name' => 'BookLive!',
                    'purchaseUrl' => 'https://ck.jp.ap.valuecommerce.com/servlet/referral?sid=3585678&pid=889532041&vc_url=https%3A%2F%2Fbooklive.jp%2Fsearch%2Fkeyword%2Fg_ids%2F7%2Fp_ids%2F740%2Fkeyword%2FBook%2Fexfasc%2F1%3Futm_source%3Dspad%26utm_medium%3Daffiliate%26utm_campaign%3D102%26utm_content%3Dnormal',
                ],
                [
                    'store.name' => 'コミックシーモア',
                    'purchaseUrl' => 'https://ck.jp.ap.valuecommerce.com/servlet/referral?sid=3585678&pid=889532035&vc_url=https%3A%2F%2Fwww.cmoa.jp%2Fsearch%2Fresult%2F%3Fheader_word%3DBook%26publisher_id%3D0000413',
                ],
                [
                    'store.name' => 'Renta!',
                    'purchaseUrl' => 'https://ck.jp.ap.valuecommerce.com/servlet/referral?sid=3585678&pid=889532040&vc_url=https%3A%2F%2Frenta.papy.co.jp%2Frenta%2Fsc%2Ffrm%2Fsearch%3Fword%3DBook%26publisher%3D%25E3%2582%25AD%25E3%2583%25AB%25E3%2582%25BF%25E3%2582%25A4%25E3%2583%25A0%25E3%2582%25B3%25E3%2583%259F%25E3%2583%25A5%25E3%2583%258B%25E3%2582%25B1%25E3%2583%25BC%25E3%2582%25B7%25E3%2583%25A7%25E3%2583%25B3',
                ],
                [
                    'store.name' => 'めちゃコミック',
                    'purchaseUrl' => 'https://mechacomic.jp/books?text=Book&genre=6',
                ],
                [
                    'store.name' => 'デジケット',
                    'purchaseUrl' => 'https://www.digiket.com',
                ],
                [
                    'store.name' => 'KTC-Store',
                    'purchaseUrl' => 'https://ktc-store.com/products/list?name=Book+1',
                ],
                [
                    'store.name' => 'ピッコマ',
                    'purchaseUrl' => 'https://piccoma.com/web/search/result?word=Book',
                ],
                [
                    'store.name' => 'LINEマンガ',
                    'purchaseUrl' => 'https://manga.line.me/search_product/list?word=Book',
                ],
                [
                    'store.name' => 'pixivコミック',
                    'purchaseUrl' => 'https://comic.pixiv.net/search?q=Book+1&tab=store',
                ],
                [
                    'store.name' => 'メロンブックス',
                    'purchaseUrl' => 'https://www.melonbooks.co.jp/maker/index.php?maker_id=899&pageno=1&search_target%5B0%5D=2&name=Book+1',
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
                    'purchaseUrl' => 'https://www.amazon.co.jp/s?k=4799218026&tag=k1040041-22',
                ],
                [
                    'store.name' => '楽天ブックス',
                    'purchaseUrl' => 'https://books.rakuten.co.jp/search?sitem=Book+1',
                ],
                [
                    'store.name' => 'DMMブックス',
                    'purchaseUrl' => 'https://www.dmm.com',
                ],
                [
                    'store.name' => 'FANZA',
                    'purchaseUrl' => 'https://www.dmm.co.jp',
                ],
                [
                    'store.name' => 'DLsite',
                    'purchaseUrl' => 'https://www.dlsite.com',
                ],
                [
                    'store.name' => 'ebookJapan',
                    'purchaseUrl' => 'https://bookstore.yahoo.co.jp/search?keyword=Book&genre=adult&publisher=400486',
                ],
                [
                    'store.name' => 'BOOK☆WALKER',
                    'purchaseUrl' => 'https://bookwalker.jp/company/116/?word=Book',
                ],
                [
                    'store.name' => 'BookLive!',
                    'purchaseUrl' => 'https://booklive.jp/search/keyword/keyword/Book/exfasc/1',
                ],
                [
                    'store.name' => 'コミックシーモア',
                    'purchaseUrl' => 'https://www.cmoa.jp/search/result/?header_word=Book',
                ],
                [
                    'store.name' => 'Renta!',
                    'purchaseUrl' => 'https://renta.papy.co.jp/renta/sc/frm/search?word=Book',
                ],
                [
                    'store.name' => 'めちゃコミック',
                    'purchaseUrl' => 'https://mechacomic.jp/books?text=Book',
                ],
                [
                    'store.name' => 'デジケット',
                    'purchaseUrl' => 'https://www.digiket.com',
                ],
                [
                    'store.name' => 'KTC-Store',
                    'purchaseUrl' => 'https://ktc-store.com/products/list?name=Book+1',
                ],
                [
                    'store.name' => 'ピッコマ',
                    'purchaseUrl' => 'https://piccoma.com/web/search/result?word=Book',
                ],
                [
                    'store.name' => 'LINEマンガ',
                    'purchaseUrl' => 'https://manga.line.me/search_product/list?word=Book',
                ],
                [
                    'store.name' => 'pixivコミック',
                    'purchaseUrl' => 'https://comic.pixiv.net/search?q=Book+1&tab=store',
                ],
                [
                    'store.name' => 'メロンブックス',
                    'purchaseUrl' => 'https://www.melonbooks.co.jp/maker/index.php?maker_id=899&pageno=1&search_target%5B0%5D=2&name=Book+1',
                ],
            ],
        ];
    }

    /**
     * @test
     *
     * @dataProvider purchaseUrlWithExplicitUrlProvider
     */
    public function purchaseUrl_購入先URLの登録がある場合、そのURLにパラメータ追加等の最適化処理を行なったものを返却すること(\Closure $createSite, array $bookAttributes, array $attachedStores, array $expectations): void
    {
        $this->seed(StoreSeeder::class);
        $bookFactory = Book::factory();
        foreach ($attachedStores as $store) {
            $bookFactory = $bookFactory->hasAttached(
                SeedStoreHelper::{"get{$store['code']}"}()->ebookStore,
                ['url' => $store['explicitUrl']]
            );
        }
        $book = $bookFactory
            ->hasAttached($site = $createSite())
            ->published()
            ->create($bookAttributes);

        $response = $this
            ->graphQL($this->allQuery, ['id' => $book->id, 'siteId' => $site->id])
            ->assertGraphQLErrorFree()
            ->assertJson(
                fn (AssertableJson $json) => $json
                    ->has('data.testBook.ebookPurchaseInfo', count($attachedStores))
            );
        foreach ($expectations as $i => $expected) {
            $response->assertJson(
                fn (AssertableJson $json) => $json
                    ->has("data.testBook.ebookPurchaseInfo.{$i}", fn (AssertableJson $json) => $json
                        ->where('store.name', $expected['store.name'])
                        ->where('purchaseUrl', $expected['purchaseUrl'])
                        ->etc())
            );
        }
    }

    public static function purchaseUrlWithExplicitUrlProvider(): iterable
    {
        yield 'KTC本体サイト' => [
            'createSite' => fn () => SeedSiteHelper::createKtcom(),
            'bookAttributes' => [
                'title' => 'Book 1',
                'volume' => '1',
                'isbn13' => '9784799218020',
            ],
            'attachedStores' => [
                [
                    'code' => 'dlsite',
                    'explicitUrl' => 'https://www.dlsite.com/books/work/=/product_id/BJ01170669.html',
                ],
                [
                    'code' => 'booklive',
                    'explicitUrl' => 'https://booklive.jp/search/keyword/keyword/Book/exfasc/1',
                ],
            ],
            'expected' => [
                [
                    'store.name' => 'DLsite',
                    'purchaseUrl' => 'https://www.dlsite.com/books/dlaf/=/link/work/aid/k86032/id/BJ01170669.html',
                ],
                [
                    'store.name' => 'BookLive!',
                    'purchaseUrl' => 'https://booklive.jp/search/keyword/keyword/Book/exfasc/1',
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
            'attachedStores' => [
                [
                    'code' => 'dlsite',
                    'explicitUrl' => 'https://www.dlsite.com/books/work/=/product_id/BJ01170669.html',
                ],
                [
                    'code' => 'booklive',
                    'explicitUrl' => 'https://booklive.jp/search/keyword/keyword/Book/exfasc/1',
                ],
            ],
            'expected' => [
                [
                    'store.name' => 'DLsite',
                    'purchaseUrl' => 'https://www.dlsite.com/books/dlaf/=/link/work/aid/k86032/id/BJ01170669.html',
                ],
                [
                    'store.name' => 'BookLive!',
                    'purchaseUrl' => 'https://ck.jp.ap.valuecommerce.com/servlet/referral?sid=3701759&pid=889550802&vc_url=https%3A%2F%2Fbooklive.jp%2Fsearch%2Fkeyword%2Fkeyword%2FBook%2Fexfasc%2F1%3Futm_source%3Dspad%26utm_medium%3Daffiliate%26utm_campaign%3D102%26utm_content%3Dnormal',
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
            'attachedStores' => [
                [
                    'code' => 'dlsite',
                    'explicitUrl' => 'https://www.dlsite.com/books/work/=/product_id/BJ01170669.html',
                ],
                [
                    'code' => 'booklive',
                    'explicitUrl' => 'https://booklive.jp/search/keyword/keyword/Book/exfasc/1',
                ],
            ],
            'expected' => [
                [
                    'store.name' => 'DLsite',
                    'purchaseUrl' => 'https://www.dlsite.com/books/dlaf/=/link/work/aid/k86032/id/BJ01170669.html',
                ],
                [
                    'store.name' => 'BookLive!',
                    'purchaseUrl' => 'https://ck.jp.ap.valuecommerce.com/servlet/referral?sid=3585678&pid=889532041&vc_url=https%3A%2F%2Fbooklive.jp%2Fsearch%2Fkeyword%2Fkeyword%2FBook%2Fexfasc%2F1%3Futm_source%3Dspad%26utm_medium%3Daffiliate%26utm_campaign%3D102%26utm_content%3Dnormal',
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
            'attachedStores' => [
                [
                    'code' => 'dlsite',
                    'explicitUrl' => 'https://www.dlsite.com/books/work/=/product_id/BJ01170669.html',
                ],
                [
                    'code' => 'booklive',
                    'explicitUrl' => 'https://booklive.jp/search/keyword/keyword/Book/exfasc/1',
                ],
            ],
            'expected' => [
                [
                    'store.name' => 'DLsite',
                    'purchaseUrl' => 'https://www.dlsite.com/books/dlaf/=/link/work/aid/k86032/id/BJ01170669.html',
                ],
                [
                    'store.name' => 'BookLive!',
                    'purchaseUrl' => 'https://booklive.jp/search/keyword/keyword/Book/exfasc/1',
                ],
            ],
        ];
    }
}
