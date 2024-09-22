<?php

namespace App\Entities\Store\Ebook;

use App\Entities\Store\PurchaseUrlBase;
use App\Interfaces\CanGenerateUrlFromBookTitle;
use App\ValueObjects\BookTitle;
use App\ValueObjects\Url;

final class EbookJapanUrl extends PurchaseUrlBase implements CanGenerateUrlFromBookTitle
{
    public function __construct(
        private array $searchParams = [],
    ) {
    }

    public function generateUrlFromBookTitle(BookTitle $title): Url
    {
        $keyword = $title
            ->replaceToWhiteSpace(BookTitle::ORTHOGRAPHICAL_VARIANTS)
            ->removeVolume()
            ->value();

        return (new Url("https://bookstore.yahoo.co.jp/search?keyword={$keyword}"))
            ->addSearchParams($this->searchParams);
    }
}
