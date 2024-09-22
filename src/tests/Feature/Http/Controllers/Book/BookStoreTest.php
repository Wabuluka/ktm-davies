<?php

namespace Tests\Feature\Http\Controllers\Book;

use App\Enums\BookStatus;
use App\Models\Book;
use App\Models\BookStore;
use App\Models\Creator;
use App\Models\EbookStore;
use App\Models\ExternalLink;
use App\Models\Genre;
use App\Models\Label;
use App\Models\Series;
use App\Models\Site;
use App\Models\Store;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Tests\Supports\BookRequestDataFactory;
use Tests\TestCase;

class BookStoreTest extends TestCase
{
    private BookRequestDataFactory $requestData;

    protected function setUp(): void
    {
        parent::setUp();
        $this->requestData = BookRequestDataFactory::new();
    }

    /** @test */
    public function 書籍を作成できること(): void
    {
        $data = [
            'title' => 'The Great Gatsby',
            'title_kana' => 'ザ・グレート・ギャツビー',
            'volume' => 'Volume 1',
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
        $requestData = $this->requestData->create($data);

        $response = $this
            ->login()
            ->post(route('books.store'), $requestData);

        $response->assertRedirect('/books');
        $this->assertDatabaseHas('books', $data);
    }

    /** @test */
    public function ログインしなければ書籍を作成できないこと(): void
    {
        $data = ['title' => 'The Great Gatsby'];
        $requestData = $this->requestData->create($data);

        $response = $this
            ->post(route('books.store'), $requestData);

        $response->assertRedirect('/login');
        $this->assertDatabaseMissing('books', $data);
    }

    /** @test */
    public function created_byとupdated_byが自動的に設定されること(): void
    {
        $data = ['title' => 'The Great Gatsby'];
        $requestData = $this->requestData->create($data);
        $user = User::factory()->create();

        $response = $this
            ->login($user)
            ->post(route('books.store'), $requestData);

        $response->assertRedirect('/books');
        $this->assertDatabaseHas('books', [
            ...$data,
            'created_by' => $user->id,
            'updated_by' => $user->id,
        ]);
    }

    /** @test */
    public function 不正なステータス指定に対してエラーが発生すること(): void
    {
        $data = ['title' => 'The Great Gatsby'];
        $requestData1 = $this->requestData
            ->setStatus(BookStatus::Draft)
            ->create([...$data, 'published_at' => now()]);
        $requestData2 = $this->requestData
            ->setStatus(BookStatus::WillBePublished)
            ->create([...$data, 'published_at' => now()]);
        $requestData3 = $this->requestData
            ->setStatus(BookStatus::Published)
            ->create([...$data, 'published_at' => now()]);

        $this->login();
        $this->post(route('books.store'), $requestData1)->assertSessionHasErrors('published_at');
        $this->post(route('books.store'), $requestData2)->assertSessionHasErrors('published_at');
        $this->post(route('books.store'), $requestData3)->assertSessionHasErrors('published_at');
        $this->assertDatabaseMissing('books', $data);
    }

    /** @test */
    public function 公開サイトを紐付けて保存できること(): void
    {
        [$site1, $site2] = Site::factory(2)->create();
        $data = ['title' => 'The Great Gatsby'];
        $requestData = $this->requestData->create([...$data, 'sites' => [$site1->id, $site2->id]]);

        $response = $this
            ->login()
            ->post(route('books.store'), $requestData);

        $response->assertRedirect('/books');
        $this->assertDatabaseHas('books', $data);
        $this->assertDatabaseHas('book_site', ['site_id' => $site1->id]);
        $this->assertDatabaseHas('book_site', ['site_id' => $site2->id]);
    }

    /** @test */
    public function 書影を紐付けて保存できること(): void
    {
        $data = ['title' => 'The Great Gatsby'];
        $cover = UploadedFile::fake()->image('cover.jpg', 200, 300);
        $requestData = $this->requestData->create([...$data, 'cover' => $cover]);

        $response = $this
            ->login()
            ->post(route('books.store'), $requestData);

        $response->assertRedirect('/books');
        $this->assertDatabaseHas('books', $data);
        $this->assertDatabaseHas('media', [
            'model_type' => 'book',
            'file_name' => 'cover.jpg',
            'custom_properties->width' => 200,
            'custom_properties->height' => 300,
        ]);
    }

    /** @test */
    public function 作家情報を紐付けて保存できること(): void
    {
        $data = ['title' => 'The Great Gatsby'];
        $creator1 = Creator::factory()->create(['name' => 'Francis Scott Key Fitzgerald']);
        $creator2 = Creator::factory()->create(['name' => '野崎孝']);
        $requestData = $this->requestData
            ->addCreation($creator1, '原作', '小説', 1)
            ->addCreation($creator2, '翻訳', null, 2)
            ->create($data);

        $response = $this
            ->login()
            ->post(route('books.store'), $requestData);

        $response
            ->assertRedirect('/books');
        $this
            ->assertDatabaseHas('books', $data)
            ->assertDatabaseHas('book_creations', [
                'creator_id' => $creator1->id,
                'creation_type' => '原作',
                'displayed_type' => '小説',
                'sort' => 1,
            ])
            ->assertDatabaseHas('book_creations', [
                'creator_id' => $creator2->id,
                'creation_type' => '翻訳',
                'displayed_type' => null,
                'sort' => 2,
            ]);
    }

    /** @test */
    public function 紙書店を紐付けて保存できること(): void
    {
        $data = ['title' => 'The Great Gatsby'];
        $amazon = BookStore::factory()
            ->for(Store::factory()->state(['name' => 'Amazon Kindle']))
            ->create();
        $rakuten = BookStore::factory()
            ->for(Store::factory()->state(['name' => '楽天ブックス']))
            ->create();
        $requestData = $this->requestData
            ->addBookStore(
                store: $amazon,
                url: $amazonUrl = 'https://www.amazon.co.jp/dp/B00JZ2KZK6',
                isPrimary: true
            )
            ->addBookStore(
                store: $rakuten,
                url: $rakutenUrl = 'https://books.rakuten.co.jp/rk/example/',
                isPrimary: false
            )
            ->create($data);

        $response = $this
            ->login()
            ->post(route('books.store'), $requestData);

        $response
            ->assertRedirect('/books');
        $this
            ->assertDatabaseHas('books', $data)
            ->assertDatabaseHas('book_book_store', [
                'book_store_id' => $amazon->id,
                'url' => $amazonUrl,
                'is_primary' => true,
            ])
            ->assertDatabaseHas('book_book_store', [
                'book_store_id' => $rakuten->id,
                'url' => $rakutenUrl,
                'is_primary' => false,
            ]);
    }

    /** @test */
    public function 電子書店を紐付けて保存できること(): void
    {
        $data = ['title' => 'The Great Gatsby'];
        $amazon = EbookStore::factory()
            ->for(Store::factory()->state(['name' => 'Amazon Kindle']))
            ->create();
        $rakuten = EbookStore::factory()
            ->for(Store::factory()->state(['name' => '楽天ブックス']))
            ->create();
        $requestData = $this->requestData
            ->addEbookStore(
                store: $amazon,
                url: $amazonUrl = 'https://www.amazon.co.jp/dp/B00JZ2KZK6',
                isPrimary: true
            )
            ->addEbookStore(
                store: $rakuten,
                url: $rakutenUrl = 'https://books.rakuten.co.jp/rk/example/',
                isPrimary: false
            )
            ->create($data);

        $response = $this
            ->login()
            ->post(route('books.store'), $requestData);

        $response
            ->assertRedirect('/books');
        $this
            ->assertDatabaseHas('books', $data)
            ->assertDatabaseHas('book_ebook_store', [
                'ebook_store_id' => $amazon->id,
                'url' => $amazonUrl,
                'is_primary' => true,
            ])
            ->assertDatabaseHas('book_ebook_store', [
                'ebook_store_id' => $rakuten->id,
                'url' => $rakutenUrl,
                'is_primary' => false,
            ]);
    }

    /** @test */
    public function 関連作品を紐付けて保存できること(): void
    {
        $data = ['title' => 'The Great Gatsby'];
        $relatedBook = Book::factory()
            ->create(['title' => 'The Great Gatsby (limited)']);
        $relatedLink = ExternalLink::factory()
            ->create(['title' => 'The Great Gatsby - Wikipedia', 'url' => 'https://example.com/wiki']);
        $requestData = $this->requestData
            ->addNewRelatedItem($relatedLink, 'Wiki', 1)
            ->addNewRelatedItem($relatedBook, '限定版', 2)
            ->create($data);

        $response = $this
            ->login()
            ->post(route('books.store'), $requestData);

        $response
            ->assertRedirect('/books');
        $this
            ->assertDatabaseHas('books', $data)
            ->assertDatabaseHas('related_items', [
                'relatable_type' => 'externalLink',
                'relatable_id' => $relatedLink->id,
                'description' => 'Wiki',
                'sort' => 1,
            ])
            ->assertDatabaseHas('related_items', [
                'relatable_type' => 'book',
                'relatable_id' => $relatedBook->id,
                'description' => '限定版',
                'sort' => 2,
            ]);
    }

    /** @test */
    public function 表示設定を紐付けて保存できること(): void
    {
        $data = ['title' => 'The Great Gatsby'];
        $requestData = $this->requestData
            ->addCommonBlock(sort: 1, displayed: true)
            ->addBookStoreBlock(sort: 2, displayed: false)
            ->addEbookStoreBlock(
                sort: 3,
                displayed: false,
                customContent: '<p>一部サイトは「電子限定版」となります</p>',
            )
            ->addBenefitBlock(sort: 4, displayed: false)
            ->addSeriesBlock(sort: 5, displayed: false)
            ->addRelatedBlock(sort: 6, displayed: false)
            ->addStoryBlock(sort: 7, displayed: false)
            ->addCharacterBlock(sort: 8, displayed: false)
            ->addCustomBlock(
                sort: 9,
                displayed: true,
                customTitle: '続編制作決定！！',
                customContent: '<p>乞うご期待</p>',
            )
            ->create($data);

        $response = $this
            ->login()
            ->post(route('books.store'), $requestData);

        $response
            ->assertRedirect('/books');
        $this
            ->assertDatabaseHas('books', $data)
            ->assertDatabaseHas('blocks', ['sort' => 1, 'type_id' => 1, 'displayed' => true,  'custom_title' => null, 'custom_content' => null])
            ->assertDatabaseHas('blocks', ['sort' => 2, 'type_id' => 2, 'displayed' => false, 'custom_title' => null, 'custom_content' => null])
            ->assertDatabaseHas('blocks', ['sort' => 3, 'type_id' => 3, 'displayed' => false, 'custom_title' => null, 'custom_content' => '<p>一部サイトは「電子限定版」となります</p>'])
            ->assertDatabaseHas('blocks', ['sort' => 4, 'type_id' => 4, 'displayed' => false, 'custom_title' => null, 'custom_content' => null])
            ->assertDatabaseHas('blocks', ['sort' => 5, 'type_id' => 5, 'displayed' => false, 'custom_title' => null, 'custom_content' => null])
            ->assertDatabaseHas('blocks', ['sort' => 6, 'type_id' => 6, 'displayed' => false, 'custom_title' => null, 'custom_content' => null])
            ->assertDatabaseHas('blocks', ['sort' => 7, 'type_id' => 7, 'displayed' => false, 'custom_title' => null, 'custom_content' => null])
            ->assertDatabaseHas('blocks', ['sort' => 8, 'type_id' => 8, 'displayed' => false, 'custom_title' => null, 'custom_content' => null])
            ->assertDatabaseHas('blocks', ['sort' => 9, 'type_id' => 9, 'displayed' => true,  'custom_title' => '続編制作決定！！', 'custom_content' => '<p>乞うご期待</p>']);
    }
}
