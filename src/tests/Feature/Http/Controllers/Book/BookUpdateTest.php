<?php

namespace Tests\Feature\Http\Controllers\Book;

use App\Enums\BlockType;
use App\Enums\BookStatus;
use App\Models\Block;
use App\Models\Book;
use App\Models\BookStore;
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
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Tests\Supports\BookRequestDataFactory;
use Tests\TestCase;

class BookUpdateTest extends TestCase
{
    private array $initialData;

    private BookRequestDataFactory $requestData;

    protected function setUp(): void
    {
        parent::setUp();

        $this->initialData = [
            'title' => 'The Great Gatsby',
            'title_kana' => 'ザ・グレート・ギャツビー',
            'volume' => 'Volume 1',
            'is_draft' => true,
            'published_at' => now(),
            'label_id' => Label::factory()->create()->id,
            'genre_id' => Genre::factory()->create()->id,
            'series_id' => Series::factory()->create()->id,
            'isbn13' => 9780743273565,
            'release_date' => '2004-09-29',
            'price' => 1000,
            'format_id' => 1,
            'size_id' => 1,
            'ebook_only' => false,
            'special_edition' => false,
            'limited_edition' => false,
            'adult' => false,
            'caption' => 'A Novel',
            'description' => 'The Great Gatsby is a 1925 novel by American author F. Scott Fitzgerald.',
            'keywords' => 'Fiction, American Literature',
            'trial_url' => 'https://example.com/trial',
        ];

        $this->requestData = BookRequestDataFactory::new();
    }

    /** @test */
    public function 書籍を更新できること(): void
    {
        $book = Book::create($this->initialData);
        $newData = [
            'title' => 'The Great Gatsby (Updated)',
            'title_kana' => 'ザ・グレート・ギャツビー (Updated)',
            'volume' => 'Volume 1 (Updated)',
            'label_id' => Label::factory()->create()->id,
            'genre_id' => Genre::factory()->create()->id,
            'series_id' => Series::factory()->create()->id,
            'isbn13' => 9780743273566,
            'release_date' => '2004-09-30',
            'price' => 2000,
            'format_id' => 2,
            'size_id' => 2,
            'ebook_only' => true,
            'special_edition' => true,
            'limited_edition' => true,
            'adult' => true,
            'caption' => 'A Novel (Updated)',
            'description' => 'The Great Gatsby is a 1925 novel by American author F. Scott Fitzgerald. (Updated)',
            'keywords' => 'Fiction, American Literature (Updated)',
            'trial_url' => 'https://example.com/trial/updated',
        ];
        $requestData = $this->requestData->create($newData);

        $response = $this
            ->login()
            ->put(route('books.update', $book), $requestData);

        $response->assertRedirect('/books');
        $this->assertDatabaseHas('books', $newData);
        $this->assertDatabaseMissing('books', $this->initialData);
    }

    /** @test */
    public function ログインしなければ書籍を更新できないこと(): void
    {
        $book = Book::create($this->initialData);
        $newData = ['title' => 'The Great Gatsby (Updated)'];
        $requestData = $this->requestData->create($newData);

        $response = $this
            ->put(route('books.update', $book), $requestData);

        $response->assertRedirect('/login');
        $this->assertDatabaseHas('books', $this->initialData);
        $this->assertDatabaseMissing('books', $newData);
    }

    /** @test */
    public function updated_byが自動的に設定されること(): void
    {
        [$user, $anotherUser] = User::factory(2)->create();
        $userBook = Book::make($this->initialData)->forceFill([
            'created_by' => $user->id,
            'updated_by' => $user->id,
        ]);
        $userBook->save();
        $newData = ['title' => 'The Great Gatsby (Updated)'];
        $requestData = $this->requestData->create($newData);

        $response = $this
            ->login($anotherUser)
            ->put(route('books.update', $userBook), $requestData);

        $response->assertRedirect('/books');
        $this->assertDatabaseHas('books', [
            ...$newData,
            'created_by' => $user->id,
            'updated_by' => $anotherUser->id,
        ]);
    }

