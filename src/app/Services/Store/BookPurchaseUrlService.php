<?php

namespace App\Services\Store;

use App\Entities\Store\PurchaseUrlBase;
use App\Exceptions\Store\InvalidSiteStorePairException;
use App\Factories\Store\BookPurchaseUrlFactory;
use App\Interfaces\CanGenerateUrlFromBookTitle;
use App\Interfaces\CanGenerateUrlFromIsbn;
use App\Interfaces\CanOptimizeAnotherUrl;
use App\Models\Book;
use App\Models\BookStore;
use App\Models\Site;
use App\ValueObjects\BookTitle;
use App\ValueObjects\Isbn;
use App\ValueObjects\Url;
use Illuminate\Support\Facades\Log;

final class BookPurchaseUrlService
{
    public function __construct(
        private BookPurchaseUrlFactory $purchaseUrlFactory
    ) {
    }

    /**
     * 紙書店の購入先 URL を生成する
     */
    public function generateUrl(Book $book, BookStore $bookStore, Site $site): Url
    {
        $purchaseUrl = $this->attemptToCreatePurchaseUrl($bookStore, $site);
        if ($purchaseUrl instanceof CanGenerateUrlFromIsbn && $book?->isbn13) {
            $isbn = new Isbn($book->isbn13);

            return $purchaseUrl->generateUrlFromIsbn($isbn);
        }
        if ($purchaseUrl instanceof CanGenerateUrlFromBookTitle) {
            $title = new BookTitle($book->title, $book?->volume);

            return $purchaseUrl->generateUrlFromBookTitle($title);
        }

        // フォールバックとして書店トップページの URL を返す
        Log::warning('紙書店と書籍の情報から URL を生成できませんでした。', [
            'store' => $bookStore->store->only(['id', 'name', 'code']),
            'site' => $site->only(['id', 'name', 'code']),
            'book' => $book->only(['id', 'title', 'volume', 'isbn13']),
        ]);

        return new Url($bookStore->store->url);
    }

    /**
     * 購入先 URL を最適化する
     *   (例: アフィリエイト用のクエリパラメータを追加する)
     */
    public function optimizeUrl(string $url, BookStore $bookStore, Site $site): Url
    {
        $purchaseUrl = $this->attemptToCreatePurchaseUrl($bookStore, $site);
        $url = new Url($url);
        if ($purchaseUrl instanceof CanOptimizeAnotherUrl) {
            return $purchaseUrl->optimizeAnotherUrl($url);
        }

        return $url;
    }

    private function attemptToCreatePurchaseUrl(BookStore $bookStore, Site $site): ?PurchaseUrlBase
    {
        try {
            return $this->purchaseUrlFactory->createOrFail($bookStore, $site);
        } catch (InvalidSiteStorePairException $e) {
            Log::warning('書店に対応する購入先 URL 生成クラスが見つかりませんでした');

            return null;
        }
    }
}
