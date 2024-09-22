<?php

namespace Tests\Feature\Http\Controllers\Api\CreationType;

use App\Models\CreationType;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class UpdateCreationTypeTest extends TestCase
{
    /** @test */
    public function 作家区分を更新できること(): void
    {
        $initialData = ['name' => '原作'];
        $creationType = CreationType::create($initialData);
        $newData = ['name' => '原作者'];

        $response = $this
            ->login()
            ->patchJson(route('api.creation-types.update', $creationType), $newData);

        $response
            ->assertSuccessful()
            ->assertJson(fn (AssertableJson $json) => $json
                ->where('name', $newData['name'])
                ->whereType('sort', 'integer')
                ->etc()
            );
        $this->assertDatabaseHas('creation_types', $newData);
        $this->assertDatabaseMissing('creation_types', $initialData);
    }

    /** @test */
    public function 不正なリクエストに対してバリデーションエラーが発生すること(): void
    {
        $initialData = ['name' => '原作'];
        $creationType = CreationType::create($initialData);
        $newData = ['name' => str_repeat('a', 256)];

        $response = $this
            ->login()
            ->patchJson(route('api.creation-types.update', $creationType), $newData);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors('name');
        $this->assertDatabaseHas('creation_types', $initialData);
        $this->assertDatabaseMissing('creation_types', $newData);
    }
}
