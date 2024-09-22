<?php

namespace Database\Factories;

use App\Models\Benefit;
use App\Models\GoodsStore;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Http\UploadedFile;
use Spatie\Image\Image;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Benefit>
 */
class BenefitFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'name' => fake()->randomElement([
                'アクリルキーホルダー',
                'イラストカード',
                'クリアファイル',
                'ステッカー',
                'タペストリー',
                'リーフレット',
                '掛け替えカバー',
                '缶バッジ',
            ]),
            'store_id' => GoodsStore::factory(),
        ];
    }

    /**
     * 店舗特典の有償フラグを変更する (デフォルト: true)
     */
    public function paid(bool $paid = true): static
    {
        return $this->state(['paid' => $paid]);
    }

    /**
     * 店舗特典にサムネイルを追加する
     */
    public function attachThumbnail(string|UploadedFile|\Closure $imagePath): static
    {
        return $this->afterCreating(function (Benefit $benefit) use ($imagePath) {
            $file = is_callable($imagePath) ? $imagePath() : $imagePath;
            $image = is_string($file) ? Image::load($file) : Image::load($file->getPathname());
            $witdh = $image->getWidth();
            $height = $image->getHeight();

            $benefit
                ->addMedia($file)
                ->withCustomProperties(['width' => $witdh, 'height' => $height])
                ->preservingOriginal()
                ->toMediaCollection('thumbnail');
        });
    }
}
