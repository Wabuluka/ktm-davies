<?php

namespace Database\Seeders\Helpers;

use App\Models\GoodsStore;
use App\Models\Store;

class SeedGoodsStoreHelper
{
    protected static $stores = [
        'animate',
        'gamers',
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
        return array_map(fn ($storeName) => static::create($storeName), static::$stores);
    }

    private static function create(string $storeName)
    {
        return GoodsStore::factory()
            ->for(SeedStoreHelper::{"get{$storeName}"}())
            ->create();
    }

    private static function get(string $storeName)
    {
        ['name' => $name] = static::retrieveStoreData($storeName);

        return Store::whereName($name)->firstOrFail();
    }

    private static function retrieveStoreData(string $storeName)
    {
        $lowerName = strtolower($storeName);
        $data = static::$stores[$lowerName] ?? throw new \Exception("{$storeName} is invalid store name");

        return $data;
    }
}
