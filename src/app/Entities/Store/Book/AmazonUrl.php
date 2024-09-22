<?php

namespace App\Entities\Store\Book;

use App\Entities\Store\PurchaseUrlBase;
use App\Interfaces\CanGenerateUrlFromIsbn;
use App\ValueObjects\Isbn;
use App\ValueObjects\Url;

final class AmazonUrl extends PurchaseUrlBase implements CanGenerateUrlFromIsbn
{
    public function generateUrlFromIsbn(Isbn $isbn): Url
    {
        $isbn10 = $isbn->convert13To10()->value();

        return new Url("https://www.amazon.co.jp/exec/obidos/ASIN/{$isbn10}");
    }
}
