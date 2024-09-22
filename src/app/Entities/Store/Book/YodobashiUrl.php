<?php

namespace App\Entities\Store\Book;

use App\Entities\Store\PurchaseUrlBase;
use App\Interfaces\CanGenerateUrlFromIsbn;
use App\ValueObjects\Isbn;
use App\ValueObjects\Url;

final class YodobashiUrl extends PurchaseUrlBase implements CanGenerateUrlFromIsbn
{
    public function generateUrlFromIsbn(Isbn $isbn): Url
    {
        return new Url("https://www.yodobashi.com/category/81001/?word={$isbn}");
    }
}
