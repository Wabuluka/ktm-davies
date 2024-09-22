<?php

namespace Tests\Unit\ValueObjects;

use App\ValueObjects\Isbn;
use Tests\TestCase;

class IsbnTest extends TestCase
{
    /** @test */
    public function convert13To10_ISBN13をISBN10に変換して返却すること(): void
    {
        $isbn_1 = new Isbn('9784774183619');

        $this->assertSame(
            '477418361X',
            $isbn_1->convert13To10()->value()
        );

        $isbn_2 = new Isbn('9784797386295');

        $this->assertSame(
            '4797386290',
            $isbn_2->convert13To10()->value()
        );

        $isbn_2 = new Isbn('9784063842760');

        $this->assertSame(
            '4063842762',
            $isbn_2->convert13To10()->value()
        );

        // 既に 1SBN 10 形式の場合は、何もしない
        $this->assertSame(
            '4063842762',
            $isbn_2->convert13To10()->convert13To10()->value()
        );
    }
}
