<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventories', function (Blueprint $table) {
            $table->id();
            $table->string('nama_barang');
            
            // Dua jenis stok
            $table->double('stok')->default(0);          // Stok milik Dapur
            $table->double('stok_supplier')->default(0); // Stok milik Supplier (BARU)
            
            $table->integer('harga')->default(0);
            $table->string('satuan');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventories');
    }
};