<?php

namespace Tests\Unit\Entities\Store\Ebook;

use App\Entities\Store\Ebook\PiccomaUrl;
use App\ValueObjects\BookTitle;
use Tests\TestCase;

class PiccomaUrlTest extends TestCase
{
    /** @test */
    public function generateUrlFromBookTitle_書籍名で商品を検索したページのURLを生成できること(): void
    {
        $purchaseUrl = new PiccomaUrl();
        $bookTitle = new BookTitle('Book');
        $this->assertSame(
            'https://piccoma.com/web/search/result?word=Book',
            (string) $purchaseUrl->generateUrlFromBookTitle($bookTitle)
        );
    }

    /** @test */
    public function generateUrlFromBookTitle_キーワードがURLエンコードされること(): void
    {
        $purchaseUrl = new PiccomaUrl();
        $bookTitle = new BookTitle('Book 1');
        $this->assertSame(
            'https://piccoma.com/web/search/result?word=Book+1',
            (string) $purchaseUrl->generateUrlFromBookTitle($bookTitle),
        );
    }

    /** @test */
    public function generateUrlFromBookTitle_書籍のタイトル末尾の関数がキーワードから削除されること(): void
    {
        $purchaseUrl = new PiccomaUrl();
        $bookTitle = new BookTitle('Book 1', '1');
        $this->assertSame(
            'https://piccoma.com/web/search/result?word=Book',
            (string) $purchaseUrl->generateUrlFromBookTitle($bookTitle),
        );
    }
}
