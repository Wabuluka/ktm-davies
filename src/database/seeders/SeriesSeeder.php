<?php

namespace Database\Seeders;

use Database\Seeders\Helpers\SeedSeriesHelper;
use Illuminate\Database\Seeder;

class SeriesSeeder extends Seeder
{
    public function run(): void
    {
        SeedSeriesHelper::createAll();
    }
}
