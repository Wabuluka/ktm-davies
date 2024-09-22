<?php

namespace Tests\Feature\GraphQL\Queries;

use App\Models\Book;
use App\Models\BookFormat;
use App\Models\CreationType;
use App\Models\Creator;
use App\Models\Site;
use Illuminate\Testing\Fluent\AssertableJson;
use Nuwave\Lighthouse\Testing\RefreshesSchemaCache;
use Tests\TestCase;

class BooksQueryTest extends TestCase
{
    use RefreshesSchemaCache;

    private string $getBooksQuery = /** @lang GraphQL */ '
        query getBooks(
            $siteId: ID!,
            $adult: AdultScopeType,
            $keyword: String,
            $formatIds: [ID!],
            $orderBy: [QueryBooksOrderByOrderByClause!]
        ) {
            books(
                scope: { siteId: $siteId, adult: $adult }
                filter: { keyword: $keyword, formatIds: $formatIds }
                orderBy: $orderBy
            ) {
                data {
                    id
                    title
                }
            }
        }';

    /** @test */
    public function 未ログイン状態であればエラーが発生すること(): void
    {
        $this
            ->graphQL($this->getBooksQuery, ['siteId' => 1])
            ->assertGraphQLErrorMessage('Unauthenticated.');
    }

    /** @test */
    public function ログイン状態であればリクエストが成功すること(): void
    {
        $this->login();

        $this
            ->graphQL($this->getBooksQuery, ['siteId' => 1])
            ->assertGraphQLErrorFree()
            ->assertJson(fn (AssertableJson $json) => $json->has('data'));
    }

    /** @test */
    public function siteIdに合致するBookのみ返却すること(): void
    {
        $this->login();

        [$site, $anotherSite] = Site::factory(2)->create();

        $bookFactory = Book::factory()
            ->published()
            ->adult(false);

        $siteBook = $bookFactory
            ->hasAttached($site)
            ->create();

        $_anotherSiteBook = $bookFactory
            ->hasAttached($anotherSite)
            ->create();

        $_noSitesBook = $bookFactory
            ->create();

        $this
            ->graphQL($this->getBooksQuery, ['siteId' => $site->id])
            ->assertGraphQLErrorFree()
            ->assertJson(fn (AssertableJson $json) => $json
                ->count('data.books.data', 1)
                ->where('data.books.data.0', [
                    'id' => (string) $siteBook->id,
                    'title' => $siteBook->title,
                ])
            );
    }

    /** @test */
    public function 指定した成人向けフラグの状態に合致する書籍のみ返却すること(): void
    {
        $this->login();

        $site = Site::factory()->create();

        $bookFactory = Book::factory()
            ->hasAttached($site)
            ->published();

        $allAgeBook = $bookFactory
            ->adult(false)
            ->create();

        $adultBook = $bookFactory
            ->adult(true)
            ->create();

        $assertSeeAllAgeBook = fn (AssertableJson $json) => $json
            ->count('data.books.data', 1)
            ->where('data.books.data.0', [
                'id' => (string) $allAgeBook->id,
                'title' => $allAgeBook->title,
            ]);

        $assertSeeAdultBook = fn (AssertableJson $json) => $json
            ->count('data.books.data', 1)
            ->where('data.books.data.0', [
                'id' => (string) $adultBook->id,
                'title' => $adultBook->title,
            ]);

        $this
            ->graphQL($this->getBooksQuery, [
                'siteId' => $site->id,
                'adult' => 'INCLUDE',
            ])
            ->assertGraphQLErrorFree()
            ->assertJsonCount(2, 'data.books.data');

        $this
            ->graphQL($this->getBooksQuery, [
                'siteId' => $site->id,
                // 指定がない場合は成人向け書籍を含めない
            ])
            ->assertGraphQLErrorFree()
            ->assertJson($assertSeeAllAgeBook);

        $this
            ->graphQL($this->getBooksQuery, [
                'siteId' => $site->id,
                'adult' => 'EXCLUDE',
            ])
            ->assertGraphQLErrorFree()
            ->assertJson($assertSeeAllAgeBook);

        $this
            ->graphQL($this->getBooksQuery, [
                'siteId' => $site->id,
                'adult' => 'ONLY',
            ])
            ->assertGraphQLErrorFree()
            ->assertJson($assertSeeAdultBook);
    }

