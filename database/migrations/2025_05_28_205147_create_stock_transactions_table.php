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
            $table->enum('type', ['in', 'out'])->default('in');
            $table->integer('qty')->default(0);
            $table->integer('stock_before')->default(0); // Stok sebelum transaksi
            $table->integer('stock_after')->default(0);  // Stok setelah transaksi
            // 'source_type' untuk menyimpan jenis sumber transaksi, misalnya 'purchase', 'sale', 'adjustment', dll.
            // 'source_id' untuk menyimpan ID sumber transaksi yang relevan
            $table->nullableMorphs('source');
            $table->text('note')->nullable();
            $table->string('batch_reference')->nullable(); // referensi batch untuk transaksi ini
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
