<?php

namespace Tests\Unit\Http\Requests\Book;

use App\Http\Requests\Book\IndexBookRequest;
use Tests\Supports\TestingFormRequest;
use Tests\TestCase;

class IndexBookRequestTest extends TestCase
{
    use TestingFormRequest;

    protected $requestClass = IndexBookRequest::class;

    /** @test */
    public function toParameters_連続するスペースを含むキーワードを単語の配列に変換すること(): void
    {
        $keywords = $this->createRequestAndValidate(['keyword' => '太宰　  治 　　メロス　'])
            ->toParameters()
            ->get('keywords')
            ?->toArray();

        $this->assertSame($keywords, ['太宰', '治', 'メロス']);
    }
}
