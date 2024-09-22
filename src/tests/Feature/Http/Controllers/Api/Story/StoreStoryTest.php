<?php

namespace Tests\Feature\Http\Controllers\Api\Story;

use App\Models\Creator;
use App\Models\Story;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Tests\TestCase;

class StoreStoryTest extends TestCase
{
    private array $params;

    private string $requestTo;

    protected function setUp(): void
    {
        parent::setUp();

        $this->params = [
            'title' => fake()->title(),
            'trial_url' => fake()->url(),
            'thumbnail' => [
                'operation' => 'stay',
            ],
            'creators' => [],
        ];
        $this->requestTo = route('stories.store');
    }

    /** @test */
    public function 未ログイン状態であればログイン画面にリダイレクトすること(): void
    {
        $count = Story::count();
        $this
            ->postJson($this->requestTo, $this->params)
            ->assertUnauthorized();
        $this
            ->assertDatabaseCount(Story::class, $count);
    }

    /** @test */
    public function ログイン状態であれば収録作品を作成できること(): void
    {
        $this->login()
            ->postJson($this->requestTo, $params = $this->params)
            ->assertSuccessful();

        $arrtibutes = Arr::only($params, ['title', 'trial_url']);
        $this->assertDatabaseHas(Story::class, $arrtibutes);
    }

    /** @test */
    public function サムネイルを紐付けて作成できること(): void
    {
        $params = [
            'thumbnail' => [
                'operation' => 'set',
                'file' => UploadedFile::fake()->image('thumb.jpg', 80, 120),
            ],
        ] + $this->params;

        $this->login()
            ->postJson($this->requestTo, $params)
            ->assertSuccessful();

        $arrtibutes = Arr::only($params, ['title', 'trial_url']);
        $this->assertDatabaseHas(Story::class, $arrtibutes);
        $this->assertDatabaseHas(Media::class, [
            'model_type' => 'story',
            'file_name' => 'thumb.jpg',
            'custom_properties->width' => 80,
            'custom_properties->height' => 120,
        ]);
    }

    /** @test */
    public function 作家情報を紐付けて作成できること(): void
    {
        [$creator1, $creator2] = Creator::factory(2)->create();
        $params = [
            'creators' => [
                ['id' => $creator2->id, 'sort' => 1],
                ['id' => $creator1->id, 'sort' => 2],
            ],
        ] + $this->params;

        $this->login()
            ->postJson($this->requestTo, $params)
            ->assertSuccessful();

        $arrtibutes = Arr::only($params, ['title', 'trial_url']);
        $this->assertDatabaseHas(Story::class, $arrtibutes);
        $this->assertDatabaseHas('creator_story', ['creator_id' => $creator2->id, 'sort' => 1]);
        $this->assertDatabaseHas('creator_story', ['creator_id' => $creator1->id, 'sort' => 2]);
    }
}
