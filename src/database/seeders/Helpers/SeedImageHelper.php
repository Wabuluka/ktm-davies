<?php

namespace Database\Seeders\Helpers;

class SeedImageHelper
{
    /**
     * get path of dummy images
     */
    public static function getDummyImagePath(string $imageType): string
    {
        return database_path("seeders/assets/images/{$imageType}/" . match ($imageType) {
            'banner' => collect([
                '01.jpeg',
                '02.jpeg',
                '03.jpeg',
                '04.jpeg',
                '05.jpeg',
            ])->random(),

            'benefit' => collect([
                '01.jpeg',
                '02.jpeg',
                '03.jpeg',
                '04.jpeg',
            ])->random(),

            'character' => collect([
                '01.jpeg',
                '02.jpeg',
                '03.jpeg',
                '04.jpeg',
            ])->random(),

            'cover' => collect([
                '01.jpeg',
                '02.jpeg',
            ])->random(),

            'news' => collect([
                '01.jpeg',
                '02.jpeg',
                '03.jpeg',
                '04.jpeg',
                '05.jpeg',
            ])->random(),

            'store' => collect([
                '01.jpeg',
                '02.jpeg',
                '03.jpeg',
                '04.jpeg',
            ])->random(),

            'story' => collect([
                '01.jpeg',
                '02.jpeg',
                '03.jpeg',
                '04.jpeg',
            ])->random(),
        });
    }
}
