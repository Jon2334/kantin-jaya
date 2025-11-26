<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('procurements', function (Blueprint $table) {
            $table->id();
            // Siapa yang minta (Kasir)
            $table->foreignId('user_id')->constrained('users'); 
            // Barang apa yang diminta
            $table->foreignId('inventory_id')->constrained('inventories')->onDelete('cascade');
            
            $table->double('jumlah'); // Jumlah barang (support desimal)
            $table->integer('total_harga')->nullable(); // Estimasi harga (opsional)
            
            // Status Transaksi
            // pending: Kasir baru minta
            // paid: Sudah dibayar (simulasi)
            // shipped: Supplier sudah kirim (Stok bertambah di sini)
            $table->enum('status', ['pending', 'paid', 'shipped'])->default('pending');

            // Data Pengiriman (Diisi Supplier)
            $table->date('tanggal_kirim')->nullable();
            $table->string('nomor_resi')->nullable();
            $table->text('catatan')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('procurements');
    }
};