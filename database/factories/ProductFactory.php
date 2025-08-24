<?php

namespace Database\Factories;

use App\Models\Product;
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

        // Generate a realistic SKU
        $sku = 'SKU' . strtoupper(substr(str_shuffle('0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 6));

        return [
            'product_categories_id' => \App\Models\ProductCategory::factory(),
            'name' => fake()->word(),
            'description' =>  $this->faker->optional(0.7)->realText(100),
            'sku' => $sku,
            'qty' => fake()->numberBetween(0, 100),
            'min_stock' => fake()->numberBetween(0, 20),
            'unit_id' => \App\Models\Unit::factory(),
            'price' => fake()->numberBetween(100, 10000),
        ];
    }

    /**
     * Set a custom date range for created_at and updated_at
     *
     * @param string|null $startDate Start date (format: Y-m-d)
     * @param string|null $endDate End date (format: Y-m-d)
     * @return $this
     */
    public function dateRange(?string $startDate = null, ?string $endDate = null): self
    {
        $startDate = $startDate ? \Carbon\Carbon::parse($startDate) : now()->subMonths(6);
        $endDate = $endDate ? \Carbon\Carbon::parse($endDate) : now();

        // Generate random date within range
        $randomTimestamp = rand($startDate->timestamp, $endDate->timestamp);
        $randomDate = \Carbon\Carbon::createFromTimestamp($randomTimestamp);

        // Set time to business hours (8 AM - 3 PM)
        $hour = rand(8, 15);
        $minute = rand(0, 59);
        $second = rand(0, 59);
        $randomDate->setTime($hour, $minute, $second);

        return $this->state(function (array $attributes) use ($randomDate) {
            return [
                'created_at' => $randomDate,
                'updated_at' => $randomDate,
            ];
        });
    }

    /**
     * Configure the model factory.
     *
     * @return $this
     */
    public function withInitialStock(int $minQty = 10, int $maxQty = 100): self
    {
        return $this->afterCreating(function (Product $product) use ($minQty, $maxQty) {
            $qty = $this->faker->numberBetween($minQty, $maxQty);

            // Create stock transaction for the initial stock
            \App\Models\StockTransaction::create([
                'product_id' => $product->id,
                'type' => 'in',
                'qty' => $qty,
                'stock_before' => 0,
                'stock_after' => $qty,
                'source_type' => Product::class,
                'source_id' => $product->id,
                'note' => "Stok awal produk {$product->name} ({$product->sku})",
                'batch_reference' => "INITIAL-STOCK-{$product->id}",
                'created_at' => $product->created_at,
                'updated_at' => $product->updated_at
            ]);

            // Update the product's qty
            $product->qty = $qty;
            $product->save();
        });
    }
}
