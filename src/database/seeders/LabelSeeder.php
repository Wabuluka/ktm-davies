<?php

namespace Database\Seeders;

use Database\Seeders\Helpers\SeedLabelHelper;
use Illuminate\Database\Seeder;

class LabelSeeder extends Seeder
{
    public function run(): void
    {
        SeedLabelHelper::createAll();
    }
}
