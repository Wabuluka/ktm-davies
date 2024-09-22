<?php

namespace Tests\Feature\Http\Controllers\Series;

use App\Models\Series;
use Tests\TestCase;

class SeriesUpdateTest extends TestCase
{
    /** @test */
    public function シリーズを更新できること(): void
    {
        $initialData = [
            'name' => 'series',
        ];
        $series = Series::create($initialData);

        $newData = [
            'name' => 'series (Updated)',
        ];

        $response = $this->login()->put(route('series.update', $series), $newData);
        $response->assertStatus(200);
        $this->assertDatabaseHas('series', $newData);
        $this->assertDatabaseMissing('series', $initialData);
    }

    /** @test */
    public function ログインしなければシリーズを更新できないこと(): void
    {
        $initialData = [
            'name' => 'series',
        ];
        $series = Series::create($initialData);

        $newData = [
            'name' => 'series (Updated)',
        ];

        $response = $this->put(route('series.update', $series), $newData);
        $response->assertRedirect('/login');
        $this->assertDatabaseHas('series', $initialData);
    }

    /** @test */
    public function バリデーションエラーが発生すること(): void
    {
        $initialData = [
            'name' => 'series',
        ];
        $series = Series::create($initialData);

        $newData = [
            'name' => '',
        ];

        $response = $this->login()->put(route('series.update', $series), $newData);
        $response->assertStatus(302)->assertSessionHasErrors('name');
        $this->assertDatabaseHas('series', $initialData);
        $this->assertDatabaseMissing('series', $newData);
    }
}
