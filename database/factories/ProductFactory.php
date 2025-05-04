<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'product_categories_id' => \App\Models\ProductCategory::factory(),
            'name' => fake()->word(),
            'sku' => 'SKU' . fake()->unique()->numberBetween(1000, 9999),
            'qty' => fake()->numberBetween(0, 100),
            'min_stock' => fake()->numberBetween(0, 20),
            'unit_id' => \App\Models\Unit::factory(),
            'price' => fake()->numberBetween(100, 10000),
        ];
    }
}
