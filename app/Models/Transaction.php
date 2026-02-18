<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'product_id',
        'date_sale',
        'total_buy',
        'total_payment',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
