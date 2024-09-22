<?php

namespace App\Entities\Store\Book;

use App\Entities\Store\PurchaseUrlBase;
use App\Interfaces\CanGenerateUrlFromIsbn;
use App\ValueObjects\Isbn;
use App\ValueObjects\Url;

final class TsutayaUrl extends PurchaseUrlBase implements CanGenerateUrlFromIsbn
{
    public function generateUrlFromIsbn(Isbn $isbn): Url
    {
        return new Url("https://shop.tsutaya.co.jp/book/product/{$isbn}/");
    }
}
