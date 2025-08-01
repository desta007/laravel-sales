<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalesVisitHistory extends Model
{
    protected $fillable = [
        'sales_id',
        'toko_id',
        'visit_date',
        'notes',
        'latitude',
        'longitude'
    ];

    public function sales()
    {
        return $this->belongsTo(Sales::class, 'sales_id');
    }

    public function toko()
    {
        return $this->belongsTo(Toko::class, 'toko_id');
    }
}
