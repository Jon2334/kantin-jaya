<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Procurement extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'inventory_id',
        'jumlah',
        'total_harga',
        'status',
        'tanggal_kirim',
        'nomor_resi',
        'catatan',
    ];

    // Relasi ke Barang (Bahan Baku)
    public function inventory()
    {
        return $this->belongsTo(Inventory::class);
    }

    // Relasi ke Pemesan (Kasir)
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}