    /** @test */
    public function 不正なステータス指定に対してエラーが発生すること(): void
    {
        $book = Book::create($this->initialData);
        $newData = ['title' => 'The Great Gatsby (Updated)'];
        $requestData1 = $this->requestData
            ->setStatus(BookStatus::Draft)
            ->create([...$newData, 'published_at' => now()]);
        $requestData2 = $this->requestData
            ->setStatus(BookStatus::WillBePublished)
            ->create([...$newData, 'published_at' => now()]);
        $requestData3 = $this->requestData
            ->setStatus(BookStatus::Published)
            ->create([...$newData, 'published_at' => now()]);

        $this->login();
        $this->put(route('books.update', $book), $requestData1)->assertSessionHasErrors('published_at');
        $this->put(route('books.update', $book), $requestData2)->assertSessionHasErrors('published_at');
        $this->put(route('books.update', $book), $requestData3)->assertSessionHasErrors('published_at');
        $this->assertDatabaseHas('books', $this->initialData);
        $this->assertDatabaseMissing('books', $newData);
    }

    /** @test */
    public function ステータス「下書き」の書籍を更新できること(): void
    {
        $this->freezeTime();
        [$draft1, $draft2] = Book::factory(2)->draft()->create(['published_at' => now()->addMonths(1)]);
        $newData = ['title' => 'The Great Gatsby (Updated)'];

        $this->login();
        $this
            ->patch(
                route('books.update', $draft1),
                $this->requestData
                    ->setStatus(BookStatus::WillBePublished)
                    ->create([...$newData, 'published_at' => now()->addMonths(2)])
            )
            ->assertRedirect('/books');
        $this
            ->patch(route('books.update', $draft2), $this->requestData->setStatus(BookStatus::Published)->create($newData))
            ->assertRedirect('/books');

        $draft1->refresh();
        $draft2->refresh();
        $this->assertSame(BookStatus::WillBePublished, $draft1->status);
        $this->assertSame(BookStatus::Published, $draft2->status);
        $this->assertSame(now()->addMonths(2)->toString(), $draft1->published_at->toString());
        $this->assertSame(now()->toString(), $draft2->published_at->toString());
    }

    /** @test */
    public function ステータス「公開予定」の書籍を更新できること(): void
    {
        $this->freezeTime();
        [$willBePublished1, $willBePublished2] = Book::factory(2)->willBePublished(now()->addMonth())->create();
        $newData = ['title' => 'The Great Gatsby (Updated)'];

        $this->login();
        $this
            ->patch(route('books.update', $willBePublished1), $this->requestData->setStatus(BookStatus::Draft)->create($newData))
            ->assertRedirect('/books');
        $this
            ->patch(route('books.update', $willBePublished2), $this->requestData->setStatus(BookStatus::Published)->create($newData))
            ->assertRedirect('/books');

        $willBePublished1->refresh();
        $willBePublished2->refresh();
        $this->assertSame(BookStatus::Draft, $willBePublished1->status);
        $this->assertSame(BookStatus::Published, $willBePublished2->status);
        $this->assertSame(now()->addMonth()->toString(), $willBePublished1->published_at->toString());
        $this->assertSame(now()->toString(), $willBePublished2->published_at->toString());
    }

    /** @test */
    public function ステータス「公開中」の書籍を更新できること(): void
    {
        $this->freezeTime();
        [$published1, $published2] = Book::factory(2)->published()->create(['published_at' => now()]);
        $newData = ['title' => 'The Great Gatsby (Updated)'];

        $this->login();
        $this
            ->patch(route('books.update', $published1), $this->requestData->setStatus(BookStatus::Draft)->create($newData))
            ->assertRedirect('/books');
        $this
            ->patch(route('books.update', $published2), $this->requestData->setStatus(BookStatus::WillBePublished, now()->addMonth())->create($newData))
            ->assertRedirect('/books');

        $published1->refresh();
        $published2->refresh();
        $this->assertSame(BookStatus::Draft, $published1->status);
        $this->assertSame(BookStatus::WillBePublished, $published2->status);
        $this->assertSame(now()->toString(), $published1->published_at->toString());
        $this->assertSame(now()->addMonth()->toString(), $published2->published_at->toString());
    }

