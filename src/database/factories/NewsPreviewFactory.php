<?php

namespace Database\Factories;

use App\Models\News;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\NewsPreview>
 */
class NewsPreviewFactory extends Factory
{
    public function definition(): array
    {
        return [
            'token' => Str::uuid(),
            'model' => News::factory()->make(),
            'file_paths' => [],
        ];
    }
}
