<?php

namespace Tests\Feature\Http\Controllers\Api\BookPreview;

use App\Enums\BlockType;
use App\Enums\BookStatus;
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
use App\Models\Story;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\Supports\BookRequestDataFactory;
use Tests\TestCase;

class PreviewBookTest extends TestCase
{
    private BookRequestDataFactory $requestData;

    protected function setUp(): void
    {
        parent::setUp();
        $this->requestData = BookRequestDataFactory::new();
    }

    private function postTo(Book $book = null): string
    {
        return route('api.books.preview', $book);
    }

    /** @test */
    public function ログインしたユーザーのみプレビューできること(): void
    {
        $this
            ->postJson($this->postTo(), $this->requestData->create())
            ->assertUnauthorized();
        $this->login()
            ->postJson($this->postTo(), $this->requestData->create())
            ->assertOk();
    }

    /** @test */
    public function 不正なリクエストに対してバリデーションエラーが発生すること(): void
    {
        $this->login()
            ->postJson($this->postTo(), $this->requestData->create([
                'title' => str_repeat('あ', 256),
            ]))
            ->assertUnprocessable();
    }

    /** @test */
    public function プレビューURLのパス情報があるサイトに対してのみ、プレビューURLを返却すること(): void
    {
        $site1 = Site::factory()->create([
            'url' => 'https://site1.example.com/',
            'book_preview_path' => 'preview/[token]',
        ]);
        $site2 = Site::factory()->create([
            'url' => 'https://site2.example.com/',
            'book_preview_path' => 'book/preview/[token]',
        ]);
        $_site3 = Site::factory()->create([
            'url' => 'https://site3.example.com/',
            'book_preview_path' => null,
        ]);

        $this->login()
            ->postJson($this->postTo(), $this->requestData->create())
            ->assertJson(fn (AssertableJson $json) => $json
                ->count('previews', 2)
                ->has('previews.0', fn (AssertableJson $json) => $json
                    ->where('site.id', $site1->id)
                    ->where('url', function (string $url) {
                        $start = 'https://site1.example.com/preview/';

                        return str_starts_with($url, $start)
                            && str($url)->remove($start)->isUuid();
                    })
                )
                ->has('previews.1', fn (AssertableJson $json) => $json
                    ->where('site.id', $site2->id)
                    ->where('url', function (string $url) {
                        $start = 'https://site2.example.com/book/preview/';

                        return str_starts_with($url, $start)
                            && str($url)->remove($start)->isUuid();
                    })
                )
            );
    }

    /** @test */
    public function ステータスが「下書き」「公開予定」の書籍もプレビューできること(): void
    {
        $this->freezeTime();

        $requestData1 = $this->requestData->setStatus(BookStatus::Draft)->create([
            'title' => '下書きの書籍',
        ]);
        $requestData2 = $this->requestData->setStatus(BookStatus::WillBePublished)->create([
            'title' => '公開予定の書籍',
        ]);

        $this->login()
            ->postJson($this->postTo(), $requestData1)
            ->assertOk();
        $this
            ->postJson($this->postTo(), $requestData2)
            ->assertOk();

        $previews = BookPreview::latest()->take(2)->get();
        $this->assertSame('下書きの書籍', $previews[0]->model->title);
        $this->assertSame('公開予定の書籍', $previews[1]->model->title);
    }

    /** @test */
    public function Bookモデルの各種アトリビュートをプレビューに含められること(): void
    {
        $attributes = [
            'title' => 'コミックシャイニー',
            'volume' => 'vol.100',
            'isbn13' => '9784123456789',
        ];

        $this->login()
            ->postJson($this->postTo(), $this->requestData->create($attributes))
            ->assertOk();

        $preview = BookPreview::latest()->first();
        $actual = $preview->model->only(array_keys($attributes));
        $this->assertSame($attributes, $actual);
    }

    /** @test */
    public function 既存のBookモデルを元に各種アトリビュートをプレビューに含められること(): void
    {
        $book = Book::factory()->create($attributes = [
            'title' => 'コミックシャイニー',
            'volume' => 'vol.101',
            'isbn13' => '9784123456789',
        ]);
        $requestData = $this->requestData->create([
            'title' => 'コミックシャイニー+',
            'volume' => 'vol.102',
            'description' => '説明文説明文説明文',
        ] + $attributes);

        $this->login()
            ->postJson($this->postTo($book), $requestData)
            ->assertOk();

        $preview = BookPreview::latest()->first();
        $this->assertSame([
            'title' => 'コミックシャイニー+',
            'volume' => 'vol.102',
            'isbn13' => '9784123456789',
            'description' => '説明文説明文説明文',
        ], $preview->model->only([
            'title', 'volume', 'isbn13', 'description',
        ]));
    }

