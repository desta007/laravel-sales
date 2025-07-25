<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalesTransactionDetail extends Model
{
    protected $fillable = [
        'sales_transaction_id', 'barang_id', 'quantity', 'price', 'subtotal',
    ];

    public function transaction()
    {
        return $this->belongsTo(SalesTransaction::class, 'sales_transaction_id');
    }

    public function barang()
    {
        return $this->belongsTo(Barang::class, 'barang_id');
    }
}
