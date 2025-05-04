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

        // dd($seriesGrouped);

        $options = [
            'h' => 7,
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
