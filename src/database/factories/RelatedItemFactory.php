<?php

namespace Database\Factories;

use App\Models\Book;
use App\Models\ExternalLink;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\RelatedItem>
 */
class RelatedItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $relatable = $this->relatable();

        return [
            'book_id' => Book::all()->random()->id,
            'relatable_type' => $relatable['map'],
            'relatable_id' => $relatable['class']::factory(),
            'description' => fake()->text(),
        ];
    }

    private function relatable()
    {
        return fake()->randomElement([
            ['class' => Book::class, 'map' => 'book'],
            ['class' => ExternalLink::class, 'map' => 'externalLink'],
        ]);
    }
}
