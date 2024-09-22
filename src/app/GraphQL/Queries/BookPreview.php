<?php

namespace App\GraphQL\Queries;

use App\Models\BookPreview as ModelsPreviewBook;

final class BookPreview
{
    /**
     * @param  null  $_
     * @param  array{}  $args
     */
    public function __invoke($_, array $args)
    {
        $preview = ModelsPreviewBook::query()
            ->where('token', $args['token'])
            ->where('created_at', '>', now()->subHours(config('preview.expire_hours')))
            ->first();

        if (! $preview) {
            return null;
        }

        return $preview->model;
    }
}