    /** @test */
    public function belongsToリレーションをプレビューに含められること(): void
    {
        $this->login()
            ->postJson($this->postTo(), $this->requestData->create([
                'label_id' => ($label = Label::factory()->create())->id,
                'genre_id' => ($genre = Genre::factory()->create())->id,
                'series_id' => ($series = Series::factory()->create())->id,
                'format_id' => ($format = BookFormat::first())->id,
                'size_id' => ($size = BookSize::first())->id,
            ]))
            ->assertOk();

        $preview = BookPreview::latest()->first();
        $this->assertSame(
            $label->only('id', 'name'),
            $preview->model->label->only('id', 'name')
        );
        $this->assertSame(
            $genre->only('id', 'name'),
            $preview->model->genre->only('id', 'name')
        );
        $this->assertSame(
            $series->only('id', 'name'),
            $preview->model->series->only('id', 'name')
        );
        $this->assertSame(
            $format->only('id', 'name'),
            $preview->model->format->only('id', 'name')
        );
        $this->assertSame(
            $size->only('id', 'name'),
            $preview->model->size->only('id', 'name')
        );
    }

    /** @test */
    public function 既存のBookモデルを元にbelongsToリレーションをプレビューに含められること(): void
    {
        $book = Book::factory()
            ->for(Genre::factory()->create())
            ->for($label = Label::factory()->create())
            ->for($series = Series::factory()->create())
            ->for($format = BookFormat::find(1), 'format')
            ->for(BookSize::find(1), 'size')
            ->create();
        $genre = Genre::factory()->create();

        $this->login()
            ->postJson($this->postTo($book), $this->requestData->create([
                'genre_id' => $genre->id,
                'size_id' => null,
            ] + $book->only([
                'label_id',
                'series_id',
                'format_id',
            ])))
            ->assertOk();

        $preview = BookPreview::latest()->first();
        $this->assertSame(
            $genre->only('id', 'name'),
            $preview->model->genre->only('id', 'name')
        );
        $this->assertSame(
            $label->only('id', 'name'),
            $preview->model->label->only('id', 'name')
        );
        $this->assertSame(
            $series->only('id', 'name'),
            $preview->model->series->only('id', 'name')
        );
        $this->assertSame(
            $format->only('id', 'name'),
            $preview->model->format->only('id', 'name')
        );
        $this->assertNull($preview->model->size);
    }

    /** @test */
    public function 選択した書影がプレビューに含まれること(): void
    {
        $requestData = [
            'cover' => UploadedFile::fake()->image('cover.jpg', 200, 300),
        ] + $this->requestData->create();

        $this->login()
            ->postJson($this->postTo(), $requestData)
            ->assertOk();

        $preview = BookPreview::latest()->first();
        $cover = $preview->model->preview_cover;
        $this->assertNotNull($cover['original_url']);
        $this->assertSame(200, Arr::get($cover, 'custom_properties.width'));
        $this->assertSame(300, Arr::get($cover, 'custom_properties.height'));
    }

    /** @test */
    public function 選択した書影がストレージに保存され、プレビューの削除と同時にストレージから削除されること(): void
    {
        $requestData = [
            'cover' => UploadedFile::fake()->image('cover.jpg', 200, 300),
        ] + $this->requestData->create();

        $this->login()
            ->postJson($this->postTo(), $requestData)
            ->assertOk();

        $preview = BookPreview::latest()->first();
        $cover = $preview->model->preview_cover;
        $path = 'tmp/preview/book/cover/' . basename($cover['original_url']);
        Storage::disk('public')->assertExists($path);
        $preview->delete();
        Storage::disk('public')->assertMissing($path);
    }

    /** @test */
    public function 書影が未選択の場合、プレビューに書影情報が含まれないこと(): void
    {
        $requestData = $this->requestData->except('cover')->create();

        $this->login()
            ->postJson($this->postTo(), $requestData)
            ->assertOk();

        $preview = BookPreview::latest()->first();
        $this->assertNull($preview->model->preview_cover);
    }

