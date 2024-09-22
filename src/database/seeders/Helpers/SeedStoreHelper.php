<?php

namespace Database\Seeders\Helpers;

use App\Models\Store;

class SeedStoreHelper
{
    protected static $stores = [
        // physical & e Books
        'amazon' => [
            'name' => 'Amazon',
            'code' => 'amazon',
            'url' => 'https://www.amazon.co.jp',
        ],
        'rakuten' => [
            'name' => 'Rakuten Books',
            'code' => 'rakuten',
            'url' => 'https://books.rakuten.co.jp',
        ],
        // physical book only
        '7net' => [
            'name' => '7net shopping',
            'code' => '7net',
            'url' => 'https://7net.omni7.jp',
        ],
        'ehon' => [
            'name' => 'e-hon',
            'code' => 'ehon',
            'url' => 'https://www.e-hon.ne.jp',
        ],
        'yodobashi' => [
            'name' => 'Yodobashi dot com',
            'code' => 'yodobashi',
            'url' => 'https://www.yodobashi.com',
        ],
        'tsutaya' => [
            'name' => 'TSUTAYA Online Shopping',
            'code' => 'tsutaya',
            'url' => 'https://shop.tsutaya.co.jp',
        ],
        'kinokuniya' => [
            'name' => 'Kinokuniya Bookstore',
            'code' => 'kinokuniya',
            'url' => 'https://www.kinokuniya.co.jp',
        ],
        // ebook only
        'dmm' => [
            'name' => 'DMM',
            'code' => 'dmm',
            'url' => 'https://www.dmm.com',
        ],
        'renta' => [
            'name' => 'Renta!',
            'code' => 'renta',
            'url' => 'https://renta.papy.co.jp',
        ],
        'line' => [
            'name' => 'LINE Manga',
            'code' => 'line',
            'url' => 'https://manga.line.me',
        ],
        // Goods only
        'animate' => [
            'name' => 'Animate',
            'code' => 'animate',
            'url' => 'https://www.animate-onlineshop.jp',
        ],
        'toranoana' => [
            'name' => 'Toranoana',
            'code' => 'toranoana',
            'url' => 'https://www.toranoana.jp/',
        ],
        'gamers' => [
            'name' => 'Gamers',
            'code' => 'gamers',
            'url' => 'https://www.gamers.co.jp/',
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
        return array_map(fn ($store) => static::create($store), array_keys(static::$stores));
    }

    private static function create(string $storeName)
    {
        ['name' => $name, 'code' => $code, 'url' => $url] = static::retrieveStoreData($storeName);

        return Store::factory()->create(['name' => $name, 'code' => $code, 'url' => $url]);
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
