<?php

namespace Database\Seeders\Helpers;

use App\Models\Genre;

class SeedGenreHelper
{
    protected static $genres = [
        'comic' => [
            'name' => 'comic',
        ],
        'novel' => [
            'name' => 'novel',
        ],
        'gamegoods' => [
            'name' => 'gamegoods',
        ],
        'genresample1' => [
            'name' => 'genresample1',
        ],
        'genresample2' => [
            'name' => 'genresample2',
        ],
        'other' => [
            'name' => 'other',
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
        return array_map(fn ($genre) => static::create($genre), array_keys(static::$genres));
    }

    private static function create(string $genreName)
    {
        $data = static::retrieveGenreData($genreName);

        return Genre::factory()->create($data);
    }

    private static function get(string $genreName)
    {
        ['name' => $name] = static::retrieveGenreData($genreName);

        return Genre::whereName($name)->firstOrFail();
    }

    private static function retrieveGenreData(string $genreName)
    {
        $lowerName = strtolower($genreName);
        $data = static::$genres[$lowerName] ?? throw new \Exception('name of method is invalid');

        return $data;
    }
}
