<?php

namespace Tests\Unit\Entities\Store\Ebook;

use App\Entities\Store\Ebook\KtcStoreUrl;
use App\ValueObjects\BookTitle;
use Tests\TestCase;

class KtcStoreUrlTest extends TestCase
{
    /** @test */
    public function generateUrlFromBookTitle_書籍名で商品を検索したページのURLを生成できること(): void
    {
        $purchaseUrl = new KtcStoreUrl();
        $booktitle = new BookTitle('Book 1');
        $this->assertSame(
            'https://ktc-store.com/products/list?name=Book+1',
            (string) $purchaseUrl->generateUrlFromBookTitle($booktitle)
        );
    }
}
