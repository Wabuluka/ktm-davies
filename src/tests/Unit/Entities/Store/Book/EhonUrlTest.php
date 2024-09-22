<?php

namespace Tests\Unit\Entities\Store\Book;

use App\Entities\Store\Book\EhonUrl;
use App\ValueObjects\Isbn;
use Tests\TestCase;

class EhonUrlTest extends TestCase
{
    /** @test */
    public function generateUrlFromIsbn_ISBNを元に、商品ページのURLを生成できること(): void
    {
        $purchaseUrl = new EhonUrl();
        $isbn = new Isbn('9784799218020');
        $this->assertSame(
            'https://www.e-hon.ne.jp/bec/SA/Forward?isbn=9784799218020&mode=kodawari&button=btnKodawari',
            (string) $purchaseUrl->generateUrlFromIsbn($isbn)
        );
    }
}
