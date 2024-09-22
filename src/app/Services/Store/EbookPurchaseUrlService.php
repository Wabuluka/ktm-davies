<?php

namespace App\Services\Store;

use App\Entities\Store\PurchaseUrlBase;
use App\Exceptions\Store\InvalidSiteStorePairException;
use App\Factories\Store\EbookPurchaseUrlFactory;
use App\Interfaces\CanGenerateUrlFromBookTitle;
use App\Interfaces\CanGenerateUrlFromIsbn;
use App\Interfaces\CanOptimizeAnotherUrl;
use App\Models\Book;
use App\Models\EbookStore;
use App\Models\Site;
use App\ValueObjects\BookTitle;
use App\ValueObjects\Isbn;
use App\ValueObjects\Url;
use Illuminate\Support\Facades\Log;

final class EbookPurchaseUrlService
{
    public function __construct(
        private EbookPurchaseUrlFactory $purchaseUrlFactory
    ) {
    }

    /**
     * 電子書店の購入先 URL を生成する
     */
    public function generateUrl(Book $book, EbookStore $ebookStore, Site $site): Url
    {
        $purchaseUrl = $this->attemptToCreatePurchaseUrl($ebookStore, $site);
        if ($purchaseUrl instanceof CanGenerateUrlFromIsbn) {
            $isbn = new Isbn($book->isbn13);

            return $purchaseUrl->generateUrlFromIsbn($isbn);
        }
        if ($purchaseUrl instanceof CanGenerateUrlFromBookTitle) {
            $title = new BookTitle($book->title, $book->volume);

            return $purchaseUrl->generateUrlFromBookTitle($title);
        }

        // フォールバックとして書店トップページの URL を返す
        Log::warning('電子書店と書籍の情報から URL を生成できませんでした。', [
            'store' => $ebookStore->store->only(['id', 'name', 'code']),
            'site' => $site->only(['id', 'name', 'code']),
            'book' => $book->only(['id', 'title', 'volume', 'isbn13']),
        ]);

        return new Url($ebookStore->store->url);
    }

    /**
     * 購入先 URL を最適化する
     *   (例: アフィリエイト用のクエリパラメータを追加する)
     */
    public function optimizeUrl(string $url, EbookStore $ebookStore, Site $site): Url
    {
        $purchaseUrl = $this->attemptToCreatePurchaseUrl($ebookStore, $site);
        $url = new Url($url);
        if ($purchaseUrl instanceof CanOptimizeAnotherUrl) {
            return $purchaseUrl->optimizeAnotherUrl($url);
        }

        return $url;
    }

    private function attemptToCreatePurchaseUrl(EbookStore $ebookStore, Site $site): ?PurchaseUrlBase
    {
        try {
            return $this->purchaseUrlFactory->createOrFail($ebookStore, $site);
        } catch (InvalidSiteStorePairException $e) {
            Log::warning('書店に対応する購入先 URL 生成クラスが見つかりませんでした');

            return null;
        }
    }
}
