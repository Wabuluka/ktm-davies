<?php

namespace App\GraphQL\Queries;

use App\Models\NewsPreview as ModelsNewsPreview;

final class NewsPreview
{
    /**
     * @param  null  $_
     * @param  array{}  $args
     */
    public function __invoke($_, array $args)
    {
        $preview = ModelsNewsPreview::query()
            ->where('token', $args['token'])
            ->where('created_at', '>', now()->subHours(config('preview.expire_hours')))
            ->first();

        if (! $preview) {
            return null;
        }

        return $preview->model;
    }
}
