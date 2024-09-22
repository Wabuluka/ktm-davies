<?php

namespace Tests\Unit\Http\Requests;

use App\Enums\BlockType;
use App\Enums\BookStatus;
use App\Http\Requests\BookRequest;
use App\Models\Block;
use App\Models\Book;
use App\Models\BookStore;
use App\Models\ExternalLink;
use App\Models\RelatedItem;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Validation\ValidationException;
use Tests\Supports\BookRequestDataFactory;
use Tests\Supports\TestingFormRequest;
use Tests\TestCase;
use ValueError;

class BookRequestTest extends TestCase
{
    use TestingFormRequest;

    protected $requestClass = BookRequest::class;

    protected BookRequestDataFactory $requestData;

    protected function setUp(): void
    {
        parent::setUp();

        $this->requestData = BookRequestDataFactory::new();
    }

    /** @test */
    public function 不正なステータス指定に対してエラーが発生すること(): void
    {
        $this->expectException(ValueError::class);
        $requestData = $this->requestData->create(['status' => 'invalid']);
        $this->createRequestAndValidate($requestData);
    }

    /** @test */
    public function ステータス「下書き」の場合、公開日時を指定できないこと(): void
    {
        $requestData = $this->requestData->setStatus(BookStatus::Published)->create(['published_at' => now()->addDay()]);

        $this->assertValidationException(
            fn () => $this->createRequestAndValidate($requestData),
            fn (ValidationException $e) => $this->assertArrayHasKey('published_at', $e->errors()),
        );
    }

    /** @test */
    public function サイズが2MBを超える書影に対してエラーが発生すること(): void
    {
        $cover = UploadedFile::fake()->image('cover.jpg')->size(2049);
        $requestData = $this->requestData->create(['cover' => $cover]);

        $this->assertValidationException(
            fn () => $this->createRequestAndValidate($requestData),
            fn (ValidationException $e) => $this->assertArrayHasKey('cover', $e->errors())
        );
    }

    /** @test */
    public function 不正な形式のISBNを入力するとエラーになること(): void
    {
        $requestData1 = (clone $this->requestData)->create(['isbn13' => 'invalid']);
        $requestData2 = (clone $this->requestData)->create(['isbn13' => '1234567890']);  // ISBN10
        $requestData3 = (clone $this->requestData)->create(['isbn13' => '123-4-567890-12-3']);  // ハイフンあり

        $this->assertValidationException(
            fn () => $this->createRequestAndValidate($requestData1),
            fn (ValidationException $e) => $this->assertArrayHasKey('isbn13', $e->errors())
        );
        $this->assertValidationException(
            fn () => $this->createRequestAndValidate($requestData2),
            fn (ValidationException $e) => $this->assertArrayHasKey('isbn13', $e->errors())
        );
        $this->assertValidationException(
            fn () => $this->createRequestAndValidate($requestData3),
            fn (ValidationException $e) => $this->assertArrayHasKey('isbn13', $e->errors())
        );
    }

    /** @test */
    public function 購入URLが未入力の購入先情報がある場合、ISBNの入力が必須になること(): void
    {
        $requestData = $this->requestData->addBookStore([], url: null)->create(['isbn13' => null]);

        $this->assertValidationException(
            fn () => $this->createRequestAndValidate($requestData),
            fn (ValidationException $e) => $this->assertArrayHasKey('isbn13', $e->errors())
        );
    }

    /**
     * @test
     *
     * @doesNotPerformAssertions
     * */
    public function 購入URLが未入力の購入先情報がない場合、ISBNの入力が任意になること(): void
    {
        $bookStore = BookStore::factory()->create();
        $requestData1 = (clone $this->requestData)->addBookStore($bookStore, url: 'https://example.com')->create(['isbn13' => null]);
        $requestData2 = (clone $this->requestData)->addBookStore($bookStore, url: 'https://example.com')->create(['isbn13' => '']);
        $this->createRequestAndValidate($requestData1);
        $this->createRequestAndValidate($requestData2);
    }

