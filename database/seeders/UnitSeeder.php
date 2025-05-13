<?php

namespace Database\Seeders;

use App\Models\Unit;
use Illuminate\Database\Seeder;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $path = storage_path('import/DATA_PENJUALAN_2024.xlsx');
        $spreadsheet = IOFactory::load($path);
        $sheet = $spreadsheet->getSheetByName('DATA');
        $rows = $sheet->toArray(null, true, true, true); // gunakan kolom huruf

        $units = collect($rows)
            ->skip(1) // skip header
            ->pluck('G') // kolom G = UoM
            ->filter()
            ->map(fn($v) => strtoupper(trim($v))) // normalisasi kode satuan
            ->unique()
            ->map(function ($code) {
                return [
                    'name' => ucfirst(strtolower($code)),  // hasilkan nama otomatis dari kode
                    'short_name' => $code,
                    'status' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            })
            ->values()
            ->toArray();

        Unit::insert($units);

        echo count($units) . " unit berhasil diimport langsung dari Excel.\n";
    }
}
