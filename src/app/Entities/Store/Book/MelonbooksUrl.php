<?php

namespace App\Entities\Store\Book;

use App\Entities\Store\PurchaseUrlBase;
use App\Interfaces\CanGenerateUrlFromBookTitle;
use App\ValueObjects\BookTitle;
use App\ValueObjects\Url;

final class MelonbooksUrl extends PurchaseUrlBase implements CanGenerateUrlFromBookTitle
{
    public function __construct(
        private ?string $makerId = null,
    ) {
    }

    public function generateUrlFromBookTitle(BookTitle $title): Url
    {
        if ($this->makerId) {
            return new Url("https://www.melonbooks.co.jp/maker/index.php?maker_id={$this->makerId}&pageno=1&search_target[]=1&name={$title}");
        } else {
            return new Url("https://www.melonbooks.co.jp/search/search.php?search_target[]=1&name={$title}");
        }
    }
}
