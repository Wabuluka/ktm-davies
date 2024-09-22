<?php

namespace Tests\Feature\Http\Controllers\Api\Label;

use App\Enums\LabelType;
use App\Models\Genre;
use App\Models\Label;
use Tests\TestCase;

class UpdateLabelTest extends TestCase
{
    /** @test */
    public function レーベルのアトリビュートを更新できること(): void
    {
        $genre1 = Genre::factory()->create(['name' => '小説']);
        $genre2 = Genre::factory()->create(['name' => 'コミック']);
        $label = Label::factory()->for($genre1)->create([
            'name' => 'ABC文庫',
            'url' => 'https://example.com/',
        ]);
        $data = [
            'name' => 'ABCコミック',
            'url' => 'https://example.com/updated',
            'genre_id' => $genre2->id,
        ];

        $this
            ->login()
            ->patchJson(route('label.update', $label), $data)
            ->assertSuccessful();

        $this->assertDatabaseHas('labels', $data);
    }

    /** @test */
    public function レーベルの種別を更新できること(): void
    {
        $label = Label::factory()
            ->for(Genre::factory()->create())
            ->magazine()->create(['name' => 'ABC文庫']);
        $data = [
            'name' => 'ABCコミック',
            'genre_id' => $label->genre->id,
            'type_ids' => [LabelType::Paperback->value, LabelType::Goods->value],
        ];

        $this
            ->login()
            ->patchJson(route('label.update', $label), $data)
            ->assertSuccessful();

        $this->assertDatabaseHas('labels', ['name' => 'ABCコミック']);
        $this->assertDatabaseHas('label_label_type', ['label_id' => $label->id, 'label_type_id' => LabelType::Paperback->value]);
        $this->assertDatabaseHas('label_label_type', ['label_id' => $label->id, 'label_type_id' => LabelType::Goods->value]);
        $this->assertDatabaseMissing('label_label_type', ['label_id' => $label->id, 'label_type_id' => LabelType::Magazine->value]);
    }

    /** @test */
    public function type_idsキーがある時のみ、レーベルの種別を更新すること(): void
    {
        $label = Label::factory()
            ->for(Genre::factory()->create())
            ->paperback()->magazine()->create();
        $attributes = ['name' => 'ABCコミック', 'genre_id' => $label->genre->id];

        $this->login();

        $this->patchJson(route('label.update', $label), $attributes)
            ->assertSuccessful();
        $this->assertDatabaseHas('label_label_type', ['label_id' => $label->id]);

        $this->patchJson(route('label.update', $label), $attributes + ['type_ids' => null])
            ->assertSuccessful();
        $this->assertDatabaseMissing('label_label_type', ['label_id' => $label->id]);
    }

    /** @test */
    public function 不正なリクエストに対してエラーが発生すること(): void
    {
        $label = Label::factory()->create();
        $data = [
            'name' => 'ABC文庫',
            'url' => 'https://example.com/',
            'genre_id' => 'invalid',
        ];

        $this
            ->login()
            ->patchJson(route('label.update', $label), $data)
            ->assertJsonValidationErrors('genre_id');

        $this->assertDatabaseMissing('labels', $data);
    }
}
