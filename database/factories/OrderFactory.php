<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Get a random customer
        $customer = User::role('Pelanggan')->inRandomOrder()->first() ??
            User::factory()->create(['is_active' => true])->assignRole('Pelanggan');

        // Determine shipping method
        $shippingMethod = $this->faker->randomElement(['pickup', 'delivery']);
        $shippingFee = $shippingMethod === 'delivery' ? $this->faker->numberBetween(10000, 50000) : null;

        return [
            'customer_id' => $customer->id,
            'name' => $customer->name,
            'email' => $customer->email,
            'phone' => $customer->phone ?? '08' . $this->faker->numberBetween(100000000, 999999999),
            'address' => $customer->address,
            'shipping_method' => $shippingMethod,
            'shipping_fee' => $shippingFee,
            'sub_total' => 0, // Will be calculated later
            'total' => 0, // Will be calculated later
            'note' => $this->faker->optional(0.3)->sentence(),
            'status' => 'pending', // Default status
            'uplink_id' => null, // Will be set conditionally
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

        $randomDate = $this->faker->dateTimeBetween($startDate, $endDate);

        return $this->state(function (array $attributes) use ($randomDate) {
            return [
                'created_at' => $randomDate,
                'updated_at' => $randomDate,
            ];
        });
    }

    /**
     * Set the order to waiting confirmation status
     *
     * @return $this
     */
    public function waitingConfirmation(): self
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'waiting_confirmation',
                'shipping_method' => 'delivery', // Waiting confirmation is only for delivery
                'shipping_fee' => $this->faker->numberBetween(10000, 50000),
            ];
        });
    }

    /**
     * Set the order to pending status
     *
     * @return $this
     */
    public function pending(): self
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'pending',
            ];
        });
    }

    /**
     * Set the order to completed status with a processor
     *
     * @return $this
     */
    public function completed(): self
    {
        $processor = User::role('Staff Gudang')->inRandomOrder()->first() ??
            User::factory()->create()->assignRole('Staff Gudang');

        return $this->state(function (array $attributes) use ($processor) {
            return [
                'status' => 'completed',
                'processed_by' => $processor->id,
            ];
        });
    }

    /**
     * Set the order to cancelled status
     *
     * @return $this
     */
    public function cancelled(): self
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'cancelled',
            ];
        });
    }

    /**
     * Set an uplink for the order (typically admin)
     *
     * @return $this
     */
    public function withUplink(): self
    {
        $uplink = User::role('Admin')->inRandomOrder()->first() ??
            User::factory()->create()->assignRole('Admin');

        return $this->state(function (array $attributes) use ($uplink) {
            return [
                'uplink_id' => $uplink->id,
            ];
        });
    }
}
