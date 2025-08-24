<?php

namespace Database\Seeders;

use App\Models\ProductCategory;
use Illuminate\Database\Seeder;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Carbon;

class ProductCategorySeeder extends Seeder
{
    public function run(): void
    {
        $path = storage_path('import/DATA_PENJUALAN_2024.xlsx');
        $spreadsheet = IOFactory::load($path);
        $sheet = $spreadsheet->getSheetByName('DATA');
        $rows = $sheet->toArray(null, true, true, true);

        // Ambil kategori dan tanggal pertama kali muncul (D = kategori, B= tanggal)
        $categoryDates = collect($rows)
            ->skip(1)
            ->filter(fn($row) => !empty($row['D']) && !empty($row['B']))
            ->groupBy('D')
            ->map(function ($items, $kategori) {
                $firstDate = collect($items)->pluck('B')->sort()->first();
                return [
                    'name' => $kategori,
                    'description' => ucfirst(strtolower($kategori)),
                    'status' => true,
                    'created_at' => \Carbon\Carbon::parse($firstDate),
                    'updated_at' => \Carbon\Carbon::parse($firstDate),
                ];
            })
            ->values()
            ->toArray();

        ProductCategory::insert($categoryDates);

        echo count($categoryDates) . " kategori berhasil diimport.\n";
    }
}
