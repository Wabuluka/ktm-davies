<?php

namespace Tests\Unit\Entities\Store\Book;

use App\Entities\Store\Book\TsutayaUrl;
use App\ValueObjects\Isbn;
use Tests\TestCase;

class TsutayaUrlTest extends TestCase
{
    /** @test */
    public function generateUrlFromIsbn_ISBNを元に、商品ページのURLを生成できること(): void
    {
        $purchaseUrl = new TsutayaUrl();
        $isbn = new Isbn('9784799218020');
        $this->assertSame(
            'https://shop.tsutaya.co.jp/book/product/9784799218020/',
            (string) $purchaseUrl->generateUrlFromIsbn($isbn)
        );
    }
}
