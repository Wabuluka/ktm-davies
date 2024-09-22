<?php

namespace App\Entities\Store\Ebook;

use App\Entities\Store\PurchaseUrlBase;
use App\Interfaces\CanGenerateUrlFromBookTitle;
use App\ValueObjects\BookTitle;
use App\ValueObjects\Url;

final class MechaComicUrl extends PurchaseUrlBase implements CanGenerateUrlFromBookTitle
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

        return (new Url("https://mechacomic.jp/books?text={$keyword}"))
            ->addSearchParams($this->searchParams);
    }
}