    /** @test */
    public function 書影が未選択の場合、書影設定済みの既存書籍のプレビューから情報の情報が削除されること(): void
    {
        $book = Book::factory()->attachCover(
            UploadedFile::fake()->image('cover.jpg', 120, 240),
        )->create();
        $requestData = [
            'cover' => null,
        ] + $this->requestData->create();

        $this->login()
            ->postJson($this->postTo($book), $requestData)
            ->assertOk();

        $preview = BookPreview::latest()->first();
        $this->assertNull($preview->model->preview_cover);
    }

    /** @test */
    public function 書影が未設定の既存書籍のプレビューに、選択した書影の情報を含められること(): void
    {
        $book = Book::factory()->create();
        $requestData = [
            'cover' => UploadedFile::fake()->image('cover.jpg', 200, 300),
        ] + $this->requestData->create();

        $this->login()
            ->postJson($this->postTo($book), $requestData)
            ->assertOk();

        $preview = BookPreview::latest()->first();
        $cover = $preview->model->preview_cover;
        $this->assertNotNull($cover['original_url']);
        $this->assertSame(200, Arr::get($cover, 'custom_properties.width'));
        $this->assertSame(300, Arr::get($cover, 'custom_properties.height'));
    }

    /** @test */
    public function 書影設定済みの既存書籍のプレビューに、設定済みの書影の情報を含められること(): void
    {
        $book = Book::factory()->attachCover(
            UploadedFile::fake()->image('cover.jpg', 120, 240),
        )->create();

        $this->login()
            ->postJson($this->postTo($book), $this->requestData->create())
            ->assertOk();

        $preview = BookPreview::latest()->first();
        $cover = $preview->model->preview_cover;
        $this->assertNotNull($cover['original_url']);
        $this->assertSame(120, Arr::get($cover, 'custom_properties.width'));
        $this->assertSame(240, Arr::get($cover, 'custom_properties.height'));
    }

    /** @test */
    public function 書影設定済みの既存書籍のプレビューに、新たに選択した書影の情報を含められること(): void
    {
        $book = Book::factory()->attachCover(
            UploadedFile::fake()->image('cover.jpg', 120, 240),
        )->create();
        $requestData = [
            'cover' => UploadedFile::fake()->image('cover.jpg', 200, 300),
        ] + $this->requestData->create();

        $this->login()
            ->postJson($this->postTo($book), $requestData)
            ->assertOk();

        $preview = BookPreview::latest()->first();
        $cover = $preview->model->preview_cover;
        $this->assertNotNull($cover['original_url']);
        $this->assertSame(200, Arr::get($cover, 'custom_properties.width'));
        $this->assertSame(300, Arr::get($cover, 'custom_properties.height'));
    }

    /** @test */
    public function 作家情報の追加・更新・削除がプレビューに反映されること(): void
    {
        $book = Book::factory()->create();
        $creator1 = Creator::factory()->hasAttached($book, [
            'sort' => 1, 'creation_type' => CreationType::factory()->create(['name' => '原作'])->name,
        ])->create();
        $creator2 = Creator::factory()->hasAttached($book, [
            'sort' => 2, 'creation_type' => CreationType::factory()->create(['name' => 'イラスト'])->name,
        ])->create();
        $creator3 = Creator::factory()->create();
        $requestData = $this->requestData
            ->addCreation(
                $creator3, 'イラスト', 'スペシャルサンクス', 1,
            )->addCreation(
                $creator1, '原作', null, 2
            )->create();

        $this->login()
            ->postJson($this->postTo($book), $requestData)
            ->assertOk();

        $preview = BookPreview::latest()->first();
        $this->assertInstanceOf(BelongsToMany::class, $preview->model->{$relation = 'creators'}());
        $this->assertCount(2, $creators = $preview->model->{$relation});
        $this->assertSame($creator3->id, $creators[0]->id);
        $this->assertSame($creator1->id, $creators[1]->id);
        $this->assertSame('スペシャルサンクス', $creators[0]->creation->type);
        $this->assertSame('原作', $creators[1]->creation->type);
    }

