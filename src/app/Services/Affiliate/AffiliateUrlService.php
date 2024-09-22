<?php

namespace App\Services\Affiliate;

use App\Entities\Affiliate\AffiliateServiceProviderBase;
use App\Exceptions\Affiliate\AspConfigNotFoundException;
use App\Factories\Affiliate\AffiliateServiceProviderFactory;
use App\Models\BookStore;
use App\Models\EbookStore;
use App\Models\Site;
use App\ValueObjects\Url;

final class AffiliateUrlService
{
    public function __construct(
        private AffiliateServiceProviderFactory $aspFactory
    ) {
    }

    public function attemptToConvertUrl(Url $url, BookStore|EbookStore $store, Site $site): Url|false
    {
        $asp = $this->attemptToCreateAsp($store, $site);

        return $asp?->convertUrl($url) ?: false;
    }

    private function attemptToCreateAsp(BookStore|EbookStore $store, Site $site): ?AffiliateServiceProviderBase
    {
        try {
            return $this->aspFactory->createOrFail($store, $site);
        } catch (AspConfigNotFoundException $e) {
            return null;
        }
    }
}
