<?php

namespace Tests\Feature\GraphQL\Types;

use App\Models\Book;
use App\Models\CreationType;
use App\Models\Creator;
use App\Models\Site;
use Illuminate\Testing\Fluent\AssertableJson;
use Nuwave\Lighthouse\Schema\Source\SchemaSourceProvider;
use Nuwave\Lighthouse\Testing\UsesTestSchema;
use Tests\TestCase;

class CreationTypeTest extends TestCase
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
            testCreator(id: ID! @eq): Creator @find
        }
        ';

        $this->schema = $testSchema;

        $this->setUpTestSchema();
    }

    /** @test */
    public function books_サイト・成人向けフラグの指定に合致する、公開中の書籍のみを返却すること(): void
    {
        [$site, $anotherSite] = Site::factory(2)->create();
        $creator = Creator::factory()->create(['name' => '太宰治']);
        $creationType = CreationType::factory()->create(['name' => '原作']);

        $allAgePublishedBook = Book::factory()
            ->hasAttached($creator, [
                'creation_type' => $creationType->name,
                'displayed_type' => '小説',
                'sort' => 1,
            ])
            ->hasAttached($site)
            ->adult(false)
            ->published()
            ->create();

        $adultPublishedBook = Book::factory()
            ->hasAttached($creator, [
                'creation_type' => $creationType->name,
                'sort' => 1,
            ])
            ->hasAttached($site)
            ->adult(true)
            ->published()
            ->create();

        $_allAgeDraftBook = Book::factory()
            ->hasAttached($creator, [
                'creation_type' => $creationType->name,
                'sort' => 1,
            ])
            ->hasAttached($site)
            ->adult(false)
            ->draft()
            ->create();

        $_anotherSiteAllAgePublishedBook = Book::factory()
            ->hasAttached($creator, [
                'creation_type' => $creationType->name,
                'sort' => 1,
            ])
            ->hasAttached($anotherSite)
            ->adult(false)
            ->published()
            ->create();

        $findCreatorQuery = /** @lang GraphQL */ '
        query findBook($id: ID!, $siteId: ID!, $adult: AdultScopeType) {
            testCreator(id: $id) {
                books(scope: { siteId: $siteId, adult: $adult }) {
                    id
                    title
                    creation {
                        type
                    }
                }
            }
        }
        ';

        $assertSeeBothBooks = fn (AssertableJson $json) => $json
            ->has('data.testCreator.books', 2)
            ->has('data.testCreator.books.0', fn (AssertableJson $json) => $json
                ->where('id', (string) $allAgePublishedBook->id)
                ->where('title', $allAgePublishedBook->title)
                ->where('creation.type', '小説')
            )
            ->has('data.testCreator.books.1', fn (AssertableJson $json) => $json
                ->where('id', (string) $adultPublishedBook->id)
                ->where('title', $adultPublishedBook->title)
                ->where('creation.type', '原作')
            );

        $assertSeeAllAgeBook = fn (AssertableJson $json) => $json
            ->has('data.testCreator.books', 1)
            ->has('data.testCreator.books.0', fn (AssertableJson $json) => $json
                ->where('id', (string) $allAgePublishedBook->id)
                ->where('title', $allAgePublishedBook->title)
                ->where('creation.type', '小説')
            );

        $assertSeeAdultBook = fn (AssertableJson $json) => $json
            ->has('data.testCreator.books', 1)
            ->has('data.testCreator.books.0', fn (AssertableJson $json) => $json
                ->where('id', (string) $adultPublishedBook->id)
                ->where('title', $adultPublishedBook->title)
                ->where('creation.type', '原作')
            );

        $this
            ->graphQL($findCreatorQuery, [
                'id' => $creator->id,
                'siteId' => $site->id,
                'adult' => 'INCLUDE',
            ])
            ->assertGraphQLErrorFree()
            ->assertJson($assertSeeBothBooks);

        $this
            ->graphQL($findCreatorQuery, [
                'id' => $creator->id,
                'siteId' => $site->id,
            ])
            ->assertGraphQLErrorFree()
            ->assertJson($assertSeeAllAgeBook);

        $this
            ->graphQL($findCreatorQuery, [
                'id' => $creator->id,
                'siteId' => $site->id,
                'adult' => 'EXCLUDE',
            ])
            ->assertGraphQLErrorFree()
            ->assertJson($assertSeeAllAgeBook);

        $this
            ->graphQL($findCreatorQuery, [
                'id' => $creator->id,
                'siteId' => $site->id,
                'adult' => 'ONLY',
            ])
            ->assertGraphQLErrorFree()
            ->assertJson($assertSeeAdultBook);
    }
}