    /** @test */
    public function 購入URLの登録が必須の書店で購入URLが未入力の場合エラーになること(): void
    {
        $requestData = (clone $this->requestData)
            ->addBookStore(['is_purchase_url_required' => true], url: null)
            ->addEbookStore(['is_purchase_url_required' => true], url: null)
            ->create(['isbn13' => '9784003101018']);

        $this->assertValidationException(
            function () use ($requestData) {
                $this->createRequestAndValidate($requestData);
            },
            function (ValidationException $e) {
                $this->assertArrayHasKey('bookstores.0.id', $e->errors());
                $this->assertArrayHasKey('ebookstores.0.id', $e->errors());
            }
        );
    }

    /** @test */
    public function related_itemsが追加・更新・削除用のパラメータに振り分けられること(): void
    {
        $book = Book::factory()->create();
        $requestData = $this->requestData
            ->addNewRelatedItem($link1 = ExternalLink::factory()->create(), '新規', 1)
            ->addUpdatedRelatedItem($toUpdate = RelatedItem::factory()->for($book)->create(), $link2 = ExternalLink::factory()->create(), '更新', 2)
            ->addDeletedRelatedItem($toDelete = RelatedItem::factory()->for($book)->create())
            ->create();

        $relatedItems = $this->createRequestAndValidate($requestData)->safe(['related_items']);

        $this->assertSame([
            'related_items' => [
                'insert' => [
                    [
                        'relatable_id' => $link1->id,
                        'relatable_type' => 'externalLink',
                        'description' => '新規',
                        'sort' => 1,
                    ],
                ],
                'update' => [
                    [
                        'id' => $toUpdate->id,
                        'relatable_id' => $link2->id,
                        'relatable_type' => 'externalLink',
                        'description' => '更新',
                        'sort' => 2,
                    ],
                ],
                'deleteIds' => [$toDelete->id],
            ],
        ], $relatedItems);
    }

    /** @test */
    public function blocksが追加・更新・削除用のパラメータに振り分けられること(): void
    {
        $book = Book::factory()->create();
        $requestData = $this->requestData
            ->addCustomBlock(
                customTitle: '自由欄',
                customContent: '<p>自由欄の内容</p>',
                sort: 1,
                displayed: true,
            )
            ->addUpdatedBlock(
                $toUpdate = Block::factory()->for($book)->create(['type_id' => BlockType::Series]),
                customTitle: '',
                customContent: '',
                sort: 2,
                displayed: false
            )
            ->addDeletedBlock(
                $toDelete = Block::factory()->for($book)->create()
            )
            ->create();

        $blocks = $this->createRequestAndValidate($requestData)->safe(['blocks']);

        $this->assertSame([
            'blocks' => [
                'insert' => [
                    [
                        'type_id' => BlockType::Custom->value,
                        'custom_title' => '自由欄',
                        'custom_content' => '<p>自由欄の内容</p>',
                        'sort' => 1,
                        'displayed' => true,
                    ],
                ],
                'update' => [
                    [
                        'id' => $toUpdate->id,
                        'type_id' => BlockType::Series->value,
                        'custom_title' => '',
                        'custom_content' => '',
                        'sort' => 2,
                        'displayed' => false,
                    ],
                ],
                'deleteIds' => [$toDelete->id],
            ],
        ], $blocks);
    }

    /** @test */
    public function 不正な関連作品の種別を指定するとエラーになること(): void
    {
        $requestData = $this->requestData->create([
            'related_items' => [
                'upsert' => [
                    [
                        'id' => '+1',
                        'relatable_type' => 'user', // User は関連作品として登録不可
                        'relatable_id' => 1,
                        'description' => 'description',
                        'sort' => 1,
                    ],
                ],
            ],
        ]);

        $this->assertValidationException(
            fn () => $this->createRequestAndValidate($requestData),
            fn (ValidationException $e) => $this->assertArrayHasKey('related_items.insert.0.relatable_type', $e->errors())
        );
    }

