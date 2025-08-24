<?php

namespace Database\Seeders;

use App\Models\Unit;
use Illuminate\Database\Seeder;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Carbon;

class UnitSeeder extends Seeder
{
    public function run(): void
    {
        $path = storage_path('import/DATA_PENJUALAN_2024.xlsx');
        $spreadsheet = IOFactory::load($path);
        $sheet = $spreadsheet->getSheetByName('DATA');
        $rows = $sheet->toArray(null, true, true, true);

        // Ambil unit dan tanggal pertama kali muncul (G = unit, B = tanggal)
        $unitDates = collect($rows)
            ->skip(1)
            ->filter(fn($row) => !empty($row['G']) && !empty($row['B']))
            ->groupBy(function ($row) {
                return strtoupper(trim($row['G']));
            })
            ->map(function ($items, $code) {
                $firstDate = collect($items)->pluck('B')->sort()->first();
                return [
                    'name' => ucfirst(strtolower($code)),
                    'short_name' => $code,
                    'status' => true,
                    'created_at' => \Carbon\Carbon::parse($firstDate),
                    'updated_at' => \Carbon\Carbon::parse($firstDate),
                ];
            })
            ->values()
            ->toArray();

        Unit::insert($unitDates);

        echo count($unitDates) . " unit berhasil diimport langsung dari Excel.\n";
    }
}
