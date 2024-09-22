<?php

namespace Tests\Unit\Entities\Affiliate;

use App\Entities\Affiliate\AmazonAssociateProvider;
use App\ValueObjects\Url;
use Tests\TestCase;

class AmazonAssociateProviderTest extends TestCase
{
    private AmazonAssociateProvider $provider;

    protected function setUp(): void
    {
        $this->provider = new AmazonAssociateProvider('associate-22');
    }

    /** @test */
    public function obidos形式のURLをAmazonアソシエイトのURLに変換して返却すること(): void
    {
        $expected = 'https://www.amazon.co.jp/exec/obidos/ASIN/4799218026/associate-22';
        $actual = (string) $this->provider->convertUrl(
            new Url('https://www.amazon.co.jp/exec/obidos/ASIN/4799218026')
        );
        $this->assertSame($expected, $actual);

        $expected = 'https://www.amazon.co.jp/exec/obidos/ASIN/4799218026/associate-22?foo=bar#fragment';
        $actual = (string) $this->provider->convertUrl(
            new Url('https://www.amazon.co.jp/exec/obidos/ASIN/4799218026/?foo=bar#fragment')
        );
        $this->assertSame($expected, $actual);
    }

    /** @test */
    public function dp形式のURLをAmazonアソシエイトのURLに変換して返却すること(): void
    {
        $expected = 'https://www.amazon.co.jp/exec/obidos/ASIN/4799218026/associate-22';
        $actual = (string) $this->provider->convertUrl(
            new Url('https://www.amazon.co.jp/dp/4799218026')
        );
        $this->assertSame($expected, $actual);

        $expected = 'https://www.amazon.co.jp/exec/obidos/ASIN/4799218026/associate-22?foo=bar#fragment';
        $actual = (string) $this->provider->convertUrl(
            new Url('https://www.amazon.co.jp/書籍名/dp/4799218026/?foo=bar#fragment')
        );
        $this->assertSame($expected, $actual);
    }
}
