<?php

namespace Database\Factories;

use App\Models\News;
use App\Models\NewsCategory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Http\UploadedFile;
use Spatie\Image\Image;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\News>
 */
class NewsFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition()
    {
        $status = fake()->randomElement([
            ['is_draft' => true],
            ['is_draft' => false, 'published_at' => now()->addMonth()],
            ['is_draft' => false, 'published_at' => now()],
        ]);

        $content = sprintf(
            '
            <p>%s</p>
            <p><img src="%s" alt="cat"></p>
            <h2>その他</h2>
            <div>
                <ul>
                    <li>%s</li>
                    <li>%s</li>
                </ul>
            </div>
            ',
            fake()->realText(),
            'https://placekitten.com/' . fake()->numberBetween(200, 400) . '/' . fake()->numberBetween(200, 400),
            fake()->realText(),
            fake()->words(3, true),
            fake()->words(3, true),
        );

        return [
            'title' => fake()->title(),
            'slug' => fake()->unique()->slug(),
            ...$status,
            'content' => $content,
            'category_id' => NewsCategory::factory(),
        ];
    }

    /**
     * NEWS のステータスを「下書き」にする
     */
    public function draft(): static
    {
        return $this->state(['is_draft' => true]);
    }

    /**
     * NEWS のステータスを「公開予定」にする
     */
    public function willBePublished(\DateTimeInterface $publishedAt = null): static
    {
        $publishedAt ??= now()->addMonth();
        now()->lte($publishedAt) ?: throw new \Exception('公開予定日は現在時刻よりも先にしてください。');

        return $this->state(['is_draft' => false, 'published_at' => $publishedAt]);
    }

    /**
     * NEWS のステータスを「公開中」にする
     */
    public function published(): static
    {
        return $this->state(['is_draft' => false, 'published_at' => now()]);
    }

    /**
     * NEWS にアイキャッチ画像を設定する
     */
    public function attachEyecatch(string|UploadedFile|\Closure $imagePath): static
    {
        return $this->afterCreating(function (News $news) use ($imagePath) {
            $file = is_callable($imagePath) ? $imagePath() : $imagePath;
            $image = is_string($file) ? Image::load($file) : Image::load($file->getPathname());
            $width = $image->getWidth();
            $height = $image->getHeight();

            $news
                ->addMedia(is_callable($imagePath) ? $imagePath() : $imagePath)
                ->withCustomProperties(['width' => $width, 'height' => $height])
                ->preservingOriginal()
                ->toMediaCollection('eyecatch');
        });
    }
}
