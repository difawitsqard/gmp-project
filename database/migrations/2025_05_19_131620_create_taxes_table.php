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
        Schema::create('taxes', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // contoh: PPN, Service Charge, dll
            $table->float('percent')->nullable(); // jika ada pajak persentase
            $table->bigInteger('fixed_amount')->nullable(); // jika ada pajak nominal tetap
            $table->boolean('status')->default(1); // status aktif/tidak aktif
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('taxes');
    }
};
