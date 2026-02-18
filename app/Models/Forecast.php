<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Forecast extends Model
{
    protected $fillable = [
        'product_id',
        'transaction_id', // Nullable
        'type', // monthly, weekly
        'month',
        'weekly',
        'year',
        'total', // quantity
        'st',
        'sst',
        'at',
        'bt',
        'forecast',
        'pe', // percentage error
        'selisih', // diff
        'evaluasi', // description/metric
        'actual_prev', // previous value
        'alpha',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }
}