    /** @test */
    public function Bookに紐付いていないRelatedItemのidを指定するとエラーになること(): void
    {
        $book1 = Book::factory()->has(RelatedItem::factory())->create();
        $book2 = Book::factory()->create();
        [$relatedItem1, $relatedItem2] = RelatedItem::factory(2)->for($book2)->create();
        $requestData = $this->requestData
            ->addUpdatedRelatedItem($relatedItem1)
            ->addDeletedRelatedItem($relatedItem2)
            ->create();

        $this->assertValidationException(
            function () use ($requestData, $book1) {
                $this->createRequestAndValidate($requestData, ['book' => $book1]);
            },
            function (ValidationException $e) {
                $this->assertArrayHasKey('related_items.update.0.id', $e->errors());
                $this->assertArrayHasKey('related_items.deleteIds.0', $e->errors());
            }
        );
    }

    /** @test */
    public function Bookに紐付いていないブロックの更新・削除を試みるとエラーになること(): void
    {
        [$book, $anotherBook] = Book::factory(2)->create();
        $requestData = $this->requestData
            ->addUpdatedBlock(
                Block::factory()->for($anotherBook)->create(['type_id' => BlockType::Related]),
            )
            ->addDeletedBlock(
                Block::factory()->for($anotherBook)->create(['type_id' => BlockType::Story])
            )
            ->create();

        $this->assertValidationException(
            function () use ($requestData, $book) {
                $this->createRequestAndValidate($requestData, ['book' => $book]);
            },
            function (ValidationException $e) {
                $this->assertArrayHasKey('blocks.update.0.id', $e->errors());
                $this->assertArrayHasKey('blocks.deleteIds.0', $e->errors());
            }
        );
    }

    /** @test */
    public function 自由欄以外のブロックの削除を試みるとエラーになること(): void
    {
        $book = Book::factory()->create();
        $requestData = $this->requestData
            ->addDeletedBlock(
                Block::factory()->for($book)->create(['type_id' => BlockType::Custom])
            )
            ->addDeletedBlock(
                Block::factory()->for($book)->create(['type_id' => BlockType::Story])
            )
            ->create();

        $this->assertValidationException(
            fn () => $this->createRequestAndValidate($requestData, ['book' => $book]),
            function (ValidationException $e) {
                $this->assertArrayNotHasKey('blocks.deleteIds.0', $e->errors());
                $this->assertArrayHasKey('blocks.deleteIds.1', $e->errors());
            }
        );
    }

    /** @test */
    public function toModel_ステータス「下書き」の場合、新規モデルに公開日時に現在日時が自動で設定されること(): void
    {
        $this->freezeTime();
        $requestData = $this->requestData->setStatus(BookStatus::Draft)->create();
        $newBook = $this->createRequestAndValidate($requestData)->toModel();

        $this->assertSame(BookStatus::Draft, $newBook->getAttribute('status'));
        $this->assertSame(now()->toString(), $newBook->getAttribute('published_at')->toString());
    }

    /** @test */
    public function toModel_ステータス「下書き」の場合、更新モデルの公開日時が変化しないこと(): void
    {
        $book = Book::factory()->published()->create();
        $requestData = $this->requestData->setStatus(BookStatus::Draft)->create();
        $updatedBook = $this->createRequestAndValidate($requestData)->toModel($book);

        $this->assertSame(
            BookStatus::Draft,
            $updatedBook->getAttribute('status')
        );
        $this->assertSame(
            $book->published_at->toString(),
            $updatedBook->getAttribute('published_at')->toString()
        );
    }

    /** @test */
    public function toModel_ステータス「公開予定」の場合、公開日時の指定が必須になること(): void
    {
        $requestData = $this->requestData->setStatus(BookStatus::WillBePublished)
            ->except('published_at')
            ->create();

        $this->assertValidationException(
            fn () => $this->createRequestAndValidate($requestData),
            fn (ValidationException $e) => $this->assertArrayHasKey('published_at', $e->errors())
        );
    }

