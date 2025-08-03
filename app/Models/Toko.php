<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Toko extends Model
{
    protected $fillable = [
        'name',
        'address',
        'phone',
        'barcode',
        'wilayah_id',
        'latitude',
        'longitude',
        'photo',
        'sales_id'
    ];

    public function wilayah()
    {
        return $this->belongsTo(Wilayah::class);
    }

    public function sales()
    {
        return $this->belongsTo(Sales::class);
    }

    public function salesTransactions()
    {
        return $this->hasMany(SalesTransaction::class, 'toko_id');
    }

    protected static function booted()
    {
        static::creating(function ($model) {
            if (empty($model->barcode)) {
                $model->barcode = self::generateBarcode();
            }
        });
    }

    public static function generateBarcode(): string
    {
        $now = Carbon::now();
        $prefix = $now->format('Y-m'); // e.g. "2025-08"

        // Ambil sequence maksimum untuk bulan ini
        $max = DB::table((new self)->getTable())
            ->where('barcode', 'like', $prefix . '-%')
            ->selectRaw('MAX(CAST(RIGHT(barcode,3) AS UNSIGNED)) as max_seq')
            ->value('max_seq');

        $next = intval($max) + 1;
        $seq = str_pad($next, 3, '0', STR_PAD_LEFT); // 3 digit dengan padding

        return "{$prefix}-{$seq}";
    }
}
