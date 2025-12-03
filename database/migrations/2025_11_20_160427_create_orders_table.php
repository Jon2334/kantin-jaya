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
            
            // Relasi ke tabel users (Pembeli)
            // onDelete cascade: jika user dihapus, riwayat order ikut terhapus
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            
            // Metode pembayaran
            $table->enum('payment_method', ['qris', 'tunai']);
            
            // Status Pesanan:
            // pending: Baru masuk
            // processing: Sedang dimasak (Dapur)
            // done: Selesai dimasak -> Notif ke Kasir
            $table->enum('status', ['pending', 'processing', 'done'])->default('pending');
            
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