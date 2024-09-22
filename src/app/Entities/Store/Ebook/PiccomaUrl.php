<?php

namespace App\Entities\Store\Ebook;

use App\Entities\Store\PurchaseUrlBase;
use App\Interfaces\CanGenerateUrlFromBookTitle;
use App\ValueObjects\BookTitle;
use App\ValueObjects\Url;

final class PiccomaUrl extends PurchaseUrlBase implements CanGenerateUrlFromBookTitle
{
    public function generateUrlFromBookTitle(BookTitle $title): Url
    {
        $word = $title->removeVolume()->value();

        return new Url("https://piccoma.com/web/search/result?word={$word}");
    }
}
