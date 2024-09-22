<?php

namespace App\Services;

use App\Enums\BookStatus;
use App\Models\Book;
use Illuminate\Database\Eloquent\Builder;

final class SearchBookService
{
    /**
     * 管理者 (CMS) 向けに書籍検索を行うクエリビルダを返却する
     */
    public function searchAsManager(
        iterable $keywords = null,
        iterable $sites = null,
        iterable $statuses = null,
        Builder $query = null,
    ): Builder {
        $query ??= Book::query();

        return $query
            ->when($keywords, fn ($query, $keywords) => $query
                ->where(function ($query) use ($keywords) {
                    foreach ($keywords as $keyword) {
                        $escaped = addcslashes($keyword, '%_\\');
                        $query->where(fn ($query) => $query
                            ->where('title', 'like', "%{$escaped}%")
                            ->orWhere('title_kana', 'like', "%{$escaped}%")
                            ->orWhere('keywords', 'like', "%{$escaped}%")
                            ->orWhereHas('label', fn ($query) => $query
                                ->where('name', 'like', "%{$escaped}%"))
                            ->orWhereHas('genre', fn ($query) => $query
                                ->where('name', 'like', "%{$escaped}%"))
                            ->orWhereHas('series', fn ($query) => $query
                                ->where('name', 'like', "%{$escaped}%"))
                            ->orWhereHas('creators', fn ($query) => $query
                                ->where('name', 'like', "%{$escaped}%")
                                ->orwhere('name_kana', 'like', "%{$escaped}%")));
                    }
                }))
            ->when($sites, fn ($query, $siteIds) => $query
                ->whereHas('sites', fn ($query) => $query->whereIn('id', $siteIds)))
            ->when($statuses, fn ($query, $statuses) => $query
                ->where(function ($query) use ($statuses) {
                    foreach ($statuses as $status) {
                        match ($status) {
                            BookStatus::Draft => $query->orWhere(fn ($query) => $query->draft()),
                            BookStatus::WillBePublished => $query->orWhere(fn ($query) => $query->willBePublished()),
                            BookStatus::Published => $query->orWhere(fn ($query) => $query->published()),
                        };
                    }
                }))
            ->orderBy('release_date', 'desc');
    }

    /**
     * 一般ユーザ (レーベルサイト) 向けに書籍検索を行うクエリビルダを返却する
     */
    public function searchAsVisitor(
        iterable $keywords = null,
        Builder $query = null,
    ): Builder {
        $query ??= Book::query();

        return $query
            ->when($keywords, fn ($query, $keywords) => $query
                ->where(function ($query) use ($keywords) {
                    foreach ($keywords as $keyword) {
                        $escaped = addcslashes($keyword, '%_\\');
                        $query->where(fn ($query) => $query
                            ->where('title', 'like', "%{$escaped}%")
                            ->orWhere('title_kana', 'liKe', "%{$escaped}%")
                            ->orWhere('keywords', 'like', "%{$escaped}%")
                            ->orWhereHas('series', fn ($query) => $query
                                ->where('name', 'like', "%{$escaped}%"))
                            ->orWhereHas('creators', fn ($query) => $query
                                ->where('name', 'like', "%{$escaped}%")
                                ->orwhere('name_kana', 'like', "%{$escaped}%")));
                    }
                }));
    }
}