    /** @test */
    public function 購入先情報の追加・更新・削除がプレビューに反映されること(): void
    {
        $book = Book::factory()->create();
        $bookStore1 = BookStore::factory()->hasAttached($book, ['is_primary' => false])->create();
        $bookStore2 = BookStore::factory()->hasAttached($book, ['is_primary' => true])->create();
        $bookStore3 = BookStore::factory()->create();
        $requestData = $this->requestData
            ->addBookStore(
                $bookStore1, 'https://store.example.com/product/1', false,
            )
            ->addBookStore(
                $bookStore3, 'https://store.example.com/product/3', true,
            )
            ->create();

        $this->login()
            ->postJson($this->postTo($book), $requestData)
            ->assertOk();

        $preview = BookPreview::latest()->first();
        $this->assertInstanceOf(BelongsToMany::class, $preview->model->{$relation = 'bookstores'}());
        $this->assertCount(2, $bookStores = $preview->model->{$relation});
        $this->assertSame($bookStore1->id, $bookStores[0]->id);
        $this->assertSame($bookStore3->id, $bookStores[1]->id);
        $this->assertSame('https://store.example.com/product/1', $bookStores[0]->pivot->url);
        $this->assertSame('https://store.example.com/product/3', $bookStores[1]->pivot->url);
        $this->assertFalse($bookStores[0]->pivot->is_primary);
        $this->assertTrue($bookStores[1]->pivot->is_primary);
    }

    /** @test */
    public function 電子書籍の購入先情報の追加・更新・削除がプレビューに反映されること(): void
    {
        $book = Book::factory()->create();
        $ebookStore1 = EbookStore::factory()->hasAttached($book, ['is_primary' => false])->create();
        $ebookStore2 = EbookStore::factory()->hasAttached($book, ['is_primary' => true])->create();
        $ebookStore3 = EbookStore::factory()->create();
        $requestData = $this->requestData
            ->addEbookStore(
                $ebookStore1, 'https://store.example.com/product/1', false,
            )
            ->addEbookStore(
                $ebookStore3, 'https://store.example.com/product/3', true,
            )
            ->create();

        $this->login()
            ->postJson($this->postTo($book), $requestData)
            ->assertOk();

        $preview = BookPreview::latest()->first();
        $this->assertInstanceOf(BelongsToMany::class, $preview->model->{$relation = 'ebookstores'}());
        $this->assertCount(2, $ebookStores = $preview->model->{$relation});
        $this->assertSame($ebookStore1->id, $ebookStores[0]->id);
        $this->assertSame($ebookStore3->id, $ebookStores[1]->id);
        $this->assertSame('https://store.example.com/product/1', $ebookStores[0]->pivot->url);
        $this->assertSame('https://store.example.com/product/3', $ebookStores[1]->pivot->url);
        $this->assertFalse($ebookStores[0]->pivot->is_primary);
        $this->assertTrue($ebookStores[1]->pivot->is_primary);
    }

    /** @test */
    public function 店舗特典の追加・更新・削除がプレビューに反映されること(): void
    {
        $book = Book::factory()->create();
        $benefit1 = Benefit::factory()->hasAttached($book)->create();
        $benefit2 = Benefit::factory()->hasAttached($book)->create();
        $benefit3 = Benefit::factory()->create();
        $requestData = $this->requestData
            ->addBenefit($benefit1)
            ->addBenefit($benefit3)
            ->create();

        $this->login()
            ->postJson($this->postTo($book), $requestData)
            ->assertOk();

        $preview = BookPreview::latest()->first();
        $this->assertInstanceOf(BelongsToMany::class, $preview->model->{$relation = 'benefits'}());
        $this->assertCount(2, $benefits = $preview->model->{$relation});
        $this->assertSame($benefit1->id, $benefits[0]->id);
        $this->assertSame($benefit3->id, $benefits[1]->id);
    }

    /** @test */
    public function 収録作品の追加・更新・削除がプレビューに反映されること(): void
    {
        $book = Book::factory()->create();
        $story1 = Story::factory()->hasAttached($book)->create();
        $story2 = Story::factory()->hasAttached($book)->create();
        $story3 = Story::factory()->create();
        $requestData = $this->requestData
            ->addStory($story1)
            ->addStory($story3)
            ->create();

        $this->login()
            ->postJson($this->postTo($book), $requestData)
            ->assertOk();

        $preview = BookPreview::latest()->first();
        $this->assertInstanceOf(BelongsToMany::class, $preview->model->{$relation = 'stories'}());
        $this->assertCount(2, $stories = $preview->model->{$relation});
        $this->assertSame($story1->id, $stories[0]->id);
        $this->assertSame($story3->id, $stories[1]->id);
    }

