<?php

namespace App\Entities\Store\Ebook;

use App\Entities\Store\PurchaseUrlBase;
use App\Interfaces\CanGenerateUrlFromBookTitle;
use App\ValueObjects\BookTitle;
use App\ValueObjects\Url;

final class RentaUrl extends PurchaseUrlBase implements CanGenerateUrlFromBookTitle
{
    public function __construct(
        private ?string $publisher = null,
    ) {
    }

    public function generateUrlFromBookTitle(BookTitle $title): Url
    {
        $word = $title->replaceToWhiteSpace(BookTitle::ORTHOGRAPHICAL_VARIANTS)
            ->removeVolume()
            ->convertToEucJp()
            ->value();

        $encodedWord = urlencode($word);
        $url = new Url("https://renta.papy.co.jp/renta/sc/frm/search?&word={$encodedWord}");

        if ($this->publisher) {
            $url->addSearchParams(['publisher' => $this->publisher]);
        }

        return $url;
    }
}