    /** @test */
    public function toModel_ステータス「公開予定」の場合、現在日時より前の公開日時を指定できないこと(): void
    {
        $requestData = $this->requestData->setStatus(BookStatus::WillBePublished)
            ->except('published_at')
            ->create(['published_at' => now()->subMinute()]);

        $this->assertValidationException(
            fn () => $this->createRequestAndValidate($requestData),
            fn (ValidationException $e) => $this->assertArrayHasKey('published_at', $e->errors())
        );
    }

    /** @test */
    public function toModel_ステータス「公開予定」の場合、新規モデルに公開日時に指定した時刻が設定されること(): void
    {
        $publishedAt = now()->addMinute()->toString();
        $requestData = $this->requestData->setStatus(BookStatus::WillBePublished)
            ->create(['published_at' => $publishedAt]);
        $newBook = $this->createRequestAndValidate($requestData)->toModel();

        $this->assertSame(BookStatus::WillBePublished, $newBook->getAttribute('status'));
        $this->assertSame($publishedAt, $newBook->getAttribute('published_at')->toString());
    }

    /** @test */
    public function toModel_ステータス「公開予定」の場合、更新モデルに公開日時に指定した時刻が設定されること(): void
    {
        Book::factory()->published()->create();
        $publishedAt = now()->addMinute()->toString();
        $requestData = $this->requestData->setStatus(BookStatus::WillBePublished)
            ->create(['published_at' => $publishedAt]);
        $updateBook = $this->createRequestAndValidate($requestData)->toModel();

        $this->assertSame(BookStatus::WillBePublished, $updateBook->getAttribute('status'));
        $this->assertSame($publishedAt, $updateBook->getAttribute('published_at')->toString());
    }

    /** @test */
    public function toModel_ステータス「公開中」の場合、公開日時を指定できないこと(): void
    {
        $requestData = $this->requestData->setStatus(BookStatus::Published)->create(['published_at' => now()]);

        $this->assertValidationException(
            fn () => $this->createRequestAndValidate($requestData),
            fn (ValidationException $e) => $this->assertArrayHasKey('published_at', $e->errors())
        );
    }

    /** @test */
    public function toModel_ステータス「公開中」の場合、新規モデルの公開日時に現在日時が自動で設定されること(): void
    {
        $this->freezeTime();
        $requestData = $this->requestData->setStatus(BookStatus::Published)->create();
        $newBook = $this->createRequestAndValidate($requestData)->toModel();
        $this->assertSame(BookStatus::Published, $newBook->getAttribute('status'));
        $this->assertSame(now()->toString(), $newBook->getAttribute('published_at')->toString());
    }

    /** @test */
    public function toModel_ステータス「公開中」の場合、更新モデルの公開日時＞＝現在日時の場合のみ、公開日時に現在日時が自動で設定されること(): void
    {
        $this->freezeTime();
        $book1 = Book::factory()->draft()->create(['published_at' => now()->addMinute()]);
        $book2 = Book::factory()->willBePublished()->create(['published_at' => now()->addMinute()]);
        $book3 = Book::factory()->draft()->create(['published_at' => now()->subMinute()]);
        $book4 = Book::factory()->willBePublished()->create(['published_at' => now()->subMinute()]);
        $requestData = $this->requestData->setStatus(BookStatus::Published)->create();

        $updatedBook1 = $this->createRequestAndValidate($requestData)->toModel($book1);
        $updatedBook2 = $this->createRequestAndValidate($requestData)->toModel($book2);
        $updatedBook3 = $this->createRequestAndValidate($requestData)->toModel($book3);
        $updatedBook4 = $this->createRequestAndValidate($requestData)->toModel($book4);

        $this->assertSame(BookStatus::Published, $updatedBook1->getAttribute('status'));
        $this->assertSame(BookStatus::Published, $updatedBook2->getAttribute('status'));
        $this->assertSame(BookStatus::Published, $updatedBook3->getAttribute('status'));
        $this->assertSame(BookStatus::Published, $updatedBook4->getAttribute('status'));
        $this->assertSame(
            now()->toString(),
            $updatedBook1->getAttribute('published_at')->toString()
        );
        $this->assertSame(
            now()->toString(),
            $updatedBook2->getAttribute('published_at')->toString()
        );
        $this->assertSame(
            now()->subMinutes()->toString(),
            $updatedBook3->getAttribute('published_at')->toString()
        );
        $this->assertSame(
            now()->subMinutes()->toString(),
            $updatedBook4->getAttribute('published_at')->toString()
        );
    }

