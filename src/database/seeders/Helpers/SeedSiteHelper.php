<?php

namespace Database\Seeders\Helpers;

use App\Models\Site;

class SeedSiteHelper
{
    protected static $sites = [
        'ktcom' => [
            'name' => 'KTC本体サイト',
            'code' => 'ktcom',
            'url' => 'https://ktcom.jp',
            'logoFile' => 'ktcom.jpeg',
            'book_preview_path' => 'book/preview/[token]',
            'news_preview_path' => 'topics/preview/[token]',
            'page_preview_path' => 'page/preview/[token]?slug=[slug]',
        ],
        'ceriserose' => [
            'name' => 'スリーズロゼ',
            'code' => 'ceriserose',
            'url' => 'https://ceriserose.jp',
            'logoFile' => 'ceriserose.png',
            'book_preview_path' => 'book/preview/[token]',
            'news_preview_path' => 'news/preview/[token]',
            'page_preview_path' => 'page/preview/[token]',
        ],
        'chocolatsucre' => [
            'name' => 'ショコラシュクレ',
            'code' => 'chocolatsucre',
            'url' => 'https://cs.ktcom.jp',
            'logoFile' => 'chocolat-sucre.png',
            'book_preview_path' => 'book/preview/[token]',
            'news_preview_path' => 'news/preview/[token]',
            'page_preview_path' => 'page/preview/[token]',
        ],
        'blackcherry' => [
            'name' => 'ブラックチェリー',
            'code' => 'blackcherry',
            'url' => 'https://ceriserose.jp/r18',
            'logoFile' => 'black-cherry.jpeg',
            'book_preview_path' => 'book/preview/[token]',
            'news_preview_path' => 'news/preview/[token]',
            'page_preview_path' => 'page/preview/[token]',
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
        throw new \Exception('メソッドが見つかりません');
    }

    public static function createAll()
    {
        return array_map(fn ($site) => static::create($site), array_keys(static::$sites));
    }

    private static function create(string $siteName)
    {
        [
            'name' => $name,
            'url' => $url,
            'code' => $code,
            'logoFile' => $logoFile,
            'book_preview_path' => $bookPreviewPath,
            'news_preview_path' => $newsPreviewPath,
            'page_preview_path' => $pagePreviewPath,
        ] = static::retrieveSiteData($siteName);

        return Site::factory()
            ->afterCreating(fn (Site $site) => $site
                ->addMedia(database_path('seeders/assets/images/site/' . $logoFile))
                ->preservingOriginal()
                ->toMediaCollection())
            ->create([
                'name' => $name,
                'code' => $code,
                'url' => $url,
                'book_preview_path' => $bookPreviewPath,
                'news_preview_path' => $newsPreviewPath,
                'page_preview_path' => $pagePreviewPath,
            ]);
    }

    private static function get(string $siteName)
    {
        ['name' => $name] = static::retrieveSiteData($siteName);

        return Site::whereName($name)->firstOrFail();
    }

    private static function retrieveSiteData(string $siteName)
    {
        $lowerName = strtolower($siteName);
        $data = static::$sites[$lowerName] ?? throw new \Exception('サイト名が不正です');

        return $data;
    }
}
