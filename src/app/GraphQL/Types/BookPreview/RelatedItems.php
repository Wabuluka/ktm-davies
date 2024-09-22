<?php

namespace App\GraphQL\Types\BookPreview;

use App\Enums\BookStatus;
use App\GraphQL\Enums\AdultScopeType;
use App\Models\Book;
use App\Models\RelatedItem;
use Illuminate\Support\Collection;

final class RelatedItems
{
    public function __invoke(Book $book, array $args): Collection
    {
        $siteId = data_get($args, 'scope.siteId');
        $adult = data_get($args, 'scope.adult', AdultScopeType::EXCLUDE);

        return $book->relatedItems->filter(function (RelatedItem $item) use ($siteId, $adult) {
            if ($item->relatable instanceof Book) {
                if (
                    $item->relatable->status !== BookStatus::Published
                ) {
                    return false;
                }
                if (
                    $adult === AdultScopeType::EXCLUDE &&
                    $item->relatable->adult
                ) {
                    return false;
                }
                if (
                    $adult === AdultScopeType::ONLY &&
                    ! $item->relatable->adult
                ) {
                    return false;
                }
                if (
                    $item->relatable->sites->map->id->doesntContain($siteId)
                ) {
                    return false;
                }
            }

            return true;
        });
    }
}
