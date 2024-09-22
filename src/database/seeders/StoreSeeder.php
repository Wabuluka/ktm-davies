<?php

namespace Database\Seeders;

use Database\Seeders\Helpers\SeedBookStoreHelper;
use Database\Seeders\Helpers\SeedEbookStoreHelper;
use Database\Seeders\Helpers\SeedGoodsStoreHelper;
use Database\Seeders\Helpers\SeedStoreHelper;
use Illuminate\Database\Seeder;

class StoreSeeder extends Seeder
{
    public function run(): void
    {
        SeedStoreHelper::createAll();
        SeedBookStoreHelper::createAll();
        SeedEbookStoreHelper::createAll();
        SeedGoodsStoreHelper::createAll();
    }
}
