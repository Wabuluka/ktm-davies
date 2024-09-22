<?php

namespace App\Entities\Store\Ebook;

use App\Entities\Store\PurchaseUrlBase;
use App\Interfaces\CanGenerateUrlFromBookTitle;
use App\Interfaces\CanGenerateUrlFromIsbn;
use App\ValueObjects\BookTitle;
use App\ValueObjects\Isbn;
use App\ValueObjects\Url;

final class AmazonKindleUrl extends PurchaseUrlBase implements CanGenerateUrlFromBookTitle, CanGenerateUrlFromIsbn
{
    public function __construct(
        private array $searchParams = [],
    ) {
    }

    public function generateUrlFromIsbn(Isbn $isbn): Url
    {
        $isbn10 = $isbn->convert13To10()->value();

        return (new Url("https://www.amazon.co.jp/s?k={$isbn10}"))
            ->addSearchParams($this->searchParams);
    }

    public function generateUrlFromBookTitle(BookTitle $title): Url
    {
        $keyword = rawurlencode(
            $title
                ->replaceToWhiteSpace(BookTitle::ORTHOGRAPHICAL_VARIANTS)
                ->orthographizeVolume()
                ->value()
        );

        return (new Url("https://www.amazon.co.jp/s?k={$keyword}"))
            ->addSearchParams($this->searchParams);
    }
}