    /** @test */
    public function 公開サイトを紐付けて保存できること(): void
    {
        [$site1, $site2, $site3] = Site::factory(3)->create();
        $book = Book::factory()->hasAttached($site1)->create();
        $newData = ['title' => 'The Great Gatsby (Updated)'];
        $requestData = $this->requestData->create([...$newData, 'sites' => [$site2->id, $site3->id]]);

        $response = $this
            ->login()
            ->put(route('books.update', $book), $requestData);

        $response->assertRedirect('/books');
        $this->assertDatabaseHas('books', $newData);
        $this->assertDatabaseMissing('book_site', ['book_id' => $book->id, 'site_id' => $site1->id]);
        $this->assertDatabaseHas('book_site', ['book_id' => $book->id, 'site_id' => $site2->id]);
        $this->assertDatabaseHas('book_site', ['book_id' => $book->id, 'site_id' => $site2->id]);
    }

    /** @test */
    public function 公開サイトの紐付けを解除できること(): void
    {
        $site = Site::factory()->create();
        $book = Book::factory()->hasAttached($site)->create();
        $newData = ['title' => 'The Great Gatsby (Updated)'];
        $requestData = $this->requestData->create([...$newData, 'sites' => null]);

        $response = $this
            ->login()
            ->patch(route('books.update', $book), $requestData);

        $response->assertRedirect('/books');
        $this->assertDatabaseHas('books', $newData);
        $this->assertDatabaseMissing('book_site', ['book_id' => $book->id, 'site_id' => $site->id]);
    }

    /** @test */
    public function 書影を紐付けて保存できること(): void
    {
        $book = Book::create($this->initialData);
        $newData = ['title' => 'The Great Gatsby (Updated)'];
        $cover = UploadedFile::fake()->image('cover.jpg', 200, 300);
        $requestData = $this->requestData->create([...$newData, 'cover' => $cover]);

        $response = $this
            ->login()
            ->put(route('books.update', $book), $requestData);

        $response->assertRedirect('/books');
        $this->assertDatabaseHas('books', $newData);
        $this->assertDatabaseHas('media', [
            'model_type' => 'book',
            'model_id' => $book->id,
            'file_name' => $cover->name,
            'custom_properties->width' => 200,
            'custom_properties->height' => 300,
        ]);
    }

    /** @test */
    public function 書籍に紐付く書影を新しい画像に更新できること(): void
    {
        $book = Book::create($this->initialData);
        $newData = ['title' => 'The Great Gatsby (Updated)'];
        $oldCover = UploadedFile::fake()->image('cover.jpg', 200, 300);
        $newCover = UploadedFile::fake()->image('cover-updated.jpg', 300, 400);
        $book
            ->addMedia($oldCover)
            ->withCustomProperties(['width' => 200, 'height' => 300])
            ->toMediaCollection('cover');
        $requestData = $this->requestData->create([...$newData, 'cover' => $newCover]);

        $response = $this
            ->login()
            ->patch(route('books.update', $book), $requestData);

        $response->assertRedirect('/books');
        $this->assertDatabaseHas('books', $newData);
        $this->assertDatabaseHas('media', [
            'model_type' => 'book',
            'model_id' => $book->id,
            'file_name' => $newCover->name,
            'custom_properties->width' => 300,
            'custom_properties->height' => 400,
        ]);
        $this->assertDatabaseMissing('media', [
            'file_name' => $oldCover->name,
        ]);
    }

    /** @test */
    public function 書籍に紐付く書影を削除できること(): void
    {
        $book = Book::create($this->initialData);
        $book
            ->addMedia(UploadedFile::fake()->image('cover.jpg', 200, 300))
            ->withCustomProperties(['width' => 200, 'height' => 300])
            ->toMediaCollection('cover');
        $newData = ['title' => 'The Great Gatsby (Updated)'];
        $requestData = $this->requestData->create([...$newData, 'cover' => null]);

        $response = $this
            ->login()
            ->put(route('books.update', $book), $requestData);

        $response->assertRedirect('/books');
        $this->assertDatabaseHas('books', $newData);
        $this->assertDatabaseEmpty('media');
    }

