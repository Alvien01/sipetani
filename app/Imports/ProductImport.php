<?php

namespace App\Imports;

use App\Models\Product;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ProductImport implements ToModel, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        if (empty($row['product_name'])) {
            return null;
        }

        $slug = Str::slug($row['product_name']);
        $originalSlug = $slug;
        $count = 1;
        while (Product::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $count++;
        }

        return new Product([
            'product_name' => trim($row['product_name']),
            'slug'         => $slug,
            'price'        => isset($row['price']) ? (float) str_replace([',', '.'], ['', '.'], $row['price']) : 0,
            'description'  => trim($row['description'] ?? ''),
            'stock'        => isset($row['stock']) ? (int) $row['stock'] : 0,
        ]);
    }
}
