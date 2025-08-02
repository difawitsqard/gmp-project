<?php

namespace App\Http\Controllers;

use Throwable;
use App\Models\Product;
use App\Models\Forecast;
use App\Models\OrderItem;
use Illuminate\Bus\Batch;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Services\NixtlaService;
use App\Services\OpenAIService;
use App\Jobs\AnalyzeForecastJob;
use App\Jobs\ForecastProductJob;
use Illuminate\Support\Facades\DB;
use OpenAI\Laravel\Facades\OpenAI;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class ForecastController extends Controller
{

    /**
     * Generate time series data dari request parameters
     */
    public function generateTimeSeriesFromRequest2(Request $request)
    {
        $validatedData = $request->validate([
            'products' => 'required|array',
            'products.*.id' => 'required|exists:products,id',
            'frequency' => 'required|in:D,W,M',
            'horizon' => 'required|integer|min:1',
            'startDate' => 'required|date',
            'endDate' => 'required|date|after_or_equal:startDate',
        ]);

        $productIds = collect($validatedData['products'])->pluck('id')->toArray();
        $frequency = $validatedData['frequency'];
        $horizon = $validatedData['horizon'];
        $startDate = Carbon::parse($validatedData['startDate']);
        $endDate = Carbon::parse($validatedData['endDate']);

        // Generate semua titik waktu berdasarkan frekuensi
        $dates = $this->generateDatesFromRange($startDate, $endDate, $frequency);

        // Inisialisasi series
        $series = [];
        foreach ($productIds as $productId) {
            // Panggil method terpisah untuk mendapatkan time series data
            $series[$productId] = $this->getTimeSeriesData(
                $productId,
                $frequency,
                $startDate,
                $endDate,
                $dates
            );
        }

        $productNames = Product::whereIn('id', $productIds)->pluck('name', 'id')->toArray();

        // Struktur data untuk OpenAI dan validasi
        $openaiStructured = [];
        $validationResults = [];

        foreach ($series as $productId => $dateSeries) {
            $openaiStructured[$productId] = [
                'name' => $productNames[$productId] ?? "Produk ID {$productId}",
                'data' => $dateSeries
            ];

            $validationResult = $this->validateTimeSeriesQuality($dateSeries, $frequency);
            $validationResults[$productId] = $validationResult;
            $openaiStructured[$productId]['data_quality'] = $validationResult;
        }

        return [
            'start_date' => $startDate->toDateString(),
            'end_date' => $endDate->toDateString(),
            'frequency' => $frequency,
            'horizon' => $horizon,
            'series' => $series,
            'openai_ready' => $openaiStructured,
            'validation_results' => $validationResults,
        ];
    }

    /**
     * Generate time series data dari request parameters
     */
    public function generateTimeSeriesFromRequest(Request $request)
    {
        $validatedData = $request->validate([
            'products' => 'required|array',
            'products.*.id' => 'required|exists:products,id',
            'frequency' => 'required|in:D,W,M',
            'horizon' => 'required|integer|min:1',
            'startDate' => 'required|date',
            'endDate' => 'required|date|after_or_equal:startDate',
        ]);

        $productIds = collect($validatedData['products'])->pluck('id')->toArray();
        $frequency = $validatedData['frequency'];
        $horizon = $validatedData['horizon'];
        $startDate = Carbon::parse($validatedData['startDate']);
        $endDate = Carbon::parse($validatedData['endDate']);

        // Generate semua titik waktu berdasarkan frekuensi
        $dates = $this->generateDatesFromRange($startDate, $endDate, $frequency);

        // Inisialisasi series
        $series = [];
        foreach ($productIds as $productId) {
            // Panggil method terpisah untuk mendapatkan time series data
            $series[$productId] = $this->getTimeSeriesData(
                $productId,
                $frequency,
                $startDate,
                $endDate,
                $dates
            );
        }

        $productNames = Product::whereIn('id', $productIds)->pluck('name', 'id')->toArray();

        // Struktur data untuk OpenAI dan validasi
        $openaiStructured = [];
        $validationResults = [];

        foreach ($series as $productId => $dateSeries) {
            $openaiStructured[$productId] = [
                'name' => $productNames[$productId] ?? "Produk ID {$productId}",
                'data' => $dateSeries
            ];

            $validationResult = $this->validateTimeSeriesQuality($dateSeries, $frequency);
            $validationResults[$productId] = $validationResult;
            $openaiStructured[$productId]['data_quality'] = $validationResult;
        }

        return [
            'start_date' => $startDate->toDateString(),
            'end_date' => $endDate->toDateString(),
            'frequency' => $frequency,
            'horizon' => $horizon,
            'series' => $series,
            'openai_ready' => $openaiStructured,
            'validation_results' => $validationResults,
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $perPage = is_numeric($request->per_page) ? $request->per_page :  10;

        $forecasts = Forecast::filter()
            ->sorting()
            ->with(['results.product', 'analyses.product'])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);

        $forecasts->getCollection()->transform(function ($forecasts) {
            return [
                'id' => $forecasts->id,
                'name' => $forecasts->name,
                'forecasted_at' => $forecasts->forecasted_at,
                'frequency' => $forecasts->frequency,
                'horizon' => $forecasts->horizon,
                'model' => $forecasts->model,
                'input_start_date' => $forecasts->input_start_date,
                'input_end_date' => $forecasts->input_end_date,
                'total_products' => $forecasts->analyses->count(),
                'status' => $forecasts->status,
                'created_by' => $forecasts->createdBy ? [
                    'id' => $forecasts->createdBy->id,
                    'name' => $forecasts->createdBy->name,
                ] : null,
            ];
        });

        $forecasts->appends(['search' => $request->search]);
        $forecasts->appends(['per_page' => $perPage]);

        if (!empty($request->search) && $request->search != '') {
            $forecasts->appends(['search' => $request->search]);
        }

        return inertia('forecasting/forecast-list', [
            'forecasts' => $forecasts,
            'filters' => $request->only('search', 'page', 'per_page'),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // ambil tanggal produk order item paling awal dan akhir
        $dateRange = OrderItem::join('orders', 'orders.id', '=', 'order_items.order_id')
            ->selectRaw('MIN(DATE(orders.created_at)) as start_date, MAX(DATE(orders.created_at)) as end_date')
            ->first();

        return inertia(
            'forecasting/add-forecast',
            [
                'initialData' => [
                    'start_date' => $dateRange->start_date ?? Carbon::now()->subYear()->toDateString(),
                    'end_date' => $dateRange->end_date ?? Carbon::now()->toDateString(),
                ]
            ]
        );
    }

    public function requestForecast(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'products' => 'required|array',
            'products.*.id' => 'required|exists:products,id',
            'frequency' => 'required|in:D,W,M',
            'horizon' => 'required|integer|min:1',
            'startDate' => 'required|date',
            'endDate' => 'required|date|after_or_equal:startDate',
            'enforceDataQuality' => 'boolean',
            'includeConfidenceIntervals' => 'boolean',
        ]);

        // dd($validatedData);

        $forecast  = Forecast::create([
            'name' =>  $validatedData['name'],
            'forecasted_at' => now(),
            'frequency' => $validatedData['frequency'],
            'horizon' => $validatedData['horizon'],
            'model' => 'timegpt-1',
            'input_start_date' => Carbon::parse($validatedData['startDate'])->format('Y-m-d'),
            'input_end_date' => Carbon::parse($validatedData['endDate'])->format('Y-m-d'),
            'created_by' => auth()->user() ? auth()->user()->id : null,
        ]);

        // $result = $this->generateTimeSeriesFromRequest($request);

        // dd($result);

        $productIds = collect($validatedData['products'])->pluck('id')->toArray();
        $chunks = array_chunk($productIds, 5);

        $generateJobs = [];

        foreach ($chunks as $chunk) {
            $generateJobs[] = new \App\Jobs\GenerateTimeSeriesJob($forecast->id, $chunk);
        }

        // Batch GenerateTimeSeriesJob
        Bus::batch($generateJobs)
            ->then(function () use ($forecast, $validatedData, $productIds) {

                $forecast->update(['status' => 'processing']);

                // Setelah semua GenerateTimeSeriesJob selesai, jalankan ForecastProductJob
                $options = [
                    'h' => (int)$validatedData['horizon'],
                    'freq' => $validatedData['frequency'],
                    'model' => 'timegpt-1',
                    'level' => request()->input('includeConfidenceIntervals', false) ? [80, 95] : null,
                ];

                // Ambil series dari cache/database sesuai implementasi kamu
                $series = [];
                foreach ($productIds as $productId) {
                    $getCacheSeries = cache()->get("series_{$forecast->id}_{$productId}");

                    if ($getCacheSeries['data_quality']['valid']) {
                        $series[$productId] = $getCacheSeries ?? [];
                    } else {
                        // batalkan ke proses selanjutnya jika enforceDataQuality = true
                        if ($validatedData['enforceDataQuality']) {
                            $forecast->update([
                                'status' => 'failed',
                                'note' => $getCacheSeries['product_name'] . ' (' . ($getCacheSeries['product_sku'] ?? '-') . ') tidak valid untuk forecast karena ' . implode(', ', $getCacheSeries['data_quality']['issues'])
                            ]);
                            foreach ($productIds as $pid) {
                                Cache::forget("series_{$forecast->id}_{$pid}");
                            }
                            return;
                        }
                        continue;
                    }
                }

                // Log::info($series);

                // Jika enforceDataQuality == false, SKIP forecast, langsung ke analisis
                if (isset($validatedData['enforceDataQuality']) && !$validatedData['enforceDataQuality']) {
                    // Langsung ke AnalyzeForecastJob
                    $analyzeJobs = [];
                    $analyzeChunks = array_chunk($productIds, 2);

                    foreach ($analyzeChunks as $chunk) {
                        $analyzeStructure = [];
                        foreach ($chunk as $productId) {
                            $getCacheSeries = cache()->get("series_{$forecast->id}_{$productId}");
                            if (!$getCacheSeries) continue;
                            $analyzeStructure[$productId] = $getCacheSeries ?? [];
                        }

                        $analyzeJobs[] = new AnalyzeForecastJob(
                            (int) $forecast->id,
                            $analyzeStructure,
                            $validatedData['frequency'],
                            (int) auth()->id()
                        );
                    }

                    Log::info("Total AnalyzeForecastJob (tanpa forecast): " . count($analyzeJobs));

                    Bus::batch($analyzeJobs)
                        ->then(function (Batch $batch) use ($forecast) {
                            $forecast->update(['status' => 'done']);
                        })
                        ->catch(function (Batch $batch, Throwable $e) use ($forecast) {
                            Log::error("Batch AnalyzeForecastJob gagal untuk forecast ID {$forecast->id}: " . $e->getMessage());
                        })
                        ->dispatch();

                    return;
                }

                // Chunk untuk ForecastProductJob 3 produk per job
                $forecastChunks = array_chunk($series, 3, true);
                $forecastJobs = [];
                foreach ($forecastChunks as $chunk) {
                    $forecastJobs[] = new ForecastProductJob($forecast->id, $chunk, $options);
                }

                Bus::batch($forecastJobs)
                    ->then(function () use ($forecast, $validatedData, $productIds) {
                        // Setelah semua ForecastProductJob selesai, lakukan analisis

                        $analyzeJobs = [];

                        // Chunk untuk AnalyzeForecastJob 2 produk per job
                        $analyzeChunks = array_chunk($productIds, 2);

                        foreach ($analyzeChunks as $chunk) {
                            $analyzeStructure = [];
                            foreach ($chunk as $productId) {
                                $getCacheSeries = cache()->get("series_{$forecast->id}_{$productId}");

                                if (!$getCacheSeries) continue;

                                $analyzeStructure[$productId] = $getCacheSeries ?? [];
                            }

                            $analyzeJobs[] = new AnalyzeForecastJob(
                                (int) $forecast->id,
                                $analyzeStructure,
                                $validatedData['frequency'],
                                (int) auth()->id()
                            );
                        }

                        Log::info("Total AnalyzeForecastJob: " . count($analyzeJobs));

                        Bus::batch($analyzeJobs)
                            ->then(function (Batch $batch) use ($forecast) {
                                // Log::info("Batch AnalyzeForecastJob sukses untuk forecast ID {$forecast->id}");
                                $forecast->update(['status' => 'done']);
                            })
                            ->catch(function (Batch $batch, Throwable $e) use ($forecast) {
                                Log::error("Batch AnalyzeForecastJob gagal untuk forecast ID {$forecast->id}: " . $e->getMessage());
                            })
                            ->dispatch();
                    })
                    ->dispatch();
            })
            ->dispatch();

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Proses generate time series dan forecast berjalan di latar belakang.',
                'forecast_id' => $forecast->id,
            ]);
        } else {
            return redirect()->route('forecasting.show', ['forecasting' => $forecast->id]);
        }
    }

    /**
     * Store forecast results in database
     *
     * @param Request $request Original request data
     * @param array $result Time series data
     * @param array $forecast Forecast results from Nixtla
     * @param array $analysis Analysis results from OpenAI
     * @return int ID of created forecast
     */
    public function storeForecastResults(Request $request, array $result, array $forecast, array $analysis, $createdBy = null): int
    {
        // 1. Insert ke tabel forecasts (data utama)
        $forecastRecord = \App\Models\Forecast::create([
            'forecasted_at' => now(),
            'frequency' => $result['frequency'],
            'horizon' => $result['horizon'],
            'model' => 'timegpt-1', // Bisa diambil dari config atau parameter
            'input_start_date' => $result['start_date'],
            'input_end_date' => $result['end_date'],
            'created_by' => $createdBy,
        ]);

        // 2. Insert forecast results untuk setiap produk
        foreach ($forecast as $productId => $forecastData) {
            // Pastikan productId valid
            if (!is_numeric($productId)) continue;

            \App\Models\ForecastResult::create([
                'forecast_id' => $forecastRecord->id,
                'product_id' => $productId,
                'predictions' => json_encode($forecastData),
                //'actuals' => json_encode($result['series'][$productId] ?? []),
            ]);
        }

        // 3. Insert forecast analyses dari OpenAI
        if (is_array($analysis)) {
            foreach ($analysis as $productId => $productAnalysis) {
                // Skip jika productId bukan numeric
                if (!is_numeric($productId)) continue;

                \App\Models\ForecastAnalysis::create([
                    'forecast_id' => $forecastRecord->id,
                    'product_id' => $productId,
                    'analysis' => json_encode($productAnalysis),
                ]);
            }
        }

        return $forecastRecord->id;
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
    public function show(Request $request, string $id)
    {
        // Load forecast dan relasi
        $forecast = \App\Models\Forecast::with(['results.product', 'analyses.product'])
            ->findOrFail($id);

        // Ambil product_id dari request, atau gunakan produk pertama jika tidak ada
        $product_id = $request->input('product_id', null);

        // Jika tidak ada product_id yang dipilih dan ada hasil forecast
        if (!$product_id && $forecast->analyses->count() > 0) {
            $product_id = $forecast->analyses->first()->product_id;
        }

        // Data time series (null jika tidak ada product_id)
        $timeSeriesData = null;

        // Jika ada produk yang dipilih, ambil data time seriesnya
        if ($product_id) {
            $timeSeriesData = $this->getTimeSeriesData(
                $product_id,
                $forecast->frequency,
                Carbon::parse($forecast->input_start_date),
                Carbon::parse($forecast->input_end_date)
            );
        }

        return inertia('forecasting/forecast-detail', [
            'forecast' => $forecast,
            'selectedProductId' => $product_id,
            'timeSeriesData' => $timeSeriesData
        ]);
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

    public function validateTimeSeriesQuality(array $timeSeries, string $frequency)
    {
        // Menyaring nilai-nilai pada array $timeSeries dan hanya mengambil yang lebih besar dari 0
        $nonZeroValues = array_filter($timeSeries, function ($value) {
            return $value > 0;
        });

        // Menghitung total jumlah elemen dalam array $timeSeries (termasuk nol)
        $totalPoints = count($timeSeries);

        // Menghitung jumlah elemen yang bernilai lebih dari 0 (non-zero values)
        $nonZeroPoints = count($nonZeroValues);


        // Initialize result array first
        $result = [
            'valid' => true,
            'issues' => [],
            'total_points' => $totalPoints,
            'non_zero_points' => $nonZeroPoints,
            'non_zero_ratio' => $totalPoints > 0 ? $nonZeroPoints / $totalPoints : 0,
        ];

        // Kriteria validasi
        $minDataPoints = match ($frequency) {
            'D' => 30,    // Minimum 30 hari data
            'W' => 13,    // Minimum 13 minggu data (3 bulan)
            'M' => 6,     // Minimum 6 bulan data
        };

        // Kriteria validasi berdasarkan frekuensi
        $minNonZeroRatio = match ($frequency) {
            'D' => 0.3,    // 30% untuk data harian
            'W' => 0.5,    // 50% untuk data mingguan
            'M' => 0.7,    // 70% untuk data bulanan
        };

        // Cek jumlah data points
        if ($totalPoints < $minDataPoints) {
            $result['valid'] = false;
            $result['issues'][] = "Jumlah titik data tidak mencukupi (minimum: {$minDataPoints})";
        }

        // Cek rasio data non-zero
        if ($result['non_zero_ratio'] < $minNonZeroRatio) {
            $result['valid'] = false;
            $result['issues'][] = "Terlalu banyak nilai kosong (0)";
        }

        // // Memeriksa periode kosong berurutan
        // $consecutiveZeros = $this->findLongestConsecutiveZeros($timeSeries);
        // $maxConsecutiveZerosAllowed = match ($frequency) {
        //     'D' => 14,   // 2 minggu kosong berturut-turut
        //     'W' => 4,    // 1 bulan kosong berturut-turut
        //     'M' => 2,    // 2 bulan kosong berturut-turut
        // };

        // if ($consecutiveZeros > $maxConsecutiveZerosAllowed) {
        //     $result['valid'] = false;
        //     $result['issues'][] = "Terdapat {$consecutiveZeros} periode berturut-turut tanpa data";
        // }

        return $result;
    }


    // Fungsi helper untuk periode kosong berurutan
    private function findLongestConsecutiveZeros(array $series): int
    {
        $longest = 0;
        $current = 0;

        foreach ($series as $value) {
            if ($value == 0) {
                $current++;
                $longest = max($longest, $current);
            } else {
                $current = 0;
            }
        }

        return $longest;
    }

    /**
     * Mendapatkan data time series untuk satu produk
     *
     * @param int $productId ID produk
     * @param string $frequency Frekuensi data (D,W,M)
     * @param Carbon $startDate Tanggal mulai
     * @param Carbon $endDate Tanggal akhir
     * @param array $dates Array tanggal yang sudah digenerate
     * @return array Time series data dengan format [date => value]
     */
    public function getTimeSeriesData($productId, $frequency, $startDate, $endDate, $dates = null)
    {
        // Generate dates jika tidak disediakan
        if ($dates === null) {
            $dates = $this->generateDatesFromRange($startDate, $endDate, $frequency);
        }

        // Inisialisasi series kosong
        $series = array_fill_keys($dates, 0);

        // Ambil data dari database berdasarkan frekuensi
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
            ->where('order_items.product_id', $productId)
            ->whereBetween('orders.created_at', [$startDate, $endDate]) // Filter by date range
            ->groupBy('product_id', match ($frequency) {
                'W' => DB::raw('YEAR(orders.created_at), WEEK(orders.created_at, 1)'),
                'M' => DB::raw("DATE_FORMAT(orders.created_at, '%Y-%m-01')"),
                default => DB::raw("DATE(orders.created_at)"),
            })
            ->get();

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

            if (isset($series[$date])) {
                $series[$date] = $item->total_qty;
            }
        }

        return $series;
    }

    /**
     * Generate array tanggal berdasarkan range dan frekuensi
     *
     * @param Carbon $startDate Tanggal mulai
     * @param Carbon $endDate Tanggal akhir
     * @param string $frequency Frekuensi (D,W,M)
     * @return array Array tanggal dalam format Y-m-d
     */
    public function generateDatesFromRange($startDate, $endDate, $frequency)
    {
        $dates = [];

        if ($frequency === 'W') {
            for ($date = $startDate->copy()->startOfWeek(); $date->lte($endDate); $date->addWeek()) {
                $dates[] = $date->format('Y-m-d');
            }
        } elseif ($frequency === 'M') {
            for ($date = $startDate->copy()->startOfMonth(); $date->lte($endDate); $date->addMonth()) {
                $dates[] = $date->format('Y-m-d');
            }
        } else { // Daily default
            for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
                $dates[] = $date->format('Y-m-d');
            }
        }

        return $dates;
    }
}
