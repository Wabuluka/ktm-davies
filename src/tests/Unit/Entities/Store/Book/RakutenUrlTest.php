<?php

namespace Tests\Unit\Entities\Store\Book;

use App\Entities\Store\Book\RakutenUrl;
use App\ValueObjects\Isbn;
use Tests\TestCase;

class RakutenUrlTest extends TestCase
{
    /** @test */
    public function generateUrlFromIsbn_ISBNを元に、商品ページのURLを生成できること(): void
    {
        $purchaseUrl = new RakutenUrl();
        $isbn = new Isbn('9784799218020');
        $this->assertSame(
            'https://books.rakuten.co.jp/rdt/item/?sid=213310&sno=ISBN%3A9784799218020',
            (string) $purchaseUrl->generateUrlFromIsbn($isbn)
        );
    }
}
