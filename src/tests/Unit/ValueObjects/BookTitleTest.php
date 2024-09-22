<?php

namespace Tests\Unit\ValueObjects;

use App\ValueObjects\BookTitle;
use Tests\TestCase;

class BookTitleTest extends TestCase
{
    /** @test */
    public function convertToEucJp_文字コードをEUCJPに変換して返却すること(): void
    {
        $utf8 = new BookTitle('転生したらスライムだった件');
        $this->assertSame(
            'UTF-8',
            mb_detect_encoding($utf8->value(), 'UTF-8', true)
        );

        $encoded = $utf8->convertToEucJp();
        $this->assertSame(
            'EUC-JP',
            mb_detect_encoding($encoded->value(), 'EUC-JP', true)
        );

        $decoded_name = mb_convert_encoding($encoded->value(), 'UTF-8', 'EUC-JP');
        $this->assertSame(
            '転生したらスライムだった件',
            $decoded_name
        );
    }

    /** @test */
    public function orthographizeApostrophe_アポストロフィを1種類に統一して返却すること(): void
    {
        $title = new BookTitle("’転生したら’ '転生したら'");
        $this->assertSame(
            '’転生したら’ ’転生したら’',
            $title->orthographizeApostrophe('’')->value()
        );
        $this->assertSame(
            "'転生したら' '転生したら'",
            $title->orthographizeApostrophe("'")->value()
        );

        // 書誌名にアポストロフィを含まない場合は何もしない
        $title = new BookTitle('転生したらスライムだった件');
        $this->assertSame(
            '転生したらスライムだった件',
            $title->orthographizeApostrophe('’')->value()
        );
    }

    /** @test */
    public function orthographizeVolume_末尾の巻数部分の書式を統一して返却すること(): void
    {
        $expected = '転生したらスライムだった件 1';

        $title = new BookTitle('転生したらスライムだった件 1', '1');
        $this->assertSame($expected, $title->orthographizeVolume()->value());

        $title = new BookTitle('転生したらスライムだった件 １', '1');
        $this->assertSame($expected, $title->orthographizeVolume()->value());

        $title = new BookTitle('転生したらスライムだった件１', '1');
        $this->assertSame($expected, $title->orthographizeVolume()->value());

        $title = new BookTitle('転生したらスライムだった件 １巻', '1');
        $this->assertSame($expected, $title->orthographizeVolume()->value());

        $title = new BookTitle('転生したらスライムだった件 第１話', '1');
        $this->assertSame($expected, $title->orthographizeVolume()->value());

        $title = new BookTitle('転生したらスライムだった件 Vol.1', '1');
        $this->assertSame($expected, $title->orthographizeVolume()->value());

        $title = new BookTitle('転生したらスライムだった件 Vol. 1', '1');
        $this->assertSame($expected, $title->orthographizeVolume()->value());

        $title = new BookTitle('転生したらスライムだった件 Vol 1', '1');
        $this->assertSame($expected, $title->orthographizeVolume()->value());

        $title = new BookTitle('転生したらスライムだった件 上', '1');
        $this->assertSame($expected, $title->orthographizeVolume()->value());

        $title = new BookTitle('転生したらスライムだった件 下', '1');
        $this->assertSame($expected, $title->orthographizeVolume()->value());

        // 巻数に該当する文字列が無い場合
        $title = new BookTitle('転生したらスライムだった件', '1');
        // NOTE: GC・ライドの挙動に合わせたが、巻数を付加しない方が自然では
        $this->assertSame($expected, $title->orthographizeVolume()->value());

        // 巻数の指定が無い場合 (何もしない)
        $title = new BookTitle('転生したらスライムだった件 1');
        $this->assertSame('転生したらスライムだった件 1', $title->orthographizeVolume()->value());
    }

    /** @test */
    public function replaceToWhiteSpace_表記ゆれが置きやすい文字列を削除して返却すること(): void
    {
        $search = ['～', '、', '―', 'THE COMIC', '!!'];
        $title = new BookTitle('転生したらスライムだった件～、―THE COMIC !!２');

        $this->assertSame(
            '転生したらスライムだった件      ２',
            $title->replaceToWhiteSpace($search)->value()
        );
    }

    /** @test */
    public function test_末尾の巻数部分の文字列を削除して返却すること(): void
    {
        $expected = '転生したらスライムだった件';

        $title = new BookTitle('転生したらスライムだった件 1', '1');
        $this->assertSame($expected, $title->removeVolume()->value());

        $title = new BookTitle('転生したらスライムだった件 １', '1');
        $this->assertSame($expected, $title->removeVolume()->value());

        $title = new BookTitle('転生したらスライムだった件１', '1');
        $this->assertSame($expected, $title->removeVolume()->value());

        $title = new BookTitle('転生したらスライムだった件 １巻', '1');
        $this->assertSame($expected, $title->removeVolume()->value());

        $title = new BookTitle('転生したらスライムだった件 第１話', '1');
        $this->assertSame($expected, $title->removeVolume()->value());

        $title = new BookTitle('転生したらスライムだった件 Vol.1', '1');
        $this->assertSame($expected, $title->removeVolume()->value());

        $title = new BookTitle('転生したらスライムだった件 Vol. 1', '1');
        $this->assertSame($expected, $title->removeVolume()->value());

        $title = new BookTitle('転生したらスライムだった件 Vol 1', '1');
        $this->assertSame($expected, $title->removeVolume()->value());

        $title = new BookTitle('転生したらスライムだった件 上', '1');
        $this->assertSame($expected, $title->removeVolume()->value());

        $title = new BookTitle('転生したらスライムだった件 下', '1');
        $this->assertSame($expected, $title->removeVolume()->value());

        // 巻数に該当する文字列が無い場合
        $title = new BookTitle('転生したらスライムだった件', '1');
        $this->assertSame($expected, $title->removeVolume()->value());

        // 巻数の指定が無い場合 (何もしない)
        $title = new BookTitle('転生したらスライムだった件 1');
        $this->assertSame('転生したらスライムだった件 1', $title->removeVolume()->value());
    }

    /** @test */
    public function truncate_書籍名を指定文字数に切り詰めて返却すること(): void
    {
        $title_1 = new BookTitle('転生したらスライムだった件');
        $this->assertSame(
            '転生したら',
            $title_1->truncate(5)->value()
        );

        // 書誌名の文字数 <= 指定文字数が多い場合は何もしない
        $title_2 = new BookTitle('転生したらスライムだった件');
        $length = mb_strlen('転生したらスライムだった件');

        $this->assertSame(
            '転生したらスライムだった件',
            $title_2->truncate($length)->value()
        );
        $this->assertSame(
            '転生したらスライムだった件',
            $title_2->truncate(100)->value()
        );
    }

    /** @test */
    public function その他_各メソッドを組み合わせて利用できること(): void
    {
        $title_1 = (new BookTitle("'転生したらスライムだった件'～、―THE COMIC !! Vol.2", '2'))
            ->replaceToWhiteSpace(['～', '、', '―', 'THE COMIC', '!!'])
            ->removeVolume()
            ->orthographizeApostrophe('’');

        $this->assertSame(
            '’転生したらスライムだった件’',
            $title_1->value()
        );

        $title_2 = $title_1
            ->truncate(3);

        $this->assertSame(
            '’転生',
            $title_2->value()
        );
    }
}
