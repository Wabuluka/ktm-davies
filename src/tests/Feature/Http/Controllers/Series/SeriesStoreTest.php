<?php

namespace Tests\Feature\Http\Controllers\Series;

use Tests\TestCase;

class SeriesStoreTest extends TestCase
{
    /** @test */
    public function シリーズを作成できること(): void
    {
        $data = [
            'name' => 'series',
        ];

        $response = $this->login()->post(route('series.store'), $data);
        $response->assertStatus(200);
        $this->assertDatabaseHas('series', $data);
    }

    /** @test */
    public function ログインしなければシリーズを作成できないこと(): void
    {
        $data = [
            'name' => 'series',
        ];

        $response = $this->post(route('series.store'), $data);
        $response->assertRedirect('/login');
        $this->assertDatabaseMissing('series', $data);
    }

    /** @test */
    public function sortに連番が自動的に設定されること(): void
    {
        $seriesData = [
            [
                'name' => 'series1',
            ],
            [
                'name' => 'series2',
            ],
            [
                'name' => 'series3',
            ],
        ];

        foreach ($seriesData as $data) {
            $response = $this->login()->post(route('series.store'), $data);
            $response->assertStatus(200);
        }
        $this->assertDatabaseHas('series', ['name' => 'series1', 'sort' => 1]);
        $this->assertDatabaseHas('series', ['name' => 'series2', 'sort' => 2]);
        $this->assertDatabaseHas('series', ['name' => 'series3', 'sort' => 3]);
    }
}
