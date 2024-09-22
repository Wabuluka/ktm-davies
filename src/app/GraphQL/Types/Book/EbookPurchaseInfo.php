<?php

namespace App\GraphQL\Types\Book;

use App\Models\Book;
use App\Models\EbookStore;
use App\Models\Site;
use App\Services\Affiliate\AffiliateUrlService;
use App\Services\Store\EbookPurchaseUrlService;
use App\ValueObjects\Url;
use Illuminate\Support\Collection;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

final class EbookPurchaseInfo
{
    public function __construct(
        private EbookPurchaseUrlService $ebookPurchaseUrlService,
        private AffiliateUrlService $affiliateUrlService,
    ) {
    }

    public function all(Book $book, array $_args, GraphQLContext $context): Collection
    {
        $site = $this->findSiteOrFail($context);
        $purchaseInfo = $book->ebookstores->map(fn (EbookStore $ebookStore) => $this->generatePurchaseInfo($book, $ebookStore, $site));

        return $purchaseInfo;
    }

    public function primary(Book $book, array $_args, GraphQLContext $context): ?array
    {
        if (! $primaryStore = $book->primaryEbookStore) {
            return null;
        }
        $site = $this->findSiteOrFail($context);
        $purchaseInfo = $this->generatePurchaseInfo($book, $primaryStore, $site);

        return $purchaseInfo;
    }

    private function generatePurchaseInfo(Book $book, EbookStore $ebookStore, Site $site)
    {
        return [
            'store' => $ebookStore->store,
            'banner' => $ebookStore->banner,
            'isPrimary' => $ebookStore->pivot->is_primary,
            'purchaseUrl' => $this->generatePurchaseUrl($book, $ebookStore, $site),
        ];
    }

    private function generatePurchaseUrl(Book $book, EbookStore $ebookStore, Site $site): Url
    {
        $explicitUrl = $ebookStore->pivot?->url;
        $purchaseUrl = $explicitUrl
            ? $this->ebookPurchaseUrlService->optimizeUrl($explicitUrl, $ebookStore, $site)
            : $this->ebookPurchaseUrlService->generateUrl($book, $ebookStore, $site);

        return $this->affiliateUrlService->attemptToConvertUrl($purchaseUrl, $ebookStore, $site)
            ?: $purchaseUrl;
    }

    private function findSiteOrFail(GraphQLContext $context): Site
    {
        $sites = cache()->driver('array')->rememberForever('sites', fn () => Site::all());
        $siteId = data_get($context->request->request->all(), 'variables.siteId') ?: throw new \Exception('Not found siteId.');

        return $sites->find($siteId) ?: throw new \Exception("siteId: {$siteId} is invalid.");
    }
}
