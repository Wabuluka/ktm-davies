<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Block>
 */
class BlockFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'type_id' => fake()->numberBetween(1, 9), // 購入先 ~ 自由欄s
            'custom_title' => '自由欄 - ' . fake()->word(),
            'custom_content' => implode('', [
                '<p>' . fake()->paragraphs(2, true) . '</p>',
                '<p><b><strong>' . fake()->paragraphs(1, true) . '</strong></b></p>',
                '<p style="color:blue;">' . fake()->paragraphs(2, true) . '</p>',
            ]),
            'displayed' => fake()->boolean(),
        ];
    }
}
