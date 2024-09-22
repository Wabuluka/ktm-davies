<?php

namespace Tests\Feature\Http\Controllers\News;

use App\Models\News;
use App\Models\NewsCategory;
use App\Models\Site;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Tests\TestCase;

class NewsStoreTest extends TestCase
{
    private array $requestData;

    private Site $site;

    protected function setUp(): void
    {
        parent::setUp();

        $this->site = Site::factory()->hasNewsCategories(2)->create();
        $this->requestData = [
            'status' => 'draft',
            'title' => 'サイトリニューアルのお知らせ',
            'slug' => 'site-renewal',
            'category_id' => $this->site->newsCategories->first()->id,
            'content' => '<p>サイトをリニューアルしました。</p>',
        ];
    }

    /** @test */
    public function ログインしなければNEWSを作成できないこと(): void
    {
        $response = $this->post(route('sites.news.store', $this->site), $this->requestData);
        $response->assertRedirect('/login');
        $this->assertDatabaseMissing(News::class, Arr::except($this->requestData, 'status'));
    }

    /** @test */
    public function NEWSを作成できること(): void
    {
        $response = $this->login()->post(route('sites.news.store', $this->site), $this->requestData);
        $response->assertRedirect(route('sites.news.index', $this->site));
        $this->assertDatabaseHas(News::class, Arr::except($this->requestData, 'status'));
    }

    /** @test */
    public function 不正なリクエストパラメータに対しバリデーションエラーが発生すること(): void
    {
        $invalidData = array_merge($this->requestData, ['title' => null, 'category_id' => 0]);
        $response = $this->login()->post(route('sites.news.store', $this->site), $invalidData);
        $response->assertSessionHasErrors(['title', 'category_id']);
    }

    /** @test */
    public function 別サイトのNewsカテゴリを指定するとバリデーションエラーが発生すること(): void
    {
        $anotherSiteCategory = NewsCategory::factory()->create();
        $requestData = array_merge($this->requestData, ['category_id' => $anotherSiteCategory->id]);
        $this->login()
            ->post(route('sites.news.store', $this->site), $requestData)
            ->assertSessionHasErrors('category_id');
    }

    /** @test */
    public function スラッグの値がサイト毎にユニークではない場合、バリデーションエラーが発生すること(): void
    {
        $slug = 'must-be-unique';

        // 同じスラッグが存在するサイト
        NewsCategory::factory()->for($this->site)->hasNews(1, ['slug' => $slug])->create();
        $requestData = array_merge($this->requestData, ['slug' => $slug]);
        $this->login()
            ->post(route('sites.news.store', $this->site), $requestData)
            ->assertSessionHasErrors(['slug' => __('validation.unique', ['attribute' => 'slug'])]);

        // 同じスラッグが存在しないサイト
        $anotherSiteCategory = NewsCategory::factory()->create();
        $anotherSiteRequestData = array_merge($this->requestData, [
            'slug' => $slug,
            'category_id' => $anotherSiteCategory->id,
        ]);
        $this->login()
            ->post(route('sites.news.store', $anotherSiteCategory->site), $anotherSiteRequestData)
            ->assertSessionHasNoErrors();
        $this->assertDatabaseHas(News::class, Arr::except($anotherSiteRequestData, 'status'));
    }

    /** @test */
    public function ステータス「下書き」か「公開中」で保存すると、公開日時に現在時刻が設定されること(): void
    {
        $this->travelTo('2023-01-01 00:00:00');

        $draftData = array_merge($this->requestData, [
            'status' => 'draft',
            'slug' => 'draft',
        ]);
        $willBePublishedData = array_merge($this->requestData, [
            'status' => 'willBePublished',
            'slug' => 'will-be-published',
            'published_at' => '2023-01-02 00:00:00',
        ]);
        $publishedData = array_merge($this->requestData, [
            'status' => 'published',
            'slug' => 'published',
        ]);
        $this->login()
            ->post(route('sites.news.store', $this->site), $draftData)
            ->assertRedirectToRoute('sites.news.index', $this->site);
        $this->login()
            ->post(route('sites.news.store', $this->site), $willBePublishedData)
            ->assertRedirectToRoute('sites.news.index', $this->site);
        $this->login()
            ->post(route('sites.news.store', $this->site), $publishedData)
            ->assertRedirectToRoute('sites.news.index', $this->site);

        $this->assertDatabaseHas(News::class, ['published_at' => '2023-01-01 00:00:00', 'slug' => 'draft']);
        $this->assertDatabaseHas(News::class, ['published_at' => '2023-01-02 00:00:00', 'slug' => 'will-be-published']);
        $this->assertDatabaseHas(News::class, ['published_at' => '2023-01-01 00:00:00', 'slug' => 'published']);
    }

    /** @test */
    public function ステータス「公開予定」の場合、公開日時の指定が必須になること(): void
    {
        $requestData = array_merge($this->requestData, [
            'status' => 'willBePublished',
            // 'published_at' => '2023-01-02 00:00:00',
        ]);
        $this->login()
            ->post(route('sites.news.store', $this->site), $requestData)
            ->assertSessionHasErrors('published_at');
    }

    /** @test */
    public function Newsにアイキャッチ画像を設定できること(): void
    {
        $mediaCount = Media::count();
        $requestData = array_merge($this->requestData, [
            'eyecatch' => UploadedFile::fake()->image('eyecatch.jpg', 200, 100),
        ]);
        $this->login()
            ->post(route('sites.news.store', $this->site), $requestData)
            ->assertRedirectToRoute('sites.news.index', $this->site);
        $this->assertDatabaseHas(News::class, Arr::except($requestData, ['status', 'eyecatch']));
        $this->assertDatabaseHas(Media::class, [
            'model_type' => 'news',
            'collection_name' => 'eyecatch',
            'custom_properties' => json_encode(['width' => 200, 'height' => 100]),
        ]);
        $this->assertDatabaseCount(Media::class, $mediaCount + 1);
    }
}
