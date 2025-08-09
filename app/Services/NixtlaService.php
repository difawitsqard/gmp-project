<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class NixtlaService
{
  protected string $apiKey;
  protected string $endpoint;

  // Default values for the forecast
  protected const DEFAULT_MODEL = 'timegpt-1';
  protected const DEFAULT_FREQ = 'D';
  protected const DEFAULT_H = 3;

  public function __construct()
  {
    $this->apiKey = config('nixtla.api_key');
    $this->endpoint = 'https://api.nixtla.io/v2/forecast';
  }

  /**
   * Membuat payload permintaan untuk API peramalan berdasarkan data deret waktu yang dikelompokkan.
   *
   * @param array $seriesGrouped Array asosiatif UID ke array pasangan timestamp => nilai.
   * @param int $h Horizon peramalan (jumlah periode yang diprediksi).
   * @param string $freq Frekuensi data deret waktu.
   * @param string $model Model peramalan yang digunakan.
   * @return array Berisi:
   *   - array $payload: Payload permintaan untuk API.
   *   - array $uids: Daftar UID sesuai input.
   *   - array $lastTimestamps: Timestamp terakhir untuk setiap UID dalam bentuk instance Carbon.
   */
  public function forecast(array $seriesGrouped, array $options = []): array
  {
    $this->validateInput($seriesGrouped, $options);

    $model = $options['model'] ?? self::DEFAULT_MODEL;
    $freq = $options['freq'] ?? self::DEFAULT_FREQ;
    $h = $options['h'] ?? self::DEFAULT_H;

    // === Step 1: Build Request Payload ===
    [$payload, $uids, $lastTimestamps] = $this->buildRequest($seriesGrouped, $h, $freq, $model);

    // === Step 2: Call API ===
    $response = $this->sendRequest($payload);

    // === Step 3: Reconstruct Forecast Result ===
    return $this->buildForecast($response, $uids, $lastTimestamps, $h, $freq);
  }

  /**
   * Membangun payload permintaan untuk API Nixtla berdasarkan data deret waktu yang dikelompokkan.
   * @param array $seriesGrouped Array asosiatif yang berisi UID sebagai kunci dan array pasangan timestamp => nilai sebagai nilai.
   * @param int $h Jumlah periode yang akan diprediksi.
   * @param string $freq Frekuensi data deret waktu (misalnya 'D' untuk harian).
   * @param string $model Model peramalan yang digunakan.
   * @return array Berisi payload permintaan, daftar UID, dan timestamp terakhir untuk setiap UID.
   */
  protected function buildRequest(array $seriesGrouped, int $h, string $freq, string $model): array
  {
    $y = [];
    $sizes = [];
    $lastTimestamps = [];

    ksort($seriesGrouped);

    foreach ($seriesGrouped as $uid => $series) {
      ksort($series);
      $sizes[] = count($series);
      $y = array_merge($y, array_values($series));
      $lastTimestamps[$uid] = Carbon::parse(array_key_last($series));
    }

    $payload = [
      'series' => [
        'y' => $y,
        'sizes' => $sizes,
        'X' => null,
        'X_future' => null
      ],
      'model' => $model,
      'h' => $h,
      'freq' => $freq,
      'clean_ex_first' => true,
      'level' => [80, 95],
      'finetune_steps' => 3,
      'finetune_depth' => 5,
      'finetune_loss' => 'default',
      'finetuned_model_id' => null,
    ];

    return [$payload, array_keys($seriesGrouped), $lastTimestamps];
  }

  /**
   * Mengirim permintaan ke API Nixtla untuk mendapatkan hasil peramalan.
   *
   * @param array $payload Payload permintaan yang akan dikirim.
   * @return array Hasil respons dari API Nixtla.
   * @throws \Exception Jika terjadi kesalahan saat mengirim permintaan.
   */
  protected function sendRequest(array $payload): array
  {
    Log::info('Sending request to Nixtla API', ['payload' => $payload]);

    $response = Http::withHeaders([
      'accept' => 'application/json',
      'content-type' => 'application/json',
      'authorization' => "Bearer {$this->apiKey}"
    ])
      ->timeout(60)       // tunggu maksimal 60 detik
      ->retry(3, 5000)     // coba ulang 3x, dengan jeda 5 detik antar percobaan
      ->post($this->endpoint, $payload);

    if (!$response->successful()) {
      Log::error('Nixtla API failed after retries', [
        'status' => $response->status(),
        'body' => $response->body(),
      ]);
      throw new \Exception("Nixtla API error: " . $response->body());
    }

    Log::info('Nixtla API response', ['response' => $response->json()]);
    return $response->json();
  }

  /**
   * Membangun hasil peramalan berdasarkan respons dari API Nixtla.
   *
   * @param array $response Respons dari API Nixtla.
   * @param array $uids Daftar UID yang sesuai dengan input.
   * @param array $lastTimestamps Timestamp terakhir untuk setiap UID.
   * @param int $h Jumlah periode yang diprediksi.
   * @param string $freq Frekuensi data deret waktu.
   * @return array Hasil peramalan yang telah dibangun.
   */
  protected function buildForecast(array $response, array $uids, array $lastTimestamps, int $h, string $freq): array
  {
    $forecast = [];
    $mean = $response['mean'] ?? [];
    $index = 0;

    foreach ($uids as $uid) {
      $last = $lastTimestamps[$uid];
      $forecast[$uid] = [];

      for ($i = 0; $i < $h; $i++) {
        if (!isset($mean[$index])) break;

        // Hitung tanggal berdasarkan frekuensi
        $ds = match (strtoupper($freq)) {
          'H' => $last->copy()->addHours($i + 1)->format('Y-m-d H:00:00'),
          'D' => $last->copy()->addDays($i + 1)->format('Y-m-d'),
          'W' => $last->copy()->addWeeks($i + 1)->startOfWeek()->format('Y-m-d'),
          'M' => $last->copy()->addMonths($i + 1)->startOfMonth()->format('Y-m-d'),
          default => $last->copy()->addDays($i + 1)->format('Y-m-d'),
        };

        $forecast[$uid][$ds] = (string) $mean[$index];

        $index++;
      }
    }

    return $forecast;
  }

  /**
   * Validasi input untuk memastikan bahwa data deret waktu yang diberikan sesuai dengan format yang diharapkan.
   *
   * @param array $seriesGrouped Array asosiatif yang berisi UID sebagai kunci dan array pasangan timestamp => nilai sebagai nilai.
   * @param array $options Opsi tambahan seperti 'h', 'freq', dan 'model'.
   * @throws \InvalidArgumentException Jika input tidak valid.
   */
  protected function validateInput(array $seriesGrouped, array $options): void
  {
    if (empty($seriesGrouped)) {
      throw new \InvalidArgumentException("Array seriesGrouped tidak boleh kosong.");
    }

    foreach ($seriesGrouped as $uid => $series) {
      if (!is_array($series)) {
        throw new \InvalidArgumentException("Setiap series dalam seriesGrouped harus berupa array.");
      }
    }

    if (!isset($options['h']) || !is_int($options['h']) || $options['h'] <= 0) {
      throw new \InvalidArgumentException("Opsi 'h' harus berupa integer positif.");
    }
  }
}
