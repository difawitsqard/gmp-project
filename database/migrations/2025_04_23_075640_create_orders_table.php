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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable(); // Nama pemesan
            $table->bigInteger('sub_total'); // Sub total harga pesanan
            $table->bigInteger('total'); // Total harga pesanan
            $table->enum('payment_method', ['cash', 'transfer_bank', 'credit_card', 'other'])->default('cash');
            $table->enum('status', ['pending', 'processing', 'completed', 'cancelled'])->default('pending');
            $table->enum('payment_status', ['unpaid', 'paid', 'failed'])->default('unpaid');
            // $table->foreignId('user_id')->constrained('users')->onDelete('restrict'); // Foreign key ke tabel users
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
