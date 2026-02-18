<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HasilPeramalan extends Model
{
    protected $table = 'hasil_peramalan';

    protected $fillable = [
        'id_produk',
        'periode',
        'tipe_periode',
        'aktual',
        'st',
        'bt',
        'forecast',
        'alpha',
        'beta',
        'pe',
        'mape',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'id_produk');
    }
}
