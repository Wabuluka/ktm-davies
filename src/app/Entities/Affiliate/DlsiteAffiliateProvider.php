<?php

namespace App\Entities\Affiliate;

use App\ValueObjects\Url;

final class DlsiteAffiliateProvider extends AffiliateServiceProviderBase
{
    public function __construct(private string $id)
    {
    }

    public function convertUrl(Url $url): Url
    {
        $search = 'work/=/product_id';
        $replace = "dlaf/=/link/work/aid/{$this->id}/id";

        $replaced = str_replace($search, $replace, $url->value());

        return new Url($replaced);
    }
}
