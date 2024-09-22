<?php

namespace Tests\Unit\Entities\Store\Ebook;

use App\Entities\Store\Ebook\BookLiveUrl;
use App\ValueObjects\BookTitle;
use App\ValueObjects\Url;
use Tests\TestCase;

class BookLiveUrlTest extends TestCase
{
    /** @test */
    public function generateUrlFromBookTitle_書籍のタイトルを元に、キーワード検索URLを生成できること(): void
    {
        $purchaseUrl = new BookLiveUrl();
        $bookTitle = new BookTitle('Book');
        $this->assertSame(
            'https://booklive.jp/search/keyword/keyword/Book/exfasc/1',
            (string) $purchaseUrl->generateUrlFromBookTitle($bookTitle)
        );
    }

    /** @test */
    public function generateUrlFromBookTitle_カテゴリを指定できること(): void
    {
        $categoryIds = 'L';
        $purchaseUrl = new BookLiveUrl(categoryIds: $categoryIds);
        $bookTitle = new BookTitle('Book 1');
        $this->assertSame(
            'https://booklive.jp/search/keyword/category_ids/L/keyword/Book%201/exfasc/1',
            (string) $purchaseUrl->generateUrlFromBookTitle($bookTitle),
        );
    }

    /** @test */
    public function generateUrlFromBookTitle_ジャンルを指定できること(): void
    {
        $genreIds = '14,3062';
        $purchaseUrl = new BookLiveUrl(genreIds: $genreIds);
        $bookTitle = new BookTitle('Book 1');
        $this->assertSame(
            'https://booklive.jp/search/keyword/g_ids/14,3062/keyword/Book%201/exfasc/1',
            (string) $purchaseUrl->generateUrlFromBookTitle($bookTitle),
        );
    }

    /** @test */
    public function generateUrlFromBookTitle_掲載誌を指定できること(): void
    {
        $keisaishiIds = '4833';
        $purchaseUrl = new BookLiveUrl(keisaishiIds: $keisaishiIds);
        $bookTitle = new BookTitle('Book 1');
        $this->assertSame(
            'https://booklive.jp/search/keyword/k_ids/4833/keyword/Book%201/exfasc/1',
            (string) $purchaseUrl->generateUrlFromBookTitle($bookTitle),
        );
    }

    /** @test */
    public function generateUrlFromBookTitle_出版社を指定できること(): void
    {
        $publisherIds = '339';
        $purchaseUrl = new BookLiveUrl(publisherIds: $publisherIds);
        $bookTitle = new BookTitle('Book 1');
        $this->assertSame(
            'https://booklive.jp/search/keyword/p_ids/339/keyword/Book%201/exfasc/1',
            (string) $purchaseUrl->generateUrlFromBookTitle($bookTitle),
        );
    }

    /** @test */
    public function generateUrlFromBookTitle_カテゴリ・ジャンル・出版社を同時に指定できること(): void
    {
        $categoryIds = 'L';
        $genreIds = '14,3062';
        $keisaishiIds = '4833';
        $publisherIds = '339';
        $purchaseUrl = new BookLiveUrl($categoryIds, $genreIds, $keisaishiIds, $publisherIds);
        $bookTitle = new BookTitle('Book 1');
        $this->assertSame(
            'https://booklive.jp/search/keyword/category_ids/L/g_ids/14,3062/k_ids/4833/p_ids/339/keyword/Book%201/exfasc/1',
            (string) $purchaseUrl->generateUrlFromBookTitle($bookTitle),
        );
    }

    /** @test */
    public function generateUrlFromBookTitle_URLにクエリパラメータを追加できること(): void
    {
        $searchParams = [
            'utm_source' => 'spad',
            'utm_medium' => 'affiliate',
            'utm_campaign' => '102',
            'utm_content' => 'normal',
        ];
        $purchaseUrl = new BookLiveUrl(searchParams: $searchParams);
        $bookTitle = new BookTitle('Book 1');
        $this->assertSame(
            'https://booklive.jp/search/keyword/keyword/Book%201/exfasc/1'
            . '?utm_source=spad&utm_medium=affiliate&utm_campaign=102&utm_content=normal',
            (string) $purchaseUrl->generateUrlFromBookTitle($bookTitle),
        );
    }

    /** @test */
    public function generateUrlFromBookTitle_キーワードがURLエンコードされること(): void
    {
        $purchaseUrl = new BookLiveUrl();
        $bookTitle = new BookTitle('Book 1');
        $this->assertSame(
            'https://booklive.jp/search/keyword/keyword/Book%201/exfasc/1',
            (string) $purchaseUrl->generateUrlFromBookTitle($bookTitle),
        );
    }

    /** @test */
    public function generateUrlFromBookTitle_書籍のタイトル末尾の関数がキーワードから削除されること(): void
    {
        $purchaseUrl = new BookLiveUrl();
        $bookTitle = new BookTitle('Book 1', '1');
        $this->assertSame(
            'https://booklive.jp/search/keyword/keyword/Book/exfasc/1',
            (string) $purchaseUrl->generateUrlFromBookTitle($bookTitle),
        );
    }

    /** @test */
    public function generateUrlFromBookTitle_表記揺れが起きやすい文字列がキーワードから削除されること(): void
    {
        $purchaseUrl = new BookLiveUrl();
        $bookTitle = new BookTitle('Book THE COMIC');
        $this->assertSame(
            'https://booklive.jp/search/keyword/keyword/Book/exfasc/1',
            (string) $purchaseUrl->generateUrlFromBookTitle($bookTitle),
        );
    }

    /** @test */
    public function optimizeAnotherUrl_URLにクエリパラメータが追加されること(): void
    {
        $searchParams = [
            'utm_source' => 'spad',
            'utm_medium' => 'affiliate',
            'utm_campaign' => '102',
            'utm_content' => 'normal',
        ];
        $purchaseUrl = new BookLiveUrl(searchParams: $searchParams);
        $anotherUrl = new Url('https://booklive.jp/search/keyword/keyword/Book/exfasc/1?foo=bar');
        $this->assertSame(
            'https://booklive.jp/search/keyword/keyword/Book/exfasc/1'
            . '?foo=bar&utm_source=spad&utm_medium=affiliate&utm_campaign=102&utm_content=normal',
            (string) $purchaseUrl->optimizeAnotherUrl($anotherUrl),
        );
    }
}
