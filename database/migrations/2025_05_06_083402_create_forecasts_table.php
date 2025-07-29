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
        Schema::create('forecasts', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable(); // Nama prediksi
            $table->dateTime('forecasted_at'); // waktu saat prediksi dibuat
            $table->enum('frequency', ['D', 'W', 'M']); // 'D' = daily, 'W' = weekly, 'M' = monthly
            $table->integer('horizon'); // jumlah periode ke depan yang diprediksi
            $table->string('model')->nullable();
            $table->date('input_start_date');
            $table->date('input_end_date');
            $table->enum('status', ['pending', 'processing', 'done', 'failed'])->default('pending'); // Status prediksi
            $table->text('note')->nullable(); // Catatan atau pesan terkait prediksi
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('forecasts');
    }
};
