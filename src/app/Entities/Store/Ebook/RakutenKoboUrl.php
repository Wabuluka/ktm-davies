<?php

namespace App\Entities\Store\Ebook;

use App\Entities\Store\PurchaseUrlBase;
use App\Interfaces\CanGenerateUrlFromBookTitle;
use App\ValueObjects\BookTitle;
use App\ValueObjects\Url;

final class RakutenKoboUrl extends PurchaseUrlBase implements CanGenerateUrlFromBookTitle
{
    public function __construct(
        private array $searchParams = [],
    ) {
    }

    public function generateUrlFromBookTitle(BookTitle $title): Url
    {
        $keyword = $title
            ->replaceToWhiteSpace(BookTitle::ORTHOGRAPHICAL_VARIANTS)
            ->orthographizeVolume();

        return (new Url("https://books.rakuten.co.jp/search?sitem={$keyword}"))
            ->addSearchParams($this->searchParams);
    }
}
