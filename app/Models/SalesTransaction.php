<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalesTransaction extends Model
{
    protected $fillable = [
        'sales_id',
        'toko_id',
        'transaction_date',
        'notes',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
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
        return $this->hasMany(SalesTransactionDetail::class, 'sales_transaction_id');
    }

    public function getTotalAmountAttribute($value): float
    {
        if ($value !== null) {
            return (float) $value;
        }

        if ($this->relationLoaded('details')) {
            return (float) $this->details->sum('subtotal');
        }

        return (float) $this->details()->sum('subtotal');
    }

    public function recalculateTotalAmount(): void
    {
        $total = (float) $this->details()->sum('subtotal');
        $this->forceFill(['total_amount' => $total])->saveQuietly();
    }
}
