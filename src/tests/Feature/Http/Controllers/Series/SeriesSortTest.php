<?php

namespace Tests\Feature\Http\Controllers\Series;

use App\Models\Series;
use Tests\TestCase;

class SeriesSortTest extends TestCase
{
    /** @test */
    public function 指定したシリーズの並び順を一つ上げること(): void
    {
        $series1 = Series::create(['name' => 'series1']);
        $series2 = Series::create(['name' => 'series2']);

        $response = $this->login()->patch(route('series.move_up', $series2));
        $response->assertStatus(200);

        $this->assertDatabaseHas('series', ['name' => 'series1', 'sort' => 2]);
        $this->assertDatabaseHas('series', ['name' => 'series2', 'sort' => 1]);
    }

    /** @test */
    public function 指定したシリーズの並び順を一つ下げること(): void
    {
        $series1 = Series::create(['name' => 'series1']);
        $series2 = Series::create(['name' => 'series2']);

        $response = $this->login()->patch(route('series.move_down', $series1));
        $response->assertStatus(200);

        $this->assertDatabaseHas('series', ['name' => 'series1', 'sort' => 2]);
        $this->assertDatabaseHas('series', ['name' => 'series2', 'sort' => 1]);
    }
}
