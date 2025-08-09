<?php

namespace App\Services;

use Carbon\Carbon;

/**
 * Service untuk validasi kualitas data time series
 * Memastikan data penjualan layak untuk dilakukan forecasting
 */
class TimeSeriesValidationService
{

  private const RULES = [
    'too_few_data' => ['message' => 'Data terlalu sedikit untuk forecasting (minimum 13 periode)'],
    'low_activity' => ['message' => 'Tidak ada cukup aktivitas penjualan (minimum 5 periode)'],
    'long_gap' => ['message' => 'Ada periode kosong terlalu panjang'],
    'flat_data' => ['message' => 'Data terlalu monoton untuk diprediksi'],
  ];

  /**
   * Validasi dengan 2 aturan sederhana saja
   */
  public function validateTimeSeriesQuality(array $timeSeries, string $frequency): array
  {
    $totalPoints = count($timeSeries); // total jumlah data time series
    $nonZeroPoints = count(array_filter($timeSeries, fn($v) => $v > 0)); // jumlah data bukan nol

    $result = [
      'valid' => true,
      'issues' => [],
      'total_points' => $totalPoints,
      'non_zero_points' => $nonZeroPoints,
      'non_zero_ratio' => $totalPoints > 0 ? $nonZeroPoints / $totalPoints : 0,
    ];

    // RULE 1: Minimal data untuk API requirement
    if ($totalPoints < 13) {
      $result['valid'] = false;
      $result['issues'][] = self::RULES['too_few_data']['message'];
    }

    // RULE 2: Harus ada minimal aktivitas penjualan
    if ($nonZeroPoints < 5) {
      $result['valid'] = false;
      $result['issues'][] = self::RULES['low_activity']['message'];
    }

    // RULE 3: Tidak boleh ada gap kosong terlalu panjang
    if ($this->hasLongConsecutiveZeros($timeSeries, $frequency)) {
      $result['valid'] = false;
      $result['issues'][] = self::RULES['long_gap']['message'];
    }

    // RULE 5: Data tidak boleh flat (semua nilai sama)
    if ($this->isDataTooFlat($timeSeries)) {
      $result['valid'] = false;
      $result['issues'][] = self::RULES['flat_data']['message'];
    }

    return $result;
  }

  public function checkForecastEligibility(array $timeSeries, string $frequency, Carbon $endDate): array
  {
    $qualityCheck = $this->validateTimeSeriesQuality($timeSeries, $frequency);

    return [
      'is_eligible' => $qualityCheck['valid'],
      'reason' => implode(', ', $qualityCheck['issues']),
      'data_quality_score' => $this->calculateSimpleScore($qualityCheck),
      'data_points' => $qualityCheck['total_points'],
      'non_zero_ratio' => round($qualityCheck['non_zero_ratio'] * 100, 1),
      'quality_details' => $qualityCheck,
      'time_series' => $timeSeries,
    ];
  }

  private function calculateSimpleScore(array $qualityCheck): int
  {
    if (!$qualityCheck['valid']) {
      return 30; // Fixed low score untuk yang gagal validasi
    }

    // Linear scaling berdasarkan activity: 50-100 range
    $activityScore = 50 + ($qualityCheck['non_zero_ratio'] * 50);

    return round($activityScore);
  }

  private function hasLongConsecutiveZeros(array $timeSeries, string $frequency): bool
  {
    $consecutiveZeros = 0;
    $maxAllowed = match ($frequency) {
      'D' => 30,
      'W' => 8,
      'M' => 3,
      default => min(30, (int)(count($timeSeries) * 0.5)),
    };

    foreach ($timeSeries as $value) {
      if ($value == 0) {
        $consecutiveZeros++;
        if ($consecutiveZeros > $maxAllowed) return true;
      } else {
        $consecutiveZeros = 0;
      }
    }
    return false;
  }

  private function isDataTooFlat(array $timeSeries): bool
  {
    $nonZeroValues = array_filter($timeSeries, fn($v) => $v > 0);

    if (count($nonZeroValues) < 2) return true;

    $uniqueValues = array_unique($nonZeroValues);

    // Jika 90% data punya nilai yang sama = too flat
    return count($uniqueValues) <= 2 && count($nonZeroValues) > 10;
  }
}
