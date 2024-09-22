<?php

namespace Tests\Feature\GraphQL\Types;

use App\Models\Book;
use App\Models\Series;
use App\Models\Site;
use Illuminate\Testing\Fluent\AssertableJson;
use Nuwave\Lighthouse\Schema\Source\SchemaSourceProvider;
use Nuwave\Lighthouse\Testing\UsesTestSchema;
use Tests\TestCase;

class SeriesTypeTest extends TestCase
{
    use UsesTestSchema;

    protected function setUp(): void
    {
        parent::setUp();

        $this->artisan('lighthouse:clear-cache');

        $originalSchema = $this->app
            ->make(SchemaSourceProvider::class)
            ->getSchemaString();

        $testSchema = $originalSchema . /** @lang GraphQL */ '
        extend type Query {
            testSeries(id: ID! @eq): Series @find
        }
        ';

        $this->schema = $testSchema;

        $this->setUpTestSchema();
    }

    /** @test */
    public function books_サイト・成人向けフラグの指定に合致する、公開中の書籍のみを返却すること(): void
    {
        [$site, $anotherSite] = Site::factory(2)->create();
        $series = Series::factory()->create();
        $bookFactory = Book::factory()->for($series);

        $allAgePublishedBook = $bookFactory
            ->hasAttached($site)
            ->adult(false)
            ->published()
            ->create();

        $adultPublishedBook = $bookFactory
            ->hasAttached($site)
            ->adult(true)
            ->published()
            ->create();

        $_allAgeDraftBook = $bookFactory
            ->hasAttached($site)
            ->adult(false)
            ->draft()
            ->create();

        $_anotherSiteAllAgePublishedBook = $bookFactory
            ->hasAttached($anotherSite)
            ->adult(false)
            ->published()
            ->create();

        $findSeriesQuery = /** @lang GraphQL */ '
        query findBook($id: ID!, $siteId: ID!, $adult: AdultScopeType) {
            testSeries(id: $id) {
                books(scope: { siteId: $siteId, adult: $adult }) {
                    id
                    title
                }
            }
        }
        ';

        $assertSeeBothBooks = fn (AssertableJson $json) => $json
            ->has('data.testSeries.books', 2)
            ->has('data.testSeries.books.0', fn (AssertableJson $json) => $json
                ->where('id', (string) $allAgePublishedBook->id)
                ->where('title', $allAgePublishedBook->title)
            )
            ->has('data.testSeries.books.1', fn (AssertableJson $json) => $json
                ->where('id', (string) $adultPublishedBook->id)
                ->where('title', $adultPublishedBook->title)
            );

        $assertSeeAllAgeBook = fn (AssertableJson $json) => $json
            ->has('data.testSeries.books', 1)
            ->has('data.testSeries.books.0', fn (AssertableJson $json) => $json
                ->where('id', (string) $allAgePublishedBook->id)
                ->where('title', $allAgePublishedBook->title)
            );

        $assertSeeAdultBook = fn (AssertableJson $json) => $json
            ->has('data.testSeries.books', 1)
            ->has('data.testSeries.books.0', fn (AssertableJson $json) => $json
                ->where('id', (string) $adultPublishedBook->id)
                ->where('title', $adultPublishedBook->title)
            );

        $this
            ->graphQL($findSeriesQuery, [
                'id' => $series->id,
                'siteId' => $site->id,
                'adult' => 'INCLUDE',
            ])
            ->assertGraphQLErrorFree()
            ->assertJson($assertSeeBothBooks);

        $this
            ->graphQL($findSeriesQuery, [
                'id' => $series->id,
                'siteId' => $site->id,
                // 指定がない場合は成人向け書籍を含めない
            ])
            ->assertGraphQLErrorFree()
            ->assertJson($assertSeeAllAgeBook);

        $this
            ->graphQL($findSeriesQuery, [
                'id' => $series->id,
                'siteId' => $site->id,
                'adult' => 'EXCLUDE',
            ])
            ->assertGraphQLErrorFree()
            ->assertJson($assertSeeAllAgeBook);

        $this
            ->graphQL($findSeriesQuery, [
                'id' => $series->id,
                'siteId' => $site->id,
                'adult' => 'ONLY',
            ])
            ->assertGraphQLErrorFree()
            ->assertJson($assertSeeAdultBook);
    }
}
