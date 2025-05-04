<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class NixtlaService
{
  protected string $apiKey;
  protected string $endpoint;

  protected const DEFAULT_MODEL = 'timegpt-1';
  protected const DEFAULT_FREQ = 'D';
  protected const DEFAULT_H = 7;

  public function __construct()
  {
    $this->apiKey = config('nixtla.api_key');
    $this->endpoint = 'https://api.nixtla.io/v2/forecast';
  }

  public function forecast(array $rawSeriesWithFeatures, array $options = []): array
  {
    $this->validateInput($rawSeriesWithFeatures, $options);

    $model = $options['model'] ?? self::DEFAULT_MODEL;
    $freq = $options['freq'] ?? self::DEFAULT_FREQ;
    $h = $options['h'] ?? self::DEFAULT_H;

    // Build structured data
    [$y, $X, $sizes, $lastTimestamps] = $this->extractSeriesAndFeatures($rawSeriesWithFeatures);

    // Auto-generate X_future from built-in callbacks if X exists
    $X_future = null;
    if (!empty($X)) {
      $X_future = $this->generateXFutureForGroupedSeries($lastTimestamps, $h, [
        fn($date) => $date->dayOfWeek,
        fn($date) => $date->month,
        fn($date) => (int) $date->isWeekend(),
      ]);
    }

    $featureContributions = $options['feature_contributions'] ?? (!empty($X) && !empty($X_future));

    dd($X_future);

    $payload = [
      'series' => [
        'y' => $y,
        'sizes' => $sizes,
        'X' => !empty($X) ? $X : null,
        'X_future' => !empty($X_future) ? $X_future : null
      ],
      'model' => $model,
      'h' => $h,
      'freq' => $freq,
      'clean_ex_first' => true,
      'level' => [80, 95],
      'finetune_steps' => 0,
      'finetune_depth' => 1,
      'finetune_loss' => 'default',
      'finetuned_model_id' => null,
      'feature_contributions' => $featureContributions
    ];

    dd($payload);

    // $response = $this->sendRequest($payload);

    if (!empty($options['raw'])) {
      return $response;
    }

    return $this->buildForecast($response, array_keys($rawSeriesWithFeatures), $lastTimestamps, $h, $freq);
  }

  protected function extractSeriesAndFeatures(array $input): array
  {
    $y = [];
    $X = [];
    $sizes = [];
    $lastTimestamps = [];

    ksort($input);

    foreach ($input as $uid => $series) {
      ksort($series);
      $seriesY = [];
      $seriesX = [];

      foreach ($series as $ds => $data) {
        if (is_array($data)) {
          $seriesY[] = $data['y'];
          $features = array_filter($data, fn($key) => $key !== 'y', ARRAY_FILTER_USE_KEY);
          $seriesX[] = array_values($features);
        } else {
          $seriesY[] = $data;
        }

        $lastTimestamps[$uid] = Carbon::parse($ds);
      }

      $sizes[] = count($seriesY);
      $y = array_merge($y, $seriesY);

      if (!empty($seriesX)) {
        $X = array_merge($X, $seriesX);
      }
    }

    return [$y, $X, $sizes, $lastTimestamps];
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

  protected function buildForecast(array $response, array $uids, array $lastTimestamps, int $h, string $freq): array
  {
    $forecast = [];
    $mean = $response['mean'] ?? [];
    $lo80 = $response['intervals']['lo-80'] ?? [];
    $hi95 = $response['intervals']['hi-95'] ?? [];
    $index = 0;

    foreach ($uids as $uid) {
      $last = $lastTimestamps[$uid];

      for ($i = 0; $i < $h; $i++) {
        if (!isset($mean[$index])) break;

        $ds = match ($freq) {
          'H' => $last->copy()->addHours($i + 1)->format('Y-m-d H:i:s'),
          default => $last->copy()->addDays($i + 1)->format('Y-m-d')
        };

        $forecast[] = [
          'unique_id' => $uid,
          'ds' => $ds,
          'mean' => round($mean[$index], 2),
          'lo_80' => round($lo80[$index] ?? 0, 2),
          'hi_95' => round($hi95[$index] ?? 0, 2)
        ];

        $index++;
      }
    }

    return $forecast;
  }

  protected function validateInput(array $input, array $options): void
  {
    if (empty($input)) {
      throw new \InvalidArgumentException("Input data cannot be empty.");
    }

    foreach ($input as $uid => $series) {
      if (!is_array($series)) {
        throw new \InvalidArgumentException("Each series must be an array.");
      }
    }

    if (!isset($options['h']) || !is_int($options['h']) || $options['h'] <= 0) {
      throw new \InvalidArgumentException("The 'h' option must be a positive integer.");
    }
  }

  public function generateXFutureFromDates(string $lastDate, int $horizon, array $featureCallbacks): array
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

  public function generateXFutureForGroupedSeries(array $lastTimestamps, int $horizon, array $featureCallbacks): array
  {
    $result = [];

    foreach ($lastTimestamps as $uid => $lastDate) {
      $xFuture = $this->generateXFutureFromDates($lastDate->format('Y-m-d'), $horizon, $featureCallbacks);
      $result = array_merge($result, $xFuture);
    }

    return $result;
  }
}
