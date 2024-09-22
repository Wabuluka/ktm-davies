<?php

namespace Tests\Feature\Http\Controllers\Book;

use App\Enums\BlockType;
use App\Models\Benefit;
use App\Models\Book;
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
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Http\UploadedFile;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class BookEditTest extends TestCase
{
    /** @test */
    public function 未ログイン状態であればログイン画面にリダイレクトすること(): void
    {
        $response = $this->get(route('books.edit', Book::factory()->create()));
        $response->assertRedirect('/login');
    }

    /** @test */
    public function ログイン状態であれば書籍作成ページを表示すること(): void
    {
        $this->login();
        $response = $this->get(route('books.edit', Book::factory()->create()));
        $response->assertOk();
    }

    /** @test */
    public function viewにBookの属性とリレーションを渡すこと(): void
    {
        $creationType1 = CreationType::factory()->create(['name' => '原作']);
        $creationType2 = CreationType::factory()->create(['name' => 'その他']);
        $label = Label::factory(['name' => 'レーベル 1']);
        $genre = Genre::factory(['name' => 'ジャンル 1']);
        $series = Series::factory(['name' => 'シリーズ 1']);
        $book = Book::factory()
            ->for($label)->for($genre)->for($series)
            ->hasAttached(Site::factory(2, new Sequence(
                ['name' => 'サイト 1'],
                ['name' => 'サイト 2'],
            ))
                ->afterCreating(fn (Site $site) => $site
                    ->addMedia(UploadedFile::fake()->create('sitelogo-jpg'))
                    ->toMediaCollection())
            )
            ->hasAttached(Creator::factory()->create(['name' => '作家 1']), [
                'creation_type' => $creationType1->name,
                'sort' => 2,
            ])
            ->hasAttached(Creator::factory()->create(['name' => '作家 2']), [
                'creation_type' => $creationType2->name,
                'sort' => 1,
                'displayed_type' => '翻訳',
            ])
            ->hasAttached(BookStore::factory()
                ->for(Store::factory()->state(['name' => '書店 1'])),
                ['is_primary' => true, 'url' => 'https://book.example.com/1']
            )
            ->hasAttached(BookStore::factory()
                ->for(Store::factory()->state(['name' => '書店 2'])),
                ['is_primary' => false, 'url' => null]
            )
            ->hasAttached(EbookStore::factory()
                ->for(Store::factory()->state(['name' => '電子書店 1'])),
                ['is_primary' => true, 'url' => 'https://ebook.example.com/1']
            )
            ->hasAttached(EbookStore::factory()
                ->for(Store::factory()->state(['name' => '電子書店 2'])),
                ['is_primary' => false, 'url' => null]
            )
            ->hasAttached(
                Benefit::factory()->paid(true)->create(['name' => 'クリアファイル'])
            )
            ->hasAttached(
                Benefit::factory()->paid(false)->create(['name' => 'サイン入りイラスト'])
            )
            ->hasAttached(
                Story::factory()->create(['title' => '桃太郎', 'trial_url' => 'https://example.com'])
            )
            ->hasAttached(
                Story::factory()->create(['title' => '浦島太郎', 'trial_url' => null])
            )
            ->has(RelatedItem::factory()
                ->for(
                    $relatedBook = Book::factory()->create(['title' => 'Related Book 1']),
                    'relatable'
                )
                ->state([
                    'description' => '原作小説',
                    'sort' => 2,
                ])
            )
            ->has(RelatedItem::factory()
                ->for(
                    $relatedLink = ExternalLink::factory()->create(['title' => 'Related Link 1']),
                    'relatable'
                )
                ->state([
                    'description' => 'スピンオフ',
                    'sort' => 1,
                ])
            )
            ->hasAttached(
                Character::factory()->create(['name' => 'おじいさん']),
            )
            ->hasAttached(
                Character::factory()->create(['name' => 'おばあさん']),
            )
            ->attachCover(UploadedFile::fake()->image('cover.jpg'))
            ->attachBlocks()
            ->ebookOnly()->specialEdition()->limitedEdition()->adult()
            ->create();

        $response = $this
            ->login()
            ->get(route('books.edit', $book));

        $response
            ->assertInertia(fn (Assert $page) => $page
                ->component('Books/Edit')
                ->has('book')
                ->has('book', fn (Assert $page) => $page
                    ->where('id', $book->id)
                    ->where('title', $book->title)
                    ->where('title_kana', $book->title_kana)
                    ->where('volume', $book->volume)
                    ->where('status', $book->status->value)
                    ->where('published_at', $book->published_at->toDateTimeLocalString('minutes'))
                    ->has('sites', 2)
                    ->has('sites.0', fn (Assert $page) => $page
                        ->where('name', 'サイト 1')
                        ->whereNot('logo', null)
                        ->etc())
                    ->has('sites.1', fn (Assert $page) => $page
                        ->where('name', 'サイト 2')
                        ->whereNot('logo', null)
                        ->etc())
                    ->has('cover', fn (Assert $page) => $page
                        ->where('file_name', 'cover.jpg')
                        ->etc())
                    ->has('label', fn (Assert $page) => $page
                        ->where('name', 'レーベル 1')
                        ->etc())
                    ->has('genre', fn (Assert $page) => $page
                        ->where('name', 'ジャンル 1')
                        ->etc())
                    ->has('series', fn (Assert $page) => $page
                        ->where('name', 'シリーズ 1')
                        ->etc())
                    ->has('creators', 2)
                    ->has('creators.0', fn (Assert $page) => $page
                        ->where('name', '作家 2')
                        ->where('creation.sort', 1)
                        ->where('creation.displayed_type', '翻訳')
                        ->where('creation.creation_type', $creationType2->name)
                        ->etc())
                    ->has('creators.1', fn (Assert $page) => $page
                        ->where('name', '作家 1')
                        ->where('creation.sort', 2)
                        ->where('creation.displayed_type', null)
                        ->where('creation.creation_type', $creationType1->name)
                        ->etc())
                    ->where('isbn13', $book->isbn13)
                    ->where('release_date', $book->release_date->format('Y-m-d'))
                    ->where('price', $book->price)
                    ->where('format_id', $book->format_id)
                    ->where('size_id', $book->size_id)
                    ->where('ebook_only', 1)
                    ->where('special_edition', 1)
                    ->where('limited_edition', 1)
                    ->where('adult', 1)
                    ->where('caption', $book->caption)
                    ->where('description', $book->description)
                    ->where('keywords', $book->keywords)
                    ->where('trial_url', $book->trial_url)
                    ->has('bookstores', 2)
                    ->has('bookstores.0', fn (Assert $page) => $page
                        ->where('store.name', '書店 1')
                        ->where('pivot.url', 'https://book.example.com/1')
                        ->where('pivot.is_primary', 1)
                        ->etc())
                    ->has('bookstores.1', fn (Assert $page) => $page
                        ->where('store.name', '書店 2')
                        ->where('pivot.url', null)
                        ->where('pivot.is_primary', 0)
                        ->etc())
                    ->has('ebookstores', 2)
                    ->has('ebookstores.0', fn (Assert $page) => $page
                        ->where('store.name', '電子書店 1')
                        ->where('pivot.url', 'https://ebook.example.com/1')
                        ->where('pivot.is_primary', 1)
                        ->etc())
                    ->has('ebookstores.1', fn (Assert $page) => $page
                        ->where('store.name', '電子書店 2')
                        ->where('pivot.url', null)
                        ->where('pivot.is_primary', 0)
                        ->etc())
                    ->has('related_items', 2)
                    ->has('related_items.0', fn (Assert $page) => $page
                        ->where('sort', 1)
                        ->where('description', 'スピンオフ')
                        ->where('relatable_type', 'externalLink')
                        ->where('relatable.title', $relatedLink->title)
                        ->etc())
                    ->has('related_items.1', fn (Assert $page) => $page
                        ->where('sort', 2)
                        ->where('description', '原作小説')
                        ->where('relatable_type', 'book')
                        ->where('relatable.title', $relatedBook->title)
                        ->etc())
                    ->has('benefits', 2)
                    ->has('benefits.0', fn (Assert $page) => $page
                        ->where('name', 'クリアファイル')
                        ->where('paid', 1)
                        ->etc())
                    ->has('benefits.1', fn (Assert $page) => $page
                        ->where('name', 'サイン入りイラスト')
                        ->where('paid', 0)
                        ->etc())
                    ->has('stories', 2)
                    ->has('stories.0', fn (Assert $page) => $page
                        ->where('title', '桃太郎')
                        ->where('trial_url', 'https://example.com')
                        ->etc())
                    ->has('stories.1', fn (Assert $page) => $page
                        ->where('title', '浦島太郎')
                        ->where('trial_url', null)
                        ->etc())
                    ->has('characters', 2)
                    ->has('characters.0', fn (Assert $page) => $page
                        ->where('name', 'おじいさん')
                        ->etc())
                    ->has('characters.1', fn (Assert $page) => $page
                        ->where('name', 'おばあさん')
                        ->etc())
                    ->has('blocks', 9)
                    ->has('blocks.0', fn (Assert $page) => $page
                        ->where('type_id', BlockType::Common->value)
                        ->where('sort', 1)
                        ->has('displayed')
                        ->etc())
                    ->has('blocks.1', fn (Assert $page) => $page
                        ->where('type_id', BlockType::BookStore->value)
                        ->where('sort', 2)
                        ->has('displayed')
                        ->etc())
                    ->has('blocks.2', fn (Assert $page) => $page
                        ->where('type_id', BlockType::EbookStore->value)
                        ->where('sort', 3)
                        ->has('displayed')
                        ->etc())
                    ->has('blocks.3', fn (Assert $page) => $page
                        ->where('type_id', BlockType::Benefit->value)
                        ->where('sort', 4)
                        ->has('displayed')
                        ->etc())
                    ->has('blocks.4', fn (Assert $page) => $page
                        ->where('type_id', BlockType::Series->value)
                        ->where('sort', 5)
                        ->has('displayed')
                        ->etc())
                    ->has('blocks.5', fn (Assert $page) => $page
                        ->where('type_id', BlockType::Related->value)
                        ->where('sort', 6)
                        ->has('displayed')
                        ->etc())
                    ->has('blocks.6', fn (Assert $page) => $page
                        ->where('type_id', BlockType::Story->value)
                        ->where('sort', 7)
                        ->has('displayed')
                        ->etc())
                    ->has('blocks.7', fn (Assert $page) => $page
                        ->where('type_id', BlockType::Character->value)
                        ->where('sort', 8)
                        ->has('displayed')
                        ->etc())
                    ->has('blocks.8', fn (Assert $page) => $page
                        ->where('type_id', BlockType::Custom->value)
                        ->whereType('custom_title', 'string')
                        ->whereType('custom_content', 'string')
                        ->where('sort', 9)
                        ->has('displayed')
                        ->etc())
                    ->etc()));
    }
}
