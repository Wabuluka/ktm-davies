<?php

namespace Database\Factories;

use App\Enums\LabelType as EnumsLabelType;
use App\Models\Genre;
use App\Models\LabelType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Label>
 */
class LabelFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'name' => fake()->name(),
            'url' => 'https://example.com/' . fake()->word(),
            'genre_id' => Genre::factory(),
        ];
    }

    public function paperback(array $pivot = []): static
    {
        return $this->hasAttached(LabelType::find(EnumsLabelType::Paperback), $pivot, 'types');
    }

    public function magazine(array $pivot = []): static
    {
        return $this->hasAttached(LabelType::find(EnumsLabelType::Magazine), $pivot, 'types');
    }

    public function goods(array $pivot = []): static
    {
        return $this->hasAttached(LabelType::find(EnumsLabelType::Goods), $pivot, 'types');
    }
}
