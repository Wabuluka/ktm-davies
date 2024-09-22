<?php

namespace Tests\Feature\Services;

use App\Enums\BookStatus;
use App\Models\Book;
use App\Models\CreationType;
use App\Models\Creator;
use App\Models\Series;
use App\Models\Site;
use App\Services\SearchBookService;
use Tests\TestCase;

class SearchBookServiceTest extends TestCase
{
    private SearchBookService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = new SearchBookService();
    }

    /** @test */
    public function searchAsManger_発売日の降順で書籍一覧を表示すること(): void
    {
        Book::factory()->create(['title' => 'Book 1', 'release_date' => now()->addDays(1)]);
        Book::factory()->create(['title' => 'Book 2', 'release_date' => now()->addDays(2)]);
        Book::factory()->create(['title' => 'Book 3', 'release_date' => now()->addDays(3)]);

        $bookNames = $this->service->searchAsManager()
            ->get()
            ->pluck('title')
            ->toArray();

        $this->assertSame(['Book 3', 'Book 2', 'Book 1'], $bookNames);
    }

    /**
     * @test
     *
     * @dataProvider searchAsManagerKkeywordProvider
     */
    public function searchAsManger_キーワードによる絞り込み検索が可能なこと($keywords, $expected): void
    {
        $type = CreationType::factory()->create();
        Book::factory()
            ->state(['release_date' => now()->addDays(6)])
            ->create(['title' => 'Comic 1']);
        Book::factory()
            ->state(['release_date' => now()->addDays(5)])
            ->create(['title' => 'Book 1']);
        Book::factory()
            ->for(Series::factory()->create(['name' => 'New Series']))
            ->state(['release_date' => now()->addDays(4)])
            ->create(['title' => 'Book 2']);
        Book::factory()
            ->for(Series::factory()->create(['name' => 'Old Series']))
            ->state(['release_date' => now()->addDays(3)])
            ->create(['title' => 'Book 3']);
        Book::factory()
            ->hasAttached(Creator::factory()->create(['name' => 'Creator (author)']), ['creation_type' => $type->name, 'sort' => 1])
            ->state(['release_date' => now()->addDays(2)])
            ->create(['title' => 'Book 4']);
        Book::factory()
            ->hasAttached(Creator::factory()->create(['name' => 'Creator (illustrator)']), ['creation_type' => $type->name, 'sort' => 1])
            ->state(['release_date' => now()->addDays(1)])
            ->create(['title' => 'Book 5']);

        $bookNames = $this->service->searchAsManager(
            keywords: $keywords
        )
            ->get()
            ->pluck('title')
            ->toArray();

        $this->assertSame($expected, $bookNames);
    }

    public static function searchAsManagerKkeywordProvider(): array
    {
        return [
            '書籍名に合致' => [
                'keywords' => ['Book'],
                'expected' => ['Book 1', 'Book 2', 'Book 3', 'Book 4', 'Book 5'],
            ],
            '書籍名に合致 (複数キーワード)' => [
                'keywords' => ['Book', '1'],
                'expected' => ['Book 1'],
            ],
            'シリーズ名に合致' => [
                'keyword' => ['Series'],
                'expected' => ['Book 2', 'Book 3'],
            ],
            'シリーズ名に合致 (複数キーワード)' => [
                'keyword' => ['New', 'Series'],
                'expected' => ['Book 2'],
            ],
            '作家名に合致' => [
                'keywords' => ['Creator'],
                'expected' => ['Book 4', 'Book 5'],
            ],
            '作家名に合致 (複数キーワード)' => [
                'keywords' => ['Creator', 'author'],
                'expected' => ['Book 4'],
            ],
        ];
    }

    /** @test */
    public function searchAsManger_キーワードにLIKE演算子のメタ文字が含まれていてもエラーにならないこと(): void
    {
        Book::factory()->create(['title' => 'Book 1']);
        Book::factory()->create(['title' => 'Book %_\\']);

        $bookNames = $this->service->searchAsManager(
            keywords: ['%_\\'],
        )
            ->get()
            ->pluck('title')
            ->toArray();

        $this->assertSame(['Book %_\\'], $bookNames);
    }

    /** @test */
    public function searchAsManger_公開サイトによる絞り込み検索が可能なこと(): void
    {
        Book::factory()
            ->hasAttached($site1 = Site::factory()->create())
            ->create(['title' => 'Book 1', 'release_date' => now()->addDays(1)]);
        Book::factory()
            ->hasAttached($site2 = Site::factory()->create())
            ->create(['title' => 'Book 2', 'release_date' => now()->addDays(2)]);
        Book::factory()
            ->hasAttached($_site3 = Site::factory()->create())
            ->create(['title' => 'Book 3', 'release_date' => now()->addDays(3)]);

        $bookNames = $this->service->searchAsManager(
            sites: [$site1->id, $site2->id],
        )
            ->get()
            ->pluck('title')
            ->toArray();

        $this->assertSame(['Book 2', 'Book 1'], $bookNames);
    }

    /** @test */
    public function searchAsManger_ステータスによる絞り込み検索が可能なこと(): void
    {
        Book::factory()->draft()->create(['title' => 'Book 1', 'release_date' => now()->addDays(1)]);
        Book::factory()->willBePublished()->create(['title' => 'Book 2', 'release_date' => now()->addDays(2)]);
        Book::factory()->published()->create(['title' => 'Book 3', 'release_date' => now()->addDays(3)]);

        $bookNames = $this->service->searchAsManager(
            statuses: [BookStatus::WillBePublished, BookStatus::Draft],
        )
            ->get()
            ->pluck('title')
            ->toArray();

        $this->assertSame(['Book 2', 'Book 1'], $bookNames);
    }

    /**
     * @test
     *
     * @dataProvider searchAsVisitorKkeywordProvider
     */
    public function searchAsVisitor_キーワードによる絞り込み検索が可能なこと($keywords, $expected): void
    {
        $series1 = Series::factory()->create(['name' => '大活字本シリーズ']);
        $series2 = Series::factory()->create(['name' => '朗読名作シリーズ']);
        $creator1 = Creator::factory()->create(['name' => '太宰治', 'name_kana' => 'ダザイオサム']);
        $creator2 = Creator::factory()->create(['name' => '夏目漱石', 'name_kana' => 'ナツメソウセキ']);
        $creationType = CreationType::factory()->create();
        $sort = 1;
        $creator1BookFactory = Book::factory()->hasAttached($creator1, [
            'creation_type' => $creationType->name,
            'sort' => $sort++,
        ]);
        $sort = 1;
        $creator2BookFactory = Book::factory()->hasAttached($creator2, [
            'creation_type' => $creationType->name,
            'sort' => $sort++,
        ]);

        $creator1BookFactory
            ->for($series1)
            ->create([
                'title' => '走れメロス',
                'title_kana' => 'ハシレメロス',
                'keywords' => '',
            ]);
        $creator1BookFactory
            ->for($series2)
            ->create([
                'title' => '人間失格',
                'title_kana' => 'ニンゲンシッカク',
                'keywords' => '',
            ]);
        $creator2BookFactory
            ->for($series1)
            ->create([
                'title' => '坊っちゃん',
                'title_kana' => 'ボッチャン',
                'keywords' => '',
            ]);
        $creator2BookFactory
            ->for($series2)
            ->create([
                'title' => '吾輩は猫である',
                'title_kana' => 'ワガハイハネコデアル',
                'keywords' => '名前はまだない',
            ]);

        $bookNames = $this->service->searchAsVisitor(
            keywords: $keywords,
        )
            ->get()
            ->pluck('title')
            ->toArray();

        $this->assertSame($expected, $bookNames);
    }

    public static function searchAsVisitorKkeywordProvider(): array
    {
        return [
            '書籍名に合致' => [
                'keywords' => ['メロス'],
                'expected' => ['走れメロス'],
            ],
            '書籍名カナに合致' => [
                'keywords' => ['ハシレ'],
                'expected' => ['走れメロス'],
            ],
            'キーワードに合致' => [
                'keywords' => ['名前はまだない'],
                'expected' => ['吾輩は猫である'],
            ],
            'シリーズに合致' => [
                'keywords' => ['大活字本シリーズ'],
                'expected' => ['走れメロス', '坊っちゃん'],
            ],
            '作家名に合致' => [
                'keywords' => ['夏目'],
                'expected' => ['坊っちゃん', '吾輩は猫である'],
            ],
            '作家名カナに合致' => [
                'keywords' => ['ナツメ'],
                'expected' => ['坊っちゃん', '吾輩は猫である'],
            ],
            '全てのキーワードに合致' => [
                'keywords' => ['太宰', '治', 'メロス'],
                'expected' => ['走れメロス'],
            ],
        ];
    }

    /** @test */
    public function searchAsVisitor_キーワードにLIKE演算子のメタ文字が含まれていてもエラーにならないこと(): void
    {
        Book::factory()->create(['title' => 'Book 1']);
        Book::factory()->create(['title' => 'Book %_\\']);

        $bookNames = $this->service->searchAsVisitor(
            keywords: ['%_\\'],
        )
            ->get()
            ->pluck('title')
            ->toArray();

        $this->assertSame(['Book %_\\'], $bookNames);
    }
}
