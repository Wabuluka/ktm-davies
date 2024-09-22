<?php

namespace Tests\Unit\Entities\Store\Ebook;

use App\Entities\Store\Ebook\CmoaUrl;
use App\ValueObjects\BookTitle;
use Tests\TestCase;

class CmoaUrlTest extends TestCase
{
    /** @test */
    public function generateUrlFromBookTitle_書籍名で商品を検索したページのURLを生成できること(): void
    {
        $purchaseUrl = new CmoaUrl();
        $bookTitle = new BookTitle('Book');
        $this->assertSame(
            'https://www.cmoa.jp/search/result/?header_word=Book',
            (string) $purchaseUrl->generateUrlFromBookTitle($bookTitle)
        );
    }

    /** @test */
    public function generateUrlFromBookTitle_書籍名が26文字以上の場合は25字でURLを生成されること(): void
    {
        $purchaseUrl = new CmoaUrl();
        $title = str_repeat('a', 26);
        $bookTitle = new BookTitle($title);
        $this->assertSame(
            'https://www.cmoa.jp/search/result/?header_word=aaaaaaaaaaaaaaaaaaaaaaaaa',
            (string) $purchaseUrl->generateUrlFromBookTitle($bookTitle)
        );
    }

    /** @test */
    public function generateUrlFromBookTitle_キーワードがURLエンコードされること(): void
    {
        $purchaseUrl = new CmoaUrl();
        $bookTitle = new BookTitle('Book 1');
        $this->assertSame(
            'https://www.cmoa.jp/search/result/?header_word=Book+1',
            (string) $purchaseUrl->generateUrlFromBookTitle($bookTitle),
        );
    }

    /** @test */
    public function generateUrlFromBookTitle_書籍のタイトル末尾の関数がキーワードから削除されること(): void
    {
        $purchaseUrl = new CmoaUrl();
        $bookTitle = new BookTitle('Book 1', '1');
        $this->assertSame(
            'https://www.cmoa.jp/search/result/?header_word=Book',
            (string) $purchaseUrl->generateUrlFromBookTitle($bookTitle),
        );
    }

    /** @test */
    public function generateUrlFromBookTitle_titleが表記揺れしやすい文字を含む場合キーワードから削除されること(): void
    {
        $purchaseUrl = new CmoaUrl();
        $bookTitle = new BookTitle('Book　THE COMIC');
        $this->assertSame(
            'https://www.cmoa.jp/search/result/?header_word=Book',
            (string) $purchaseUrl->generateUrlFromBookTitle($bookTitle)
        );
    }

    /** @test */
    public function generateUrlFromBookTitle_出版社を指定できること(): void
    {
        $publisherId = '0000413';
        $purchaseUrl = new CmoaUrl(publisherId: $publisherId);
        $bookTitle = new BookTitle('Book');
        $this->assertSame(
            'https://www.cmoa.jp/search/result/?header_word=Book&publisher_id=0000413',
            (string) $purchaseUrl->generateUrlFromBookTitle($bookTitle)
        );
    }
}
