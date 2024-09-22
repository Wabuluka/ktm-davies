<?php

namespace App\Entities\Affiliate;

use App\ValueObjects\Url;

final class AmazonAssociateProvider extends AffiliateServiceProviderBase
{
    public function __construct(private string $associateid)
    {

    }

    public function convertUrl(Url $url): Url
    {
        str_contains($url, '/ASIN/')
            ? preg_match('/\/exec\/obidos\/ASIN\/(?<asinOrIsbn10>[A-Z0-9]+)(\/|$)/', $url->value(), $matches)
            : preg_match('/\/dp\/(?<asinOrIsbn10>[A-Z0-9]+)(\/|$)/', $url->value(), $matches);

        if (isset($matches['asinOrIsbn10'])) {
            return $url->setPath("/exec/obidos/ASIN/{$matches['asinOrIsbn10']}/{$this->associateid}");
        }

        return $url->addSearchParams(['tag' => $this->associateid]);
    }
}
