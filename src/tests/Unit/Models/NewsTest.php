<?php

namespace Tests\Unit\Models;

use App\Enums\NewsStatus;
use App\Models\News;
use Closure;
use Tests\TestCase;

class NewsTest extends TestCase
{
    /**
     * @test
     *
     * @dataProvider statusDataProvider
     */
    public function status_書籍のステータスを返却すること(NewsStatus $expected, bool $is_draft, Closure $published_at): void
    {
        $this->freezeTime();
        $this->assertSame(
            News::factory()->create(['is_draft' => $is_draft, 'published_at' => $published_at])->status,
            $expected
        );
    }

    /**
     * @test
     *
     * @dataProvider statusDataProvider
     */
    public function scopeDraft_ステータスが「下書き」の書籍に絞り込むこと(NewsStatus $expected, bool $is_draft, Closure $published_at): void
    {
        $this->freezeTime();
        News::factory()->create(['is_draft' => $is_draft, 'published_at' => $published_at]);
        $expectedCount = $expected === NewsStatus::Draft ? 1 : 0;
        $this->assertCount($expectedCount, News::draft()->get());
    }

    /**
     * @test
     *
     * @dataProvider statusDataProvider
     */
    public function scopeWillBePublished_ステータスが「公開予定」の書籍に絞り込むこと(NewsStatus $expected, bool $is_draft, Closure $published_at): void
    {
        $this->freezeTime();
        News::factory()->create(['is_draft' => $is_draft, 'published_at' => $published_at]);
        $expectedCount = $expected === NewsStatus::WillBePublished ? 1 : 0;
        $this->assertCount($expectedCount, News::willBePublished()->get());
    }

    /**
     * @test
     *
     * @dataProvider statusDataProvider
     */
    public function scopePublished_ステータスが「下書き」の書籍に絞り込むこと(NewsStatus $expected, bool $is_draft, Closure $published_at): void
    {
        $this->freezeTime();
        News::factory()->create(['is_draft' => $is_draft, 'published_at' => $published_at]);
        $expectedCount = $expected === NewsStatus::Published ? 1 : 0;
        $this->assertCount($expectedCount, News::published()->get());
    }

    public static function statusDataProvider()
    {
        return [
            '下書き ON & 現在日時 < 公開日時の場合' => [
                'expected' => NewsStatus::Draft, 'is_draft' => true, 'published_at' => fn () => now()->addMinute(),
            ],
            '下書き ON & 現在日時 >= 公開日時の場合' => [
                'expected' => NewsStatus::Draft, 'is_draft' => true, 'published_at' => fn () => now(),
            ],
            '下書き OFF & 現在日時 < 公開日時の場合' => [
                'expected' => NewsStatus::WillBePublished, 'is_draft' => false, 'published_at' => fn () => now()->addMinute(),
            ],
            '下書き OFF & 現在日時 >= 公開日時の場合' => [
                'expected' => NewsStatus::Published, 'is_draft' => false, 'published_at' => fn () => now(),
            ],
        ];
    }
}
