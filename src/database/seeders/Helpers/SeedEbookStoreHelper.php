<?php

namespace Database\Seeders\Helpers;

use App\Models\EbookStore;
use Illuminate\Database\Eloquent\Builder;

class SeedEbookStoreHelper
{
    protected static $stores = [
        'amazon' => [
            'bannerFile' => '01.jpeg',
        ],
        'rakuten' => [
            'bannerFile' => '04.jpeg',
        ],
        'line' => [
            'bannerFile' => 'line-manga.png',
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
        $factory = EbookStore::factory();
        $data = static::getStoreData($storeName);

        if ($bannerFile = $data['bannerFile'] ?? null) {
            $bannerPath = database_path('seeders/assets/images/store/' . $bannerFile);
            $factory = $factory->attachBanner($bannerPath);
        }

        return $factory
            ->for(SeedStoreHelper::{"get{$storeName}"}())
            ->create(['is_purchase_url_required' => $data['purchaseUrlRequired'] ?? false]);
    }

    private static function get(string $siteName)
    {
        $_data = static::getStoreData($siteName);

        return EbookStore::whereHas('store', fn (Builder $query) => $query->where('code', $siteName))->firstOrFail();
    }

    private static function getStoreData(string $storeName)
    {
        $lowerName = strtolower($storeName);
        $data = static::$stores[$lowerName] ?? throw new \Exception("{$storeName} is invalid store name");

        return $data;
    }
}
