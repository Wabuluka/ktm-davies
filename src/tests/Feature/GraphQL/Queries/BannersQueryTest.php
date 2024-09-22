<?php

namespace Tests\Feature\GraphQL\Queries;

use App\Models\Banner;
use App\Models\BannerPlacement;
use Illuminate\Testing\Fluent\AssertableJson;
use Nuwave\Lighthouse\Testing\RefreshesSchemaCache;
use Tests\TestCase;

class BannersQueryTest extends TestCase
{
    use RefreshesSchemaCache;

    private string $findBannersQuery = /** @lang GraphQL */ '
        query findBanners(
            $placementId: ID!,
        ) {
            banners(
                scope: { placementId: $placementId }
            ) {
                id
                name
            }
        }';

    /** @test */
    public function 未ログイン状態であればエラーが発生すること(): void
    {
        $this
            ->graphQL($this->findBannersQuery, ['placementId' => 1])
            ->assertGraphQLErrorMessage('Unauthenticated.');
    }

    /** @test */
    public function ログイン状態であればリクエストが成功すること(): void
    {
        $this->login();

        $this
            ->graphQL($this->findBannersQuery, ['placementId' => 1])
            ->assertGraphQLErrorFree()
            ->assertJson(fn (AssertableJson $json) => $json->has('data'));
    }

    /** @test */
    public function placementIdに合致するBannerのみ返却すること(): void
    {
        $placement = BannerPlacement::factory()->create();
        $anotherPlacement = BannerPlacement::factory()->create();
        $banner = Banner::factory()
            ->for($placement, 'placement')
            ->displayed()
            ->create();
        $_anotherBanner = Banner::factory()
            ->for($anotherPlacement, 'placement')
            ->displayed()
            ->create();
        $_noSitesBanner = Banner::factory()
            ->displayed()
            ->create();

        $this->login();
        $this
            ->graphQL($this->findBannersQuery, ['placementId' => $placement->id])
            ->assertGraphQLErrorFree()
            ->assertJson(fn (AssertableJson $json) => $json
                ->count('data.banners', 1)
                ->where('data.banners.0', [
                    'id' => (string) $banner->id,
                    'name' => $banner->name,
                ])
            );
    }

    /** @test */
    public function 表示ONのBannerのみ返却すること(): void
    {
        $placement = BannerPlacement::factory()->create();
        $displayed = Banner::factory()
            ->for($placement, 'placement')
            ->displayed(true)
            ->create();
        $hidden = Banner::factory()
            ->for($placement, 'placement')
            ->displayed(false)
            ->create();

        $this->login();
        $this
            ->graphQL($this->findBannersQuery, ['placementId' => $placement->id])
            ->assertGraphQLErrorFree()
            ->assertJson(fn (AssertableJson $json) => $json
                ->count('data.banners', 1)
                ->where('data.banners.0', [
                    'id' => (string) $displayed->id,
                    'name' => $displayed->name,
                ])
            );
    }

    /** @test */
    public function 管理者が指定した並び順で返却すること(): void
    {
        $placement = BannerPlacement::factory()->create();
        $factory = Banner::factory()->displayed()->for($placement, 'placement');
        $sort2 = $factory->create(['sort' => 2]);
        $sort3 = $factory->create(['sort' => 3]);
        $sort1 = $factory->create(['sort' => 1]);

        $this->login();
        $this
            ->graphQL($this->findBannersQuery, ['placementId' => $placement->id])
            ->assertGraphQLErrorFree()
            ->assertJson(fn (AssertableJson $json) => $json
                ->count('data.banners', 3)
                ->where('data.banners.0', [
                    'id' => (string) $sort1->id,
                    'name' => $sort1->name,
                ])
                ->where('data.banners.1', [
                    'id' => (string) $sort2->id,
                    'name' => $sort2->name,
                ])
                ->where('data.banners.2', [
                    'id' => (string) $sort3->id,
                    'name' => $sort3->name,
                ])
            );
    }
}
