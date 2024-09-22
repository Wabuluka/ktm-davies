<?php

namespace Tests\Unit\Entities\Store\Book;

use App\Entities\Store\Book\SevenNetUrl;
use App\ValueObjects\Isbn;
use Tests\TestCase;

class SevenNetUrlTest extends TestCase
{
    /** @test */
    public function generateUrlFromIsbn_ISBNを元に、商品ページのURLを生成できること(): void
    {
        $purchaseUrl = new SevenNetUrl();
        $isbn = new Isbn('9784799218020');
        $this->assertSame(
            'https://7net.omni7.jp/detail_isbn/9784799218020',
            (string) $purchaseUrl->generateUrlFromIsbn($isbn)
        );
    }
}