    /** @test */
    public function 作家情報を更新できること(): void
    {
        [$creator1, $creator2, $creator3] = Creator::factory(3)->create();
        [$type1, $type2, $type3] = CreationType::factory(3)->create();
        // 作家情報を保存:
        //     1. | Creator 1 | 区分1 (サイト上の表記は "小説") |
        //     2. | Creator 2 | 区分2 |
        //     3. | Creator 3 | 区分3 |
        $book = Book::factory()
            ->hasAttached($creator1, ['sort' => 1, 'creation_type' => $type1->name, 'displayed_type' => '小説'])
            ->hasAttached($creator2, ['sort' => 2, 'creation_type' => $type2->name, 'displayed_type' => null])
            ->hasAttached($creator3, ['sort' => 3, 'creation_type' => $type3->name, 'displayed_type' => null])
            ->create($this->initialData);
        // 作家情報を更新:
        //     1. | Creator 3 | 区分: 区分1 (サイト上の表記は "小説") |
        //     2. | Creator 1 | 区分: 区分3 |
        $newData = ['title' => 'The Great Gatsby (Updated)'];
        $requestData = $this->requestData
            ->addCreation($creator3, $type1, '小説', 1)
            ->addCreation($creator1, $type3, null, 2)
            ->create($newData);

        $response = $this
            ->login()
            ->put(route('books.update', $book), $requestData);

        $response
            ->assertRedirect('/books');
        $this
            ->assertDatabaseHas('books', $newData)
            ->assertDatabaseHas('book_creations', [
                'creator_id' => $creator3->id,
                'creation_type' => $type1->name,
                'displayed_type' => '小説',
                'sort' => 1,
            ])
            ->assertDatabaseHas('book_creations', [
                'creator_id' => $creator1->id,
                'creation_type' => $type3->name,
                'displayed_type' => null,
                'sort' => 2,
            ])
            ->assertDatabaseMissing('book_creations', [
                'creator_id' => $creator2->id,
            ]);
    }

    /** @test */
    public function 作家情報を0件にできること(): void
    {
        $creator =
            Creator::factory()->create();
        $type =
            CreationType::factory()->create();
        $book =
            Book::factory()
                ->hasAttached($creator, ['sort' => 1, 'creation_type' => $type->name, 'displayed_type' => '小説'])
                ->create($this->initialData);
        $newData = ['title' => 'The Great Gatsby (Updated)'];
        $requestData = $this->requestData->create([...$newData, 'creations' => null]);

        $response = $this
            ->login()
            ->put(route('books.update', $book), $requestData);

        $response
            ->assertRedirect('/books');
        $this
            ->assertDatabaseHas('books', $newData)
            ->assertDatabaseMissing('book_creations', [
                'book_id' => $book->id,
            ]);
    }

    /** @test */
    public function 購入先情報（紙）を更新できること(): void
    {
        [$store1, $store2, $store3] = BookStore::factory(3)->for(Store::factory())->create();
        $book = Book::factory()
            ->hasAttached($store1, ['url' => 'https://book.example.com/1', 'is_primary' => true])
            ->hasAttached($store2, ['url' => 'https://book.example.com/2', 'is_primary' => false])
            ->hasAttached($store3, ['url' => 'https://book.example.com/3', 'is_primary' => false])
            ->create($this->initialData);
        $requestData = $this->requestData
            ->addBookStore($store1, url: 'https://book.example.com/1/updated', isPrimary: false)
            ->addBookStore($store2, url: 'https://book.example.com/2/updated', isPrimary: true)
            ->create($newData = ['title' => 'The Great Gatsby (Updated)']);

        $response = $this
            ->login()
            ->put(route('books.update', $book), $requestData);

        $response
            ->assertRedirect('/books');
        $this
            ->assertDatabaseHas('books', $newData)
            ->assertDatabaseHas('book_book_store', [
                'book_id' => $book->id,
                'book_store_id' => $store1->id,
                'url' => 'https://book.example.com/1/updated',
                'is_primary' => false,
            ])
            ->assertDatabaseHas('book_book_store', [
                'book_id' => $book->id,
                'book_store_id' => $store2->id,
                'url' => 'https://book.example.com/2/updated',
                'is_primary' => true,
            ])
            ->assertDatabaseMissing('book_book_store', [
                'book_id' => $book->id,
                'book_store_id' => $store3->id,
            ]);
    }

