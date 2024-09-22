<?php

namespace Tests\Feature\Http\Controllers\Api\Creator;

use App\Models\Book;
use App\Models\CreationType;
use App\Models\Creator;
use Tests\TestCase;

class DestroyCreatorTest extends TestCase
{
    /** @test */
    public function 作家を削除できること(): void
    {
        $creator = Creator::factory()->create();

        $response = $this
            ->login()
            ->deleteJson(route('api.creators.destroy', $creator));

        $response->assertSuccessful();
        $this->assertModelMissing($creator);
    }

    /** @test */
    public function ログインしなければ作家を削除できないこと(): void
    {
        $creator = Creator::factory()->create();

        $response = $this
            ->deleteJson(route('api.creators.destroy', $creator));

        $response->assertUnauthorized();
        $this->assertModelExists($creator);
    }

    /** @test */
    public function 書籍に紐付いた作家を削除できないこと(): void
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
            ->deleteJson(route('api.creators.destroy', $creator));

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['creator' => ['この作家に属するデータが既に存在します。']]);
        $this->assertModelExists($book);
        $this->assertModelExists($creationType);
        $this->assertModelExists($creator);
    }
}
