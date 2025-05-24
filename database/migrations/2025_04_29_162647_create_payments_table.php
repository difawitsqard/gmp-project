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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Models\Order::class, 'order_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('midtrans_order_id')->unique()->nullable(); // contoh: ORD-123-1
            $table->string('payment_type')->nullable(); // qris, bank_transfer, etc
            $table->string('snap_token')->nullable(); // Token Snap Midtrans (jika pakai)
            $table->enum('status', ['pending', 'paid', 'failed', 'expired', 'cancelled'])->default('pending');
            $table->dateTime('paid_at')->nullable();
            $table->dateTime('expired_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
