<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\Product;
use App\Models\OrderItem;
use App\Models\Payment;
use Illuminate\Support\Carbon;
use Illuminate\Database\Seeder;
use PhpOffice\PhpSpreadsheet\IOFactory;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $path = storage_path('import/DATA_PENJUALAN_2024.xlsx');
        $spreadsheet = IOFactory::load($path);
        $sheet = $spreadsheet->getSheetByName('DATA');
        $rows = $sheet->toArray(null, true, true, true);

        $groupedOrders = [];

        foreach (array_slice($rows, 1) as $row) {
            $customer = trim($row['A'] ?? '');
            $tanggal = trim($row['B'] ?? '');
            $productName = trim($row['E'] ?? '');
            $qty = (int) $row['F'] ?? 0;
            $price = (int) preg_replace('/[^0-9]/', '', $row['H'] ?? '') / 100;
            $total = (int) preg_replace('/[^0-9]/', '', $row['K'] ?? '') / 100;

            if (!$customer || !$productName || !$qty || !$price || !$tanggal) {
                continue;
            }

            $product = Product::where('name', $productName)->first();
            if (!$product) {
                dump("Produk tidak ditemukan: " . $productName);
                continue;
            }

            $subtotal = $qty * $price;
            $orderKey = $customer . '|' . $tanggal;

            $groupedOrders[$orderKey]['name'] = $customer;
            $groupedOrders[$orderKey]['date'] = $tanggal;
            $groupedOrders[$orderKey]['sub_total'] = ($groupedOrders[$orderKey]['sub_total'] ?? 0) + $subtotal;
            $groupedOrders[$orderKey]['total'] = ($groupedOrders[$orderKey]['total'] ?? 0) + $total;

            $groupedOrders[$orderKey]['items'][] = [
                'product_id' => $product->id,
                'quantity' => $qty,
                'price' => $price,
                'subtotal' => $subtotal,
            ];
        }

        foreach ($groupedOrders as $groupKey => $orderData) {
            $orderDate = Carbon::parse($orderData['date']);

            $order = Order::create([
                'name' => $orderData['name'],
                'email' => null,
                'phone' => null,
                'address' => '-',
                'shipping_method' => 'pickup',
                'shipping_fee' => null,
                'sub_total' => $orderData['sub_total'],
                'total' => $orderData['total'],
                'status' => 'completed',
                'uplink_id' => null,
                'created_at' => $orderDate,
                'updated_at' => $orderDate,
            ]);

            foreach ($orderData['items'] as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'subtotal' => $item['subtotal'],
                    'note' => null,
                    'created_at' => $orderDate,
                    'updated_at' => $orderDate,
                ]);
            }

            Payment::create([
                'order_id' => $order->id,
                'status' => 'paid',
                'payment_type' => 'cash',
                'paid_at' => $orderDate,
                'created_at' => $orderDate,
                'updated_at' => $orderDate,
            ]);
        }

        echo count($groupedOrders) . " orders (grouped by name + date) berhasil diimport.\n";
    }
}
