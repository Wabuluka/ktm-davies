<?php

namespace Tests\Feature\GraphQL\Queries;

use App\Models\News;
use App\Models\NewsCategory;
use Illuminate\Testing\Fluent\AssertableJson;
use Nuwave\Lighthouse\Testing\RefreshesSchemaCache;
use Tests\TestCase;

class NewsArchiveQueryTest extends TestCase
{
    use RefreshesSchemaCache;

    private string $getNewsArchiveQuery = /** @lang GraphQL */ '
        query getNewsArchiveMonths(
            $siteId: ID!,
        ) {
            newsArchive(
                scope: { siteId: $siteId }
            ) {
                data {
                    year
                    month
                }
            }
        }';

    /** @test */
    public function 未ログイン状態であればエラーが発生すること(): void
    {
        $this
            ->graphQL($this->getNewsArchiveQuery, ['siteId' => 1])
            ->assertGraphQLErrorMessage('Unauthenticated.');
    }

    /** @test */
    public function ログイン状態であればリクエストが成功すること(): void
    {
        $this->login();

        $this
            ->graphQL($this->getNewsArchiveQuery, ['siteId' => 1])
            ->assertGraphQLErrorFree()
            ->assertJson(fn (AssertableJson $json) => $json->has('data'));
    }

    /** @test */
    public function siteIdに合致するNewsの公開月の一覧を返却すること(): void
    {
        $this->travelTo('2024-12-31');
        $category =
            NewsCategory::factory()->create();
        $anotherSiteCategory =
            NewsCategory::factory()->create();
        $newsFactory =
            News::factory()->for($category, 'category');
        $anotherSiteNewsFactory =
            News::factory()->for($anotherSiteCategory, 'category');
        $newsFactory
            ->create(['is_draft' => false, 'published_at' => '2023-12-31']);
        $newsFactory
            ->create(['is_draft' => false, 'published_at' => '2024-01-29']);
        $newsFactory
            ->create(['is_draft' => false, 'published_at' => '2024-02-29']);
        $anotherSiteNewsFactory
            ->create(['is_draft' => false, 'published_at' => '2024-03-31']);

        $this->login();
        $this
            ->graphQL($this->getNewsArchiveQuery, ['siteId' => $category->site_id])
            ->assertGraphQLErrorFree()
            ->assertJson(fn (AssertableJson $json) => $json
                ->count('data.newsArchive.data', 3)
                ->where('data.newsArchive.data.0', [
                    'year' => '2024',
                    'month' => '02',
                ])
                ->where('data.newsArchive.data.1', [
                    'year' => '2024',
                    'month' => '01',
                ])
                ->where('data.newsArchive.data.2', [
                    'year' => '2023',
                    'month' => '12',
                ])
            );
        $this
            ->graphQL($this->getNewsArchiveQuery, ['siteId' => $anotherSiteCategory->site_id])
            ->assertGraphQLErrorFree()
            ->assertJson(fn (AssertableJson $json) => $json
                ->count('data.newsArchive.data', 1)
                ->where('data.newsArchive.data.0', [
                    'year' => '2024',
                    'month' => '03',
                ])
            );
    }

    /** @test */
    public function 公開中のNewsを含まない月は、公開月の一覧から除外されること(): void
    {
        $this->travelTo('2024-12-31');
        $category = NewsCategory::factory()->create();
        $newsFactory = News::factory()->for($category, 'category');
        // 公開中と下書きの News を含む月
        $newsFactory
            ->create(['is_draft' => false, 'published_at' => '2024-01-01']);
        $newsFactory
            ->create(['is_draft' => true, 'published_at' => '2024-01-31']);
        // 公開中の News のみ含む月
        $newsFactory
            ->create(['is_draft' => false, 'published_at' => '2024-02-01']);
        // 下書きの News のみ含む月
        $newsFactory
            ->create(['is_draft' => true, 'published_at' => '2024-03-01']);
        // 公開予定の News のみ含む月
        $newsFactory
            ->create(['is_draft' => false, 'published_at' => '2025-01-01']);

        $this->login();
        $this
            ->graphQL($this->getNewsArchiveQuery, ['siteId' => $category->site_id])
            ->assertGraphQLErrorFree()
            ->assertJson(fn (AssertableJson $json) => $json
                ->count('data.newsArchive.data', 2)
                ->where('data.newsArchive.data.0', [
                    'year' => '2024',
                    'month' => '02',
                ])
                ->where('data.newsArchive.data.1', [
                    'year' => '2024',
                    'month' => '01',
                ])
            );
    }
}
