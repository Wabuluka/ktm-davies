<?php

namespace Tests\Feature\GraphQL\Queries;

use App\Models\Page;
use App\Models\Site;
use Illuminate\Testing\Fluent\AssertableJson;
use Nuwave\Lighthouse\Testing\RefreshesSchemaCache;
use Tests\TestCase;

class PageQueryTest extends TestCase
{
    use RefreshesSchemaCache;

    private string $findNewsQuery = /** @lang GraphQL */ '
        query findNews(
            $slug: String!
            $siteId: ID!,
        ) {
            page(
                slug: $slug
                scope: { siteId: $siteId }
            ) {
                id
                title
            }
        }';

    /** @test */
    public function 未ログイン状態であればエラーが発生すること(): void
    {
        $this
            ->graphQL($this->findNewsQuery, [
                'slug' => 'slug',
                'siteId' => 1,
            ])
            ->assertGraphQLErrorMessage('Unauthenticated.');
    }

    /** @test */
    public function ログイン状態であればリクエストが成功すること(): void
    {
        $this->login();

        $this
            ->graphQL($this->findNewsQuery, [
                'slug' => 'slug',
                'siteId' => 1,
            ])
            ->assertGraphQLErrorFree()
            ->assertJson(fn (AssertableJson $json) => $json->has('data'));
    }

    /** @test */
    public function siteIdに合致するPageのみ返却すること(): void
    {
        [$site, $anotherSite] = Site::factory(2)->create();
        $page = Page::factory()->for($site)->create();
        $anotherSitePage = Page::factory()->for($anotherSite)->create();

        $this->login();
        $this
            ->graphQL($this->findNewsQuery, [
                'slug' => $page->slug,
                'siteId' => $site->id,
            ])
            ->assertGraphQLErrorFree()
            ->assertJson(fn (AssertableJson $json) => $json
                ->where('data.page', [
                    'id' => (string) $page->id,
                    'title' => $page->title,
                ])
            );
        $this
            ->graphQL($this->findNewsQuery, [
                'slug' => $anotherSitePage->slug,
                'siteId' => $site->id,
            ])
            ->assertGraphQLErrorFree()
            ->assertJsonPath('data.page', null);
    }
}
