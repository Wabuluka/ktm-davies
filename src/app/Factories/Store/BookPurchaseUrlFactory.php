<?php

namespace App\Factories\Store;

use App\Entities\Store\Book\AmazonUrl;
use App\Entities\Store\Book\EhonUrl;
use App\Entities\Store\Book\KinokuniyaUrl;
use App\Entities\Store\Book\MelonbooksUrl;
use App\Entities\Store\Book\RakutenUrl;
use App\Entities\Store\Book\SevenNetUrl;
use App\Entities\Store\Book\TsutayaUrl;
use App\Entities\Store\Book\YodobashiUrl;
use App\Entities\Store\PurchaseUrlBase;
use App\Exceptions\Store\InvalidSiteStorePairException;
use App\Models\BookStore;
use App\Models\Site;
use App\Traits\HandleConfig;

final class BookPurchaseUrlFactory
{
    use HandleConfig;

    /**
     * @throws InvalidSiteStorePairException
     */
    public function createOrFail(BookStore $bookStore, Site $site): PurchaseUrlBase
    {
        return match ($bookStore->store->code) {
            'amazon' => new AmazonUrl(),
            'rakuten' => new RakutenUrl(),
            '7net' => new SevenNetUrl(),
            'yodobashi' => new YodobashiUrl(),
            'tsutaya' => new TsutayaUrl(),
            'kinokuniya' => new KinokuniyaUrl(),
            'ehon' => new EhonUrl(),
            'melonbooks' => new MelonbooksUrl(
                $this->getConfigAsNullableString("bookstore.{$site->code}.melonbooks.params.maker_id"),
            ),
            default => throw new InvalidSiteStorePairException(),
        };
    }
}
