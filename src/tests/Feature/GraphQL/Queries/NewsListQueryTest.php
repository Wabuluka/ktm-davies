<?php

namespace Tests\Feature\GraphQL\Queries;

use App\Models\News;
use App\Models\NewsCategory;
use Illuminate\Testing\Fluent\AssertableJson;
use Nuwave\Lighthouse\Testing\RefreshesSchemaCache;
use Tests\TestCase;

class NewsListQueryTest extends TestCase
{
    use RefreshesSchemaCache;

    private string $getNewsListQuery = /** @lang GraphQL */ '
        query getNewsList(
            $siteId: ID!,
            $year: String,
            $month: String,
            $orderBy: [QueryNewsListOrderByOrderByClause!]
        ) {
            newsList(
                scope: { siteId: $siteId }
                filter: { year: $year, month: $month }
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
            ->graphQL($this->getNewsListQuery, ['siteId' => 1])
            ->assertGraphQLErrorMessage('Unauthenticated.');
    }

    /** @test */
    public function ログイン状態であればリクエストが成功すること(): void
    {
        $this->login();

        $this
            ->graphQL($this->getNewsListQuery, ['siteId' => 1])
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
            ->for($category, 'category')
            ->published()
            ->create();
        $_anotherSiteNews = News::factory()
            ->for($anotherSiteCategory, 'category')
            ->published()
            ->create();
        $_noSitesNews = News::factory()
            ->published()
            ->create();

        $this->login();
        $this
            ->graphQL($this->getNewsListQuery, ['siteId' => $category->site_id])
            ->assertGraphQLErrorFree()
            ->assertJson(fn (AssertableJson $json) => $json
                ->count('data.newsList.data', 1)
                ->where('data.newsList.data.0', [
                    'id' => (string) $news->id,
                    'title' => $news->title,
                ])
            );
    }

    /** @test */
    public function 公開日が指定年月に合致するNewsのみ返却すること(): void
    {
        $this->travelTo('2024-12-31');
        $category = NewsCategory::factory()->create();
        $newsFactory = News::factory()
            ->for($category, 'category');
        $_news1 =
            $newsFactory->create(['is_draft' => false, 'published_at' => '2023-08-31']);
        $news2 =
            $newsFactory->create(['is_draft' => false, 'published_at' => '2024-08-01']);
        $news3 =
            $newsFactory->create(['is_draft' => false, 'published_at' => '2024-08-31']);
        $_news4 =
            $newsFactory->create(['is_draft' => false, 'published_at' => '2024-09-01']);

        $this->login();
        $this
            ->graphQL($this->getNewsListQuery, [
                'siteId' => $category->site_id,
                'year' => '2024',
                'month' => '8',
            ])
            ->assertGraphQLErrorFree()
            ->assertJson(fn (AssertableJson $json) => $json
                ->count('data.newsList.data', 2)
                ->where('data.newsList.data.0', [
                    'id' => (string) $news2->id,
                    'title' => $news2->title,
                ])
                ->where('data.newsList.data.1', [
                    'id' => (string) $news3->id,
                    'title' => $news3->title,
                ])
            );
    }

    /** @test */
    public function 指定した並び順でNewsを返却すること(): void
    {
        $category = NewsCategory::factory()->create();
        $newsFactory = News::factory()
            ->for($category, 'category')
            ->published();
        $news1 = $newsFactory->create(['published_at' => now()->subDays(1)]);
        $news2 = $newsFactory->create(['published_at' => now()]);

        $this->login();
        $this
            ->graphQL($this->getNewsListQuery, [
                'siteId' => $category->site_id,
                'orderBy' => [
                    [
                        'column' => 'PUBLISHED_AT',
                        'order' => 'ASC',
                    ],
                ],

            ])
            ->assertGraphQLErrorFree()
            ->assertJson(fn (AssertableJson $json) => $json
                ->whereAll([
                    'data.newsList.data.0.id' => (string) $news1->id,
                    'data.newsList.data.1.id' => (string) $news2->id,
                ])
            );
        $this
            ->graphQL($this->getNewsListQuery, [
                'siteId' => $category->site_id,
                'orderBy' => [
                    [
                        'column' => 'PUBLISHED_AT',
                        'order' => 'DESC',
                    ],
                ],
            ])
            ->assertGraphQLErrorFree()
            ->assertJson(fn (AssertableJson $json) => $json
                ->whereAll([
                    'data.newsList.data.0.id' => (string) $news2->id,
                    'data.newsList.data.1.id' => (string) $news1->id,
                ])
            );
    }

    /** @test */
    public function 公開中のNewsのみ返却すること(): void
    {
        $category = NewsCategory::factory()->create();
        $publishedNews = News::factory()
            ->for($category, 'category')
            ->published()
            ->create();
        $_draftNews = News::factory()
            ->for($category, 'category')
            ->draft()
            ->create();

        $this->login();
        $this
            ->graphQL($this->getNewsListQuery, ['siteId' => $category->site_id])
            ->assertGraphQLErrorFree()
            ->assertJson(fn (AssertableJson $json) => $json
                ->count('data.newsList.data', 1)
                ->where('data.newsList.data.0', [
                    'id' => (string) $publishedNews->id,
                    'title' => $publishedNews->title,
                ])
            );
    }
}
