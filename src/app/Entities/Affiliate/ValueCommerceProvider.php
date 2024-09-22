<?php

namespace App\Entities\Affiliate;

use App\ValueObjects\Url;

final class ValueCommerceProvider extends AffiliateServiceProviderBase
{
    public function __construct(private string $sid, private string $pid)
    {
    }

    public function convertUrl(Url $url): Url
    {
        return new Url("https://ck.jp.ap.valuecommerce.com/servlet/referral?sid={$this->sid}&pid={$this->pid}&vc_url={$url->encode()}");
    }
}
