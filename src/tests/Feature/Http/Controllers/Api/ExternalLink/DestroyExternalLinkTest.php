<?php

namespace Tests\Feature\Http\Controllers\Api\ExternalLink;

use App\Models\Book;
use App\Models\ExternalLink;
use App\Models\RelatedItem;
use Tests\TestCase;

class DestroyExternalLinkTest extends TestCase
{
    /** @test */
    public function 外部リンクが削除されること(): void
    {
        /** @var ExternalLink $link */
        $link = ExternalLink::factory()->create();

        $this
            ->login()
            ->deleteJson(route('external-links.destroy', $link))
            ->assertSuccessful();

        $this->assertDatabaseMissing('external_links', $link->only('id'));
    }

    /** @test */
    public function 外部リンクが関連作品として登録されている場合は。削除に失敗すること(): void
    {
        /** @var ExternalLink $link */
        $link = ExternalLink::factory()->create();
        Book::factory()
            ->has(RelatedItem::factory()->for($link, 'relatable'))
            ->create();

        $this
            ->login()
            ->deleteJson(route('external-links.destroy', $link))
            ->assertJsonValidationErrors('externalLink');

        $this->assertDatabaseHas('external_links', $link->only('id'));
    }
}
