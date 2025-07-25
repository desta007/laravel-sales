<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalesTransaction extends Model
{
    protected $fillable = [
        'sales_id', 'toko_id', 'transaction_date', 'total_amount', 'notes',
    ];

    public function sales()
    {
        return $this->belongsTo(Sales::class, 'sales_id');
    }

    public function toko()
    {
        return $this->belongsTo(Toko::class, 'toko_id');
    }

    public function details()
    {
        return $this->hasMany(SalesTransactionDetail::class);
    }
}
