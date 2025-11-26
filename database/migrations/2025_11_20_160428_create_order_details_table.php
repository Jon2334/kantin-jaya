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
        Schema::create('order_details', function (Blueprint $table) {
            $table->id();
            
            // Terhubung ke tabel orders
            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
            
            // Terhubung ke tabel items (Menu)
            $table->foreignId('item_id')->constrained('items')->onDelete('cascade');
            
            $table->integer('qty'); // Jumlah porsi yang dipesan
            
            // Kita simpan harga saat transaksi terjadi
            // Agar jika harga menu naik besok, riwayat transaksi lama harganya tetap benar
            $table->integer('harga_satuan'); 
            
            $table->integer('subtotal'); // qty * harga_satuan
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_details');
    }
};