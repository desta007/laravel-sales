<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Toko extends Model
{
    protected $fillable = [
        'name', 'address', 'phone', 'barcode', 'wilayah_id',
        'latitude', 'longitude', 'photo',
    ];

    public function wilayah()
    {
        return $this->belongsTo(Wilayah::class);
    }
}
