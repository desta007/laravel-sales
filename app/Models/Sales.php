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

    // Removed booted method to prevent double hashing of password

    public function wilayah()
    {
        return $this->belongsTo(Wilayah::class);
    }
}
