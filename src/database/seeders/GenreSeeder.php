<?php

namespace Database\Seeders;

use Database\Seeders\Helpers\SeedGenreHelper;
use Illuminate\Database\Seeder;

class GenreSeeder extends Seeder
{
    public function run(): void
    {
        SeedGenreHelper::createAll();
    }
}
