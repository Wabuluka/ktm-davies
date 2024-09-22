<?php

namespace Tests\Feature\Http\Controllers\News;

use App\Models\News;
use App\Models\NewsCategory;
use App\Models\Site;
use Database\Factories\NewsFactory;
use Illuminate\Http\UploadedFile;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Tests\TestCase;

class NewsUpdateTest extends TestCase
{
    private Site $site;

    private NewsFactory $factory;

    private array $requestData;

    protected function setUp(): void
    {
        parent::setUp();

        $this->site = Site::factory()->hasNewsCategories(2)->create();
        $this->factory = News::factory()->for($this->site->newsCategories->first(), 'category');
        $this->requestData = [
            'status' => 'draft',
            'title' => 'サイトリニューアルのお知らせ',
            'slug' => 'site-renewal',
            'category_id' => $this->site->newsCategories->first()->id,
            'content' => '<p>サイトをリニューアルしました。</p>',
        ];
    }

    /** @test */
    public function ログインしなければNEWSを更新できないこと(): void
    {
        $news = $this->factory->create(['title' => 'タイトル']);
        $response = $this->patch(route('news.update', $news), ['title' => '新しいタイトル']);
        $response->assertRedirect('/login');
        $this->assertDatabaseMissing(News::class, ['title' => '新しいタイトル']);
    }

    /** @test */
    public function NEWSを更新できること(): void
    {
        $this->travelTo('2023-01-01 00:00:00');

        $news = $this->factory->create([
            'title' => 'タイトル',
            'slug' => 'slug',
        ]);
        $requestData = array_merge($this->requestData, [
            'title' => '新しいタイトル',
            'slug' => 'new-slug',
        ]);
        $response = $this->login()->patch(route('news.update', $news), $requestData);
        $response->assertRedirect(route('sites.news.index', $this->site));
        $this->assertDatabaseHas(News::class, [
            'id' => $news->id,
            'title' => '新しいタイトル',
            'slug' => 'new-slug',
        ]);
    }

    /** @test */
    public function 不正なリクエストパラメータに対しバリデーションてエラーが発生すること(): void
    {
        $news = $this->factory->create();
        $requestData = array_merge($this->requestData, [
            'title' => null,
            'slug' => null,
        ]);
        $response = $this->login()->patch(route('news.update', $news), $requestData);
        $response->assertSessionHasErrors(['title', 'slug']);
    }

    /** @test */
    public function 別サイトのNewsカテゴリを指定するとバリデーションエラーが発生すること(): void
    {
        $news = $this->factory->create();
        $requestData = array_merge($this->requestData, [
            'category_id' => NewsCategory::factory()->create()->id,
        ]);
        $response = $this->login()->patch(route('news.update', $news), $requestData);
        $response->assertSessionHasErrors('category_id');
    }

    /** @test */
    public function スラッグの値がサイト毎にユニークではない場合、バリデーションエラーが発生すること(): void
    {
        // 同じサイト
        $slug = 'must-be-unique';
        $_duplicatedSlugNews = $this->factory->create(['slug' => $slug]);
        $news = $this->factory->create(['slug' => 'still-unique']);
        $requestData = array_merge($this->requestData, ['slug' => $slug]);
        $this->login();
        $this
            ->patch(route('news.update', $news), $requestData)
            ->assertSessionHasErrors(['slug' => __('validation.unique', ['attribute' => 'slug'])]);
        // 別サイト
        $anotherSiteNews = News::factory()->create(['slug' => 'also-unique']);
        $anotherSiteRequestData = array_merge($this->requestData, [
            'slug' => $slug,
            'category_id' => $anotherSiteNews->category->id,
        ]);
        $this
            ->patch(route('news.update', $anotherSiteNews), $anotherSiteRequestData)
            ->assertSessionHasNoErrors();
        $this
            ->assertDatabaseHas(News::class, ['id' => $anotherSiteNews->id, 'slug' => $slug]);
    }

