<?php

namespace App\Entities\Store\Ebook;

use App\Entities\Store\PurchaseUrlBase;
use App\Interfaces\CanGenerateUrlFromBookTitle;
use App\ValueObjects\BookTitle;
use App\ValueObjects\Url;

final class CmoaUrl extends PurchaseUrlBase implements CanGenerateUrlFromBookTitle
{
    public function __construct(
        private ?string $publisherId = null,
        private array $searchParams = [],
    ) {
    }

    public function generateUrlFromBookTitle(BookTitle $title): Url
    {
        $word = $title
            ->replaceToWhiteSpace(BookTitle::ORTHOGRAPHICAL_VARIANTS)
            ->removeVolume()
            ->truncate(25)
            ->value();

        $url = (new Url('https://www.cmoa.jp/search/result/'))->addSearchParams(['header_word' => $word]);

        if ($this->publisherId) {
            $url->addSearchParams(['publisher_id' => $this->publisherId]);
        }
        if ($this->searchParams) {
            $url->addSearchParams($this->searchParams);
        }

        return $url;
    }
}
