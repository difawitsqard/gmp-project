<?php

namespace Database\Seeders;

use App\Models\ProductCategory;
use Illuminate\Database\Seeder;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ProductCategorySeeder extends Seeder
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

        $categories = collect($rows)
            ->skip(1) // Skip header
            ->pluck('D') // Kolom D = Kategori
            ->filter()
            ->unique()
            ->map(function ($kategori) {
                return [
                    'name' => $kategori,
                    'description' => ucfirst(strtolower($kategori)),
                    'status' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            })
            ->values()
            ->toArray();

        // Insert categories into the database
        ProductCategory::insert($categories);

        echo count($categories) . " kategori berhasil diimport.\n";
    }
}
