<?php

namespace App\Jobs;

use Exception;
use Illuminate\Bus\Batchable;
use App\Services\NixtlaService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;

class ForecastProductJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, Batchable;

    public function __construct(
        public int $forecastId,
        public array $seriesChunk, // key: productId, value: series
        public array $options
    ) {}

    public function handle()
    {
        $nixtla = app(NixtlaService::class);
        try {
            $forecast = $nixtla->forecast($this->seriesChunk, $this->options);

            foreach ($forecast as $productId => $forecastData) {
                \App\Models\ForecastResult::create([
                    'forecast_id' => $this->forecastId,
                    'product_id' => $productId,
                    'predictions' => $forecastData ? json_encode($forecastData) : json_encode([]),
                ]);
            }

            Log::info("ForecastProductJob sukses untuk produk: " . implode(',', array_keys($this->seriesChunk)));
        } catch (Exception $e) {
            Log::error("ForecastProductJob gagal untuk produk: " . implode(',', array_keys($this->seriesChunk)) . ': ' . $e->getMessage());
        }
    }
}
