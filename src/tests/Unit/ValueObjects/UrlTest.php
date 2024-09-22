<?php

namespace Tests\Unit\ValueObjects;

use App\ValueObjects\Url;
use Stringable;
use Tests\TestCase;

class UrlTest extends TestCase
{
    /** @test */
    public function addSearchParams_URLにクエリパラメータを追加できること(): void
    {
        $url = new Url(
            'https://example.com/'
                . '?foo=bar'
                . '&arr[]=1&arr[]=2'
                . '&object[key]=value'
                . '&duplicated=before'
                . '#fragment'
        );
        $this->assertSame(
            'https://example.com/'
                . '?foo=bar'
                . '&arr%5B0%5D=1&arr%5B1%5D=2'
                . '&object%5Bkey%5D=value'
                . '&duplicated=before' // 既存パラメータの上書きはできない
                . '&new=value'
                . '#fragment',
            $url->addSearchParams(['new' => 'value', 'duplicated' => 'after'])->value()
        );
    }

    /** @test */
    public function setPath_URLのパスを上書きできること(): void
    {
        $url = new Url('https://example.com/path/to/old-file');
        $this->assertSame(
            'https://example.com/path/to/new-file',
            $url->setPath('/path/to/new-file')->value()
        );
        $this->assertSame(
            'https://example.com/path/to/new-file',
            $url->setPath(['path', 'to', 'new-file'])->value()
        );
    }

    /** @test */
    public function encode_URL上パーセントエンコードできること(): void
    {
        $url = new Url('https://example.com/search/?q=Book 1');
        $this->assertSame(
            'https%3A%2F%2Fexample.com%2Fsearch%2F%3Fq%3DBook%2B1',
            $url->encode()->value()
        );
        $this->assertSame(
            'https%25253A%25252F%25252Fexample.com%25252Fsearch%25252F%25253Fq%25253DBook%25252B1',
            $url->encode(2)->value()
        );
    }

    /** @test */
    public function toString_URLをstring型で返却すること(): void
    {
        $url = new Url('https://example.com/?foo=bar#fragment');
        $this->assertInstanceOf(Stringable::class, $url);
        $this->assertSame('https://example.com/?foo=bar#fragment', (string) $url);
    }
}
