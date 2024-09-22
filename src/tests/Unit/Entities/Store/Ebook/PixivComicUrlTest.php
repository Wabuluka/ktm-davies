<?php

namespace Tests\Unit\Entities\Store\Ebook;

use App\Entities\Store\Ebook\PixivComicUrl;
use App\ValueObjects\BookTitle;
use Tests\TestCase;

class PixivComicUrlTest extends TestCase
{
    /** @test */
    public function generateUrlFromBookTitle_書籍名で商品を検索したページのURLを生成できること(): void
    {
        $purchaseUrl = new PixivComicUrl();
        $booktitle = new BookTitle('Book 1');
        $this->assertSame(
            'https://comic.pixiv.net/search?q=Book+1&tab=store',
            (string) $purchaseUrl->generateUrlFromBookTitle($booktitle)
        );
    }
}
