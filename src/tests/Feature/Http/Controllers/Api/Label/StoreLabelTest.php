<?php

namespace Tests\Feature\Http\Controllers\Api\Label;

use App\Enums\LabelType;
use App\Models\Genre;
use App\Models\Label;
use Tests\TestCase;

class StoreLabelTest extends TestCase
{
    /** @test */
    public function レーベルを作成できること(): void
    {
        $attributes = [
            'name' => 'ABC文庫',
            'url' => 'https://example.com/',
            'genre_id' => Genre::factory()->create()->id,
        ];
        $relationships = [
            'type_ids' => [LabelType::Paperback->value],
        ];

        $this
            ->login()
            ->postJson(route('label.store'), $attributes + $relationships)
            ->assertSuccessful();

        $this->assertDatabaseHas('labels', $attributes);
        $this->assertDatabaseHas('label_label_type', [
            'label_id' => Label::latest()->first()->id,
            'label_type_id' => LabelType::Paperback->value,
        ]);
    }

    /** @test */
    public function 不正なリクエストに対してエラーが発生すること(): void
    {
        $data = [
            'name' => 'ABC文庫',
            'url' => 'https://example.com/',
            'genre_id' => 'invalid',
        ];

        $this
            ->login()
            ->postJson(route('label.store'), $data)
            ->assertJsonValidationErrors('genre_id');

        $this->assertDatabaseMissing('labels', $data);
    }
}
