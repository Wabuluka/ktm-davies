<?php

namespace Tests\Unit\Entities\Store\Ebook;

use App\Entities\Store\Ebook\EbookJapanUrl;
use App\ValueObjects\BookTitle;
use Tests\TestCase;

class EbookJapanUrlTest extends TestCase
{
    /** @test */
    public function generateUrlFromBookTitle_タイトルを元に、キーワード検索URLを生成できること(): void
    {
        $purchaseUrl = new EbookJapanUrl();
        $title = new BookTitle('Book');
        $this->assertSame(
            'https://bookstore.yahoo.co.jp/search?keyword=Book',
            (string) $purchaseUrl->generateUrlFromBookTitle($title)
        );
    }

    /** @test */
    public function generateUrlFromBookTitle_表記揺れが起きやすい文字列がキーワードから削除されること(): void
    {
        $purchaseUrl = new EbookJapanUrl();
        $title = new BookTitle('Book THE COMIC');
        $this->assertSame(
            'https://bookstore.yahoo.co.jp/search?keyword=Book',
            (string) $purchaseUrl->generateUrlFromBookTitle($title)
        );
    }

    /** @test */
    public function generateUrlFromBookTitle_書籍のタイトル末尾の関数がキーワードから削除されること(): void
    {
        $purchaseUrl = new EbookJapanUrl();
        $bookTitle = new BookTitle('Book 1', '1');
        $this->assertSame(
            'https://bookstore.yahoo.co.jp/search?keyword=Book',
            (string) $purchaseUrl->generateUrlFromBookTitle($bookTitle),
        );
    }

    /** @test */
    public function generateUrlFromBookTitle_URLにクエリパラメータが追加されること(): void
    {
        $searchParams = [
            'publisher' => '400486',
        ];
        $purchaseUrl = new EbookJapanUrl($searchParams);
        $title = new BookTitle('Book');
        $this->assertSame(
            'https://bookstore.yahoo.co.jp/search?keyword=Book&publisher=400486',
            (string) $purchaseUrl->generateUrlFromBookTitle($title)
        );
    }
}
