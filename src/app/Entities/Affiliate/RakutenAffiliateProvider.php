<?php

namespace App\Entities\Affiliate;

use App\ValueObjects\Url;

final class RakutenAffiliateProvider extends AffiliateServiceProviderBase
{
    public function __construct(private string $id, private ?string $keisokuId = null)
    {
    }

    public function convertUrl(Url $url): Url
    {
        $path = $this->keisokuId
            ? "{$this->id}/{$this->keisokuId}"
            : $this->id;

        return new Url("https://hb.afl.rakuten.co.jp/hgc/{$path}?pc={$url->encode()}");
    }
}
