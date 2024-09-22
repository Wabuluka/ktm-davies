<?php

namespace Tests\Unit\Entities\Store\Ebook;

use App\Entities\Store\Ebook\MechaComicUrl;
use App\ValueObjects\BookTitle;
use Tests\TestCase;

class MechaComicUrlTest extends TestCase
{
    /** @test */
    public function generateUrlFromBookTitle_タイトルを元に、キーワード検索URLを生成できること(): void
    {
        $purchaseUrl = new MechaComicUrl();
        $title = new BookTitle('Book');
        $this->assertSame(
            'https://mechacomic.jp/books?text=Book',
            (string) $purchaseUrl->generateUrlFromBookTitle($title)
        );
    }

    /** @test */
    public function generateUrlFromBookTitle_表記揺れが起きやすい文字列がキーワードから削除されること(): void
    {
        $purchaseUrl = new MechaComicUrl();
        $title = new BookTitle('Book THE COMIC');
        $this->assertSame(
            'https://mechacomic.jp/books?text=Book',
            (string) $purchaseUrl->generateUrlFromBookTitle($title)
        );
    }

    /** @test */
    public function generateUrlFromBookTitle_書籍のタイトル末尾の関数がキーワードから削除されること(): void
    {
        $purchaseUrl = new MechaComicUrl();
        $bookTitle = new BookTitle('Book 1', '1');
        $this->assertSame(
            'https://mechacomic.jp/books?text=Book',
            (string) $purchaseUrl->generateUrlFromBookTitle($bookTitle),
        );
    }

    /** @test */
    public function generateUrlFromBookTitle_URLにクエリパラメータが追加されること(): void
    {
        $searchParams = [
            'genre' => '24',
        ];
        $purchaseUrl = new MechaComicUrl($searchParams);
        $title = new BookTitle('Book');
        $this->assertSame(
            'https://mechacomic.jp/books?text=Book&genre=24',
            (string) $purchaseUrl->generateUrlFromBookTitle($title)
        );
    }
}
