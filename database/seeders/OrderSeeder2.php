<?php

namespace Database\Seeders;

use App\Models\Tax;
use App\Models\User;
use App\Models\Order;
use App\Models\Product;
use App\Models\Payment;
use App\Models\StockTransaction;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Faker\Factory as FakerFactory;

class OrderSeeder2 extends Seeder
{
    protected $faker;

    public function __construct()
    {
        $this->faker = FakerFactory::create('id_ID');
    }

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Set date range
        $startDate = Carbon::create(2025, 7, 27);
        $endDate = Carbon::create(2025, 8, 8);

        // Check if we have customers, staff, and products
        $customerCount = User::role('Pelanggan')->count();
        $staffCount = User::role('Staff Gudang')->count();
        $productCount = Product::where('qty', '>', 0)->count();

        if ($customerCount === 0 || $productCount === 0) {
            $this->command->error('No customers or products found. Please seed users and products first.');
            return;
        }

        // Get active taxes
        $taxes = Tax::where('status', 1)->get();

        // Create orders with different statuses
        $orderConfigs = [
            [
                'status' => 'completed',
                'count' => 30,
                'paymentStatus' => 'paid',
                'withProcessor' => true,
            ],
            // [
            //     'status' => 'pending',
            //     'count' => 15,
            //     'paymentStatus' => 'pending',
            //     'withProcessor' => false,
            // ],
            // [
            //     'status' => 'waiting_confirmation',
            //     'count' => 10,
            //     'paymentStatus' => null,
            //     'withProcessor' => false,
            // ],
            [
                'status' => 'cancelled',
                'count' => 3,
                'paymentStatus' => 'cancelled',
                'withProcessor' => false,
            ],
        ];

        $this->command->info('Creating orders...');

        foreach ($orderConfigs as $config) {
            $this->createOrders(
                $config['status'],
                $config['count'],
                $taxes,
                $startDate,
                $endDate,
                $config['paymentStatus'],
                $config['withProcessor']
            );
        }

        $this->command->info('Order seeding completed successfully!');

        // create order 

        // Set date range
        $startDate = Carbon::create(2025, 8, 11);
        $endDate = Carbon::create(2025, 8, 21);

        // Create orders with different statuses
        $orderConfigs = [
            [
                'status' => 'completed',
                'count' => 13,
                'paymentStatus' => 'paid',
                'withProcessor' => true,
            ],
            // [
            //     'status' => 'pending',
            //     'count' => 15,
            //     'paymentStatus' => 'pending',
            //     'withProcessor' => false,
            // ],
            // [
            //     'status' => 'waiting_confirmation',
            //     'count' => 10,
            //     'paymentStatus' => null,
            //     'withProcessor' => false,
            // ],
            [
                'status' => 'cancelled',
                'count' => 3,
                'paymentStatus' => 'cancelled',
                'withProcessor' => false,
            ],
        ];

        $this->command->info('Creating orders...');
        foreach ($orderConfigs as $config) {
            $this->createOrders(
                $config['status'],
                $config['count'],
                $taxes,
                $startDate,
                $endDate,
                $config['paymentStatus'],
                $config['withProcessor']
            );
        }

