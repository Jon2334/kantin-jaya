<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'item_id',
        'qty',
        'harga_satuan',
        'subtotal',
    ];

    /**
     * Relasi: Detail ini milik Order ID berapa?
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Relasi: Detail ini merujuk ke Item (Menu) yang mana?
     */
    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}