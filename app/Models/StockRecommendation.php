<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockRecommendation extends Model
{
    protected $table = 'stock_recommendations';

    protected $fillable = [
        'id_produk',
        'periode',
        'tipe_periode',
        'current_stock',
        'forecast_qty',
        'safety_stock',
        'recommended_qty',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'id_produk', 'id');
    }
}
