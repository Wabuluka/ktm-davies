<?php

namespace Tests\Feature\Http\Controllers\Api\Creator;

use Tests\TestCase;

class StoreCreatorTest extends TestCase
{
    /** @test */
    public function 作家を作成できること(): void
    {
        $data = [
            'name' => 'creator',
            'name_kana' => 'クリエイター',
        ];

        $response = $this
            ->login()
            ->postJson(route('api.creators.store'), $data);

        $response->assertSuccessful();
        $this->assertDatabaseHas('creators', $data);
    }

    /** @test */
    public function ログインしなければ作家を作成できないこと(): void
    {
        $data = [
            'name' => 'creator',
            'name_kana' => 'クリエイター',
        ];

        $response = $this
            ->postJson(route('api.creators.store'), $data);

        $response->assertUnauthorized();
        $this->assertDatabaseMissing('creators', $data);
    }

    /** @test */
    public function 不正なリクエストに対してバリデーションエラーが発生すること(): void
    {
        $data = [
            'name' => '',
        ];

        $response = $this
            ->login()
            ->postJson(route('api.creators.store'), $data);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors('name');
        $this->assertDatabaseMissing('creators', $data);
    }
}
