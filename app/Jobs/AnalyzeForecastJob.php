<?php

namespace App\Jobs;

use Illuminate\Http\Request;
use Illuminate\Bus\Batchable;
use App\Services\OpenAIService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Http\Controllers\ForecastController;
use App\Models\Forecast;

class AnalyzeForecastJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, Batchable;

    public function __construct(
        public int $forecastId,
        public array $products,
        public string $frequency,
        public int $createdBy
    ) {}

    public function handle()
    {
        $openai = app(OpenAIService::class);

        // Ambil data dari hasil forecast result database
        foreach ($this->products as $id => $product) {
            $forecastResult = Forecast::where('id', $this->forecastId)
                ->first()?->results()
                ->where('product_id', $id)
                ->first();

            if ($forecastResult) {
                // Update struktur data dengan hasil forecast
                $this->products[$id]['predictions'] = json_decode($forecastResult->predictions, true);
            }
        }

        // Log::info("AnalyzeForecastJob " . json_encode($this->products));

        try {
            $openaiResult = $openai->analyzeTimeSeriesAndForecast(
                $this->products,
                $this->frequency,
            );

            if (is_array($openaiResult['analysis'])) {
                foreach ($openaiResult['analysis'] as $productId => $productAnalysis) {
                    if (!is_numeric($productId)) continue;

                    \App\Models\ForecastAnalysis::create([
                        'forecast_id' => $this->forecastId,
                        'product_id' => $productId,
                        'analysis' => json_encode($productAnalysis),
                    ]);

                    // hapus cache untuk produk ini "series_{$this->forecastId}_{$productId}"
                    Cache::forget("series_{$this->forecastId}_{$productId}");
                }
            }
            Log::info("AnalyzeForecastJob sukses untuk forecast {$this->forecastId}");
        } catch (\Exception $e) {
            //  Log::error("AnalyzeForecastJob gagal untuk forecast {$this->forecastId}: " . $e->getMessage());

            // Update status forecast ke failed dan simpan pesan error
            \App\Models\Forecast::where('id', $this->forecastId)
                ->update([
                    'status' => 'failed',
                    'note' => $e->getMessage()
                ]);

            // Hentikan batch dengan melempar exception
            throw $e;
        }
    }
}
