<?php

namespace Tests\Unit\Entities\Store\Ebook;

use App\Entities\Store\Ebook\RentaUrl;
use App\ValueObjects\BookTitle;
use Tests\TestCase;

class RentaUrlTest extends TestCase
{
    /** @test */
    public function generateUrlFromBookTitle_出版社名と書籍名で商品を検索したページのURLを生成できること(): void
    {
        $purchaseUrl = new RentaUrl('キルタイムコミュニケーション');
        $booktitle = new BookTitle('Book 1', '1');
        $this->assertSame(
            'https://renta.papy.co.jp/renta/sc/frm/search?word=Book&publisher=%E3%82%AD%E3%83%AB%E3%82%BF%E3%82%A4%E3%83%A0%E3%82%B3%E3%83%9F%E3%83%A5%E3%83%8B%E3%82%B1%E3%83%BC%E3%82%B7%E3%83%A7%E3%83%B3',
            (string) $purchaseUrl->generateUrlFromBookTitle($booktitle)
        );
    }

    /** @test */
    public function generateUrlFromBookTitle_出版社名が未設定の場合は書籍名で商品を検索したページのURLを生成できること(): void
    {
        $purchaseUrl = new RentaUrl('');
        $booktitle = new BookTitle('Book 1', '1');
        $this->assertSame(
            'https://renta.papy.co.jp/renta/sc/frm/search?word=Book',
            (string) $purchaseUrl->generateUrlFromBookTitle($booktitle)
        );
    }

    /** @test */
    public function generateUrlFromBookTitle_日本語の商品を検索したページのURLを生成できること(): void
    {
        $purchaseUrl = new RentaUrl('キルタイムコミュニケーション');
        $booktitle = new BookTitle('ブック 1', '1');
        $this->assertSame(
            'https://renta.papy.co.jp/renta/sc/frm/search?word=%A5%D6%A5%C3%A5%AF&publisher=%E3%82%AD%E3%83%AB%E3%82%BF%E3%82%A4%E3%83%A0%E3%82%B3%E3%83%9F%E3%83%A5%E3%83%8B%E3%82%B1%E3%83%BC%E3%82%B7%E3%83%A7%E3%83%B3', (string) $purchaseUrl->generateUrlFromBookTitle($booktitle)
        );
    }
}
