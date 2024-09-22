<?php

namespace App\GraphQL\Types\Book;

use App\Models\Book;
use App\Models\BookStore;
use App\Models\Site;
use App\Services\Affiliate\AffiliateUrlService;
use App\Services\Store\BookPurchaseUrlService;
use App\ValueObjects\Url;
use Illuminate\Support\Collection;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

final class BookPurchaseInfo
{
    public function __construct(
        private BookPurchaseUrlService $bookPurchaseUrlService,
        private AffiliateUrlService $affiliateUrlService,
    ) {
    }

    public function all(Book $book, array $_args, GraphQLContext $context): Collection
    {
        $site = $this->findSiteOrFail($context);
        $purchaseInfo = $book->bookstores->map(fn (BookStore $bookStore) => $this->generatePurchaseInfo($book, $bookStore, $site));

        return $purchaseInfo;
    }

    public function primary(Book $book, array $_args, GraphQLContext $context): ?array
    {
        if (! $primaryStore = $book->primaryBookStore) {
            return null;
        }
        $site = $this->findSiteOrFail($context);
        $purchaseInfo = $this->generatePurchaseInfo($book, $primaryStore, $site);

        return $purchaseInfo;
    }

    private function generatePurchaseInfo(Book $book, BookStore $bookStore, Site $site)
    {
        return [
            'store' => $bookStore->store,
            'banner' => $bookStore->banner,
            'isPrimary' => $bookStore->pivot->is_primary,
            'purchaseUrl' => $this->generatePurchaseUrl($book, $bookStore, $site),
        ];
    }

    private function generatePurchaseUrl(Book $book, BookStore $bookStore, Site $site): Url
    {
        $explicitUrl = $bookStore->pivot?->url;
        $purchaseUrl = $explicitUrl
            ? $this->bookPurchaseUrlService->optimizeUrl($explicitUrl, $bookStore, $site)
            : $this->bookPurchaseUrlService->generateUrl($book, $bookStore, $site);

        return $this->affiliateUrlService->attemptToConvertUrl($purchaseUrl, $bookStore, $site)
            ?: $purchaseUrl;
    }

    private function findSiteOrFail(GraphQLContext $context): Site
    {
        $sites = cache()->driver('array')->rememberForever('sites', fn () => Site::all());
        $siteId = data_get($context->request->request->all(), 'variables.siteId') ?: throw new \Exception('Not found siteId.');

        return $sites->find($siteId) ?: throw new \Exception("siteId: {$siteId} is invalid.");
    }
}
