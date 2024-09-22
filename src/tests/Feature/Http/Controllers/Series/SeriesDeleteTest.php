<?php

namespace Tests\Feature\Http\Controllers\Series;

use App\Models\Series;
use Tests\TestCase;

class SeriesDeleteTest extends TestCase
{
    /** @test */
    public function シリーズを削除できること(): void
    {
        $series = Series::factory()->create();
        $response = $this->login()->delete(route('series.destroy', $series));
        $response->assertStatus(200);
        $this->assertModelMissing($series);
    }

    /** @test */
    public function ログインしなければシリーズを削除できないこと(): void
    {
        $series = Series::factory()->create();
        $response = $this->delete(route('series.destroy', $series));
        $response->assertRedirect('/login');
        $this->assertModelExists($series);
    }

    /** @test */
    public function 書籍に紐付いたシリーズを削除できないこと(): void
    {
        $series = Series::factory()->hasBooks()->create();
        $response = $this->login()->delete(route('series.destroy', $series));
        $response->assertStatus(422)
            ->assertJson([
                'message' => __('validation.not_in_use', ['attribute' => 'シリーズ']),
                'errors' => ['series' => [__('validation.not_in_use', ['attribute' => 'シリーズ'])]],
            ]);
        $this->assertModelExists($series);
    }
}
