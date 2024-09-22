<?php

namespace App\Providers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;

class DebugServiceProvider extends ServiceProvider
{
    public function register(): void
    {
    }

    public function boot(): void
    {
        if (! app()->isProduction()) {
            Model::shouldBeStrict();

            // ENABLE_LOCAL_SQL_LOGGING=true の時に SQL ログを出力する
            if (env('ENABLE_LOCAL_SQL_LOGGING', false)) {
                DB::listen(function (QueryExecuted $query) {
                    $stringableBindings =
                        array_map(fn ($binding) => $this->getStrigableValue($binding), $query->bindings);
                    $sql = \Str::replaceArray('?', $stringableBindings, $query->sql);
                    Log::debug("{$sql};", ['executionTime' => "{$query->time}ms"]);
                });
            }
        }
    }

    private function getStrigableValue(mixed $value): mixed
    {
        if ($value instanceof \DateTimeInterface) {
            return $value->format('Y-m-d H:i:s');
        }

        if (is_bool($value)) {
            return $value ? '1' : '0';
        }

        return $value;
    }
}
