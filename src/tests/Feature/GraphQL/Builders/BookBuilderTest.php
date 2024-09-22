<?php

namespace Tests\Feature\GraphQL\Builders;

use App\GraphQL\Builders\BookBuilder;
use App\GraphQL\Enums\AdultScopeType;
use App\Models\Book;
use App\Models\CreationType;
use App\Models\Creator;
use App\Models\Series;
use Tests\TestCase;

class BookBuilderTest extends TestCase
{
    private BookBuilder $builder;

    protected function setUp(): void
    {
        parent::setUp();

        $this->builder = new BookBuilder();
    }

    /** @test */
    public function 指定した成人向けフラグの状態に合致する書籍のみ返却すること(): void
    {
        Book::factory()
            ->adult(false)
            ->create(['title' => '全年齢']);

        Book::factory()
            ->adult(true)
            ->create(['title' => '成人向け']);

        $includeAdultBooksQuery
            = $this->builder->scopedByAdult(Book::query(), AdultScopeType::INCLUDE);

        $excludeAdultBooksQuery
            = $this->builder->scopedByAdult(Book::query(), AdultScopeType::EXCLUDE);

        $onlyAdultBooksQuery
            = $this->builder->scopedByAdult(Book::query(), AdultScopeType::ONLY);

        $this->assertSame(
            ['全年齢', '成人向け'],
            $includeAdultBooksQuery->pluck('title')->toArray()
        );
        $this->assertSame(
            ['全年齢'],
            $excludeAdultBooksQuery->pluck('title')->toArray()
        );
        $this->assertSame(
            ['成人向け'],
            $onlyAdultBooksQuery->pluck('title')->toArray()
        );
    }

    /** @test */
    public function キーワードに合致するBookのみ返却すること(): void
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

        $bookNames = $this->builder
            ->filteredByKeyword(Book::query(), '太宰　  治 　　メロス　')
            ->pluck('title')
            ->toArray();

        $this->assertSame(['走れメロス'], $bookNames);
    }

    /** @test */
    public function キーワードとしてnullが渡ってきた場合、Builderをそのまま返却すること(): void
    {
        $query = Book::whereName('Book');
        $this->assertSame($query, $this->builder->filteredByKeyword($query, null));
    }
}
