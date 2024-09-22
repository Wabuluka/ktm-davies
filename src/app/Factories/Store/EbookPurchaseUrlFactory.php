<?php

namespace App\Factories\Store;

use App\Entities\Store\Ebook\AmazonKindleUrl;
use App\Entities\Store\Ebook\BookLiveUrl;
use App\Entities\Store\Ebook\BookwalkerUrl;
use App\Entities\Store\Ebook\CmoaUrl;
use App\Entities\Store\Ebook\EbookJapanUrl;
use App\Entities\Store\Ebook\KtcStoreUrl;
use App\Entities\Store\Ebook\LineMangaUrl;
use App\Entities\Store\Ebook\MechaComicUrl;
use App\Entities\Store\Ebook\MelonbooksUrl;
use App\Entities\Store\Ebook\PiccomaUrl;
use App\Entities\Store\Ebook\PixivComicUrl;
use App\Entities\Store\Ebook\RakutenKoboUrl;
use App\Entities\Store\Ebook\RentaUrl;
use App\Entities\Store\PurchaseUrlBase;
use App\Exceptions\Store\InvalidSiteStorePairException;
use App\Models\EbookStore;
use App\Models\Site;
use App\Traits\HandleConfig;

final class EbookPurchaseUrlFactory
{
    use HandleConfig;

    /**
     * @throws InvalidSiteStorePairException
     */
    public function createOrFail(EbookStore $ebookStore, Site $site): PurchaseUrlBase
    {
        return match ($ebookStore->store->code) {
            'amazon' => new AmazonKindleUrl(
                $this->getConfigAsArray("ebookstore.{$site->code}.amazon.params.query"),
            ),
            'rakuten' => new RakutenKoboUrl(
                $this->getConfigAsArray("ebookstore.{$site->code}.rakuten.params.query"),
            ),
            'booklive' => new BookLiveUrl(
                $this->getConfigAsNullableString("ebookstore.{$site->code}.booklive.params.category_ids"),
                $this->getConfigAsNullableString("ebookstore.{$site->code}.booklive.params.g_ids"),
                $this->getConfigAsNullableString("ebookstore.{$site->code}.booklive.params.k_ids"),
                $this->getConfigAsNullableString("ebookstore.{$site->code}.booklive.params.p_ids"),
                $this->getConfigAsArray("ebookstore.{$site->code}.booklive.params.query"),
            ),
            'ebookjapan' => new EbookJapanUrl(
                $this->getConfigAsArray("ebookstore.{$site->code}.ebookjapan.params.query"),
            ),
            'bookwalker' => new BookwalkerUrl(
                $this->getConfigAsNullableString("ebookstore.{$site->code}.bookwalker.params.company"),
            ),
            'cmoa' => new CmoaUrl(
                $this->getConfigAsNullableString("ebookstore.{$site->code}.cmoa.params.publisher_id"),
                $this->getConfigAsArray("ebookstore.{$site->code}.cmoa.params.query"),
            ),
            'renta' => new RentaUrl(
                $this->getConfigAsNullableString("ebookstore.{$site->code}.renta.params.publisher"),
            ),
            'line' => new LineMangaUrl(),
            'mechacomic' => new MechaComicUrl(
                $this->getConfigAsArray("ebookstore.{$site->code}.mechacomic.params.query"),
            ),
            'piccoma' => new PiccomaUrl(),
            'melonbooks' => new MelonbooksUrl(
                $this->getConfigAsNullableString("ebookstore.{$site->code}.melonbooks.params.maker_id"),
            ),
            'pixivcomic' => new PixivComicUrl(),
            'ktcstore' => new KtcStoreUrl(),
            default => throw new InvalidSiteStorePairException(),
        };
    }
}
