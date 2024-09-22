<?php

namespace Tests\Unit\Entities\Affiliate;

use App\Entities\Affiliate\ValueCommerceProvider;
use App\ValueObjects\Url;
use Tests\TestCase;

class ValueCommerceProviderTest extends TestCase
{
    private ValueCommerceProvider $provider;

    protected function setUp(): void
    {
        $this->provider = new ValueCommerceProvider('sid', 'pid');
    }

    /** @test */
    public function URLをバリューコマースのアフィリエイトURLに変換して返却すること(): void
    {
        $expected = 'https://ck.jp.ap.valuecommerce.com/servlet/referral?sid=sid&pid=pid&vc_url=https%3A%2F%2Fexample.com%2Fsearch%3Fq%3DBook%2B1';
        $actual = (string) $this->provider->convertUrl(
            new Url('https://example.com/search?q=Book 1')
        );
        $this->assertSame($expected, $actual);
    }
}
