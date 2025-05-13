<?php

namespace Database\Seeders;

use App\Models\Unit;
use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Database\Seeder;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class ProductSeeder extends Seeder
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
            ->map(function ($row, $i) use ($categoryMap, $unitMap, $unitShortNames) {
                $kategori = strtoupper(trim($row['D'] ?? ''));
                $nama_barang = trim($row['E'] ?? '');
                $rawSatuan = strtoupper(trim($row['G'] ?? ''));
                $hargaStr = trim($row['H'] ?? '');

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
                    'sku' => 'SKU' . str_pad($i + 1, 5, '0', STR_PAD_LEFT),
                    'qty' => rand(1, 100),
                    'min_stock' => rand(1, 15),
                    'unit_id' => $unitMap[$satuan],
                    'price' => $harga,
                    //'tax' => 11,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            })
            ->filter()
            ->unique(function ($item) {
                return $item['name'] . '-' . $item['unit_id'];
            })
            ->values()
            ->toArray();


        Product::insert($products);

        echo count($products) . " produk berhasil diimport dari Excel.\n";
    }
}
