<?php

namespace App\GraphQL\Queries;

use App\Models\News;
use Illuminate\Support\Facades\DB;

final class ArchiveMonthQuery
{
    /**
     * 月次リストを返す
     *
     * @param  array{scope: array{siteId: int}}  $args
     */
    public function __invoke(mixed $root, array $args)
    {
        $query =
            News::published()
                ->publishedAtSite($args['scope']['siteId'])
                ->select(
                    DB::raw("DATE_FORMAT(`published_at`, '%Y') as year"),
                    DB::raw("DATE_FORMAT(`published_at`, '%m') as month"),
                )
                ->groupBy('year', 'month')
                ->orderByDesc('published_at');
        $results = $query->get();

        return [
            'data' => $results->toArray(),
        ];
    }
}
