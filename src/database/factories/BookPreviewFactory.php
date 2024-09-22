<?php

namespace Database\Factories;

use App\Models\Book;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\BookPreview>
 */
class BookPreviewFactory extends Factory
{
    public function definition(): array
    {
        return [
            'token' => fake()->uuid(),
            'model' => Book::factory()->make(),
            'file_paths' => [],
        ];
    }
}
