<?php

namespace Database\Factories;

use App\Models\Page;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PagePreview>
 */
class PagePreviewFactory extends Factory
{
    public function definition(): array
    {
        return [
            'token' => fake()->uuid(),
            'model' => Page::factory(),
        ];
    }
}
