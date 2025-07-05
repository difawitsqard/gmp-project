<?php

namespace App\Services;

use OpenAI\Laravel\Facades\OpenAI;

class OpenAIService
{
  /**
   * Menganalisis data time series dan hasil forecast
   *
   * @param array $timeSeriesData Data time series dan forecast
   * @param array $validProducts List product IDs valid
   * @param array $invalidProducts List produk invalid dengan detail masalah
   * @param string $frequency Frekuensi data (D,W,M)
   * @return array Hasil analisis dari OpenAI
   */
  public function analyzeTimeSeriesAndForecast(array $timeSeriesData, array $validProducts, array $invalidProducts, string $frequency)
  {
    // Bangun prompt untuk OpenAI
    $prompt = $this->buildPrompt($timeSeriesData['openai_ready'], $frequency);

    // Kirim ke OpenAI
    $response = OpenAI::chat()->create([
      'model' => 'gpt-4o',
      'messages' => [
        ['role' => 'system', 'content' => $this->getSystemPrompt()],
        ['role' => 'user', 'content' => $prompt],
      ],
      'temperature' => 0.3,
    ]);

    // Extract hasil analisis
    $content = $response->choices[0]->message->content;

    // Parse JSON dari respons
    preg_match('/{.*}/s', $content, $matches);
    $analysisJson = $matches[0] ?? null;
    $analysis = $analysisJson ? json_decode($analysisJson, true) : [];

    return [
      'raw_response' => $content,
      'analysis' => $analysis,
    ];
  }

  /**
   * Membangun prompt untuk OpenAI berdasarkan data
   */
  private function buildPrompt(array $openaiReady, string $frequency)
  {
    $freqName = [
      'D' => 'harian',
      'W' => 'mingguan',
      'M' => 'bulanan',
    ][$frequency] ?? 'harian';

    $prompt = "# Analisis Data Penjualan dan Forecast\n\n";
    $prompt .= "Berikut adalah data penjualan {$freqName} dari beberapa produk beserta hasil forecast (jika tersedia).\n\n";

    foreach ($openaiReady as $productId => $info) {
      $prompt .= "## Produk ID: {$productId}\n";
      $prompt .= "Nama: {$info['name']}\n\n";

      $prompt .= "### Data Penjualan {$freqName}:\n";
      $prompt .= "```json\n" . json_encode($info['data'], JSON_PRETTY_PRINT) . "\n```\n\n";

      if (isset($info['is_valid']) && !$info['is_valid']) {
        $prompt .= "### Masalah Data:\n";
        $prompt .= "Data untuk produk ini tidak memenuhi kriteria kualitas untuk forecast.\n";
        $prompt .= "Masalah: " . implode(", ", $info['issues']) . "\n\n";
      } elseif (isset($info['forecast'])) {
        $prompt .= "### Hasil Forecast:\n";
        $prompt .= "```json\n" . json_encode($info['forecast'], JSON_PRETTY_PRINT) . "\n```\n\n";
      }
    }

    $prompt .= "# Tugas Anda\n\n";
    $prompt .= "Analisis setiap produk dan berikan insight yang komprehensif berdasarkan data historis dan forecast (jika tersedia).\n\n";

    $prompt .= "Untuk produk dengan data valid dan forecast:\n";
    $prompt .= "1. Analisis pola penjualan historis (tren, seasonality, outlier)\n";
    $prompt .= "2. Analisis hasil forecast (peningkatan/penurunan yang diperkirakan)\n";
    $prompt .= "3. Berikan rekomendasi untuk manajemen inventory dan strategi penjualan\n\n";

    $prompt .= "Untuk produk dengan data tidak valid (tanpa forecast):\n";
    $prompt .= "1. Analisis pola penjualan historis untuk mendapatkan insight\n";
    $prompt .= "2. Identifikasi kemungkinan penyebab kualitas data rendah\n";
    $prompt .= "3. Berikan saran untuk perbaikan pengumpulan data dan pola penjualan\n\n";

    $prompt .= "Berikan hasil analisis dalam format JSON berikut:\n\n";
    $prompt .= "```json\n";
    $prompt .= "{\n";
    $prompt .= "  \"[product_id]\": {\n";
    $prompt .= "    \"name\": \"nama produk\",\n";
    $prompt .= "    \"has_forecast\": true/false,\n";
    $prompt .= "    \"historical_analysis\": \"analisis pola historis\",\n";
    $prompt .= "    \"forecast_analysis\": \"analisis hasil forecast (jika ada)\",\n";
    $prompt .= "    \"recommendations\": \"rekomendasi bisnis\",\n";
    $prompt .= "    \"data_quality\": \"komentar tentang kualitas data\"\n";
    $prompt .= "  }\n";
    $prompt .= "}\n```\n";

    return $prompt;
  }

  /**
   * Mendapatkan system prompt untuk OpenAI
   */
  private function getSystemPrompt()
  {
    return "Anda adalah analis bisnis dan data yang ahli dalam menganalisis data time series dan forecast.
        
    Anda memiliki pemahaman mendalam tentang:
    1. Analisis pola data penjualan dan time series
    2. Interpretasi hasil forecast
    3. Manajemen inventory dan supply chain
    4. Strategi penjualan dan marketing

    Tugas Anda adalah menganalisis data penjualan beserta hasil forecast (jika tersedia) dan memberikan insight yang bernilai strategis untuk pengambilan keputusan bisnis.

    Analisis Anda harus objektif, faktual, dan memberikan nilai tambah yang konkret. Gunakan data kuantitatif dari pola historis dan hasil forecast untuk mendukung rekomendasi Anda.

    Format respons harus berupa JSON yang terstruktur sesuai format yang diminta. Gunakan bahasa yang profesional dan ringkas.";
  }
}
