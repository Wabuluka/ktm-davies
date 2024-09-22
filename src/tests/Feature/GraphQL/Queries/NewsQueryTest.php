<?php

namespace Tests\Feature\GraphQL\Queries;

use App\Models\News;
use App\Models\NewsCategory;
use Illuminate\Testing\Fluent\AssertableJson;
use Nuwave\Lighthouse\Testing\RefreshesSchemaCache;
use Tests\TestCase;

class NewsQueryTest extends TestCase
{
    use RefreshesSchemaCache;

    private string $findNewsQuery = /** @lang GraphQL */ '
        query findNews(
            $slug: String!
            $siteId: ID!,
        ) {
            news(
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
    public function siteIdに合致するNewsのみ返却すること(): void
    {
        $category = NewsCategory::factory()
            ->create();
        $anotherSiteCategory = NewsCategory::factory()
            ->create();
        $news = News::factory()
            ->published()
            ->for($category, 'category')
            ->create();
        $anotherSiteNews = News::factory()
            ->published()
            ->for($anotherSiteCategory, 'category')
            ->create();
        $noSitesNews = News::factory()
            ->published()
            ->create();

        $this->login();
        $this
            ->graphQL($this->findNewsQuery, [
                'slug' => $news->slug,
                'siteId' => $category->site_id,
            ])
            ->assertGraphQLErrorFree()
            ->assertJson(fn (AssertableJson $json) => $json
                ->where('data.news', [
                    'id' => (string) $news->id,
                    'title' => $news->title,
                ])
            );
        $this
            ->graphQL($this->findNewsQuery, [
                'slug' => $anotherSiteNews->slug,
                'siteId' => $category->site_id,
            ])
            ->assertGraphQLErrorFree()
            ->assertJsonPath('data.news', null);
        $this
            ->graphQL($this->findNewsQuery, [
                'slug' => $noSitesNews->slug,
                'siteId' => $category->site_id,
            ])
            ->assertGraphQLErrorFree()
            ->assertJsonPath('data.news', null);
    }

    /** @test */
    public function 公開中のNewsのみ返却すること(): void
    {
        $category = NewsCategory::factory()->create();
        $publishedNews = News::factory()
            ->for($category, 'category')
            ->published()
            ->create();
        $draftNews = News::factory()
            ->for($category, 'category')
            ->draft()
            ->create();

        $this->login();
        $this
            ->graphQL($this->findNewsQuery, [
                'slug' => $publishedNews->slug,
                'siteId' => $category->site_id,
            ])
            ->assertGraphQLErrorFree()
            ->assertJson(fn (AssertableJson $json) => $json
                ->where('data.news', [
                    'id' => (string) $publishedNews->id,
                    'title' => $publishedNews->title,
                ])
            );
        $this
            ->graphQL($this->findNewsQuery, [
                'slug' => $draftNews->slug,
                'siteId' => $category->site_id,
            ])
            ->assertGraphQLErrorFree()
            ->assertJsonPath('data.news', null);
    }
}
