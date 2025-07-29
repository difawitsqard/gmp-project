<?php

namespace App\Jobs;

use Carbon\Carbon;
use App\Models\Forecast;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Bus\Batchable;
use App\Http\Controllers\ForecastController;
use Illuminate\Support\Facades\Cache;

class GenerateTimeSeriesJob implements ShouldQueue
{
    use Queueable;

    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, Batchable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public int $forecastId,
        public array $productIds,
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $forecast = Forecast::findorFail($this->forecastId);

        try {
            $controller = app(ForecastController::class);

            $frequency = $forecast->frequency;
            $startDate = Carbon::parse($forecast->input_start_date);
            $endDate = Carbon::parse($forecast->input_end_date);

            $dates = $controller->generateDatesFromRange($startDate, $endDate, $frequency);

            foreach ($this->productIds as $productId) {

                // ambil data product berdasarkan ID
                $product = \App\Models\Product::findOrFail($productId);

                $series = $controller->getTimeSeriesData(
                    $productId,
                    $frequency,
                    $startDate,
                    $endDate,
                    $dates
                );

                $validatedSeries = $controller->validateTimeSeriesQuality($series, $frequency);

                $data = [
                    'product_id' => $product->id,
                    'product_sku' => $product->sku,
                    'product_name' => $product->name,
                    'series' => $series,
                    'data_quality' => $validatedSeries
                ];

                Cache::put("series_{$this->forecastId}_{$productId}", $data, now()->addHours(2));

                // Log::info("Generate time series {$product->name}: " . json_encode($data));
            }
        } catch (\Exception $e) {
            Log::error("GenerateTimeSeriesJob gagal untuk forecast ID {$this->forecastId}: " . $e->getMessage());
        }
    }
}
