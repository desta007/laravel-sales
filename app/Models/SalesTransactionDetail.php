<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalesTransactionDetail extends Model
{
    protected $fillable = [
        'sales_transaction_id',
        'barang_id',
        'quantity',
        'price',
        'subtotal',
    ];

    public function transaction()
    {
        return $this->belongsTo(SalesTransaction::class, 'sales_transaction_id');
    }

    public function barang()
    {
        return $this->belongsTo(Barang::class, 'barang_id');
    }

    protected static function booted()
    {
        static::saving(function (self $detail) {
            // Jika price belum di-set atau berubah karena barang, ambil dari barang
            if ($detail->barang?->price !== null) {
                $detail->price = $detail->barang->price;
            }

            // Pastikan subtotal konsisten
            $detail->subtotal = ($detail->quantity ?? 0) * ($detail->price ?? 0);
        });

        static::saved(function (self $detail) {
            $detail->transaction?->recalculateTotalAmount();
        });

        static::deleted(function (self $detail) {
            $detail->transaction?->recalculateTotalAmount();
        });
    }
}
