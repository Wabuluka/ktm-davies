<?php

namespace Tests\Feature\Http\Controllers\Api\Story;

use App\Models\Creator;
use App\Models\Story;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Tests\TestCase;

class UpdateStoryTest extends TestCase
{
    private Story $story;

    private array $params;

    private string $requestTo;

    protected function setUp(): void
    {
        parent::setUp();

        $story = Story::factory()->create();
        $this->story = $story;
        $this->params = [
            'title' => $story->title . '_updated',
            'trial_url' => $story->trial_url . '_updated',
            'thumbnail' => [
                'operation' => 'stay',
            ],
            'creators' => [],
        ];
        $this->requestTo = route('stories.update', $story);
    }

    /** @test */
    public function 未ログイン状態であればログイン画面にリダイレクトすること(): void
    {
        $this
            ->putJson($this->requestTo, $this->params)
            ->assertUnauthorized();
        $arrtibutes = $this->story->only(['title', 'trial_url']);
        $this->assertDatabaseHas(Story::class, $arrtibutes);
    }

    /** @test */
    public function ログイン状態であれば収録作品を作成できること(): void
    {
        $this->login()
            ->putJson($this->requestTo, $params = $this->params)
            ->assertSuccessful();

        $arrtibutes = Arr::only($params, ['title', 'trial_url']);
        $this->assertDatabaseHas(Story::class, $arrtibutes);
    }

    /** @test */
    public function サムネイルを紐付けて更新できること(): void
    {
        $params = [
            'thumbnail' => [
                'operation' => 'set',
                'file' => UploadedFile::fake()->image('thumb.jpg', 80, 120),
            ],
        ] + $this->params;

        $this->login()
            ->putJson($this->requestTo, $params)
            ->assertSuccessful();

        $arrtibutes = Arr::only($params, ['title', 'trial_url']);
        $this->assertDatabaseHas(Story::class, $arrtibutes);
        $this->assertDatabaseHas(Media::class, [
            'model_type' => 'story',
            'model_id' => $this->story->id,
            'file_name' => 'thumb.jpg',
            'custom_properties->width' => 80,
            'custom_properties->height' => 120,
        ]);
    }

    /** @test */
    public function サムネイルを削除できること(): void
    {
        $params = [
            'thumbnail' => [
                'operation' => 'delete',
            ],
        ] + $this->params;

        $this->login()
            ->putJson($this->requestTo, $params)
            ->assertSuccessful();

        $arrtibutes = Arr::only($params, ['title', 'trial_url']);
        $this->assertDatabaseHas(Story::class, $arrtibutes);
        $this->assertDatabaseMissing(Media::class, [
            'model_type' => 'story',
            'model_id' => $this->story->id,
        ]);
    }

    /** @test */
    public function 作家情報を更新できること(): void
    {
        [$creator1, $creator2, $creator3] = Creator::factory(3)->create();
        $this->story->creators()->sync([
            $creator1->id => ['sort' => 1],
            $creator2->id => ['sort' => 2],
        ]);
        $params = [
            'creators' => [
                ['id' => $creator3->id, 'sort' => 1],
                ['id' => $creator1->id, 'sort' => 2],
            ],
        ] + $this->params;

        $this->login()
            ->putJson($this->requestTo, $params)
            ->assertSuccessful();

        $arrtibutes = Arr::only($params, ['title', 'trial_url']);
        $this->assertDatabaseHas(Story::class, $arrtibutes);
        $this->assertDatabaseHas('creator_story', ['story_id' => $this->story->id, 'creator_id' => $creator3->id, 'sort' => 1]);
        $this->assertDatabaseHas('creator_story', ['story_id' => $this->story->id, 'creator_id' => $creator1->id, 'sort' => 2]);
        $this->assertDatabaseMissing('creator_story', ['story_id' => $this->story->id, 'creator_id' => $creator2->id]);
    }

    /** @test */
    public function 作家情報を0件にできること(): void
    {
        [$creator1, $creator2] = Creator::factory(2)->create();
        $this->story->creators()->sync([
            $creator1->id => ['sort' => 1],
            $creator2->id => ['sort' => 2],
        ]);
        $params = [
            'creators' => [],
        ] + $this->params;

        $this->login()
            ->putJson($this->requestTo, $params)
            ->assertSuccessful();

        $arrtibutes = Arr::only($params, ['title', 'trial_url']);
        $this->assertDatabaseHas(Story::class, $arrtibutes);
        $this->assertCount(0, $this->story->refresh()->creators);
    }
}