    /** @test */
    public function 購入先情報（紙）を0件にできること(): void
    {
        $book = Book::factory()
            ->hasAttached(
                BookStore::factory()->for(Store::factory())->create(),
                ['url' => 'https://book.example.com/1', 'is_primary' => true],
            )
            ->create($this->initialData);
        $newData = ['title' => 'The Great Gatsby (Updated)'];
        $requestData = $this->requestData->create([...$newData, 'bookstores' => null]);

        $response = $this
            ->login()
            ->put(route('books.update', $book), $requestData);

        $response
            ->assertRedirect('/books');
        $this
            ->assertDatabaseHas('books', $newData)
            ->assertDatabaseMissing('book_ebook_store', [
                'book_id' => $book->id,
            ]);
    }

    /** @test */
    public function 購入先情報（電子）を更新できること(): void
    {
        [$store1, $store2, $store3] = EbookStore::factory(3)->for(Store::factory())->create();
        $book = Book::factory()
            ->hasAttached($store1, ['url' => 'https://ebook.example.com/1', 'is_primary' => true])
            ->hasAttached($store2, ['url' => 'https://ebook.example.com/2', 'is_primary' => false])
            ->hasAttached($store3, ['url' => 'https://ebook.example.com/3', 'is_primary' => false])
            ->create($this->initialData);
        $requestData = $this->requestData
            ->addEbookStore($store1, url: 'https://ebook.example.com/1/updated', isPrimary: false)
            ->addEbookStore($store2, url: 'https://ebook.example.com/2/updated', isPrimary: true)
            ->create($newData = ['title' => 'The Great Gatsby (Updated)']);

        $response = $this
            ->login()
            ->put(route('books.update', $book), $requestData);

        $response
            ->assertRedirect('/books');
        $this
            ->assertDatabaseHas('books', $newData)
            ->assertDatabaseHas('book_ebook_store', [
                'book_id' => $book->id,
                'ebook_store_id' => $store1->id,
                'url' => 'https://ebook.example.com/1/updated',
                'is_primary' => false,
            ])
            ->assertDatabaseHas('book_ebook_store', [
                'book_id' => $book->id,
                'ebook_store_id' => $store2->id,
                'url' => 'https://ebook.example.com/2/updated',
                'is_primary' => true,
            ])
            ->assertDatabaseMissing('book_ebook_store', [
                'book_id' => $book->id,
                'ebook_store_id' => $store3->id,
            ]);
    }

    /** @test */
    public function 購入先情報（電子）を0件にできること(): void
    {
        $book = Book::factory()
            ->hasAttached(
                EbookStore::factory()->for(Store::factory())->create(),
                ['url' => 'https://ebook.example.com/1', 'is_primary' => true],
            )
            ->create($this->initialData);
        $newData = ['title' => 'The Great Gatsby (Updated)'];
        $requestData = $this->requestData->create([...$newData, 'ebookstores' => null]);

        $response = $this
            ->login()
            ->put(route('books.update', $book), $requestData);

        $response
            ->assertRedirect('/books');
        $this
            ->assertDatabaseHas('books', $newData)
            ->assertDatabaseMissing('book_ebook_store', [
                'book_id' => $book->id,
            ]);
    }

    /** @test */
    public function 関連作品を追加できること(): void
    {
        $book = Book::factory()->create();
        $relatedLink = ExternalLink::factory()->create(['title' => 'The Great Gatsby - Wikipedia']);
        $newData = ['title' => 'The Great Gatsby (Updated)'];
        $requestData = $this->requestData
            ->addNewRelatedItem($relatedLink, 'Wiki', 1)
            ->create($newData);

        $response = $this
            ->login()
            ->patch(route('books.update', $book), $requestData);

        $response
            ->assertRedirect('/books');
        $this
            ->assertDatabaseHas('books', $newData)
            ->assertDatabaseHas('related_items', [
                'relatable_type' => 'externalLink',
                'relatable_id' => $relatedLink->id,
                'description' => 'Wiki',
                'sort' => 1,
            ]);
    }

