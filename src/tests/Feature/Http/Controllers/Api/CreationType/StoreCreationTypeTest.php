<?php

namespace Tests\Feature\Http\Controllers\Api\CreationType;

use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class StoreCreationTypeTest extends TestCase
{
    /** @test */
    public function 作家区分を作成できること(): void
    {
        $data = ['name' => '原作'];

        $response = $this
            ->login()
            ->postJson(route('api.creation-types.store'), $data);

        $response
            ->assertSuccessful()
            ->assertJson(fn (AssertableJson $json) => $json
                ->where('name', $data['name'])
                ->whereType('sort', 'integer')
            );
        $this->assertDatabaseHas('creation_types', $data);
    }

    /** @test */
    public function 不正なリクエストに対してバリデーションエラーが発生すること(): void
    {
        $data = ['name' => str_repeat('a', 256)];

        $response = $this
            ->login()
            ->postJson(route('api.creation-types.store'), $data);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors('name');
        $this->assertDatabaseMissing('creation_types', $data);
    }
}
