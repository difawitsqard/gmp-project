<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\Product;
use App\Models\OrderItem;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $product = Product::where('qty', '>', 0)->inRandomOrder()->first();

        // Default values if no product found
        $price = 10000;
        $quantity = $this->faker->numberBetween(1, 5);

        if ($product) {
            $price = $product->price;
            // Don't order more than available stock
            $quantity = $this->faker->numberBetween(1, min(5, $product->qty));
        }

        return [
            'order_id' => Order::factory(),
            'product_id' => $product ? $product->id : null,
            'quantity' => $quantity,
            'price' => $price,
            'subtotal' => $price * $quantity,
            'note' => $this->faker->optional(0.3)->sentence(),
        ];
    }

    /**
     * Configure the model factory.
     *
     * @return $this
     */
    public function configure()
    {
        return $this->afterMaking(function (OrderItem $orderItem) {
            // Ensure subtotal is calculated correctly
            if ($orderItem->price && $orderItem->quantity) {
                $orderItem->subtotal = $orderItem->price * $orderItem->quantity;
            }
        });
    }

    /**
     * Set custom date for created_at and updated_at
     *
     * @param \Carbon\Carbon $date
     * @return $this
     */
    public function withDate($date)
    {
        return $this->state(function (array $attributes) use ($date) {
            return [
                'created_at' => $date,
                'updated_at' => $date,
            ];
        });
    }
}