    /** @test */
    public function 紐付けた関連作品を更新できること(): void
    {
        $book = Book::factory()->create();
        $relatedBook = Book::factory()
            ->create(['title' => 'The Great Gatsby (limited)']);
        $relatedLink1 = ExternalLink::factory()
            ->create(['title' => 'The Great Gatsby - Wikipedia']);
        $relatedLink2 = ExternalLink::factory()
            ->create(['title' => 'F・スコット・フィッツジェラルド - Wikipedia']);
        $relatedItem1 = RelatedItem::factory()
            ->for($relatedBook, 'relatable')
            ->for($book, 'booK')
            ->create(['description' => '限定版', 'sort' => 1]);
        $relatedItem2 = RelatedItem::factory()
            ->for($relatedLink1, 'relatable')
            ->for($book, 'booK')
            ->create(['description' => 'Wiki', 'sort' => 2]);
        $beforeRelatedItemCount = RelatedItem::count();
        $newData = ['title' => 'The Great Gatsby (Updated)'];
        $requestData = $this->requestData
            ->addUpdatedRelatedItem($relatedItem1, $relatedLink2, '原作者Wiki', 2)
            ->addUpdatedRelatedItem($relatedItem2, $relatedLink1, '原作Wiki', 1)
            ->create($newData);

        $response = $this
            ->login()
            ->patch(route('books.update', $book), $requestData);

        $response
            ->assertRedirect('/books');
        $this
            ->assertDatabaseHas('books', $newData)
            ->assertDatabaseHas('related_items', [
                'id' => $relatedItem1->id,
                'relatable_type' => 'externalLink',
                'relatable_id' => $relatedLink2->id,
                'description' => '原作者Wiki',
                'sort' => 2,
            ])
            ->assertDatabaseHas('related_items', [
                'id' => $relatedItem2->id,
                'relatable_type' => 'externalLink',
                'relatable_id' => $relatedLink1->id,
                'description' => '原作Wiki',
                'sort' => 1,
            ])
            ->assertDatabaseCount('related_items', $beforeRelatedItemCount);
    }

    /** @test */
    public function 紐付けた関連作品を削除できること(): void
    {
        $book = Book::factory()->create();
        $relatedBook = Book::factory()
            ->create(['title' => 'The Great Gatsby (limited)']);
        $relatedLink = ExternalLink::factory()
            ->create(['title' => 'The Great Gatsby - Wikipedia']);
        $relatedItem1 = RelatedItem::factory()
            ->for($relatedBook, 'relatable')
            ->for($book, 'booK')
            ->create(['description' => '限定版', 'sort' => 1]);
        $relatedItem2 = RelatedItem::factory()
            ->for($relatedLink, 'relatable')
            ->for($book, 'booK')
            ->create(['description' => 'Wiki', 'sort' => 2]);
        $newData = ['title' => 'The Great Gatsby (Updated)'];
        $requestData = $this->requestData
            ->addUpdatedRelatedItem($relatedItem1)
            ->addDeletedRelatedItem($relatedItem2)
            ->create($newData);

        $response = $this
            ->login()
            ->patch(route('books.update', $book), $requestData);

        $response
            ->assertRedirect('/books');
        $this
            ->assertDatabaseHas('books', $newData)
            ->assertDatabaseHas('related_items', [
                'id' => $relatedItem1->id,
                'relatable_type' => 'book',
                'relatable_id' => $relatedBook->id,
                'description' => '限定版',
                'sort' => 1,
            ])
            ->assertDatabaseMissing('related_items', [
                'id' => $relatedItem2->id,
            ]);
    }

