<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class KategoriSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $kategoris = [
            'sayur',
            'buah',
            'makanan',
            'cemilan',
            'kebutuhan dapur',
            'bumbu dapur',
        ];

        foreach ($kategoris as $kategori) {
            DB::table('kategoris')->insert([
                'name' => ucfirst($kategori),
                'slug' => Str::slug($kategori),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
