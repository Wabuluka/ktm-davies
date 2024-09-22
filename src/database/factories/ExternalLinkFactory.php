<?php

namespace Database\Factories;

use App\Models\ExternalLink;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Http\UploadedFile;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ExternalLink>
 */
class ExternalLinkFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'title' => fake()->name(),
            'url' => 'https://example.com/' . fake()->unique()->word(),
        ];
    }

    /**
     * 外部リンクにサムネイルを追加する
     */
    public function attachThumbnail(string|UploadedFile|\Closure $imagePath): static
    {
        return $this->afterCreating(fn (ExternalLink $link) => $link
            ->addMedia(is_callable($imagePath) ? $imagePath() : $imagePath)
            ->preservingOriginal()
            ->toMediaCollection('thumbnail'));
    }
}
