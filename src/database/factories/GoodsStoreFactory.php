<?php

namespace Database\Factories;

use App\Models\Store;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\GoodsStore>
 */
class GoodsStoreFactory extends Factory
{
    public function definition(): array
    {
        return [
            'store_id' => Store::factory(),
        ];
    }
}
