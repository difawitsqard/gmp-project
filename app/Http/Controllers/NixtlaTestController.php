<?php

namespace App\Http\Controllers;

use Exception;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Services\NixtlaService;
use PhpOffice\PhpSpreadsheet\IOFactory;

class NixtlaTestController extends Controller
{

    public function forecast(NixtlaService $nixtla, Request $request)
    {

        $filePath = public_path('Vehicle_Sales.csv');
        $spreadsheet = IOFactory::load($filePath);
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray();

        $header = $rows[0];
        $unique_id = array_search('vehicle_model', $header);
        $date = array_search('sale_date', $header);
        $qty = array_search('units_sold', $header);
        $is_festival = array_search('is_festival', $header);

        if ($unique_id === false || $date === false || $qty === false) {
            return response()->json(['error' => 'Kolom tidak lengkap di file Excel.'], 400);
        }


        foreach ($rows as $row) {
            if ($row[$unique_id] !== 'vehicle_model') {
                $uid = $row[$unique_id];
                $dateValue = Carbon::parse($row[$date])->format('Y-m-d');
                $qtyValue = (float)$row[$qty];
                $is_festival = (int)$row['2'] ?? 0;

                if (!isset($seriesGrouped[$uid])) {
                    $seriesGrouped[$uid] = [];
                }

                $seriesGrouped[$uid][$dateValue] = [
                    'y' => $qtyValue,
                    'is_vestival' => $is_festival,
                ];
            }
        }

        // $seriesnew = [
        //     'uid1' => [
        //         '2023-01-01' => 10,
        //         '2023-01-02' => 20,
        //         '2023-01-03' => 30,
        //         '2023-01-04' => 40,
        //         '2023-01-05' => 50,
        //         '2023-01-06' => 30,
        //         '2023-01-07' => 40,
        //         '2023-01-08' => 50,
        //     ],
        // ];

        // $seriesGrouped['uid1'] = $seriesnew['uid1'];

        //dd($seriesGrouped);

        $seriesGrouped = [
            "2024-01-01" => 12,
            "2024-01-08" => 0,
            "2024-01-15" => 94,
            "2024-01-22" => 24,
            "2024-01-29" => 0,
            "2024-02-05" => 0,
            "2024-02-12" => 30,
            "2024-02-19" => 0,
            "2024-02-26" => 6,
            "2024-03-04" => 8,
            "2024-03-11" => 0,
            "2024-03-18" => 12,
            "2024-03-25" => 10,
            "2024-04-01" => 90,
            "2024-04-08" => 55,
            "2024-04-15" => 0,
            "2024-04-22" => 0,
            "2024-04-29" => 0,
            "2024-05-06" => 0,
            "2024-05-13" => 0,
            "2024-05-20" => 0,
            "2024-05-27" => 0,
            "2024-06-03" => 6,
            "2024-06-10" => 0,
            "2024-06-17" => 10,
            "2024-06-24" => 0,
            "2024-07-01" => 0,
            "2024-07-08" => 0,
            "2024-07-15" => 20,
            "2024-07-22" => 21,
            "2024-07-29" => 0,
            "2024-08-05" => 12,
            "2024-08-12" => 0,
            "2024-08-19" => 0,
            "2024-08-26" => 0,
            "2024-09-02" => 4,
            "2024-09-09" => 6,
            "2024-09-16" => 10,
            "2024-09-23" => 5,
            "2024-09-30" => 12,
            "2024-10-07" => 12,
            "2024-10-14" => 0,
            "2024-10-21" => 40,
            "2024-10-28" => 10,
            "2024-11-04" => 20,
            "2024-11-11" => 0,
            "2024-11-18" => 0,
            "2024-11-25" => 39
        ];

        $options = [
            'h' => 4,
            'freq' => 'D',
            'model' => 'timegpt-1',
        ];

        $forecast = $nixtla->forecast($seriesGrouped, $options);

        return response()->json($forecast);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