    /** @test */
    public function 関連作品の追加・更新・削除を同時に行えることこと(): void
    {
        $book = Book::factory()->create();
        $relatedBook = Book::factory()
            ->create(['title' => 'The Great Gatsby (limited)']);
        $relatedLink1 = ExternalLink::factory()
            ->create(['title' => 'The Great Gatsby - Wikipedia']);
        $relatedLink2 = ExternalLink::factory()
            ->create(['title' => 'F・スコット・フィッツジェラルド - Wikipedia']);
        $relatedItem1 = RelatedItem::factory()
            ->for($relatedBook, 'relatable')
            ->for($book, 'booK')
            ->create(['description' => '限定版', 'sort' => 1]);
        $relatedItem2 = RelatedItem::factory()
            ->for($relatedLink1, 'relatable')
            ->for($book, 'booK')
            ->create(['description' => 'Wiki', 'sort' => 2]);
        $beforeRelatedItemCount = RelatedItem::count();
        $newData = ['title' => 'The Great Gatsby (Updated)'];
        $requestData = $this->requestData
            ->addDeletedRelatedItem($relatedItem1)
            ->addNewRelatedItem($relatedLink2, '原作者Wiki', 1)
            ->addUpdatedRelatedItem($relatedItem2, null, '原作Wiki', 2)
            ->create($newData);

        $response = $this
            ->login()
            ->patch(route('books.update', $book), $requestData);

        $response
            ->assertRedirect('/books');
        $this
            ->assertDatabaseHas('books', $newData)
            ->assertDatabaseHas('related_items', [
                'relatable_type' => 'externalLink',
                'relatable_id' => $relatedLink2->id,
                'description' => '原作者Wiki',
                'sort' => 1,
            ])
            ->assertDatabaseHas('related_items', [
                'id' => $relatedItem2->id,
                'relatable_type' => 'externalLink',
                'relatable_id' => $relatedLink1->id,
                'description' => '原作Wiki',
                'sort' => 2,
            ])
            ->assertDatabaseMissing('related_items', [
                'id' => $relatedItem1->id,
            ])
            ->assertDatabaseCount('related_items', $beforeRelatedItemCount);
    }

    /** @test */
    public function 自由欄ブロックを追加できること(): void
    {
        $book = Book::factory()
            ->has(Block::factory()->state(['sort' => 1, 'displayed' => true, 'type_id' => BlockType::Common]))
            ->has(Block::factory()->state(['sort' => 2, 'displayed' => true, 'type_id' => BlockType::Series]))
            ->create();
        [$block1, $block2] = $book->blocks;
        $newData = ['title' => 'The Great Gatsby (Updated)'];
        $requestData = $this->requestData
            ->addUpdatedBlock($block1)
            ->addUpdatedBlock($block2)
            ->addCustomBlock(
                customTitle: '続編制作決定！！',
                customContent: '<p>乞うご期待</p>',
                sort: 3,
                displayed: true,
            )
            ->create($newData);

        $response = $this
            ->login()
            ->patch(route('books.update', $book), $requestData);

        $response
            ->assertRedirect('/books');
        $this
            ->assertDatabaseHas('books', $newData)
            ->assertDatabaseHas('blocks', ['book_id' => $book->id, 'type_id' => BlockType::Common, 'sort' => 1, 'displayed' => true])
            ->assertDatabaseHas('blocks', ['book_id' => $book->id, 'type_id' => BlockType::Series, 'sort' => 2, 'displayed' => true])
            ->assertDatabaseHas('blocks', [
                'book_id' => $book->id,
                'type_id' => BlockType::Custom,
                'sort' => 3,
                'displayed' => true,
                'custom_title' => '続編制作決定！！',
                'custom_content' => '<p>乞うご期待</p>',
            ]);
    }

    /** @test */
    public function ブロックを更新できること(): void
    {
        $book = Book::factory()
            ->has(Block::factory()->state(['sort' => 1, 'displayed' => true, 'type_id' => BlockType::Common]))
            ->has(Block::factory()->state([
                'sort' => 2,
                'displayed' => true,
                'type_id' => BlockType::EbookStore,
                'custom_content' => '<p>一部サイトは「電子限定版」となります</p>',
            ]))
            ->has(Block::factory()->state([
                'sort' => 3,
                'displayed' => false,
                'type_id' => BlockType::Custom,
                'custom_title' => '続編制作決定！！',
                'custom_content' => '<p>乞うご期待</p>',
            ]))
            ->create();
        [$block1, $block2, $block3] = $book->blocks;
        $newData = ['title' => 'The Great Gatsby (Updated)'];
        $requestData = $this->requestData
            ->addUpdatedBlock($block1)
            ->addUpdatedBlock($block2, sort: 3, customContent: '<p>一部サイトは「電子限定版」となります (Updated)</p>')
            ->addUpdatedBlock($block3, sort: 2, displayed: true, customTitle: '続編制作決定！！ (Updated)', customContent: '<a href="#">特設サイト</a>')
            ->create($newData);

        $response = $this
            ->login()
            ->patch(route('books.update', $book), $requestData);

        $response
            ->assertRedirect('/books');
        $this
            ->assertDatabaseHas('books', $newData)
            ->assertDatabaseHas('blocks', [
                'book_id' => $book->id,
                'sort' => 1,
                'displayed' => true,
                'type_id' => BlockType::Common,
            ])
            ->assertDatabaseHas('blocks', [
                'book_id' => $book->id,
                'sort' => 2,
                'displayed' => true,
                'type_id' => BlockType::Custom,
                'custom_title' => '続編制作決定！！ (Updated)',
                'custom_content' => '<a href="#">特設サイト</a>',
            ])
            ->assertDatabaseHas('blocks', [
                'book_id' => $book->id,
                'sort' => 3,
                'displayed' => true,
                'type_id' => BlockType::EbookStore,
                'custom_content' => '<p>一部サイトは「電子限定版」となります (Updated)</p>',
            ]);
    }

