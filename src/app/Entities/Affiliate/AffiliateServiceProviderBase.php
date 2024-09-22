<?php

namespace App\Entities\Affiliate;

use App\ValueObjects\Url;

abstract class AffiliateServiceProviderBase
{
    abstract public function convertUrl(Url $url): Url;
}
