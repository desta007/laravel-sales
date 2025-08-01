<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Barang extends Model
{
    protected $fillable = [
        'name',
        'code',
        'price',
        'stock',
        'kategori_barang_id'
    ];

    public function kategoriBarang()
    {
        return $this->belongsTo(KategoriBarang::class);
    }
}
