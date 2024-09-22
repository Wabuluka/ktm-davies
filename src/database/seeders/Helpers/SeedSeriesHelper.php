<?php

namespace Database\Seeders\Helpers;

use App\Models\Series;
use Illuminate\Support\Str;

class SeedSeriesHelper
{
    protected static $series = [
        'comic-dangerous-separated-volume' => [
            'name' => 'Comic-Dangerous separated volume',
        ],
    ];

    public static function __callStatic($method, $args)
    {
        if (str_starts_with($method, 'create')) {
            return static::create(preg_replace('/^create/i', '', $method));
        }
        if (str_starts_with($method, 'get')) {
            return static::get(preg_replace('/^get/i', '', $method));
        }
        throw new \Exception('method is not found');
    }

    public static function createAll()
    {
        return array_map(fn ($series) => static::create($series), array_keys(static::$series));
    }

    private static function create(string $seriesName)
    {
        $data = static::getSeriesData($seriesName);

        return Series::factory()->create($data);
    }

    private static function get(string $seriesName)
    {
        ['name' => $name] = static::getSeriesData($seriesName);

        return Series::whereName($name)->firstOrFail();
    }

    private static function getSeriesData(string $seriesName)
    {
        $snake = Str::kebab($seriesName);
        $data = static::$series[$snake] ?? throw new \Exception("{$seriesName} is invalid method name");

        return $data;
    }
}
