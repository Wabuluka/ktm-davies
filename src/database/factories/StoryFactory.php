<?php

namespace Database\Factories;

use App\Models\Story;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Http\UploadedFile;
use Spatie\Image\Image;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Story>
 */
class StoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'title' => fake()->name(),
            'trial_url' => 'https://example.com/trial/' . fake()->word(),
        ];
    }

    /**
     * サムネイルを追加する
     */
    public function attachThumbnail(string|UploadedFile|\Closure $imagePath): static
    {
        return $this->afterCreating(function (Story $story) use ($imagePath) {
            $file = is_callable($imagePath) ? $imagePath() : $imagePath;
            $image = is_string($file) ? Image::load($file) : Image::load($file->getPathname());
            $witdh = $image->getWidth();
            $height = $image->getHeight();

            $story
                ->addMedia($file)
                ->withCustomProperties(['width' => $witdh, 'height' => $height])
                ->preservingOriginal()
                ->toMediaCollection('thumbnail');
        });
    }
}
