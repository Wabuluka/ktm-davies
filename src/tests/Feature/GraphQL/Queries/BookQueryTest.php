<?php

namespace Tests\Feature\GraphQL\Queries;

use App\Models\Book;
use App\Models\Site;
use Illuminate\Testing\Fluent\AssertableJson;
use Nuwave\Lighthouse\Testing\RefreshesSchemaCache;
use Tests\TestCase;

class BookQueryTest extends TestCase
{
    use RefreshesSchemaCache;

    private string $findBookQuery = /** @lang GraphQL */ '
        query findBook(
            $id: ID!
            $siteId: ID!,
            $adult: AdultScopeType,
        ) {
            book(
                id: $id
                scope: { siteId: $siteId, adult: $adult }
            ) {
                id
                title
            }
        }';

    /** @test */
    public function 未ログイン状態であればエラーが発生すること(): void
    {
        $this
            ->graphQL($this->findBookQuery, [
                'id' => 1,
                'siteId' => 1,
            ])
            ->assertGraphQLErrorMessage('Unauthenticated.');
    }

    /** @test */
    public function ログイン状態であればリクエストが成功すること(): void
    {
        $this->login();

        $this
            ->graphQL($this->findBookQuery, [
                'id' => 1,
                'siteId' => 1,
            ])
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

        $anotherSiteBook = $bookFactory
            ->hasAttached($anotherSite)
            ->create();

        $noSitesBook = $bookFactory
            ->create();

        $this
            ->graphQL($this->findBookQuery, [
                'id' => $siteBook->id,
                'siteId' => $site->id,
            ])
            ->assertGraphQLErrorFree()
            ->assertJson(fn (AssertableJson $json) => $json
                ->where('data.book', [
                    'id' => (string) $siteBook->id,
                    'title' => $siteBook->title,
                ])
            );

        $this
            ->graphQL($this->findBookQuery, [
                'id' => $anotherSiteBook->id,
                'siteId' => $site->id,
            ])
            ->assertGraphQLErrorFree()
            ->assertJsonPath('data.book', null);

        $this
            ->graphQL($this->findBookQuery, [
                'id' => $noSitesBook->id,
                'siteId' => $site->id,
            ])
            ->assertGraphQLErrorFree()
            ->assertJsonPath('data.book', null);
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
            ->where('data.book', [
                'id' => (string) $allAgeBook->id,
                'title' => $allAgeBook->title,
            ]);

        $assertSeeAdultBook = fn (AssertableJson $json) => $json
            ->where('data.book', [
                'id' => (string) $adultBook->id,
                'title' => $adultBook->title,
            ]);

        $this
            ->graphQL($this->findBookQuery, [
                'id' => $allAgeBook->id,
                'siteId' => $site->id,
            ])
            ->assertGraphQLErrorFree()
            ->assertJson($assertSeeAllAgeBook);

        $this
            ->graphQL($this->findBookQuery, [
                'id' => $allAgeBook->id,
                'siteId' => $site->id,
                'adult' => 'EXCLUDE',
            ])
            ->assertGraphQLErrorFree()
            ->assertJson($assertSeeAllAgeBook);

        $this
            ->graphQL($this->findBookQuery, [
                'id' => $adultBook->id,
                'siteId' => $site->id,
                'adult' => 'EXCLUDE',
            ])
            ->assertGraphQLErrorFree()
            ->assertJsonPath('data.book', null);

        $this
            ->graphQL($this->findBookQuery, [
                'id' => $allAgeBook->id,
                'siteId' => $site->id,
                'adult' => 'INCLUDE',
            ])
            ->assertGraphQLErrorFree()
            ->assertJson($assertSeeAllAgeBook);

        $this
            ->graphQL($this->findBookQuery, [
                'id' => $adultBook->id,
                'siteId' => $site->id,
                'adult' => 'INCLUDE',
            ])
            ->assertGraphQLErrorFree()
            ->assertJson($assertSeeAdultBook);

        $this
            ->graphQL($this->findBookQuery, [
                'id' => $allAgeBook->id,
                'siteId' => $site->id,
                'adult' => 'ONLY',
            ])
            ->assertGraphQLErrorFree()
            ->assertJsonPath('data.book', null);

        $this
            ->graphQL($this->findBookQuery, [
                'id' => $adultBook->id,
                'siteId' => $site->id,
                'adult' => 'ONLY',
            ])
            ->assertGraphQLErrorFree()
            ->assertJson($assertSeeAdultBook);
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

        $draftBook = $bookFactory
            ->draft()
            ->create();

        $this
            ->graphQL($this->findBookQuery, [
                'id' => $publishedBook->id,
                'siteId' => $site->id,
            ])
            ->assertGraphQLErrorFree()
            ->assertJson(fn (AssertableJson $json) => $json
                ->where('data.book', [
                    'id' => (string) $publishedBook->id,
                    'title' => $publishedBook->title,
                ])
            );

        $this
            ->graphQL($this->findBookQuery, [
                'id' => $draftBook->id,
                'siteId' => $site->id,
            ])
            ->assertGraphQLErrorFree()
            ->assertJsonPath('data.book', null);
    }
}
