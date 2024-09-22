<?php

namespace Tests\Unit\Models;

use App\Models\Label;
use Tests\TestCase;

class LabelTest extends TestCase
{
    /** @test */
    public function scopeHasSite_専用サイトURLの登録があるレーベルのみに絞り込むこと(): void
    {
        $hasSite = Label::factory()->create([
            'url' => 'https://example.com',
        ]);
        $_noSite1 = Label::factory()->create([
            'url' => null,
        ]);
        $_noSite2 = Label::factory()->create([
            'url' => '',
        ]);
        $labelIds = Label::hasSite()->get()->pluck('id')->toArray();
        $this->assertSame([$hasSite->id], $labelIds);
    }
}
