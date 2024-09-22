<?php

namespace Tests\Feature\Http\Controllers\Api\CreationType;

use App\Models\CreationType;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class SortCreationTypeTest extends TestCase
{
    /** @test */
    public function 指定した作家区分の並び順を一つ上げること(): void
    {
        $creationType1 = CreationType::create(['name' => 'creationType1', 'sort' => 1]);
        $creationType2 = CreationType::create(['name' => 'creationType2', 'sort' => 2]);

        $response = $this
            ->login()
            ->patchJson(route('api.creation-types.move_up', $creationType2));

        $response
            ->assertSuccessful()
            ->assertJson(fn (AssertableJson $json) => $json
                ->where('name', $creationType2->name)
                ->where('sort', 1)
                ->etc()
            );
        $this->assertDatabaseHas('creation_types', ['name' => $creationType1->name, 'sort' => 2]);
        $this->assertDatabaseHas('creation_types', ['name' => $creationType2->name, 'sort' => 1]);
    }

    /** @test */
    public function 指定した作家区分の並び順を一つ下げること(): void
    {
        $creationType1 = CreationType::create(['name' => 'creationType1', 'sort' => 1]);
        $creationType2 = CreationType::create(['name' => 'creationType2', 'sort' => 2]);

        $response = $this
            ->login()
            ->patchJson(route('api.creation-types.move_down', $creationType1));

        $response
            ->assertSuccessful()
            ->assertJson(fn (AssertableJson $json) => $json
                ->where('name', $creationType1->name)
                ->where('sort', 2)
                ->etc()
            );
        $this->assertDatabaseHas('creation_types', ['name' => $creationType1->name, 'sort' => 2]);
        $this->assertDatabaseHas('creation_types', ['name' => $creationType2->name, 'sort' => 1]);
    }
}