    /** @test */
    public function キャラクターの追加・更新・削除がプレビューに反映されること(): void
    {
        $book = Book::factory()->create();
        $character1 = Character::factory()->hasAttached($book)->create();
        $character2 = Character::factory()->hasAttached($book)->create();
        $character3 = Character::factory()->create();
        $requestData = $this->requestData
            ->addCharacter($character1)
            ->addCharacter($character3)
            ->create();

        $this->login()
            ->postJson($this->postTo($book), $requestData)
            ->assertOk();

        $preview = BookPreview::latest()->first();
        $this->assertInstanceOf(BelongsToMany::class, $preview->model->{$relation = 'characters'}());
        $this->assertCount(2, $characters = $preview->model->{$relation});
        $this->assertSame($character1->id, $characters[0]->id);
        $this->assertSame($character3->id, $characters[1]->id);
    }

    /** @test */
    public function 関連作品の追加・更新・削除がプレビューに反映されること(): void
    {
        $book = Book::factory()->create();
        $item1 = RelatedItem::factory()->for($book)->create();
        $item2 = RelatedItem::factory()->for($book)->create();
        $item3 = RelatedItem::factory()->for($book)->create();
        $requestData = $this->requestData
            ->addNewRelatedItem(
                $relatable1 = ExternalLink::factory()->create(),
                description: 'アンソロジー', sort: '1',
            )->addUpdatedRelatedItem(
                $item3,
                $relatable2 = ExternalLink::factory()->create(),
                description: 'アートワーク', sort: '2',
            )->addUpdatedRelatedItem(
                $item2,
                $relatable3 = Book::factory()->create(),
                description: '原作小説', sort: '3',
            )->addDeletedRelatedItem(
                $item1,
            )->create();

        $this->login()
            ->postJson($this->postTo($book), $requestData)
            ->assertOk();

        $preview = BookPreview::latest()->first();
        $this->assertInstanceOf(HasMany::class, $preview->model->{$relation = 'relatedItems'}());
        $this->assertCount(3, $items = $preview->model->{$relation});
        $this->assertSame('アンソロジー', $items[0]->description);
        $this->assertSame('アートワーク', $items[1]->description);
        $this->assertSame('原作小説', $items[2]->description);
        $this->assertSame($relatable1->id, $items[0]->relatable->id);
        $this->assertSame($relatable2->id, $items[1]->relatable->id);
        $this->assertSame($relatable3->id, $items[2]->relatable->id);
        $this->assertInstanceOf(ExternalLink::class, $items[0]->relatable);
        $this->assertInstanceOf(ExternalLink::class, $items[1]->relatable);
        $this->assertInstanceOf(Book::class, $items[2]->relatable);
    }

    /** @test */
    public function 表示設定の追加・更新・削除がプレビューに反映されること(): void
    {
        $book = Book::factory()->create();
        $block1 = Block::factory()->for($book)->create(['sort' => 1, 'displayed' => true, 'type_id' => BlockType::Common]);
        $block2 = Block::factory()->for($book)->create(['sort' => 2, 'displayed' => true, 'type_id' => BlockType::Series]);
        $block3 = Block::factory()->for($book)->create(['sort' => 3, 'displayed' => true, 'type_id' => BlockType::Custom]);
        $requestData = $this->requestData
            ->addCustomBlock(
                customTitle: '自由欄',
                customContent: '<p>自由欄</p>',
                sort: 1, displayed: true,
            )->addUpdatedBlock(
                $block1,
                sort: 2, displayed: true,
            )->addEbookStoreBlock(
                customContent: '<p>電子書籍 購入先</p>',
                sort: 3, displayed: true,
            )->addUpdatedBlock(
                $block2,
                sort: 4, displayed: false,
            )->addDeletedBlock(
                $block3
            )->create();

        $this->login()
            ->postJson($this->postTo($book), $requestData)
            ->assertOk();

        $preview = BookPreview::latest()->first();
        $this->assertInstanceOf(HasMany::class, $preview->model->{$relation = 'blocks'}());
        $this->assertCount(3, $blokcs = $preview->model->{$relation});
        [$custom, $common, $ebookStore] = $blokcs;
        $this->assertSame(BlockType::Custom, $custom->type);
        $this->assertSame(BlockType::Common, $common->type);
        $this->assertSame(BlockType::EbookStore, $ebookStore->type);
        $this->assertSame('自由欄', $custom->custom_title);
        $this->assertSame('<p>自由欄</p>', $custom->custom_content);
        $this->assertSame('<p>電子書籍 購入先</p>', $ebookStore->custom_content);
    }
}
