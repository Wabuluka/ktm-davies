<?php

namespace Database\Factories;

use App\Models\Character;
use App\Models\Series;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Http\UploadedFile;
use Spatie\Image\Image;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Character>
 */
class CharacterFactory extends Factory
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
            'description' => fake()->text(),
            'series_id' => Series::factory(),
        ];
    }

    /**
     * サムネイルを追加する
     */
    public function attachThumbnail(string|UploadedFile|\Closure $imagePath): static
    {
        return $this->afterCreating(function (Character $character) use ($imagePath) {
            $file = is_callable($imagePath) ? $imagePath() : $imagePath;
            $image = is_string($file) ? Image::load($file) : Image::load($file->getPathname());
            $witdh = $image->getWidth();
            $height = $image->getHeight();

            $character
                ->addMedia($file)
                ->withCustomProperties(['width' => $witdh, 'height' => $height])
                ->preservingOriginal()
                ->toMediaCollection('thumbnail');
        });
    }
}