        $this->command->info('Order seeding completed successfully!');
    }

    /**
     * Create orders with specified status
     */
    private function createOrders(
        string $status,
        int $count,
        $taxes,
        Carbon $startDate,
        Carbon $endDate,
        ?string $paymentStatus = null,
        bool $withProcessor = false
    ): void {
        $this->command->info("Creating {$count} orders with status: {$status}");

        // Get customers and staff
        $customers = User::role('Pelanggan')->where('email', '!=', 'pelanggan@example.com')->get();
        $staffs = User::role('Staff Gudang')->where('email', '!=', 'staffgudang@example.com')->get();
        $admins = User::role('Admin')->where('email', '!=', 'admin@example.com')->get();

        for ($i = 0; $i < $count; $i++) {
            DB::beginTransaction();

            try {
                // Generate random date within range
                $randomTimestamp = rand($startDate->timestamp, $endDate->timestamp);
                $orderDate = Carbon::createFromTimestamp($randomTimestamp);

                // Set time to business hours (8 AM - 3 PM)
                $hour = rand(8, 15);
                $minute = rand(0, 59);
                $second = rand(0, 59);
                $orderDate->setTime($hour, $minute, $second);

                // Get random customer
                $customer = $customers->random();

                // Determine shipping method (for waiting_confirmation, always use delivery)
                $shippingMethod = $status === 'waiting_confirmation' ? 'delivery' :
                    $this->faker->randomElement(['pickup', 'delivery']);

                $shippingFee = $shippingMethod === 'delivery' ? rand(10000, 50000) : null;

                // Create order
                $order = new Order();
                $order->uuid = (string) \Illuminate\Support\Str::uuid();
                $order->customer_id = $customer->id;
                $order->name = $customer->name;
                $order->email = $customer->email;
                $order->phone = $customer->phone ?? '08' . rand(10000000, 99999999);
                $order->address = $customer->address;
                $order->shipping_method = $shippingMethod;
                $order->shipping_fee = $shippingFee;
                $order->status = $status;
                $order->sub_total = 0; // Set initial value
                $order->total = 0;     // Set initial value

                // Set processor for completed orders
                if ($withProcessor && !$staffs->isEmpty()) {
                    $order->processed_by = $staffs->random()->id;
                }

                // 30% chance of having uplink (admin)
                if (rand(1, 10) <= 3 && !$admins->isEmpty()) {
                    $order->uplink_id = $admins->random()->id;
                }

                // add note status success
                if ($status === 'completed') {
                    $successNotes = [
                        'Informasi pengiriman lanjutkan akan dihubungi oleh kurir yang bersangkutan',
                        'Pengiriman sudah diproses, nomor kurir 082294428433 (Raka R)'
                    ];
                    $order->note = $this->faker->randomElement($successNotes);
                } else {
                    $order->note = '';
                }

                // Set dates
                $order->created_at = $orderDate;
                $order->updated_at = $orderDate;

                $order->save();

                // Add 1-5 order items
                $subTotal = $this->addOrderItems($order, $orderDate);

                // Add taxes
                $taxAmount = $this->addTaxes($order, $taxes, $subTotal, $orderDate);

                // Calculate and update total
                $total = $subTotal + $taxAmount + ($shippingFee ?? 0);
                $order->sub_total = $subTotal;
                $order->total = $total;
                $order->save();

                // Create payment record if needed
                if ($paymentStatus) {
                    $this->createPayment($order, $paymentStatus, $orderDate);
                }

                DB::commit();

                if (($i + 1) % 10 === 0) {
                    $this->command->info("Created " . ($i + 1) . " {$status} orders");
                }
            } catch (\Exception $e) {
                DB::rollBack();
                $this->command->error("Error creating order: " . $e->getMessage());
            }
        }
    }

    /**
     * Add order items to an order
     */
    private function addOrderItems(Order $order, Carbon $orderDate): float
    {
        // Maximum order total limit
        $maxOrderTotal = 1380000;

        // Reserve space for potential shipping fee and taxes (approximately 15%)
        $maxReservedForFeesAndTaxes = $maxOrderTotal * 0.15;

        // Maximum subtotal we can have
        $maxSubtotal = $maxOrderTotal - $maxReservedForFeesAndTaxes - ($order->shipping_fee ?? 0);

        // Get available products
        $availableProducts = Product::where('qty', '>', 0)->inRandomOrder()->get();
        $subTotal = 0;
        $orderItems = [];

        // Select products while staying under maximum subtotal
        foreach ($availableProducts as $product) {
            // Skip if product is too expensive on its own
            if ($product->price > $maxSubtotal) {
                continue;
            }

            // Calculate maximum quantity based on price and remaining budget
            $remainingBudget = $maxSubtotal - $subTotal;
            $maxQty = min(
                floor($remainingBudget / $product->price), // Max based on budget
                min(3, $product->qty), // Max 3 or available stock
                5 // Never more than 5 of any product
            );

            // Skip if we can't add at least one
            if ($maxQty < 1) {
                continue;
            }

            // Random quantity between 1 and max
            $quantity = rand(1, $maxQty);
            $price = $product->price;
            $itemSubtotal = $price * $quantity;

            // Add to our order
            $orderItems[] = [
                'product' => $product,
                'quantity' => $quantity,
                'price' => $price,
                'subtotal' => $itemSubtotal
            ];

            $subTotal += $itemSubtotal;

            // Stop if we have enough items (1-5 products)
            if (count($orderItems) >= rand(1, 5) || $subTotal >= $maxSubtotal * 0.95) {
                break;
            }
        }

        // Create order items
        foreach ($orderItems as $item) {
            $order->items()->create([
                'product_id' => $item['product']->id,
                'quantity' => $item['quantity'],
                'price' => $item['price'],
                'subtotal' => $item['subtotal'],
                'created_at' => $orderDate,
                'updated_at' => $orderDate,
            ]);

            // If completed order, decrease product stock
            if ($order->status === 'completed') {
                // Create stock transaction
                StockTransaction::create([
                    'product_id' => $item['product']->id,
                    'type' => 'out',
                    'qty' => $item['quantity'],
                    'stock_before' => $item['product']->qty,
                    'stock_after' => $item['product']->qty - $item['quantity'],
                    'source_type' => Order::class,
                    'source_id' => $order->id,
                    'note' => "Order {$order->uuid}",
                    'batch_reference' => "ORDER-{$order->id}",
                    'created_at' => $orderDate,
                    'updated_at' => $orderDate,
                ]);

                // Update product stock
                $item['product']->qty -= $item['quantity'];
                $item['product']->save();
            }
        }

        return $subTotal;
    }

    /**
     * Add taxes to an order
     */
    private function addTaxes(Order $order, $taxes, float $subTotal, Carbon $orderDate): float
    {
        $taxAmount = 0;

        foreach ($taxes as $tax) {
            $amount = $tax->percent
                ? ($subTotal * $tax->percent) / 100
                : ($tax->fixed_amount ?? 0);

            $order->taxes()->attach($tax->id, [
                'amount' => $amount,
                'created_at' => $orderDate,
                'updated_at' => $orderDate,
            ]);

            $taxAmount += $amount;
        }

        return $taxAmount;
    }

    /**
     * Create payment record for an order
     */
    private function createPayment(Order $order, string $status, Carbon $orderDate): void
    {
        $paymentTypes = [
            'bank_transfer',
            // 'gopay',
            // 'shopeepay',
            // 'credit_card'
        ];
        $paymentType = $status !== 'pending' ? $paymentTypes[array_rand($paymentTypes)] : null;

        // Map our statuses to payment statuses based on the database ENUM values
        $paymentStatusMap = [
            'success' => 'paid',
            'paid' => 'paid',
            'pending' => 'pending',
            'cancelled' => 'cancelled',
            'cancel' => 'cancelled'
        ];

        $paymentStatus = $paymentStatusMap[$status] ?? 'pending';

        // For successful payments, add a paid_at date slightly after order date
        $paidAt = null;
        if ($status === 'success' || $status === 'paid') {
            // Clone the order date and add random minutes
            $paidAt = (clone $orderDate)->addMinutes(rand(5, 60));

            // If payment time is after 3 PM, move to the next business day
            if ($paidAt->hour > 15) {
                $paidAt->addDay()->setTime(
                    rand(8, 10), // Morning hours for next-day payments
                    rand(0, 59),
                    rand(0, 59)
                );
            }
        }

        Payment::create([
            'order_id' => $order->id,
            'midtrans_uuid' => $order->uuid,
            'payment_type' => $paymentType,
            'snap_token' => (string) \Illuminate\Support\Str::uuid(),
            'status' => $paymentStatus,
            'paid_at' => $paidAt,
            'created_at' => $orderDate,
            'updated_at' => $paidAt ?? $orderDate,
        ]);
    }
}
