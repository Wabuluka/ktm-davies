<?php

namespace Tests\Unit\Entities\Affiliate;

use App\Entities\Affiliate\DlsiteAffiliateProvider;
use App\ValueObjects\Url;
use Tests\TestCase;

class DlsiteAffiliateProviderTest extends TestCase
{
    private DlsiteAffiliateProvider $provider;

    protected function setUp(): void
    {
        $this->provider = new DlsiteAffiliateProvider('k12345');
    }

    /** @test */
    public function DLSiteの作品詳細ページURLをアフィリエイトURLに変換して返却すること(): void
    {
        $expected = 'https://www.dlsite.com/books/dlaf/=/link/work/aid/k12345/id/BJ01170669.html';
        $actual = (string) $this->provider->convertUrl(
            new Url('https://www.dlsite.com/books/work/=/product_id/BJ01170669.html')
        );
        $this->assertSame($expected, $actual);
    }
}
