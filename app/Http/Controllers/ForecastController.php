<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Services\NixtlaService;
use Illuminate\Support\Facades\DB;
use OpenAI\Laravel\Facades\OpenAI;

class ForecastController extends Controller
{

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return inertia('forecasting/add-forecast');
    }

    public function generateSeriesData(array $productIds, string $frequency, int $horizon)
    {
        $dateRange = OrderItem::join('orders', 'orders.id', '=', 'order_items.order_id')
            ->whereIn('order_items.product_id', $productIds)
            ->selectRaw('MIN(DATE(orders.created_at)) as start_date, MAX(DATE(orders.created_at)) as end_date')
            ->first();

        if (!$dateRange || !$dateRange->start_date || !$dateRange->end_date) {
            return response()->json([
                'error' => 'Tidak ditemukan data transaksi untuk produk yang dipilih.'
            ], 422);
        }

        $start = Carbon::parse($dateRange->start_date);
        $end = Carbon::parse($dateRange->end_date);

        // if ($end->lt(Carbon::today())) {
        //     $end = Carbon::today();
        // }

        // Generate semua titik waktu berdasarkan frekuensi
        $dates = [];
        if ($frequency === 'W') {
            for ($date = $start->copy()->startOfWeek(); $date->lte($end); $date->addWeek()) {
                $dates[] = $date->format('Y-m-d');
            }
        } elseif ($frequency === 'M') {
            for ($date = $start->copy()->startOfMonth(); $date->lte($end); $date->addMonth()) {
                $dates[] = $date->format('Y-m-d');
            }
        } else {
            for ($date = $start->copy(); $date->lte($end); $date->addDay()) {
                $dates[] = $date->format('Y-m-d');
            }
        }

        // Ambil data berdasarkan frekuensi
        $orderItems = OrderItem::select(
            'product_id',
            DB::raw(match ($frequency) {
                'W' => "YEAR(orders.created_at) as year, WEEK(orders.created_at, 1) as week",
                'M' => "DATE_FORMAT(orders.created_at, '%Y-%m-01') as period",
                default => "DATE(orders.created_at) as period",
            }),
            DB::raw('SUM(order_items.quantity) as total_qty')
        )
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->whereIn('order_items.product_id', $productIds)
            ->groupBy('product_id', match ($frequency) {
                'W' => DB::raw('YEAR(orders.created_at), WEEK(orders.created_at, 1)'),
                'M' => DB::raw("DATE_FORMAT(orders.created_at, '%Y-%m-01')"),
                default => DB::raw("DATE(orders.created_at)"),
            })
            ->get();

        // Inisialisasi series
        $series = [];
        foreach ($productIds as $productId) {
            foreach ($dates as $date) {
                $series[$productId][$date] = 0;
            }
        }

        // Masukkan nilai ke series
        foreach ($orderItems as $item) {
            if ($frequency === 'W') {
                $date = Carbon::now()
                    ->setISODate((int)$item->year, (int)$item->week)
                    ->startOfWeek()
                    ->format('Y-m-d');
            } else {
                $date = $item->period;
            }

            if (isset($series[$item->product_id][$date])) {
                $series[$item->product_id][$date] = $item->total_qty;
            }
        }

        $productNames = Product::whereIn('id', $productIds)->pluck('name', 'id')->toArray();

        $openaiStructured = [];
        foreach ($series as $productId => $dateSeries) {
            $openaiStructured[$productId] = [
                'name' => $productNames[$productId] ?? "Produk ID {$productId}",
                'data' => $dateSeries
            ];
        }

        return [
            'start_date' => $start->toDateString(),
            'end_date' => $end->toDateString(),
            'frequency' => $frequency,
            'horizon' => $horizon,
            'series' => $series,
            'openai_ready' => $openaiStructured,
        ];
    }


    public function requestForecast(NixtlaService $nixtla, Request $request)
    {
        $validatedData = $request->validate([
            'products' => 'required|array',
            'products.*.id' => 'required|exists:products,id',
            'frequency' => 'required|in:D,W,M',
            'horizon' => 'required|integer',
        ]);

        $productIds = collect($validatedData['products'])->pluck('id')->toArray();
        $frequency = $validatedData['frequency'];
        $horizon = $validatedData['horizon'];

        // Generate series
        $result = $this->generateSeriesData($productIds, $frequency, $horizon);

        //dd($result);

        if (isset($result['error'])) {
            return back()->withErrors(['series' => $result['error']]);
        }

        // //Validasi kelayakan tiap produk
        // $errors = [];
        // foreach ($validatedData['products'] as $index => $product) {
        //     $series = $result['series'][$product['id']] ?? [];

        //     $validation = $nixtla->validate($series, 0.2, 5, $frequency);

        //     if (!$validation['valid']) {
        //         $errors["products.{$index}.id"] = "{$product['id']} tidak layak forecast: {$validation['reason']}";
        //     }
        // }

        // if (!empty($errors)) {
        //     throw \Illuminate\Validation\ValidationException::withMessages($errors);
        // }W

        foreach ($validatedData['products'] as $index => $product) {
            $series = $result['series'][$product['id']] ?? [];
            $validation = $nixtla->validate($series, 0.2, 5, $frequency);
            // Tandai bahwa forecast tidak valid, tetapi pola tetap dianalisis
            $result['openai_ready'][$product['id']]['forecast'] = $validation['valid'];
            // remove series if not valid
            if (!$validation['valid']) {
                unset($result['series'][$product['id']]);
            }
        }

        //get time series terbanyak
        // $timeseries = DB::table('order_items as oi')
        //     ->join('orders as o', 'o.id', '=', 'oi.order_id')
        //     ->select('oi.product_id', DB::raw('COUNT(DISTINCT DATE(o.created_at)) as unique_days'))
        //     ->groupBy('oi.product_id')
        //     ->orderByDesc('unique_days')
        //     ->limit(5)
        //     ->get();

        // dd($timeseries);

        // $timeseries =  DB::table('order_items as oi')
        //     ->join('orders as o', 'o.id', '=', 'oi.order_id')
        //     ->select('oi.product_id', DB::raw('COUNT(DISTINCT YEARWEEK(o.created_at, 1)) as unique_weeks'))
        //     ->groupBy('oi.product_id')
        //     ->orderByDesc('unique_weeks')
        //     ->first();


        // dd($validatedData);
        // dd($result['series']);

        // dd($result['series'],  $this->smoothSeries($result['series']));

        // dd($result['series']);

        // dd($result['series']);

        // $result['series'] = $this->smoothSeries($result['series'], 3);

        // dd($result['series']);

        // potong 4 tanggal terakhir
        $result['series'] = array_map(function ($series) {
            $keys = array_keys($series);
            $remainingKeys = array_slice($keys, 0, -4);
            return array_intersect_key($series, array_flip($remainingKeys));
        }, $result['series']);

        //dd($result['series']);


        dd($result);

        $options = [
            'h' => $validatedData['horizon'],
            //'freq' => $validatedData['frequency'],
            'freq' =>  $frequency,
            'model' => 'timegpt-1',
        ];

        $forecast = $nixtla->forecast($result['series'], $options);

        dd($forecast);

        foreach ($forecast as $productId => $data) {
            $result['openai_ready'][$productId]['forecast'] = $data;
        }

        $prompt = "Berikut adalah data penjualan dari beberapa produk. 
            Tugas Anda adalah menganalisis pola penjualan dan memberikan saran manajerial berdasarkan pola tersebut.
            Jika forecast = false, berarti produk tersebut tidak diprediksi, tetapi jika ada hasil forecast,
            Anda harus memberikan saran manajerial dan menyimpulkan apakah hasil forecast tersebut sesuai dengan pola atau tidak dan layak digunakan atau tidak.

            Kembalikan hasil dalam format JSON dengan struktur sebagai berikut:

            {
            \"id\": {
                \"name\": \"nama produk\",
                \"forecast\": \"perkiraan penjualan dan saran manajerial berdasarkan pola data\"
            },
            ...
            }

            Berikut data yang perlu dianalisis:\n\n";

        foreach ($result['openai_ready'] as $productId => $info) {
            $prompt .= "ID: {$productId}\n";
            $prompt .= "Nama Produk: {$info['name']}\n";
            $prompt .= "Data Penjualan (mingguan): " . json_encode($info['data']) . "\n\n";
            if (isset($info['forecast'])) {
                $prompt .= "Forecast: " . json_encode($info['forecast']) . "\n\n";
            } else {
                $prompt .= "Forecast: false\n\n";
            }
        }



        // Kirim ke OpenAI versi murah (gpt-3.5-turbo)
        $response = OpenAI::chat()->create([
            'model' => 'gpt-4o',
            'messages' => [
                ['role' => 'user', 'content' => $prompt],
            ],
            'temperature' => 0.3,
        ]);

        // Ambil dan tampilkan hasil
        $content = $response->choices[0]->message->content;

        // gett json only in $content
        $jsonString = preg_replace('/.*?({.*}).*/s', '$1', $content);

        dd($response, $prompt, $content, $jsonString);

        return response()->json($forecast);
    }

    // OpenAI 


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

    public function smoothSeries(array $series, int $window = 3): array
    {
        $smoothedSeries = [];

        foreach ($series as $productId => $data) {
            $keys = array_keys($data);
            $values = array_values($data);
            $smoothed = [];

            for ($i = 0; $i < count($values); $i++) {
                $start = max(0, $i - ($window - 1));
                $windowSlice = array_slice($values, $start, $window);
                $average = array_sum($windowSlice) / count($windowSlice);
                $smoothed[$keys[$i]] = round($average, 2); // bisa pakai floor/ceil jika integer
            }

            $smoothedSeries[$productId] = $smoothed;
        }

        return $smoothedSeries;
    }
}
