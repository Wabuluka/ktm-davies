<?php

namespace App\Entities\Store\Ebook;

use App\Entities\Store\PurchaseUrlBase;
use App\Interfaces\CanGenerateUrlFromBookTitle;
use App\ValueObjects\BookTitle;
use App\ValueObjects\Url;

final class BookwalkerUrl extends PurchaseUrlBase implements CanGenerateUrlFromBookTitle
{
    public function __construct(
        private ?string $company = null,
    ) {
    }

    public function generateUrlFromBookTitle(BookTitle $title): Url
    {
        $word = $title->removeVolume()
            ->replaceToWhiteSpace(BookTitle::ORTHOGRAPHICAL_VARIANTS)
            ->value();
        if ($this->company) {
            return new Url("https://bookwalker.jp/company/{$this->company}/?word={$word}");
        } else {
            return new Url("https://bookwalker.jp/search/?qcat=&word={$word}");
        }
    }
}
