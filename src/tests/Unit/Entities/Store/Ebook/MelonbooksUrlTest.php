<?php

namespace Tests\Unit\Entities\Store\Ebook;

use App\Entities\Store\Ebook\MelonbooksUrl;
use App\ValueObjects\BookTitle;
use Tests\TestCase;

class MelonbooksUrlTest extends TestCase
{
    /** @test */
    public function generateUrlFromBookTitle_makerIdと書籍名で商品を検索したページのURLを生成できること(): void
    {
        $purchaseUrl = new MelonbooksUrl('899');
        $booktitle = new BookTitle('コミックアンリアル Vol.1');
        $this->assertSame(
            'https://www.melonbooks.co.jp/maker/index.php?maker_id=899&pageno=1&search_target%5B0%5D=2&name=%E3%82%B3%E3%83%9F%E3%83%83%E3%82%AF%E3%82%A2%E3%83%B3%E3%83%AA%E3%82%A2%E3%83%AB+Vol.1',
            (string) $purchaseUrl->generateUrlFromBookTitle($booktitle)
        );
    }

    /** @test */
    public function generateUrlFromBookTitle_makerIdが未設定の場合は書籍名で商品を検索したページのURLを生成できること(): void
    {
        $purchaseUrl = new MelonbooksUrl('');
        $booktitle = new BookTitle('コミックアンリアル Vol.1');
        $this->assertSame(
            'https://www.melonbooks.co.jp/search/search.php?search_target%5B0%5D=2&name=%E3%82%B3%E3%83%9F%E3%83%83%E3%82%AF%E3%82%A2%E3%83%B3%E3%83%AA%E3%82%A2%E3%83%AB+Vol.1',
            (string) $purchaseUrl->generateUrlFromBookTitle($booktitle)
        );
    }
}