    /** @test */
    public function 自由欄ブロックを削除できること(): void
    {
        $book = Book::factory()
            ->has(Block::factory()->state(['sort' => 1, 'displayed' => true, 'type_id' => BlockType::Common]))
            ->has(Block::factory()->state(['sort' => 2, 'displayed' => true, 'type_id' => BlockType::Series]))
            ->has(Block::factory()->state(['sort' => 3, 'displayed' => false, 'type_id' => BlockType::Custom]))
            ->create();
        [$block1, $block2, $block3] = $book->blocks;
        $newData = ['title' => 'The Great Gatsby (Updated)'];
        $requestData = $this->requestData
            ->addUpdatedBlock($block1)
            ->addUpdatedBlock($block2)
            ->addDeletedBlock($block3)
            ->create($newData);

        $response = $this
            ->login()
            ->patch(route('books.update', $book), $requestData);

        $response
            ->assertRedirect('/books');
        $this
            ->assertDatabaseHas('books', $newData)
            ->assertDatabaseHas('blocks', ['book_id' => $book->id, 'sort' => 1, 'displayed' => true, 'type_id' => BlockType::Common])
            ->assertDatabaseHas('blocks', ['book_id' => $book->id, 'sort' => 2, 'displayed' => true, 'type_id' => BlockType::Series])
            ->assertModelMissing($block3);
    }

    /** @test */
    public function ブロックの追加・更新・削除を同時に行えることこと(): void
    {
        $book = Book::factory()
            ->has(Block::factory()->state(['sort' => 1, 'displayed' => true, 'type_id' => BlockType::Common]))
            ->has(Block::factory()->state(['sort' => 2, 'displayed' => true, 'type_id' => BlockType::Benefit]))
            ->has(Block::factory()->state(['sort' => 3, 'displayed' => false, 'type_id' => BlockType::Custom]))
            ->create();
        [$block1, $block2, $block3] = $book->blocks;
        $newData = ['title' => 'The Great Gatsby (Updated)'];
        $requestData = $this->requestData
            ->addUpdatedBlock($block1, sort: 2)
            ->addUpdatedBlock($block2, sort: 3, displayed: false)
            ->addDeletedBlock($block3)
            ->addCustomBlock(
                customTitle: '続編制作決定！！',
                customContent: '<p>乞うご期待</p>',
                sort: 1,
                displayed: true,
            )
            ->create($newData);

        $response = $this
            ->login()
            ->patch(route('books.update', $book), $requestData);

        $response
            ->assertRedirect('/books');
        $this
            ->assertDatabaseHas('books', $newData)
            ->assertDatabaseHas('blocks', [
                'book_id' => $book->id,
                'sort' => 1,
                'displayed' => true,
                'type_id' => BlockType::Custom,
                'custom_title' => '続編制作決定！！',
                'custom_content' => '<p>乞うご期待</p>',
            ])
            ->assertDatabaseHas('blocks', ['book_id' => $book->id, 'sort' => 2, 'displayed' => true, 'type_id' => BlockType::Common])
            ->assertDatabaseHas('blocks', ['book_id' => $book->id, 'sort' => 3, 'displayed' => false, 'type_id' => BlockType::Benefit])
            ->assertModelMissing($block3);
    }
}
