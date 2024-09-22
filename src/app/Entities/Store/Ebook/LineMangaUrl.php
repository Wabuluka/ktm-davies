<?php

namespace App\Entities\Store\Ebook;

use App\Entities\Store\PurchaseUrlBase;
use App\Interfaces\CanGenerateUrlFromBookTitle;
use App\ValueObjects\BookTitle;
use App\ValueObjects\Url;

final class LineMangaUrl extends PurchaseUrlBase implements CanGenerateUrlFromBookTitle
{
    public function generateUrlFromBookTitle(BookTitle $title): Url
    {
        $word = $title
            ->replaceToWhiteSpace(BookTitle::ORTHOGRAPHICAL_VARIANTS)
            ->removeVolume()
            ->value();

        return new Url("https://manga.line.me/search_product/list?word={$word}");
    }
}
