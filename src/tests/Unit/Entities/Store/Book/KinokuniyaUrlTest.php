<?php

namespace Tests\Unit\Entities\Store\Book;

use App\Entities\Store\Book\KinokuniyaUrl;
use App\ValueObjects\Isbn;
use Tests\TestCase;

class KinokuniyaUrlTest extends TestCase
{
    /** @test */
    public function generateUrlFromIsbn_ISBNを元に、商品ページのURLを生成できること(): void
    {
        $purchaseUrl = new KinokuniyaUrl();
        $isbn = new Isbn('9784799218020');
        $this->assertSame(
            'https://www.kinokuniya.co.jp/f/dsg-01-9784799218020',
            (string) $purchaseUrl->generateUrlFromIsbn($isbn)
        );
    }
}