    /** @test */
    public function ステータスが「公開中」且つ公開日＜＝現在時刻の場合は、公開日に現在時刻が自動で設定されること(): void
    {
        [$news1, $news2, $news3, $news4] = $this->factory
            ->count(4)
            ->sequence(
                ['slug' => 'news-1', 'published_at' => '2022-12-31'],
                ['slug' => 'news-2', 'published_at' => '2022-12-31'],
                ['slug' => 'news-3', 'published_at' => '2022-12-31'],
                ['slug' => 'news-4', 'published_at' => '2023-01-02'],
            )
            ->create();
        $this->travelTo('2023-01-01');

        $this->login()
            ->patch(route('news.update', $news1), array_merge($this->requestData, [
                'status' => 'draft',
                'slug' => 'news-1-draft',
            ]))
            ->assertRedirectToRoute('sites.news.index', $this->site);
        $this->login()
            ->patch(route('news.update', $news2), array_merge($this->requestData, [
                'status' => 'willBePublished',
                'slug' => 'news-2-will-be-published',
                'published_at' => '2023-01-02',
            ]))
            ->assertRedirectToRoute('sites.news.index', $this->site);
        $this->login()
            ->patch(route('news.update', $news3), array_merge($this->requestData, [
                'status' => 'published',
                'slug' => 'news-3-published',
            ]))
            ->assertRedirectToRoute('sites.news.index', $this->site);
        $this->login()
            ->patch(route('news.update', $news4), array_merge($this->requestData, [
                'status' => 'published',
                'slug' => 'news-4-published',
            ]))
            ->assertRedirectToRoute('sites.news.index', $this->site);

        $this->assertDatabaseHas(News::class, ['published_at' => '2022-12-31', 'slug' => 'news-1-draft']);
        $this->assertDatabaseHas(News::class, ['published_at' => '2023-01-02', 'slug' => 'news-2-will-be-published']);
        $this->assertDatabaseHas(News::class, ['published_at' => '2022-12-31', 'slug' => 'news-3-published']);
        $this->assertDatabaseHas(News::class, ['published_at' => '2023-01-01', 'slug' => 'news-4-published']);
    }

    /** @test */
    public function ステータスを「下書き」に更新できること(): void
    {
        $this->travelTo('2023-01-01 00:00:00');

        $willBePublishedNews = $this->factory
            ->willBePublished()
            ->create(['title' => '公開予定のNEWS', 'published_at' => now()->addMonth()]);
        $publishedNews = $this->factory
            ->published()
            ->create(['title' => '公開中のNEWS', 'published_at' => now()]);

        $willBePublishedToDraft = array_merge($this->requestData, [
            'status' => 'draft',
            'slug' => $willBePublishedNews->slug,
        ]);
        $publisheddToDraft = array_merge($this->requestData, [
            'status' => 'draft',
            'slug' => $publishedNews->slug,
        ]);
        $this->login()
            ->patch(route('news.update', $willBePublishedNews), $willBePublishedToDraft)
            ->assertRedirectToRoute('sites.news.index', $this->site);
        $this->login()
            ->patch(route('news.update', $publishedNews), $publisheddToDraft)
            ->assertRedirectToRoute('sites.news.index', $this->site);

        $this->assertDatabaseHas(News::class, [
            'id' => $willBePublishedNews->id,
            'published_at' => '2023-02-01 00:00:00',
            'is_draft' => true,
        ]);
        $this->assertDatabaseHas(News::class, [
            'id' => $publishedNews->id,
            'published_at' => '2023-01-01 00:00:00',
            'is_draft' => true,
        ]);
    }

    /** @test */
    public function ステータスを「公開予定」に更新できること(): void
    {
        $this->travelTo('2023-01-01 00:00:00');

        $draftNews = $this->factory
            ->draft()
            ->create(['title' => '下書きのNEWS']);
        $publishedNews = $this->factory
            ->published()
            ->create(['title' => '公開中のNEWS']);

        $draftToWillBePublished = array_merge($this->requestData, [
            'status' => 'willBePublished',
            'published_at' => '2023-01-02 00:00:00',
            'slug' => $draftNews->slug,
        ]);
        $publisheddToWillBePublished = array_merge($this->requestData, [
            'status' => 'willBePublished',
            'published_at' => '2023-01-02 00:00:00',
            'slug' => $publishedNews->slug,
        ]);
        $this->login()
            ->patch(route('news.update', $draftNews), $draftToWillBePublished)
            ->assertRedirectToRoute('sites.news.index', $this->site);
        $this->login()
            ->patch(route('news.update', $publishedNews), $publisheddToWillBePublished)
            ->assertRedirectToRoute('sites.news.index', $this->site);

        $this->assertDatabaseHas(News::class, [
            'id' => $draftNews->id,
            'published_at' => '2023-01-02 00:00:00',
            'is_draft' => false,
        ]);
        $this->assertDatabaseHas(News::class, [
            'id' => $publishedNews->id,
            'published_at' => '2023-01-02 00:00:00',
            'is_draft' => false,
        ]);
    }

