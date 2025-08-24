<?php

namespace App\Jobs;

use Exception;
use Illuminate\Bus\Batchable;
use App\Services\NixtlaService;
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

        // Memperbarui struktur data seriesChunk
        // Pastikan setiap entry memiliki 'series' sebagai key
        foreach ($this->seriesChunk as $productId => $data) {
            if (isset($data['series']) && is_array($data['series'])) {
                $this->seriesChunk[$productId] = $data['series'];
            }
        }

        // Log::info("ForecastProductJob  " . json_encode($this->seriesChunk));

        try {
            $forecast = $nixtla->forecast($this->seriesChunk, $this->options);

            foreach ($forecast as $productId => $forecastData) {
                // $forecastData format: ['2025-01-06' => '-81.72002', ...]
                // Jadikan angka, clamp <1 => 0, dan (opsional) bulatkan.
                $filtered = array_map(function ($v) {
                    $x = is_numeric($v) ? (float)$v : 0.0;

                    // Handle NaN/Inf jika ada
                    if (!is_finite($x)) {
                        $x = 0.0;
                    }

                    // Kriteria kamu: di bawah 1 ATAU minus => 0
                    $x = ($x < 1) ? 0.0 : $x;

                    // Jika butuh integer unit, aktifkan pembulatan:
                    // $x = (float) round($x);   // atau (int) round($x);

                    return $x; // biarkan numeric agar JSON menyimpan angka, bukan string
                }, $forecastData ?? []);

                \App\Models\ForecastResult::create([
                    'forecast_id' => $this->forecastId,
                    'product_id' => $productId,
                    'predictions' => json_encode($filtered, JSON_UNESCAPED_UNICODE),
                ]);
            }

            Log::info("ForecastProductJob sukses untuk produk: " . implode(',', array_keys($this->seriesChunk)));
        } catch (Exception $e) {
            Log::error("ForecastProductJob gagal untuk produk: " . implode(',', array_keys($this->seriesChunk)) . ': ' . $e->getMessage());
            // Update status forecast ke failed dan simpan pesan error
            \App\Models\Forecast::where('id', $this->forecastId)
                ->update([
                    'status' => 'failed',
                    'note' => $e->getMessage()
                ]);

            throw $e;
        }
    }
}
