<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Genre>
 */
class GenreFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'name' => fake()->unique()->name(),
        ];
    }

    /**
     * ジャンルの成人向けフラグを変更する (デフォルト: true)
     */
    public function adult(bool $is_adult = true): static
    {
        return $this->state(['adult' => $is_adult]);
    }
}
