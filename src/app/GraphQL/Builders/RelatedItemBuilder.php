<?php

namespace App\GraphQL\Builders;

use App\GraphQL\Enums\AdultScopeType;
use App\Traits\Http\HandleRequestParameters;
use Illuminate\Database\Eloquent\Builder;

final class RelatedItemBuilder
{
    use HandleRequestParameters;

    /**
     * 成人向けフラグによる絞り込みを行う
     */
    public function scopedByAdult(Builder $builder, AdultScopeType $adult): Builder
    {
        return match ($adult) {
            AdultScopeType::ONLY => $builder->adult(true),
            AdultScopeType::EXCLUDE => $builder->adult(false),
            AdultScopeType::INCLUDE => $builder,
        };
    }
}
