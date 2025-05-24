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
            //$table->foreignId('customer_id')->constrained('users', 'id')->onDelete('restrict'); // User pemilik pesanan
            $table->string('name')->nullable(); // Nama 
            $table->string('email')->nullable(); // Email pemesan
            $table->string('phone')->nullable(); // Nomor telepon pemesan
            $table->string('address')->nullable(); // Alamat 
            $table->enum('shipping_method', ['pickup', 'delivery'])->default('pickup');
            $table->bigInteger('shipping_fee')->nullable();
            $table->bigInteger('sub_total'); // Sub total harga pesanan
            $table->bigInteger('total'); // Total harga pesanan
            $table->enum('status', [
                'waiting_confirmation', // Menunggu konfirmasi admin ( Menentukan Ongkir )
                'pending',              // Menunggu pembayaran
                'waiting_processing',   // Sudah dibayar, Menunggu Disiapkan atau di proses oleh staff gudang
                'completed',            // Selesai, barang sudah siap diambil atau sedang dikirim
                'cancelled'             // Dibatalkan
            ])->default('pending');
            $table->foreignId('uplink_id')->nullable()->constrained('users', 'id')->onDelete('restrict'); // Foreign key ke tabel users, kolom uplink_id
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
