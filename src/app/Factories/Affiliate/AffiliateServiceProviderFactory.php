<?php

namespace App\Factories\Affiliate;

use App\Entities\Affiliate\AffiliateServiceProviderBase;
use App\Entities\Affiliate\AmazonAssociateProvider;
use App\Entities\Affiliate\DlsiteAffiliateProvider;
use App\Entities\Affiliate\RakutenAffiliateProvider;
use App\Entities\Affiliate\ValueCommerceProvider;
use App\Exceptions\Affiliate\AspConfigNotFoundException;
use App\Models\BookStore;
use App\Models\EbookStore;
use App\Models\Site;
use App\Traits\HandleConfig;

final class AffiliateServiceProviderFactory
{
    use HandleConfig;

    /**
     * @throws AspConfigNotFoundException
     */
    public function createOrFail(BookStore|EbookStore $store, Site $site): AffiliateServiceProviderBase
    {
        [$aspName, $params] = $this->getAspNameAndParams($store, $site);

        return match ($aspName) {
            'amazon' => new AmazonAssociateProvider($params['id']),
            'rakuten' => new RakutenAffiliateProvider($params['id'], $params['keisokuId'] ?? null),
            'valuecommerce' => new ValueCommerceProvider($params['sid'], $params['pid']),
            'dlsite' => new DlsiteAffiliateProvider($params['id']),
            null => throw new AspConfigNotFoundException(),
        };
    }

    /**
     * @return array{0: ?string, 1: array}
     */
    private function getAspNameAndParams(BookStore|EbookStore $store, Site $site): array
    {
        return $store instanceof BookStore
            ? [
                $this->getConfigAsNullableString("bookstore.{$site->code}.{$store->store->code}.asp.name"),
                $this->getConfigAsArray("bookstore.{$site->code}.{$store->store->code}.asp.params"),
            ]
            : [
                $this->getConfigAsNullableString("ebookstore.{$site->code}.{$store->store->code}.asp.name"),
                $this->getConfigAsArray("ebookstore.{$site->code}.{$store->store->code}.asp.params"),
            ];
    }
}
