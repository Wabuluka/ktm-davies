<?php

namespace Tests\Feature\Http\Controllers\Api\Creator;

use App\Models\Creator;
use Tests\TestCase;

class UpdateCreatorTest extends TestCase
{
    /** @test */
    public function 作家を更新できること(): void
    {
        $initialData = [
            'name' => 'creator',
            'name_kana' => 'クリエイター',
        ];
        $creator = Creator::create($initialData);
        $newData = [
            'name' => 'creator (Updated)',
            'name_kana' => 'クリエイター (更新済み)',
        ];

        $response = $this
            ->login()
            ->putJson(route('api.creators.update', $creator), $newData);

        $response->assertSuccessful();
        $this->assertDatabaseHas('creators', $newData);
        $this->assertDatabaseMissing('creators', $initialData);
    }

    /** @test */
    public function ログインしなければ作家を更新できないこと(): void
    {
        $initialData = [
            'name' => 'creator',
            'name_kana' => 'クリエイター',
        ];
        $creator = Creator::create($initialData);
        $newData = [
            'name' => 'creator (Updated)',
            'name_kana' => 'クリエイター (更新済み)',
        ];

        $response = $this
            ->putJson(route('api.creators.update', $creator), $newData);

        $response->assertUnauthorized();
        $this->assertDatabaseHas('creators', $initialData);
        $this->assertDatabaseMissing('creators', $newData);
    }

    /** @test */
    public function 不正なリクエストに対してバリデーションエラーが発生すること(): void
    {
        $initialData = [
            'name' => 'creator',
            'name_kana' => 'クリエイター',
        ];
        $creator = Creator::create($initialData);
        $newData = [
            'name' => '',
        ];

        $response = $this
            ->login()
            ->putJson(route('api.creators.update', $creator), $newData);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors('name');
        $this->assertDatabaseHas('creators', $initialData);
        $this->assertDatabaseMissing('creators', $newData);
    }
}
