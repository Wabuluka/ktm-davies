<?php

namespace Tests\Feature\GraphQL\Queries;

use App\Models\Page;
use App\Models\PagePreview;
use Illuminate\Support\Str;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class PagePreviewQueryTest extends TestCase
{
    private string $findPreviewQuery = /** @lang GraphQL */ '
        query findPagePreview(
            $token: String!
        ) {
            pagePreview(token: $token) {
                id
                title
                slug
                content
                createdAt
                updatedAt
            }
        }';

    /** @test */
    public function ログインしたユーザのみリクエストが成功すること(): void
    {
        $this
            ->graphQL($this->findPreviewQuery, ['token' => Str::uuid()])
            ->assertGraphQLErrorMessage('Unauthenticated.');

        $this->login()
            ->graphQL($this->findPreviewQuery, ['token' => Str::uuid()])
            ->assertGraphQLErrorFree()
            ->assertJson(fn (AssertableJson $json) => $json->has('data'));
    }

    /** @test */
    public function Pageのアトリビュートとリレーションがレスポンスに含まれること(): void
    {
        $this->travelTo('2023-12-31 23:59:59');

        $page = Page::factory()->create([
            'title' => 'プライバシーポリシー',
            'slug' => 'privacy',
            'content' => '<h1>プライバシーポリシー</h1>',
        ]);

        $this->travelTo('2024-01-01 00:00:00');

        PagePreview::create([
            'token' => $token = Str::uuid(),
            'model' => tap($page)->update([
                'title' => 'プライバシーに関する声明',
                'content' => '<h1>プライバシーに関する声明</h1>',
            ]),
        ]);

        $this->login()
            ->graphQL($this->findPreviewQuery, ['token' => $token])
            ->assertGraphQLErrorFree()
            ->assertJson(fn (AssertableJson $json) => $json
                ->has('data.pagePreview', fn (AssertableJson $json) => $json
                    ->where('id', "{$page->id}")
                    ->where('title', 'プライバシーに関する声明')
                    ->where('slug', 'privacy')
                    ->where('content', '<h1>プライバシーに関する声明</h1>')
                    ->where('createdAt', '2023-12-31 23:59:59')
                    ->where('updatedAt', '2024-01-01 00:00:00')
                )
            );
    }
}
