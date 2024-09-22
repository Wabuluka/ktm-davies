<?php

namespace App\Console\Commands\Preview;

use App\Models\BookPreview;
use App\Models\NewsPreview;
use App\Models\PagePreview;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class DestroyExpired extends Command
{
    /** @var string */
    protected $signature = 'app:preview:destroy-expired {model*}';

    /** @var string */
    protected $description = '期限切れのプレビューを削除する';

    public function handle()
    {
        $models = array_map(
            fn ($model) => Str::studly($model),
            Arr::wrap($this->argument('model')),
        );

        foreach ($models as $model) {
            $count = $this->destroyExpiredPreviewsOf($model);
            if ($count === 0) {
                $this->info("No expired previews of {$model} found.");

                continue;
            }
            $this->info("Destroyed {$count} expired previews of {$model}.");
        }
    }

    private function destroyExpiredPreviewsOf(string $model): int
    {
        $previewModel = match ($model) {
            'Book' => BookPreview::class,
            'News' => NewsPreview::class,
            'Page' => PagePreview::class,
        };
        $hours = config('preview.expire_hours');

        return $previewModel::destroy(
            $previewModel::where('created_at', '<=', now()->subHours($hours))->pluck('id'),
        );
    }
}
