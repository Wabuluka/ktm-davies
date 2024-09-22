<?php

namespace Tests\Feature\Http\Controllers\Api\Creator;

use App\Models\Creator;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class IndexCreatorTest extends TestCase
{
    /** @test */
    public function 作家を一覧できること(): void
    {
        $creators = Creator::factory(3)->create();

        $response = $this
            ->login()
            ->getJson(route('api.creators.index'));

        $fragment = $creators->collect()->only('name', 'name_kana')->toArray();
        $response
            ->assertSuccessful()
            ->assertJsonCount(3, 'data')
            ->assertJsonFragment($fragment);
    }

    /** @test */
    public function ログインしなければ作家を一蘭できないこと(): void
    {
        $response = $this
            ->getJson(route('api.creators.index'));

        $response->assertUnauthorized();
    }

    /** @test */
    public function 作成日の降順で作家を返却すること(): void
    {
        $this->travel(1)->days(fn () => Creator::factory()->create([
            'name' => 'Creator 1',
        ]));
        $this->travel(2)->days(fn () => Creator::factory()->create([
            'name' => 'Creator 2',
        ]));
        $this->travel(3)->days(fn () => Creator::factory()->create([
            'name' => 'Creator 3',
        ]));

        $response = $this
            ->login()
            ->getJson(route('api.creators.index'));

        $response->assertSeeInOrder([
            'Creator 3',
            'Creator 2',
            'Creator 1',
        ]);
    }

    /** @test */
    public function キーワード指定時は、作家名か作家名カナに合致する作家のみ返却すること(): void
    {
        $this->travel(1)->days(fn () => Creator::factory()->create([
            'name' => '浅田', 'name_kana' => 'アサダ',
        ]));
        $this->travel(2)->days(fn () => Creator::factory()->create([
            'name' => '朝田', 'name_kana' => 'アサダ',
        ]));
        $this->travel(3)->days(fn () => Creator::factory()->create([
            'name' => '麻田', 'name_kana' => 'アサダ',
        ]));

        $this->login();
        $this
            ->getJson(route('api.creators.index', ['keyword' => '浅田']))
            ->assertJson(fn (AssertableJson $json) => $json
                ->has('data', 1)
                ->where('data.0.name', '浅田')
                ->etc()
            );
        $this
            ->getJson(route('api.creators.index', ['keyword' => 'アサダ']))
            ->assertJson(fn (AssertableJson $json) => $json
                ->has('data', 3)
                ->where('data.0.name', '麻田')
                ->where('data.1.name', '朝田')
                ->where('data.2.name', '浅田')
                ->etc()
            );
    }

    /** @test */
    public function ページ毎に最大10件の作家を取得できること(): void
    {
        Creator::factory(11)->create();

        $this->login();
        $this
            ->getJson(route('api.creators.index', ['page' => 1]))
            ->assertJson(fn (AssertableJson $json) => $json
                ->where('current_page', 1)
                ->where('per_page', 10)
                ->where('total', 11)
                ->has('data', 10)
                ->etc()
            );
        $this
            ->getJson(route('api.creators.index', ['page' => 2]))
            ->assertJson(fn (AssertableJson $json) => $json
                ->where('current_page', 2)
                ->where('per_page', 10)
                ->where('total', 11)
                ->has('data', 1)
                ->etc()
            );
    }

    /** @test */
    public function 不正なリクエストに対してバリデーションエラーが発生すること(): void
    {
        $response = $this
            ->login()
            ->getJson(route('api.creators.index', [
                'keyword' => str_repeat('a', 256),
            ]));

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors('keyword');
    }
}
