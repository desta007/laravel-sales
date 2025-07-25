<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class Sales extends Model
{
    use HasApiTokens;

    protected $fillable = [
        'name', 'email', 'password', 'phone', 'wilayah_id',
    ];

    protected static function booted()
    {
        static::saving(function ($sales) {
            if ($sales->isDirty('password')) {
                $sales->password = bcrypt($sales->password);
            }
        });
    }

    public function wilayah()
    {
        return $this->belongsTo(Wilayah::class);
    }
}
