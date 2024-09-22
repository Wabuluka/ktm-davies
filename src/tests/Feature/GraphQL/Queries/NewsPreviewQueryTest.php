<?php

namespace Tests\Feature\GraphQL\Queries;

use App\Models\News;
use App\Models\NewsCategory;
use App\Models\NewsPreview;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class NewsPreviewQueryTest extends TestCase
{
    private string $getAttributesQuery = /** @lang GraphQL */ '
        query getAttributes(
            $token: String!
        ) {
            newsPreview(token: $token) {
                id
                publishedAt
                title
                slug
                content
                category {
                    id
                    name
                }
                eyecatch {
                    url
                    customProperties {
                        width
                        height
                    }
                }
                updatedAt
            }
        }';

    /** @test */
    public function ログインしたユーザのみリクエストが成功すること(): void
    {
        $this
            ->graphQL($this->getAttributesQuery, ['token' => Str::uuid()])
            ->assertGraphQLErrorMessage('Unauthenticated.');

        $this->login()
            ->graphQL($this->getAttributesQuery, ['token' => Str::uuid()])
            ->assertGraphQLErrorFree()
            ->assertJson(fn (AssertableJson $json) => $json->has('data'));
    }

    /** @test */
    public function プレビュー用のアイキャッチ画像がレスポンスに含まれること(): void
    {
        $news = News::factory()->published()->attachEyecatch(
            UploadedFile::fake()->image('original-eyecatch.jpg', 10, 20)
        )->create();
        $news->preview_eyecatch = [
            'original_url' => 'https://cdn.example.com/preview-eyecatch.jpg',
            'custom_properties' => [
                'width' => 30,
                'height' => 40,
            ],
        ];
        NewsPreview::create([
            'token' => $token = Str::uuid(),
            'model' => $news,
            'file_paths' => ['tmp/preview/news/eyecatch/preview-eyecatch.jpg'],
        ]);

        $this->login()
            ->graphQL($this->getAttributesQuery, ['token' => $token])
            ->assertGraphQLErrorFree()
            ->assertJsonPath('data.newsPreview.eyecatch', [
                'url' => 'https://cdn.example.com/preview-eyecatch.jpg',
                'customProperties' => [
                    'width' => 30,
                    'height' => 40,
                ],
            ]);
    }

    /** @test */
    public function Newsのアトリビュートとリレーションがレスポンスに含まれること(): void
    {
        $this->freezeTime();

        $news = News::factory()->willBePublished()
            ->for($category = NewsCategory::factory()->create([
                'name' => 'お知らせ',
            ]), 'category')
            ->create([
                'title' => 'サイトリニューアルのお知らせ',
                'slug' => 'site-renewal',
                'content' => '<p>サイトをリニューアルしました。</p>',
                'published_at' => now()->addDay(),
            ]);
        NewsPreview::create([
            'token' => $token = Str::uuid(),
            'model' => $news,
            'file_paths' => [],
        ]);

        $this->login()
            ->graphQL($this->getAttributesQuery, ['token' => $token])
            ->assertGraphQLErrorFree()
            ->assertJson(fn (AssertableJson $json) => $json
                ->has('data.newsPreview', fn (AssertableJson $json) => $json
                    ->where('id', "{$news->id}")
                    ->where('publishedAt', now()->addDay()->toDateTimeString())
                    ->where('title', $news->title)
                    ->where('slug', $news->slug)
                    ->where('content', $news->content)
                    ->has('category', fn (AssertableJson $json) => $json
                        ->where('id', "{$category->id}")
                        ->where('name', $category->name)
                    )
                    ->where('eyecatch', null)
                    ->where('updatedAt', now()->toDateTimeString())
                )
            );
    }
}
