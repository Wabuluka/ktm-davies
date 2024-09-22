<?php

namespace Tests\Feature\Http\Controllers\Api\ExternalLink;

use App\Models\ExternalLink;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class StoreExternalLinkTest extends TestCase
{
    /** @test */
    public function 外部リンクを作成できること(): void
    {
        $data = ['title' => '原作小説 1', 'url' => 'https://example.com/'];

        $this
            ->login()
            ->postJson('/api/external-links', [
                ...$data,
                'thumbnail' => ['operation' => 'stay'],
            ])
            ->assertSuccessful();

        $this->assertDatabaseHas('external_links', $data);
    }

    /** @test */
    public function 不正なリクエストに対してエラーが発生すること(): void
    {
        $link = ExternalLink::factory()->create(['title' => '原作小説 1']);
        $data = [
            'title' => $link->title,
            'url' => $link->url . '/path',
        ];

        $this
            ->login()
            ->postJson('/api/external-links', [
                ...$data,
                'thumbnail' => ['operation' => 'stay'],
            ])
            ->assertJsonValidationErrors('title');

        $this->assertDatabaseMissing('external_links', $data);
    }

    /** @test */
    public function サムネイルを紐付けて保存できること(): void
    {
        $data = ['title' => '原作小説 1', 'url' => 'https://example.com/'];
        $thumbnail = UploadedFile::fake()->image('thumbnail.jpg', 200, 300);

        $this
            ->login()
            ->postJson('/api/external-links', [
                ...$data,
                'thumbnail' => [
                    'operation' => 'set',
                    'file' => $thumbnail,
                ],
            ])
            ->assertSuccessful();

        $this->assertDatabaseHas('external_links', $data);
        $this->assertDatabaseHas('media', [
            'model_type' => 'externalLink',
            'file_name' => 'thumbnail.jpg',
            'custom_properties->width' => 200,
            'custom_properties->height' => 300,
        ]);
    }
}