    /** @test */
    public function キーワードに合致するBookのみ返却すること(): void
    {
        $this->login();

        $site = Site::factory()->create();
        $creator = Creator::factory()->create(['name' => '太宰治']);
        $creationType = CreationType::factory()->create();

        $bookFactory = Book::factory()
            ->hasAttached($site)
            ->hasAttached($creator, [
                'creation_type' => $creationType->name,
                'sort' => 1,
            ])
            ->published()
            ->adult(false);

        $book1 = $bookFactory->create([
            'title' => '走れメロス',
        ]);
        $_book2 = $bookFactory->create([
            'title' => '人間失格',
        ]);
        $_book3 = $bookFactory->create([
            'title' => '斜陽',
        ]);

        $this
            ->graphQL($this->getBooksQuery, [
                'siteId' => $site->id,
                'keyword' => 'メロス',
            ])
            ->assertGraphQLErrorFree()
            ->assertJson(fn (AssertableJson $json) => $json
                ->count('data.books.data', 1)
                ->where('data.books.data.0', [
                    'id' => (string) $book1->id,
                    'title' => $book1->title,
                ])
            );

        $this
            ->graphQL($this->getBooksQuery, [
                'siteId' => $site->id,
                'keyword' => '太宰治',
            ])
            ->assertGraphQLErrorFree()
            ->assertJsonCount(3, 'data.books.data');
    }

    /** @test */
    public function キーワードは255文字まで入力可能なこと(): void
    {
        $this->login();

        $site = Site::factory()->create();

        Book::factory()
            ->hasAttached($site)
            ->published()
            ->adult(false)
            ->create(['title' => str_repeat('あ', 255)]);

        $this
            ->graphQL($this->getBooksQuery, [
                'siteId' => $site->id,
                'keyword' => str_repeat('あ', 255),
            ])
            ->assertGraphQLErrorFree()
            ->assertJsonCount(1, 'data.books.data');

        $this
            ->graphQL($this->getBooksQuery, [
                'siteId' => $site->id,
                'keyword' => str_repeat('あ', 256),
            ])
            ->assertGraphQLErrorMessage('Validation failed for the field [books].');
    }

    /** @test */
    public function 配信形態に合致するBookのみ返却すること(): void
    {
        $this->login();

        $site = Site::factory()->create();
        [$bookFormat, $anothorBookFormat] = BookFormat::take(2)->get();

        $bookFactory = Book::factory()
            ->hasAttached($site)
            ->published()
            ->adult(false);

        $book1 = $bookFactory
            ->for($bookFormat, 'format')
            ->create();

        $_book2 = $bookFactory
            ->for($anothorBookFormat, 'format')
            ->create();

        $this
            ->graphQL($this->getBooksQuery, [
                'siteId' => $site->id,
                'formatIds' => $bookFormat->id,
            ])
            ->assertGraphQLErrorFree()
            ->assertJson(fn (AssertableJson $json) => $json
                ->count('data.books.data', 1)
                ->where('data.books.data.0', [
                    'id' => (string) $book1->id,
                    'title' => $book1->title,
                ])
            );

        $this
            ->graphQL($this->getBooksQuery, [
                'siteId' => $site->id,
                'formatIds' => [$bookFormat->id, $anothorBookFormat->id],
            ])
            ->assertJsonCount(2, 'data.books.data');
    }

    /** @test */
    public function 指定した並び順でBookを返却すること(): void
    {
        $this->login();

        $site = Site::factory()->create();

        $bookFactory = Book::factory()
            ->hasAttached($site)
            ->published()
            ->adult(false);

        $book1 = $bookFactory->create(['release_date' => now()]);
        $book2 = $bookFactory->create(['release_date' => now()->addDays(1)]);

        $this
            ->graphQL($this->getBooksQuery, [
                'siteId' => $site->id,
                'orderBy' => [
                    [
                        'column' => 'RELEASE_DATE',
                        'order' => 'ASC',
                    ],
                ],

            ])
            ->assertGraphQLErrorFree()
            ->assertJson(fn (AssertableJson $json) => $json
                ->whereAll([
                    'data.books.data.0.id' => (string) $book1->id,
                    'data.books.data.1.id' => (string) $book2->id,
                ])
            );

        $this
            ->graphQL($this->getBooksQuery, [
                'siteId' => $site->id,
                'orderBy' => [
                    [
                        'column' => 'RELEASE_DATE',
                        'order' => 'DESC',
                    ],
                ],
            ])
            ->assertGraphQLErrorFree()
            ->assertJson(fn (AssertableJson $json) => $json
                ->whereAll([
                    'data.books.data.0.id' => (string) $book2->id,
                    'data.books.data.1.id' => (string) $book1->id,
                ])
            );
    }

    /** @test */
    public function 公開中のBookのみ返却すること(): void
    {
        $this->login();

        $site = Site::factory()->create();

        $bookFactory = Book::factory()
            ->hasAttached($site)
            ->adult(false);

        $publishedBook = $bookFactory
            ->published()
            ->create();

        $_draftBook = $bookFactory
            ->draft()
            ->create();

        $this
            ->graphQL($this->getBooksQuery, ['siteId' => $site->id])
            ->assertGraphQLErrorFree()
            ->assertJson(fn (AssertableJson $json) => $json
                ->count('data.books.data', 1)
                ->where('data.books.data.0', [
                    'id' => (string) $publishedBook->id,
                    'title' => $publishedBook->title,
                ])
            );
    }
}
