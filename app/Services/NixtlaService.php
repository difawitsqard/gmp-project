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
  protected const DEFAULT_H = 7;

  public function __construct()
  {
    $this->apiKey = config('nixtla.api_key');
    $this->endpoint = 'https://api.nixtla.io/v2/forecast';
  }

  public function forecast(array $seriesGrouped, array $options = []): array
  {
    $this->validateInput($seriesGrouped, $options);

    $model = $options['model'] ?? self::DEFAULT_MODEL;
    $freq = $options['freq'] ?? self::DEFAULT_FREQ;
    $h = $options['h'] ?? self::DEFAULT_H;
    $X = $options['X'] ?? null;
    $X_future = $options['X_future'] ?? null;

    // === Step 1: Build Request Payload ===
    [$payload, $uids, $lastTimestamps] = $this->buildRequest($seriesGrouped, $h, $freq, $model);

    // === Step 2: Call API ===
    $response = $this->sendRequest($payload);

    // === Step 3: Reconstruct Forecast Result ===
    return $this->buildForecast($response, $uids, $lastTimestamps, $h, $freq);
  }

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
      'finetune_steps' => 0,
      'finetune_depth' => 3,
      'finetune_loss' => 'default',
      'finetuned_model_id' => null,
      'feature_contributions' => false
    ];

    return [$payload, array_keys($seriesGrouped), $lastTimestamps];
  }

  protected function sendRequest(array $payload): array
  {
    Log::info('Sending request to Nixtla API', ['payload' => $payload]);

    $response = Http::withHeaders([
      'accept' => 'application/json',
      'content-type' => 'application/json',
      'authorization' => "Bearer {$this->apiKey}"
    ])->post($this->endpoint, $payload);

    if (!$response->successful()) {
      throw new \Exception("Nixtla API error: " . $response->body());
    }

    Log::info('Nixtla API response', ['response' => $response->json()]);
    return $response->json();
  }

  // protected function buildForecast(array $response, array $uids, array $lastTimestamps, int $h, string $freq): array
  // {
  //   $forecast = [];
  //   $mean = $response['mean'] ?? [];
  //   $index = 0;

  //   foreach ($uids as $uid) {
  //     $last = $lastTimestamps[$uid];

  //     for ($i = 0; $i < $h; $i++) {
  //       if (!isset($mean[$index])) break;

  //       // Hitung tanggal berdasarkan frekuensi
  //       $ds = match (strtoupper($freq)) {
  //         'H' => $last->copy()->addHours($i + 1)->format('Y-m-d H:00:00'),
  //         'D' => $last->copy()->addDays($i + 1)->format('Y-m-d'),
  //         'W' => $last->copy()->addWeeks($i + 1)->startOfWeek()->format('Y-m-d'),
  //         'M' => $last->copy()->addMonths($i + 1)->startOfMonth()->format('Y-m-d'),
  //         default => $last->copy()->addDays($i + 1)->format('Y-m-d'),
  //       };

  //       $forecast[] = [
  //         'unique_id' => $uid,
  //         'ds' => $ds,
  //         'TimeGPT' => $mean[$index],
  //       ];

  //       $index++;
  //     }
  //   }

  //   return $forecast;
  // }

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


  protected function validateInput(array $seriesGrouped, array $options): void
  {
    if (empty($seriesGrouped)) {
      throw new \InvalidArgumentException("The seriesGrouped array cannot be empty.");
    }

    foreach ($seriesGrouped as $uid => $series) {
      if (!is_array($series)) {
        throw new \InvalidArgumentException("Each series in seriesGrouped must be an array.");
      }
    }

    if (!isset($options['h']) || !is_int($options['h']) || $options['h'] <= 0) {
      throw new \InvalidArgumentException("The 'h' option must be a positive integer.");
    }
  }

  /**
   * Validasi apakah produk layak untuk forecast berdasarkan time series-nya.
   *
   * @param array $series ['2024-01-01' => 10, '2024-01-02' => 0, ...]
   * @param float $minRatio Rasio minimum non-zero agar layak forecast
   * @param int $maxGap Jarak maksimum antar titik non-zero
   * @param string $freq 'H' (hour), 'D' (day), 'W' (week), 'M' (month)
   * @return array ['valid' => bool, 'reason' => string|null]
   */
  public function validate(array $series, float $minRatio = 0.2, int $maxGap = 5, string $freq = 'D'): array
  {
    $dates = array_keys($series);
    $total = count($series);

    if ($total < 8) {
      return ['valid' => false, 'reason' => 'Data terlalu sedikit'];
    }

    $nonZeroDates = collect($series)->filter(fn($v) => $v > 0)->keys()->all();
    $nonZeroCount = count($nonZeroDates);
    $ratio = $nonZeroCount / $total;

    if ($ratio < $minRatio) {
      return ['valid' => false, 'reason' => 'Rasio data non-zero terlalu kecil'];
    }

    // Hitung jarak antar titik non-zero berdasarkan freq
    $gapUnits = [];
    for ($i = 1; $i < count($nonZeroDates); $i++) {
      $start = Carbon::parse($nonZeroDates[$i - 1]);
      $end = Carbon::parse($nonZeroDates[$i]);

      $gap = match ($freq) {
        'H' => $start->diffInHours($end),
        'D' => $start->diffInDays($end),
        'W' => $start->diffInWeeks($end),
        'M' => $start->diffInMonths($end),
        default => $start->diffInDays($end),
      };

      $gapUnits[] = $gap;
    }

    $maxDetectedGap = count($gapUnits) ? max($gapUnits) : 0;

    if ($maxDetectedGap > $maxGap) {
      return ['valid' => false, 'reason' => "Jarak antar titik terlalu jauh ($maxDetectedGap $freq)"];
    }

    return ['valid' => true, 'reason' => null];
  }


  function generateXFutureFromDates(string $lastDate, int $horizon, array $featureCallbacks): array
  {
    $result = [];
    $start = Carbon::parse($lastDate);

    for ($i = 1; $i <= $horizon; $i++) {
      $date = $start->copy()->addDays($i);
      $featureRow = [];

      foreach ($featureCallbacks as $callback) {
        $featureRow[] = $callback($date);
      }

      $result[] = $featureRow;
    }

    return $result;
  }
}
