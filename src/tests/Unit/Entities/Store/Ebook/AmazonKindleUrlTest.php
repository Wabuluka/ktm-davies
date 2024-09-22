<?php

namespace Tests\Unit\Entities\Store\Ebook;

use App\Entities\Store\Ebook\AmazonKindleUrl;
use App\ValueObjects\BookTitle;
use App\ValueObjects\Isbn;
use Tests\TestCase;

class AmazonKindleUrlTest extends TestCase
{
    /** @test */
    public function generateUrlFromIsbn_ISBNを元に、キーワード検索URLを生成できること(): void
    {
        $purchaseUrl = new AmazonKindleUrl();
        $isbn = new Isbn('9784799218020');
        $this->assertSame(
            'https://www.amazon.co.jp/s?k=4799218026',
            (string) $purchaseUrl->generateUrlFromIsbn($isbn)
        );
    }

    /** @test */
    public function generateUrlFromIsbn_URLにクエリパラメータが追加されること(): void
    {
        $searchParams = [
            'rh' => 'n:2250738051,n:2275256051,n:2410280051',
        ];
        $purchaseUrl = new AmazonKindleUrl($searchParams);
        $isbn = new Isbn('9784799218020');
        $this->assertSame(
            'https://www.amazon.co.jp/s?k=4799218026&rh=n%3A2250738051%2Cn%3A2275256051%2Cn%3A2410280051',
            (string) $purchaseUrl->generateUrlFromIsbn($isbn)
        );
    }

    /** @test */
    public function generateUrlFromTitle_タイトルを元に、キーワード検索URLを生成できること(): void
    {
        $purchaseUrl = new AmazonKindleUrl();
        $title = new BookTitle('Book 1');
        $this->assertSame(
            'https://www.amazon.co.jp/s?k=Book+1',
            (string) $purchaseUrl->generateUrlFromBookTitle($title)
        );
    }

    /** @test */
    public function generateUrlFromTitle_書籍のタイトル末尾の巻数が半角英数に変換されること(): void
    {
        $purchaseUrl = new AmazonKindleUrl();
        $title = new BookTitle('Book 　1', '1');
        $this->assertSame(
            'https://www.amazon.co.jp/s?k=Book+1',
            (string) $purchaseUrl->generateUrlFromBookTitle($title)
        );
    }

    /** @test */
    public function generateUrlFromTitle_URLにクエリパラメータが追加されること(): void
    {
        $searchParams = [
            'rh' => 'n:2250738051,n:2275256051,n:2410280051',
        ];
        $purchaseUrl = new AmazonKindleUrl($searchParams);
        $title = new BookTitle('Book 1');
        $this->assertSame(
            'https://www.amazon.co.jp/s?k=Book+1&rh=n%3A2250738051%2Cn%3A2275256051%2Cn%3A2410280051',
            (string) $purchaseUrl->generateUrlFromBookTitle($title)
        );
    }
}
