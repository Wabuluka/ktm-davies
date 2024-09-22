<?php

namespace Tests\Feature\Http\Controllers\Api\Creator;

use App\Models\Creator;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class ShowCreatorTest extends TestCase
{
    /** @test */
    public function 作家を取得できること(): void
    {
        $creator = Creator::factory()->create();

        $response = $this
            ->login()
            ->getJson(route('api.creators.show', $creator));

        $response
            ->assertSuccessful()
            ->assertJson(fn (AssertableJson $json) => $json
                ->where('id', $creator->id)
                ->where('name', $creator->name)
                ->where('name_kana', $creator->name_kana)
                ->etc()
            );
    }
}
