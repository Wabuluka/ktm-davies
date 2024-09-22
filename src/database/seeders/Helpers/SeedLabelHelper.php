<?php

namespace Database\Seeders\Helpers;

use App\Enums\LabelType;
use App\Models\Label;
use App\Models\LabelType as ModelsLabelType;

class SeedLabelHelper
{
    protected static $labels = [
        // comic
        'beyondholizon' => [
            'name' => 'Beyond Holizon',
            'url' => null,
            'genre' => 'comic',
            'types' => [LabelType::Paperback],
        ],
        'mapleleafpress' => [
            'name' => 'Maple Leaf Press',
            'url' => null,
            'genre' => 'comic',
            'types' => [LabelType::Paperback],
        ],
        'sapphirestories' => [
            'name' => 'Sapphire Stories',
            'url' => null,
            'genre' => 'comic',
            'types' => [LabelType::Paperback, LabelType::Magazine],
        ],
        'attackoftitan' => [
            'name' => 'Attack of Titan',
            'url' => null,
            'genre' => 'comic',
            'types' => [LabelType::Paperback],
        ],

        // novel
        'shinchobunko' => [
            'name' => 'Shincho Bunko',
            'url' => null,
            'genre' => 'novel',
            'types' => [LabelType::Paperback],
        ],
        'iwanamibunko' => [
            'name' => 'Iwanami Bunko',
            'url' => null,
            'genre' => 'novel',
            'types' => [LabelType::Paperback],
        ],
        'kodansyabunko' => [
            'name' => 'Kodansya Bunko',
            'url' => null,
            'genre' => 'novel',
            'types' => [LabelType::Paperback],
        ],
        'heibonsyabunko' => [
            'name' => 'Heibonsya Bunko',
            'url' => null,
            'genre' => 'novel',
            'types' => [LabelType::Paperback],
        ],
        'beginningnovels' => [
            'name' => 'Beginning Novels',
            'url' => null,
            'genre' => 'novel',
            'types' => [LabelType::Paperback],
        ],

        // genresample1
        'maidenrose' => [
            'name' => 'Maiden Rose',
            'url' => null,
            'genre' => 'genresample1',
            'types' => [LabelType::Paperback],
        ],
        // genresample2
        'wanderingforest' => [
            'name' => 'Wondering Forest',
            'url' => null,
            'genre' => 'genresample2',
            'types' => [LabelType::Paperback],
        ],
        // gamegoods
        'game' => [
            'name' => 'Game',
            'url' => null,
            'genre' => 'gamegoods',
            'types' => [LabelType::Goods],
        ],
        'anime' => [
            'name' => 'Anime',
            'url' => null,
            'genre' => 'gamegoods',
            'types' => [LabelType::Goods],
        ],
        // other
        'artbookfanbook' => [
            'name' => 'ArtBookFanbook',
            'url' => null,
            'genre' => 'other',
            'types' => [],
        ],
        'book' => [
            'name' => 'book',
            'url' => null,
            'genre' => 'other',
            'types' => [],
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
        return array_map(fn ($label) => static::create($label), array_keys(static::$labels));
    }

    private static function create(string $labelName)
    {
        [
            'name' => $name,
            'url' => $url,
            'genre' => $genre,
            'types' => $types,
        ] = static::retrieveLabelData($labelName);
        $getGenre = "get{$genre}";

        return Label::factory()
            ->hasAttached(ModelsLabelType::find(collect($types)->map->value), [], 'types')
            ->create([
                'name' => $name,
                'url' => $url,
                'genre_id' => SeedGenreHelper::$getGenre(),
            ]);
    }

    private static function get(string $labelName)
    {
        ['name' => $name] = static::retrieveLabelData($labelName);

        return Label::whereName($name)->firstOrFail();
    }

    private static function retrieveLabelData(string $labelName)
    {
        $lowerName = strtolower($labelName);
        $data = static::$labels[$lowerName] ?? throw new \Exception('name of method is invalid');

        return $data;
    }
}
