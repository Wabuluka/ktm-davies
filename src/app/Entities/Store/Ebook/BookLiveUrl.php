<?php

namespace App\Entities\Store\Ebook;

use App\Entities\Store\PurchaseUrlBase;
use App\Interfaces\CanGenerateUrlFromBookTitle;
use App\Interfaces\CanOptimizeAnotherUrl;
use App\ValueObjects\BookTitle;
use App\ValueObjects\Url;

final class BookLiveUrl extends PurchaseUrlBase implements CanGenerateUrlFromBookTitle, CanOptimizeAnotherUrl
{
    public function __construct(
        private ?string $categoryIds = null,
        private ?string $genreIds = null,
        private ?string $keisaishiIds = null,
        private ?string $publisherIds = null,
        private array $searchParams = [],
    ) {
    }

    public function generateUrlFromBookTitle(BookTitle $title): Url
    {
        $path = 'keyword';
        if ($this->categoryIds) {
            $path .= "/category_ids/{$this->categoryIds}";
        }
        if ($this->genreIds) {
            $path .= "/g_ids/{$this->genreIds}";
        }
        if ($this->keisaishiIds) {
            $path .= "/k_ids/{$this->keisaishiIds}";
        }
        if ($this->publisherIds) {
            $path .= "/p_ids/{$this->publisherIds}";
        }
        $keyword = rawurlencode(
            $title
                ->replaceToWhiteSpace(BookTitle::ORTHOGRAPHICAL_VARIANTS)
                ->removeVolume()
                ->value()
        );

        return (new Url("https://booklive.jp/search/{$path}/keyword/{$keyword}/exfasc/1"))
            ->addSearchParams($this->searchParams);
    }

    public function optimizeAnotherUrl(Url $url): Url
    {
        return $url->addSearchParams($this->searchParams);
    }
}
