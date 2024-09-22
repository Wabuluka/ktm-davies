<?php

namespace App\Entities\Store\Book;

use App\Entities\Store\PurchaseUrlBase;
use App\Interfaces\CanGenerateUrlFromIsbn;
use App\ValueObjects\Isbn;
use App\ValueObjects\Url;

final class RakutenUrl extends PurchaseUrlBase implements CanGenerateUrlFromIsbn
{
    public function generateUrlFromIsbn(Isbn $isbn): Url
    {
        return new Url("https://books.rakuten.co.jp/rdt/item/?sid=213310&sno=ISBN:{$isbn}");
    }
}
