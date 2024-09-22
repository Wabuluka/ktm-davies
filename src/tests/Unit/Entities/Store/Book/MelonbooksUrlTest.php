<?php

namespace Tests\Unit\Entities\Store\Book;

use App\Entities\Store\Book\MelonbooksUrl;
use App\ValueObjects\BookTitle;
use Tests\TestCase;

class MelonbooksUrlTest extends TestCase
{
    /** @test */
    public function generateUrlFromBookTitle_makerIdと書籍名で商品を検索したページのURLを生成できること(): void
    {
        $purchaseUrl = new MelonbooksUrl('899');
        $booktitle = new BookTitle('Book 1');
        $this->assertSame(
            'https://www.melonbooks.co.jp/maker/index.php?maker_id=899&pageno=1&search_target%5B0%5D=1&name=Book+1',
            (string) $purchaseUrl->generateUrlFromBookTitle($booktitle)
        );
    }

    /** @test */
    public function generateUrlFromBookTitle_makerIdが未設定の場合は書籍名で商品を検索したページのURLを生成できること(): void
    {
        $purchaseUrl = new MelonbooksUrl('');
        $booktitle = new BookTitle('Book 1');
        $this->assertSame(
            'https://www.melonbooks.co.jp/search/search.php?search_target%5B0%5D=1&name=Book+1',
            (string) $purchaseUrl->generateUrlFromBookTitle($booktitle)
        );
    }
}
