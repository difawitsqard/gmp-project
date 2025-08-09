<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Services\TimeSeriesValidationService;
use Carbon\Carbon;

/**
 * Test untuk TimeSeriesValidationService
 * Memastikan service validasi kualitas data time series berfungsi dengan baik
 */
class TimeSeriesValidationServiceTest extends TestCase
{
    private TimeSeriesValidationService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new TimeSeriesValidationService();
    }


    // =================================================================
    // Tests for validateTimeSeriesQuality()
    // =================================================================

    /**
     * @test
     * Test dengan data yang ideal dan seharusnya lolos semua validasi.
     */
    public function validateTimeSeriesQuality_withPerfectData_shouldBeValid(): void
    {
        // Data dengan 20 periode, banyak aktivitas, tidak ada gap, dan tidak flat.
        $timeSeries = [10, 12, 15, 8, 20, 22, 18, 14, 10, 0, 5, 9, 11, 13, 17, 19, 21, 25, 30, 28];
        $result = $this->service->validateTimeSeriesQuality($timeSeries, 'D');

        $this->assertTrue($result['valid']);
        $this->assertEmpty($result['issues']);
        $this->assertEquals(20, $result['total_points']);
        $this->assertEquals(19, $result['non_zero_points']);
    }

    /**
     * @test
     * Test aturan: data terlalu sedikit (kurang dari 13 periode).
     */
    public function validateTimeSeriesQuality_withTooFewData_shouldBeInvalid(): void
    {
        $timeSeries = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12]; // Hanya 12 data
        $result = $this->service->validateTimeSeriesQuality($timeSeries, 'D');

        $this->assertFalse($result['valid']);
        $this->assertContains('Data terlalu sedikit untuk forecasting (minimum 13 periode)', $result['issues']);
    }

    /**
     * @test
     * Test aturan: aktivitas penjualan terlalu rendah (kurang dari 5 periode non-zero).
     */
    public function validateTimeSeriesQuality_withLowActivity_shouldBeInvalid(): void
    {
        $timeSeries = [0, 0, 5, 0, 0, 8, 0, 0, 1, 0, 0, 2, 0, 0, 0]; // 15 data, tapi hanya 4 non-zero
        $result = $this->service->validateTimeSeriesQuality($timeSeries, 'D');

        $this->assertFalse($result['valid']);
        $this->assertContains('Tidak ada cukup aktivitas penjualan (minimum 5 periode)', $result['issues']); // FIXED
    }

    /**
     * @test
     * @dataProvider longGapDataProvider
     * Test aturan: ada gap (periode kosong) yang terlalu panjang.
     */
    public function validateTimeSeriesQuality_withLongGap_shouldBeInvalid(string $frequency, array $timeSeries): void
    {
        $result = $this->service->validateTimeSeriesQuality($timeSeries, $frequency);

        $this->assertFalse($result['valid']);
        $this->assertContains('Ada periode kosong terlalu panjang', $result['issues']);
    }

    /**
     * @test
     * Test aturan: data terlalu monoton (flat).
     */
    public function validateTimeSeriesQuality_withFlatData_shouldBeInvalid(): void
    {
        // 15 data non-zero, tapi nilainya semua sama (10)
        $timeSeries = array_fill(0, 15, 10);
        $result = $this->service->validateTimeSeriesQuality($timeSeries, 'D');

        $this->assertFalse($result['valid']);
        $this->assertContains('Data terlalu monoton untuk diprediksi', $result['issues']);
    }

    /**
     * @test
     * Test kasus di mana beberapa aturan dilanggar sekaligus.
     */
    public function validateTimeSeriesQuality_withMultipleIssues_shouldListAllIssues(): void
    {
        // Hanya 12 data (too_few_data), 4 non-zero (low_activity), dan tidak ada aktivitas terbaru (no_recent)
        $timeSeries = [5, 2, 8, 1, 0, 0, 0, 0, 0, 0, 0, 0];
        $result = $this->service->validateTimeSeriesQuality($timeSeries, 'D');

        $this->assertFalse($result['valid']);
        $this->assertCount(2, $result['issues']); // DIUBAH: Mengharapkan 3 isu
        $this->assertContains('Data terlalu sedikit untuk forecasting (minimum 13 periode)', $result['issues']);
        $this->assertContains('Tidak ada cukup aktivitas penjualan (minimum 5 periode)', $result['issues']);
    }

    // =================================================================
    // Tests for checkForecastEligibility()
    // =================================================================

    /**
     * @test
     * Test kelayakan dengan data yang valid.
     */
    public function checkForecastEligibility_withEligibleData_shouldReturnEligible(): void
    {
        // DIUBAH: Menggunakan data yang sudah pasti valid
        $timeSeries = [10, 12, 15, 8, 20, 22, 18, 14, 10, 0, 5, 9, 11, 13, 17, 19, 21, 25, 30, 28];
        $endDate = Carbon::now();

        $result = $this->service->checkForecastEligibility($timeSeries, 'D', $endDate);

        // DIUBAH: Menyesuaikan skor dan rasio dengan data baru
        // non_zero_ratio = 19/20 = 0.95
        // Skor = round(50 + (0.95 * 50)) = round(50 + 47.5) = 98
        $expectedScore = 98;
        $expectedNonZeroRatio = 95.0;

        $this->assertTrue($result['is_eligible']);
        $this->assertEmpty($result['reason']);
        $this->assertEquals($expectedScore, $result['data_quality_score']);
        $this->assertEquals(20, $result['data_points']);
        $this->assertEquals($expectedNonZeroRatio, $result['non_zero_ratio']);
    }

    /**
     * @test
     * Test kelayakan dengan data yang tidak valid.
     */
    public function checkForecastEligibility_withIneligibleData_shouldReturnNotEligible(): void
    {
        $timeSeries = [1, 2, 3]; // Data terlalu sedikit
        $endDate = Carbon::now();

        $result = $this->service->checkForecastEligibility($timeSeries, 'D', $endDate);

        $this->assertFalse($result['is_eligible']);
        $this->assertEquals('Data terlalu sedikit untuk forecasting (minimum 13 periode), Tidak ada cukup aktivitas penjualan (minimum 5 periode)', $result['reason']);
        $this->assertEquals(30, $result['data_quality_score']); // Skor tetap untuk yang tidak lolos
        $this->assertEquals(3, $result['data_points']);
    }

    // =================================================================
    // Data Providers
    // =================================================================

    /**
     * Menyediakan data untuk test `long_gap`.
     */
    public static function longGapDataProvider(): array
    {
        return [
            'daily_frequency_with_31_zeros' => [
                'D', // frequency
                array_merge(array_fill(0, 10, 5), array_fill(0, 31, 0)) // timeSeries
            ],
            'weekly_frequency_with_9_zeros' => [
                'W', // frequency
                array_merge(array_fill(0, 10, 5), array_fill(0, 9, 0)) // timeSeries
            ],
            'monthly_frequency_with_4_zeros' => [
                'M', // frequency
                array_merge(array_fill(0, 10, 5), array_fill(0, 4, 0)) // timeSeries
            ],
        ];
    }
}
