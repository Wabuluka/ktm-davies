<?php

namespace Database\Factories;

use App\Enums\BlockType;
use App\Models\Book;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Spatie\Image\Image;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Book>
 */
class BookFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition()
    {
        $status = ['is_draft' => false, 'published_at' => now()]; // published

        $description = sprintf('
            <p>%s</p>
            <p>%s</p>',
            fake()->realText(200),
            fake()->realText(50),
        );

        return [
            'title' => fake()->words(3, true),
            'title_kana' => fake()->name(),
            'volume' => fake()->numberBetween(1, 10),
            ...$status,
            'isbn13' => fake()->isbn13(),
            'price' => fake()->randomElement([500, 750, 1000, 2000]),
            'release_date' => fake()->dateTimeBetween('-1 year', '+2 months'),
            'ebook_only' => fake()->boolean(20),
            'special_edition' => fake()->boolean(20),
            'limited_edition' => fake()->boolean(20),
            'adult' => fake()->boolean(50),
            'caption' => fake()->realText(50),
            'description' => $description,
            'keywords' => implode(',', fake()->words(5)),
            'trial_url' => 'https://example.com/trial/' . fake()->word(),
            'survey_url' => 'https://example.com/survey/' . fake()->word(),
        ];
    }

    /**
     * 書籍のステータスを「下書き」にする
     */
    public function draft(): static
    {
        return $this->state(['is_draft' => true]);
    }

    /**
     * 書籍のステータスを「公開予定」にする
     */
    public function willBePublished(\DateTimeInterface $publishedAt = null): static
    {
        $publishedAt ??= now()->addMonth();
        now()->lte($publishedAt) ?: throw new \Exception('公開予定日は現在時刻よりも先にしてください。');

        return $this->state(['is_draft' => false, 'published_at' => $publishedAt]);
    }

    /**
     * 書籍のステータスを「公開中」にする
     */
    public function published(): static
    {
        return $this->state(['is_draft' => false, 'published_at' => now()]);
    }

    /**
     * 書籍の電子限定フラグを変更する (デフォルト: true)
     */
    public function ebookOnly(bool $is_ebook_only = true): static
    {
        return $this->state(['ebook_only' => $is_ebook_only]);
    }

    /**
     * 書籍の特装版フラグを変更する (デフォルト: true)
     */
    public function specialEdition(bool $is_special_edition = true): static
    {
        return $this->state(['special_edition' => $is_special_edition]);
    }

    /**
     * 書籍の限定版フラグを変更する (デフォルト: true)
     */
    public function limitedEdition(bool $is_limited_edition = true): static
    {
        return $this->state(['limited_edition' => $is_limited_edition]);
    }

    /**
     * 書籍の成人向けフラグを変更する (デフォルト: true)
     */
    public function adult(bool $is_adult = true): static
    {
        return $this->state(['adult' => $is_adult]);
    }

    /**
     * 書籍に書影を追加する
     */
    public function attachCover(string|UploadedFile|\Closure $imagePath): static
    {
        return $this->afterCreating(function (Book $book) use ($imagePath) {
            $file = is_callable($imagePath) ? $imagePath() : $imagePath;
            $image = is_string($file) ? Image::load($file) : Image::load($file->getPathname());
            $witdh = $image->getWidth();
            $height = $image->getHeight();

            $book
                ->addMedia($file)
                ->withCustomProperties(['width' => $witdh, 'height' => $height])
                ->preservingOriginal()
                ->toMediaCollection('cover');
        });
    }

    /**
     * 書籍に表示設定を追加する
     */
    public function attachBlocks($randomOrder = false): static
    {
        $blocks = collect([
            ['type_id' => BlockType::Common, 'custom_title' => null, 'custom_content' => null, 'displayed' => true],
            ['type_id' => BlockType::BookStore, 'custom_title' => null, 'custom_content' => null],
            ['type_id' => BlockType::EbookStore, 'custom_title' => null, 'custom_content' => null],
            ['type_id' => BlockType::Benefit, 'custom_title' => null, 'custom_content' => null],
            ['type_id' => BlockType::Series, 'custom_title' => null, 'custom_content' => null],
            ['type_id' => BlockType::Related, 'custom_title' => null, 'custom_content' => null],
            ['type_id' => BlockType::Story, 'custom_title' => null, 'custom_content' => null],
            ['type_id' => BlockType::Character, 'custom_title' => null, 'custom_content' => null],
            ['type_id' => BlockType::Custom],
        ])
            ->when($randomOrder, fn (Collection $blocks) => $blocks->shuffle())
            ->each(fn (array $block, int $index) => [...$block, 'sort' => $index + 1]);

        return $this->hasBlocks(9, new Sequence(...$blocks));
    }
}
