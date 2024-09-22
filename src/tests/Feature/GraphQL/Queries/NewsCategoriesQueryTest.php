<?php

namespace Tests\Feature\GraphQL\Queries;

use App\Models\NewsCategory;
use App\Models\Site;
use Illuminate\Testing\Fluent\AssertableJson;
use Nuwave\Lighthouse\Testing\RefreshesSchemaCache;
use Tests\TestCase;

class NewsCategoriesQueryTest extends TestCase
{
    use RefreshesSchemaCache;

    private string $getNewsCategoriesQuery = /** @lang GraphQL */ '
        query getNewsCategories(
            $siteId: ID!,
        ) {
            newsCategories(
                scope: { siteId: $siteId }
            ) {
                id
                name
            }
        }';

    /** @test */
    public function 未ログイン状態であればエラーが発生すること(): void
    {
        $this
            ->graphQL($this->getNewsCategoriesQuery, ['siteId' => 1])
            ->assertGraphQLErrorMessage('Unauthenticated.');
    }

    /** @test */
    public function ログイン状態であればリクエストが成功すること(): void
    {
        $this->login();

        $this
            ->graphQL($this->getNewsCategoriesQuery, ['siteId' => 1])
            ->assertGraphQLErrorFree()
            ->assertJson(fn (AssertableJson $json) => $json->has('data'));
    }

    /** @test */
    public function siteIdに合致するNewsCategoryのみ返却すること(): void
    {
        [$site, $anotherSite] = Site::factory(2)->create();
        $category = NewsCategory::factory()
            ->for($site)
            ->create();
        $_anotherSiteCategory = NewsCategory::factory()
            ->for($anotherSite)
            ->create();

        $this->login();
        $this
            ->graphQL($this->getNewsCategoriesQuery, ['siteId' => $category->site_id])
            ->assertGraphQLErrorFree()
            ->assertJson(fn (AssertableJson $json) => $json
                ->count('data.newsCategories', 1)
                ->where('data.newsCategories.0', [
                    'id' => (string) $category->id,
                    'name' => $category->name,
                ])
            );
    }
}
