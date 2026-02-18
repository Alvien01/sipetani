<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Product extends Model
{
    protected $fillable = [
        'product_name',
        'slug',
        'price',
        'description',
        'stock',
        'images',
    ];

    public $timestamps = false;

    const CREATED_AT = 'created_at';
    const UPDATED_AT = null;

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($product) {
            if (empty($product->slug)) {
                $product->slug = Str::slug($product->product_name);
            }
        });
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function forecasts()
    {
        return $this->hasMany(Forecast::class);
    }

    public function hasilPeramalan()
    {
        return $this->hasMany(HasilPeramalan::class, 'id_produk');
    }
}
