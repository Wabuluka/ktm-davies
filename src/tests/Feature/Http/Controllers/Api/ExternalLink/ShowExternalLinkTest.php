<?php

namespace Tests\Feature\Http\Controllers\Api\ExternalLink;

use App\Models\ExternalLink;
use Illuminate\Http\UploadedFile;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class ShowExternalLinkTest extends TestCase
{
    /** @test */
    public function 外部リンクのタイトルとURLとサムネイルを返却すること(): void
    {
        $thumbnail = UploadedFile::fake()->image('thumbnail.jpg');
        $link = ExternalLink::factory()
            ->attachThumbnail($thumbnail)
            ->create();

        $response = $this
            ->login()
            ->get(route('external-links.show', $link));

        $response
            ->assertJson(fn (AssertableJson $json) => $json
                ->where('title', $link->title)
                ->where('url', $link->url)
                ->has('thumbnail', fn (AssertableJson $json) => $json
                    ->where('file_name', 'thumbnail.jpg')
                    ->etc())
                ->etc());
    }
}