    /** @test */
    public function toModel_新規モデルに自動でcreated_byとupdated_byが設定されること(): void
    {
        $user = User::factory()->create();
        $requestData = [
            ...$this->requestData->create(),
            'created_by' => 0,
            'updated_by' => 0,
        ];
        $newBook = $this->createRequestAndValidate($requestData, user: $user)->toModel();

        $this->assertSame($user->id, $newBook->getAttribute('created_by'));
        $this->assertSame($user->id, $newBook->getAttribute('updated_by'));
    }

    /** @test */
    public function toModel_更新モデルに自動でupdated_byが設定されること(): void
    {
        [$user, $anotherUser] = User::factory(2)->create();
        $book = Book::factory()->create()->forceFill([
            'created_by' => $user->id,
            'updated_by' => $user->id,
        ]);
        $book->save();
        $requestData = [
            ...$this->requestData->create(),
            'created_by' => 0,
            'updated_by' => 0,
        ];
        $updatedBook = $this->createRequestAndValidate($requestData, user: $anotherUser)->toModel($book);

        $this->assertSame($user->id, $book->getAttribute('created_by'));
        $this->assertSame($anotherUser->id, $updatedBook->getAttribute('updated_by'));
    }

    /** @test */
    public function makeCoverData_リクエストでcoverが指定されていない場合、nullを返却すること(): void
    {
        $requestData = $this->requestData->except('cover')->create();
        $coverData = $this->createRequestAndValidate($requestData)->makeCoverData();

        $this->assertNull($coverData);
    }

    /** @test */
    public function makeCoverData_リクエストのcoverがnullの場合、書影を削除するために、coverにnullを指定すること(): void
    {
        $requestData = $this->requestData->create(['cover' => null]);
        $coverData = $this->createRequestAndValidate($requestData)->makeCoverData();

        $this->assertArrayHasKey('cover', $coverData);
        $this->assertNull($coverData['cover']);
    }

    /** @test */
    public function makeCoverData_リクエストのcoverが画像データの場合、画像のwidth・height情報と共に、画像データを返却すること(): void
    {
        $cover = UploadedFile::fake()->image('cover.jpg', 200, 300);
        $requestData = $this->requestData->create(['cover' => $cover]);
        $coverData = $this->createRequestAndValidate($requestData)->makeCoverData();

        $expected = [
            'cover' => $cover,
            'dimensions' => [
                'width' => 200,
                'height' => 300,
            ],
        ];
        $this->assertSame($expected, $coverData);
    }

    /** @test */
    public function makeSiteSyncData_リクエストでsitesが指定されていない場合、nullを返却すること(): void
    {
        $requestData = $this->requestData->except('sites')->create();
        $sitesSyncData = $this->createRequestAndValidate($requestData)->makeSitesSyncData();

        $this->assertNull($sitesSyncData);
    }

    /** @test */
    public function makeSiteSyncData_リクエストのsitesがnullの場合、公開サイトの紐付けを削除するために、空の配列を返却すること(): void
    {
        $requestData = $this->requestData->create(['sites' => null]);
        $sitesSyncData = $this->createRequestAndValidate($requestData)->makeSitesSyncData();

        $this->assertSame([], $sitesSyncData);
    }
}
