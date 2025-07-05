<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('forecast_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('forecast_id')->constrained('forecasts')->onDelete('cascade');
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->json('predictions')->nullable(); // menyimpan hasil prediksi dalam format JSON
            $table->json('actuals')->nullable(); // menyimpan nilai aktual dalam format JSON
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('forecast_results');
    }
};
