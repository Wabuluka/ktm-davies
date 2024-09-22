<?php

namespace App\GraphQL\Builders;

use App\GraphQL\Enums\AdultScopeType;
use App\Services\SearchBookService;
use App\Traits\Http\HandleRequestParameters;
use Illuminate\Database\Eloquent\Builder;

final class BookBuilder
{
    use HandleRequestParameters;

    /**
     * 成人向けフラグによる絞り込みを行う
     */
    public function scopedByAdult(Builder $builder, AdultScopeType $adult): Builder
    {
        return match ($adult) {
            AdultScopeType::ONLY => $builder->where('adult', true),
            AdultScopeType::EXCLUDE => $builder->where('adult', false),
            AdultScopeType::INCLUDE => $builder,
        };
    }

    /**
     * キーワードで絞り込む
     */
    public function filteredByKeyword(Builder $builder, string $keyword = null): Builder
    {
        if (! $keyword) {
            return $builder;
        }

        $keywords = $this->parseSpaceSeparatedKeywords($keyword, 5);
        $builder = (new SearchBookService())->searchAsVisitor($keywords, $builder);

        return $builder;
    }
}
