<?php

namespace Tests\Feature\GraphQL\Types;

use App\Models\Book;
use App\Models\Label;
use App\Models\Site;
use Illuminate\Testing\Fluent\AssertableJson;
use Nuwave\Lighthouse\Schema\Source\SchemaSourceProvider;
use Nuwave\Lighthouse\Testing\UsesTestSchema;
use Tests\TestCase;

class LabelTypeTest extends TestCase
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
            testLabel(id: ID! @eq): Label @find
            testLabels: [Label!]! @all
        }
        ';

        $this->schema = $testSchema;

        $this->setUpTestSchema();
    }

    /** @test */
    public function types_レーベルの種別を返却すること(): void
    {
        Label::factory()->paperback()->create();
        Label::factory()->magazine()->goods()->create();

        $query = /** @lang GraphQL */ '
        query findAllLabels {
            testLabels {
                types
            }
        }
        ';

        $this
            ->graphQL($query)
            ->assertGraphQLErrorFree()
            ->assertJson(fn (AssertableJson $json) => $json
                ->has('data.testLabels', 2)
                ->where('data.testLabels.0.types', ['Paperback'])
                ->where('data.testLabels.1.types', ['Magazine', 'Goods'])
            );
    }

    /** @test */
    public function books_サイト・成人向けフラグの指定に合致する、公開中の書籍のみを返却すること(): void
    {
        [$site, $anotherSite] = Site::factory(2)->create();
        $label = Label::factory()->create();
        $bookFactory = Book::factory()->for($label);

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

        $findLabelQuery = /** @lang GraphQL */ '
        query findBook($id: ID!, $siteId: ID!, $adult: AdultScopeType) {
            testLabel(id: $id) {
                books(scope: { siteId: $siteId, adult: $adult }) {
                    id
                    title
                }
            }
        }
        ';

        $assertSeeBothBooks = fn (AssertableJson $json) => $json
            ->has('data.testLabel.books', 2)
            ->has('data.testLabel.books.0', fn (AssertableJson $json) => $json
                ->where('id', (string) $allAgePublishedBook->id)
                ->where('title', $allAgePublishedBook->title)
            )
            ->has('data.testLabel.books.1', fn (AssertableJson $json) => $json
                ->where('id', (string) $adultPublishedBook->id)
                ->where('title', $adultPublishedBook->title)
            );

        $assertSeeAllAgeBook = fn (AssertableJson $json) => $json
            ->has('data.testLabel.books', 1)
            ->has('data.testLabel.books.0', fn (AssertableJson $json) => $json
                ->where('id', (string) $allAgePublishedBook->id)
                ->where('title', $allAgePublishedBook->title)
            );

        $assertSeeAdultBook = fn (AssertableJson $json) => $json
            ->has('data.testLabel.books', 1)
            ->has('data.testLabel.books.0', fn (AssertableJson $json) => $json
                ->where('id', (string) $adultPublishedBook->id)
                ->where('title', $adultPublishedBook->title)
            );

        $this
            ->graphQL($findLabelQuery, [
                'id' => $label->id,
                'siteId' => $site->id,
                'adult' => 'INCLUDE',
            ])
            ->assertGraphQLErrorFree()
            ->assertJson($assertSeeBothBooks);

        $this
            ->graphQL($findLabelQuery, [
                'id' => $label->id,
                'siteId' => $site->id,
                // 指定がない場合は成人向け書籍を含めない
            ])
            ->assertGraphQLErrorFree()
            ->assertJson($assertSeeAllAgeBook);

        $this
            ->graphQL($findLabelQuery, [
                'id' => $label->id,
                'siteId' => $site->id,
                'adult' => 'EXCLUDE',
            ])
            ->assertGraphQLErrorFree()
            ->assertJson($assertSeeAllAgeBook);

        $this
            ->graphQL($findLabelQuery, [
                'id' => $label->id,
                'siteId' => $site->id,
                'adult' => 'ONLY',
            ])
            ->assertGraphQLErrorFree()
            ->assertJson($assertSeeAdultBook);
    }
}
