<?php

namespace Tests\Feature\GraphQL\Queries;

use App\Models\Genre;
use App\Models\Label;
use Illuminate\Testing\Fluent\AssertableJson;
use Nuwave\Lighthouse\Testing\RefreshesSchemaCache;
use Tests\TestCase;

class LabelsQueryTest extends TestCase
{
    use RefreshesSchemaCache;

    private string $findLabelsQuery = /** @lang GraphQL */ '
        query findLabels(
            $genreIds: [ID!],
            $hasSite: Boolean,
        ) {
            labels(
                filter: { genreIds: $genreIds, hasSite: $hasSite }
            ) {
                id
                name
                types
            }
        }';

    /** @test */
    public function 未ログイン状態であればエラーが発生すること(): void
    {
        $this
            ->graphQL($this->findLabelsQuery)
            ->assertGraphQLErrorMessage('Unauthenticated.');
    }

    /** @test */
    public function ログイン状態であればリクエストが成功すること(): void
    {
        $this->login();

        $this
            ->graphQL($this->findLabelsQuery)
            ->assertGraphQLErrorFree()
            ->assertJson(fn (AssertableJson $json) => $json->has('data'));
    }

    /** @test */
    public function genreIdsに合致するBannerのみ返却すること(): void
    {
        [$genre1, $genre2, $genre3] = Genre::factory(3)->create();
        $label1 = Label::factory()->for($genre1)->create();
        $label2 = Label::factory()->for($genre2)->create();
        $_label3 = Label::factory()->for($genre3)->create();

        $this->login();
        $this
            ->graphQL($this->findLabelsQuery, ['genreIds' => [$genre1->id, $genre2->id]])
            ->assertGraphQLErrorFree()
            ->assertJson(fn (AssertableJson $json) => $json
                ->count('data.labels', 2)
                ->where('data.labels.0', [
                    'id' => (string) $label1->id,
                    'name' => $label1->name,
                    'types' => [],
                ])
                ->where('data.labels.1', [
                    'id' => (string) $label2->id,
                    'name' => $label2->name,
                    'types' => [],
                ])
            );
    }

    /** @test */
    public function 専用サイトURLがあるLabelのみ返却すること(): void
    {
        $label1 = Label::factory()->create(['url' => 'https://example.com']);
        $_label2 = Label::factory()->create(['url' => '']);
        $_label3 = Label::factory()->create(['url' => null]);

        $this->login();
        $this
            ->graphQL($this->findLabelsQuery, ['hasSite' => true])
            ->assertGraphQLErrorFree()
            ->assertJson(fn (AssertableJson $json) => $json
                ->count('data.labels', 1)
                ->where('data.labels.0', [
                    'id' => (string) $label1->id,
                    'name' => $label1->name,
                    'types' => [],
                ])
            );

        $this
            ->graphQL($this->findLabelsQuery)
            ->assertGraphQLErrorFree()
            ->assertJsonCount(3, 'data.labels');
    }
}
