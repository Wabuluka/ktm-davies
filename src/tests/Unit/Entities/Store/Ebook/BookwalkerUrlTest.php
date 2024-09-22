<?php

namespace Tests\Unit\Entities\Store\Ebook;

use App\Entities\Store\Ebook\BookwalkerUrl;
use App\ValueObjects\BookTitle;
use Tests\TestCase;

class BookwalkerUrlTest extends TestCase
{
    /** @test */
    public function generateUrlFromBookTitle_書籍名で商品を検索したページのURLを生成できること(): void
    {
        $purchaseUrl = new BookwalkerUrl('116');
        $booktitle = new BookTitle('Book 1');
        $this->assertSame(
            'https://bookwalker.jp/company/116/?word=Book+1',
            (string) $purchaseUrl->generateUrlFromBookTitle($booktitle)
        );
    }

    /** @test */
    public function generateUrlFromBookTitle_companyが未設定の場合は書籍名で商品を検索したページのURLを生成できること(): void
    {
        $purchaseUrl = new BookwalkerUrl('');
        $booktitle = new BookTitle('Book 1');
        $this->assertSame(
            'https://bookwalker.jp/search/?qcat=&word=Book+1',
            (string) $purchaseUrl->generateUrlFromBookTitle($booktitle)
        );
    }

    /** @test */
    public function generateUrlFromBookTitle_titleが表記揺れしやすい文字を含む場合でも書籍名を検索したページのURLを生成できること(): void
    {
        $purchaseUrl = new BookwalkerUrl('116');
        $title = new BookTitle('Book　THE COMIC 10', '10');
        $this->assertSame(
            'https://bookwalker.jp/company/116/?word=Book',
            (string) $purchaseUrl->generateUrlFromBookTitle($title)
        );
    }
}
