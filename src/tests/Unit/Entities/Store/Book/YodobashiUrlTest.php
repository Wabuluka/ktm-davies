<?php

namespace Tests\Unit\Entities\Store\Book;

use App\Entities\Store\Book\YodobashiUrl;
use App\ValueObjects\Isbn;
use Tests\TestCase;

class YodobashiUrlTest extends TestCase
{
    /** @test */
    public function generateUrlFromIsbn_ISBNを元に、商品ページのURLを生成できること(): void
    {
        $purchaseUrl = new YodobashiUrl();
        $isbn = new Isbn('9784799218020');
        $this->assertSame(
            'https://www.yodobashi.com/category/81001/?word=9784799218020',
            (string) $purchaseUrl->generateUrlFromIsbn($isbn)
        );
    }
}
