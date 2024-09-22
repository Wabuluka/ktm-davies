<?php

namespace Tests\Feature\Http\Controllers\Api\CreationType;

use App\Models\Book;
use App\Models\CreationType;
use App\Models\Creator;
use Tests\TestCase;

class DestroyCreationTypeTest extends TestCase
{
    /** @test */
    public function 作家区分を削除できること(): void
    {
        $creationType = CreationType::factory()->create();

        $response = $this
            ->login()
            ->deleteJson(route('api.creation-types.destroy', $creationType));

        $response->assertSuccessful();
        $this->assertModelMissing($creationType);
    }

    /** @test */
    public function 作家に紐付いた作家区分を削除できないこと(): void
    {
        $book
            = Book::factory()->create();
        $creationType
            = CreationType::factory()->create();
        $creator
            = Creator::factory()
                ->hasAttached($book, [
                    'creation_type' => $creationType->name,
                    'sort' => 1,
                ])
                ->create();

        $response = $this
            ->login()
            ->deleteJson(route('api.creation-types.destroy', $creationType));

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['creationType' => ['この作家区分に属するデータが既に存在します。']]);
        $this->assertModelExists($book);
        $this->assertModelExists($creationType);
        $this->assertModelExists($creator);
    }
}
