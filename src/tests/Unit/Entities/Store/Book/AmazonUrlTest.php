<?php

namespace Tests\Unit\Entities\Store\Book;

use App\Entities\Store\Book\AmazonUrl;
use App\ValueObjects\Isbn;
use Tests\TestCase;

class AmazonUrlTest extends TestCase
{
    /** @test */
    public function generateUrlFromIsbn_ISBNを元に、商品ページのURLを生成できること(): void
    {
        $purchaseUrl = new AmazonUrl();
        $isbn = new Isbn('9784799218020');
        $this->assertSame(
            'https://www.amazon.co.jp/exec/obidos/ASIN/4799218026',
            (string) $purchaseUrl->generateUrlFromIsbn($isbn)
        );
    }
}
