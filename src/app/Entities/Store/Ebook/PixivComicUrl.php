<?php

namespace App\Entities\Store\Ebook;

use App\Entities\Store\PurchaseUrlBase;
use App\Interfaces\CanGenerateUrlFromBookTitle;
use App\ValueObjects\BookTitle;
use App\ValueObjects\Url;

final class PixivComicUrl extends PurchaseUrlBase implements CanGenerateUrlFromBookTitle
{
    public function generateUrlFromBookTitle(BookTitle $title): Url
    {
        return new Url("https://comic.pixiv.net/search?q={$title}&tab=store");
    }
}
