<?php

namespace Tests\Feature\Http\Controllers\Api\CreationType;

use App\Models\CreationType;
use Tests\TestCase;

class IndexCreationTypeTest extends TestCase
{
    /** @test */
    public function 作家区分を一覧できること(): void
    {
        $creators = CreationType::factory(5)->create();

        $response = $this
            ->login()
            ->getJson(route('api.creation-types.index'));

        $fragment = $creators->collect()->only('name', 'sort')->toArray();
        $response
            ->assertSuccessful()
            ->assertJsonCount(5)
            ->assertJsonFragment($fragment);
    }
}
