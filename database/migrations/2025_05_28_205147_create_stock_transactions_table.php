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
        Schema::create('stock_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            // 'in' untuk penambahan stok, 'out' untuk pengurangan stok, 'adjustment' untuk penyesuaian stok
            $table->enum('type', ['in', 'out', 'adjustment'])->default('in');
            $table->integer('qty')->default(0);
            // 'source_type' untuk menyimpan jenis sumber transaksi, misalnya 'purchase', 'sale', 'adjustment', dll.
            // 'source_id' untuk menyimpan ID sumber transaksi yang relevan
            $table->nullableMorphs('source');
            $table->text('note')->nullable();
            // catat oleh siapa transaksi ini dibuat
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_transactions');
    }
};