    /** @test */
    public function ステータスを「公開中」に更新できること(): void
    {
        $this->travelTo('2023-01-01 00:00:00');

        $draftNews = $this->factory
            ->draft()
            ->create(['title' => '下書きのNEWS']);
        $willBePublishedNews = $this->factory
            ->willBePublished()
            ->create(['title' => '公開中のNEWS']);

        $draftToPublished = array_merge($this->requestData, [
            'status' => 'published',
            'slug' => $draftNews->slug,
        ]);
        $willBePublishedToPublished = array_merge($this->requestData, [
            'status' => 'published',
            'slug' => $willBePublishedNews->slug,
        ]);

        $this->login()
            ->patch(route('news.update', $draftNews), $draftToPublished)
            ->assertRedirectToRoute('sites.news.index', $this->site);
        $this->login()
            ->patch(route('news.update', $willBePublishedNews), $willBePublishedToPublished)
            ->assertRedirectToRoute('sites.news.index', $this->site);

        $this->assertDatabaseHas(News::class, [
            'id' => $draftNews->id,
            'published_at' => '2023-01-01 00:00:00',
            'is_draft' => false,
        ]);
        $this->assertDatabaseHas(News::class, [
            'id' => $willBePublishedNews->id,
            'published_at' => '2023-01-01 00:00:00',
            'is_draft' => false,
        ]);
    }

    /** @test */
    public function Newsにアイキャッチ画像を設定できること(): void
    {
        $news = $this->factory->create();
        $requestData = array_merge($this->requestData, [
            'eyecatch' => UploadedFile::fake()->image('eyecatch.jpg', 200, 100),
        ]);

        $this->login()
            ->patch(route('news.update', $news), $requestData)
            ->assertRedirectToRoute('sites.news.index', $this->site);

        $this->assertDatabaseHas(Media::class, [
            'model_id' => $news->id,
            'model_type' => 'news',
            'collection_name' => 'eyecatch',
            'custom_properties' => json_encode(['width' => 200, 'height' => 100]),
        ]);
    }

    /** @test */
    public function Newsからアイキャッチ画像を削除できること(): void
    {
        $news = $this->factory->attachEyecatch(
            UploadedFile::fake()->image('eyecatch.jpg', 200, 100)
        )->create();
        $mediaCount = Media::count();
        $requestData = array_merge($this->requestData, ['eyecatch' => null]);

        $this->login()
            ->patch(route('news.update', $news), $requestData)
            ->assertRedirectToRoute('sites.news.index', $this->site);

        $this->assertDatabaseMissing(Media::class, [
            'model_id' => $news->id,
            'model_type' => 'news',
        ]);
        $this->assertDatabaseCount(Media::class, $mediaCount - 1);
    }

    /** @test */
    public function Newsからアイキャッチ画像を上書きできること(): void
    {
        $news = $this->factory->attachEyecatch(
            UploadedFile::fake()->image('eyecatch.jpg', 200, 100)
        )->create();
        $requestData = array_merge($this->requestData, [
            'eyecatch' => UploadedFile::fake()->image('new-eyecatch.jpg', 150, 150),
        ]);

        $this->login()
            ->patch(route('news.update', $news), $requestData)
            ->assertRedirectToRoute('sites.news.index', $this->site);

        $this->assertDatabaseHas(Media::class, [
            'model_id' => $news->id,
            'model_type' => 'news',
            'custom_properties' => json_encode(['width' => 150, 'height' => 150]),
        ]);
    }
}
