<?php

namespace Tests\Unit\Entities\Store\Ebook;

use App\Entities\Store\Ebook\RakutenKoboUrl;
use App\ValueObjects\BookTitle;
use Tests\TestCase;

class RakutenKoboUrlTest extends TestCase
{
    /** @test */
    public function generateUrlFromTitle_タイトルを元に、キーワード検索URLを生成できること(): void
    {
        $purchaseUrl = new RakutenKoboUrl();
        $title = new BookTitle('Book 1');
        $this->assertSame(
            'https://books.rakuten.co.jp/search?sitem=Book+1',
            (string) $purchaseUrl->generateUrlFromBookTitle($title)
        );
    }

    /** @test */
    public function generateUrlFromTitle_書籍のタイトル末尾の巻数が半角英数に変換されること(): void
    {
        $purchaseUrl = new RakutenKoboUrl();
        $title = new BookTitle('Book 　1', '1');
        $this->assertSame(
            'https://books.rakuten.co.jp/search?sitem=Book+1',
            (string) $purchaseUrl->generateUrlFromBookTitle($title)
        );
    }

    /** @test */
    public function generateUrlFromTitle_表記揺れが起きやすい文字列がキーワードから削除されること(): void
    {
        $purchaseUrl = new RakutenKoboUrl();
        $title = new BookTitle('Book THE COMIC');
        $this->assertSame(
            'https://books.rakuten.co.jp/search?sitem=Book',
            (string) $purchaseUrl->generateUrlFromBookTitle($title)
        );
    }

    /** @test */
    public function generateUrlFromTitle_URLにクエリパラメータが追加されること(): void
    {
        $searchParams = [
            'g' => '101901',
        ];
        $purchaseUrl = new RakutenKoboUrl($searchParams);
        $title = new BookTitle('Book');
        $this->assertSame(
            'https://books.rakuten.co.jp/search?sitem=Book&g=101901',
            (string) $purchaseUrl->generateUrlFromBookTitle($title)
        );
    }
}
