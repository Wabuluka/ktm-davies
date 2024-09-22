<?php

namespace Database\Factories;

use App\Models\EbookStore;
use App\Models\Store;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Http\UploadedFile;
use Intervention\Image\Exception\NotReadableException;
use Spatie\Image\Image;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\EbookStore>
 */
class EbookStoreFactory extends Factory
{
    public function definition(): array
    {
        return [
            'store_id' => Store::factory(),
            'is_purchase_url_required' => false,
        ];
    }

    public function requirePurchaseUrl(bool $required = true): static
    {
        return $this->state(['is_purchase_url_required' => $required]);
    }

    /**
     * 電子書店にバナー画像を追加する
     */
    public function attachBanner(string|UploadedFile|\Closure $imagePath): static
    {
        return $this->afterCreating(function (EbookStore $ebookStore) use ($imagePath) {
            $file = is_callable($imagePath) ? $imagePath() : $imagePath;
            $image = is_string($file) ? Image::load($file) : Image::load($file->getPathname());
            try {
                $width = $image->getWidth();
                $height = $image->getHeight();
            } catch (NotReadableException $_e) {
                $width = null;
                $height = null;
            }

            $ebookStore
                ->addMedia($file)
                ->withCustomProperties(['width' => $width, 'height' => $height])
                ->preservingOriginal()
                ->toMediaCollection('banner');
        });
    }
}
