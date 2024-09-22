<?php

namespace Database\Factories;

use App\Models\Banner;
use App\Models\BannerPlacement;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Http\UploadedFile;
use Spatie\Image\Image;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Banner>
 */
class BannerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->word(),
            'url' => 'https://example.com/' . fake()->unique()->slug(),
            'new_tab' => fake()->boolean(),
            'displayed' => fake()->boolean(),
            'placement_id' => BannerPlacement::factory(),
        ];
    }

    /**
     * 表示フラグを変更する (デフォルト: true)
     */
    public function displayed(bool $displayed = true): static
    {
        return $this->state(['displayed' => $displayed]);
    }

    /**
     * バナー画像を追加する
     */
    public function attachImage(string|UploadedFile|\Closure $imagePath): static
    {
        return $this->afterCreating(function (Banner $banner) use ($imagePath) {
            $file = is_callable($imagePath) ? $imagePath() : $imagePath;
            $image = is_string($file) ? Image::load($file) : Image::load($file->getPathname());
            $witdh = $image->getWidth();
            $height = $image->getHeight();

            $banner
                ->addMedia($file)
                ->withCustomProperties(['width' => $witdh, 'height' => $height])
                ->preservingOriginal()
                ->toMediaCollection('image');
        });
    }
}
