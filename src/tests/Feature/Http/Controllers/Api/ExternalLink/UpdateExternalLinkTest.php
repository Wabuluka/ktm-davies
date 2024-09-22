<?php

namespace Tests\Feature\Http\Controllers\Api\ExternalLink;

use App\Models\ExternalLink;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class UpdateExternalLinkTest extends TestCase
{
    /** @test */
    public function 外部リンクを更新できること(): void
    {
        $thumbnail = UploadedFile::fake()->image('thumbnail.jpg', 200, 300);
        $link = ExternalLink::factory()
            ->attachThumbnail($thumbnail)
            ->create(['title' => '原作小説 1']);
        $data = [
            'title' => '原作小説 1 (Updated)',
            'url' => 'https://example.com/updated',
        ];

        $this
            ->login()
            ->patchJson(route('external-links.update', $link), [
                ...$data,
                'thumbnail' => ['operation' => 'stay'],
            ])
            ->assertSuccessful();

        $this->assertDatabaseHas('external_links', $data);
        $this->assertDatabaseHas('media', [
            'model_type' => 'externalLink',
            'model_id' => $link->id,
        ]);
    }

    /** @test */
    public function 不正なリクエストに対してエラーが発生すること(): void
    {
        $link1 = ExternalLink::factory()->create(['title' => '原作小説 1']);
        $link2 = ExternalLink::factory()->create(['title' => '原作小説 2']);
        $data = [
            'title' => $link2->title,
            'url' => $link1->url . '/path',
        ];

        $this
            ->login()
            ->patchJson(route('external-links.update', $link1), [
                ...$data,
                'thumbnail' => ['operation' => 'stay'],
            ])
            ->assertJsonValidationErrors('title');

        $this->assertDatabaseMissing('external_links', $data);
    }

    /** @test */
    public function サムネイルを紐付けて保存できること(): void
    {
        $link = ExternalLink::factory()->create(['title' => '原作小説 1']);
        $data = [
            'title' => '原作小説 1 (Updated)',
            'url' => 'https://example.com/updated',
        ];

        $this
            ->login()
            ->patchJson(route('external-links.update', $link), [
                ...$data,
                'thumbnail' => [
                    'operation' => 'set',
                    'file' => UploadedFile::fake()->image('thumbnail.jpg', 200, 300),
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

    /** @test */
    public function サムネイルを更新できること(): void
    {
        $thumbnail =
            UploadedFile::fake()->image('thumbnail.jpg', 200, 300);
        $newThumbnail =
            UploadedFile::fake()->image('thumbnail-updated.jpg', 300, 400);
        $link = ExternalLink::factory()
            ->attachThumbnail($thumbnail)
            ->create(['title' => '原作小説 1']);
        $data = [
            'title' => '原作小説 1 (Updated)',
            'url' => 'https://example.com/updated',
        ];

        $this
            ->login()
            ->patchJson(route('external-links.update', $link), [
                ...$data,
                'thumbnail' => [
                    'operation' => 'set',
                    'file' => $newThumbnail,
                ],
            ])
            ->assertSuccessful();

        $this->assertDatabaseHas('external_links', $data);
        $this->assertDatabaseHas('media', [
            'model_type' => 'externalLink',
            'file_name' => 'thumbnail-updated.jpg',
            'custom_properties->width' => 300,
            'custom_properties->height' => 400,
        ]);
        $this->assertDatabaseMissing('media', [
            'file_name' => 'thumbnail.jpg',
        ]);
    }

    /** @test */
    public function サムネイルを削除できること(): void
    {
        $thumbnail = UploadedFile::fake()->image('thumbnail.jpg', 200, 300);
        $link = ExternalLink::factory()
            ->attachThumbnail($thumbnail)
            ->create(['title' => '原作小説 1']);
        $data = [
            'title' => '原作小説 1 (Updated)',
            'url' => 'https://example.com/updated',
        ];

        $this
            ->login()
            ->patchJson(route('external-links.update', $link), [
                ...$data,
                'thumbnail' => [
                    'operation' => 'delete',
                ],
            ])
            ->assertSuccessful();

        $this->assertDatabaseHas('external_links', $data);
        $this->assertDatabaseMissing('media', [
            'model_type' => 'externalLink',
            'model_id' => $link->id,
        ]);
    }
}
