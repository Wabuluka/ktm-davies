<?php

namespace App\Traits\Http;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Intervention\Image\Exception\NotReadableException;
use Spatie\Image\Image;

trait HandleRequestParameters
{
    /**
     * スペース区切りのキーワードを Illuminate\Support\Cullection に変換する
     */
    protected function parseSpaceSeparatedKeywords(string $keyword, int $limit = 5): Collection
    {
        return Str::of($keyword)
            ->replaceMatches('/[　\s]+/u', ' ')
            ->trim()
            ->explode(' ', $limit);
    }

    /**
     * 画像の幅と高さを取得する
     *
     * @return array{width: int|null, height: int|null}
     */
    public function getImageDimensions(UploadedFile $uploaded): array
    {
        $image = Image::load($uploaded->getPathname());

        try {
            // getWidth() や getHeight() は .svg などをサポートしていないので NotReadableException を投げる。
            return ['width' => $image->getWidth(), 'height' => $image->getHeight()];
        } catch (NotReadableException $_e) {
            // 画像が読み込めないか、サポートされていない場合は、$width と $height を null のままにする。
            return ['width' => null, 'height' => null];
        }
    }
}
