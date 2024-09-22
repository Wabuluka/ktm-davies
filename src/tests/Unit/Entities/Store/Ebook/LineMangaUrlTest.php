<?php

namespace Tests\Unit\Entities\Store\Ebook;

use App\Entities\Store\Ebook\LineMangaUrl;
use App\ValueObjects\BookTitle;
use Tests\TestCase;

class LineMangaUrlTest extends TestCase
{
    /** @test */
    public function generateUrlFromBookTitle_書籍名で商品を検索したページのURLを生成できること(): void
    {
        $purchaseUrl = new LineMangaUrl();
        $bookTitle = new BookTitle('Book');
        $this->assertSame(
            'https://manga.line.me/search_product/list?word=Book',
            (string) $purchaseUrl->generateUrlFromBookTitle($bookTitle)
        );
    }

    /** @test */
    public function generateUrlFromBookTitle_キーワードがURLエンコードされること(): void
    {
        $purchaseUrl = new LineMangaUrl();
        $bookTitle = new BookTitle('Book 1');
        $this->assertSame(
            'https://manga.line.me/search_product/list?word=Book+1',
            (string) $purchaseUrl->generateUrlFromBookTitle($bookTitle),
        );
    }

    /** @test */
    public function generateUrlFromBookTitle_書籍のタイトル末尾の関数がキーワードから削除されること(): void
    {
        $purchaseUrl = new LineMangaUrl();
        $bookTitle = new BookTitle('Book 1', '1');
        $this->assertSame(
            'https://manga.line.me/search_product/list?word=Book',
            (string) $purchaseUrl->generateUrlFromBookTitle($bookTitle),
        );
    }

    /** @test */
    public function generateUrlFromBookTitle_titleが表記揺れしやすい文字を含む場合キーワードから削除されること(): void
    {
        $purchaseUrl = new LineMangaUrl();
        $bookTitle = new BookTitle('Book　THE COMIC');
        $this->assertSame(
            'https://manga.line.me/search_product/list?word=Book',
            (string) $purchaseUrl->generateUrlFromBookTitle($bookTitle)
        );
    }
}
