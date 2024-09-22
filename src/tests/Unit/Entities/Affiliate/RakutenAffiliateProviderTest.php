<?php

namespace Tests\Unit\Entities\Affiliate;

use App\Entities\Affiliate\RakutenAffiliateProvider;
use App\ValueObjects\Url;
use Tests\TestCase;

class RakutenAffiliateProviderTest extends TestCase
{
    private RakutenAffiliateProvider $provider;

    private RakutenAffiliateProvider $providerWithoutKeisokuId;

    protected function setUp(): void
    {
        $this->provider = new RakutenAffiliateProvider('id', 'keisokuId');
        $this->providerWithoutKeisokuId = new RakutenAffiliateProvider('id');
    }

    /** @test */
    public function URLを楽天アフィリエイトのURLに変換して返却すること(): void
    {
        $url = new Url('https://books.rakuten.co.jp/search?sitem=Book+1');
        $this->assertSame(
            'https://hb.afl.rakuten.co.jp/hgc/id/keisokuId?pc=https%3A%2F%2Fbooks.rakuten.co.jp%2Fsearch%3Fsitem%3DBook%2B1',
            (string) $this->provider->convertUrl($url),
        );
    }

    /** @test */
    public function 計測IDがなくても、URLを楽天アフィリエイトのURLに変換して返却できること(): void
    {
        $url = new Url('https://books.rakuten.co.jp/search?sitem=Book+1');
        $this->assertSame(
            'https://hb.afl.rakuten.co.jp/hgc/id?pc=https%3A%2F%2Fbooks.rakuten.co.jp%2Fsearch%3Fsitem%3DBook%2B1',
            (string) $this->providerWithoutKeisokuId->convertUrl($url),
        );
    }
}
