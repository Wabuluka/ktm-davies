<?php

namespace App\Entities\Store\Book;

use App\Entities\Store\PurchaseUrlBase;
use App\Interfaces\CanGenerateUrlFromIsbn;
use App\ValueObjects\Isbn;
use App\ValueObjects\Url;

final class EhonUrl extends PurchaseUrlBase implements CanGenerateUrlFromIsbn
{
    public function generateUrlFromIsbn(Isbn $isbn): Url
    {
        return new Url("https://www.e-hon.ne.jp/bec/SA/Forward?isbn={$isbn}&mode=kodawari&button=btnKodawari");
    }
}
