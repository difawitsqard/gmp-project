<?php

namespace Database\Seeders;

use App\Models\Unit;
use App\Models\Product;
use Illuminate\Support\Carbon;
use App\Models\ProductCategory;
use Illuminate\Database\Seeder;
use App\Models\StockTransaction;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $startDate = \Carbon\Carbon::create(2025, 6, 1, 0, 0, 0); // June 1, 2025 at 00:00:00
        $endDate = \Carbon\Carbon::create(2025, 6, 24, 0, 0, 0); // Current date and time

        $randomDateTime = \Carbon\Carbon::createFromTimestamp(
            rand($startDate->timestamp, $endDate->timestamp)
        );


        $path = storage_path('import/DATA_PENJUALAN_2024.xlsx');
        $spreadsheet = IOFactory::load($path);
        $sheet = $spreadsheet->getSheetByName('DATA');
        $rows = $sheet->toArray(null, true, true, true);

        // Buat map kategori dan unit
        $categoryMap = ProductCategory::all()
            ->pluck('id', 'name')
            ->mapWithKeys(fn($id, $name) => [strtoupper($name) => $id])
            ->toArray();

        $unitMap = Unit::all()
            ->pluck('id', 'short_name')
            ->mapWithKeys(fn($id, $short) => [strtoupper($short) => $id])
            ->toArray();

        $unitShortNames = array_keys($unitMap);
        $products = collect($rows)
            ->skip(1)
            ->map(function ($row, $i) use ($categoryMap, $unitMap, $unitShortNames, $randomDateTime) {
                $kategori = strtoupper(trim($row['D'] ?? ''));
                $nama_barang = trim($row['E'] ?? '');
                $tanggal = trim($row['B'] ?? '');
                $rawSatuan = strtoupper(trim($row['G'] ?? ''));
                $hargaStr = trim($row['H'] ?? '');

                $orderDate = Carbon::parse($tanggal);

                // Bersihkan harga dari simbol
                $harga = (int) preg_replace('/[^0-9]/', '', $hargaStr) / 100;

                // normalisasi satuan
                $satuan = isset($unitMap[$rawSatuan])
                    ? $rawSatuan
                    : $unitShortNames[array_rand($unitShortNames)];

                if (!$rawSatuan) {
                    dump("Normalisasi Satuan baris ke-{$i} ($satuan):", $row);
                }

                if (!$kategori || !$nama_barang || !$harga) {
                    dump("Data tidak lengkap di baris ke-{$i}:", $row);
                    return null;
                }

                if (!isset($categoryMap[$kategori])) {
                    dump("Kategori tidak ditemukan: {$kategori}");
                    return null;
                }

                return [
                    'product_categories_id' => $categoryMap[$kategori],
                    'name' => $nama_barang,
                    'sku' => 'SKU' . strtoupper(substr(str_shuffle('0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 6)),
                    'qty' =>  rand(0, 150),
                    'min_stock' => rand(1, 30),
                    'unit_id' => $unitMap[$satuan],
                    'price' => $harga,
                    //'tax' => 11,
                    'created_at' =>           $orderDate,
                    'updated_at' =>            $orderDate,
                ];
            })
            ->filter()
            ->unique(function ($item) {
                return $item['name'] . '-' . $item['unit_id'];
            })
            ->values()
            ->toArray();

        // Insert products into the database
        Product::insert($products);

        // inisialisasi stock
        foreach ($products as $product) {
            $productModel = Product::where('sku', $product['sku'])->first();
            if ($productModel) {
                // Create stock transaction with proper fields
                StockTransaction::create([
                    'product_id' => $productModel->id,
                    'type' => 'in', // Incoming stock
                    'qty' =>  $productModel->qty,
                    'stock_before' => 0, // Initial stock is 0
                    'stock_after' => $productModel->qty, // After adding initial stock
                    'source_type' => null,
                    'source_id' => null,
                    'created_by' => null, // No user created this (system)
                    'note' => "Stok awal produk {$productModel->name} ({$productModel->sku})",
                    'batch_reference' => "INITIAL-STOCK-{$productModel->id}",
                    'created_at' => $productModel->created_at,
                    'updated_at' => $productModel->updated_at
                ]);
            }
        }

        echo count($products) . " produk berhasil diimport dari Excel.\n";
    }
}
