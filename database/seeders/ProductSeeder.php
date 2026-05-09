<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $products = [
            ['id' => 214, 'product_name' => 'Daun Kremah', 'id_kategori' => 1, 'slug' => 'daun-kremah', 'price' => 2000.00, 'description' => '-', 'stock' => 0, 'images' => NULL, 'created_at' => '2026-03-05 08:58:12'],
            ['id' => 230, 'product_name' => 'Seledri Bks', 'id_kategori' => 1, 'slug' => 'seledri-bks', 'price' => 2000.00, 'description' => '-', 'stock' => 22, 'images' => NULL, 'created_at' => '2026-03-05 08:58:13'],
            ['id' => 300, 'product_name' => 'Cabe Kering', 'id_kategori' => 1, 'slug' => 'cabe-kering', 'price' => 3000.00, 'description' => '-', 'stock' => 0, 'images' => NULL, 'created_at' => '2026-03-05 08:58:13'],
            ['id' => 317, 'product_name' => 'Caisin Hidroponik', 'id_kategori' => 1, 'slug' => 'caisin-hidroponik', 'price' => 5000.00, 'description' => '-', 'stock' => 0, 'images' => NULL, 'created_at' => '2026-03-05 08:58:13'],
            ['id' => 373, 'product_name' => 'Tomat Cherry', 'id_kategori' => 1, 'slug' => 'tomat-cherry', 'price' => 10000.00, 'description' => '-', 'stock' => 0, 'images' => NULL, 'created_at' => '2026-03-05 08:58:13'],
            ['id' => 385, 'product_name' => 'Bawang Lanang', 'id_kategori' => 1, 'slug' => 'bawang-lanang', 'price' => 10000.00, 'description' => '-', 'stock' => 9, 'images' => NULL, 'created_at' => '2026-03-05 08:58:13'],
            ['id' => 415, 'product_name' => 'Daun Pepaya', 'id_kategori' => 1, 'slug' => 'daun-pepaya', 'price' => 7500.00, 'description' => '-', 'stock' => 0, 'images' => NULL, 'created_at' => '2026-03-05 08:58:13'],
            ['id' => 543, 'product_name' => 'Oseng Daun pepaya', 'id_kategori' => 1, 'slug' => 'oseng-daun-pepaya', 'price' => 7500.00, 'description' => '-', 'stock' => 0, 'images' => NULL, 'created_at' => '2026-03-05 08:58:13'],
            ['id' => 659, 'product_name' => 'Kemangi Bks', 'id_kategori' => 1, 'slug' => 'kemangi-bks', 'price' => 1750.00, 'description' => '-', 'stock' => 9, 'images' => NULL, 'created_at' => '2026-03-05 08:58:14'],
            ['id' => 667, 'product_name' => 'Tomat Merah B', 'id_kategori' => 1, 'slug' => 'tomat-merah-b', 'price' => 12000.00, 'description' => '-', 'stock' => 0, 'images' => NULL, 'created_at' => '2026-03-05 08:58:14'],
            ['id' => 670, 'product_name' => 'Daun Salam', 'id_kategori' => 1, 'slug' => 'daun-salam', 'price' => 1000.00, 'description' => '-', 'stock' => 18, 'images' => NULL, 'created_at' => '2026-03-05 08:58:14'],
            ['id' => 681, 'product_name' => 'Daun Pisang TM', 'id_kategori' => 1, 'slug' => 'daun-pisang-tm', 'price' => 2400.00, 'description' => '-', 'stock' => 11, 'images' => NULL, 'created_at' => '2026-03-05 08:58:14'],
            ['id' => 705, 'product_name' => 'Sayur Sop', 'id_kategori' => 1, 'slug' => 'sayur-sop', 'price' => 5000.00, 'description' => '-', 'stock' => 0, 'images' => NULL, 'created_at' => '2026-03-05 08:58:14'],
            ['id' => 710, 'product_name' => 'WORTEL MANIS', 'id_kategori' => 1, 'slug' => 'wortel-manis', 'price' => 17000.00, 'description' => '-', 'stock' => 0, 'images' => NULL, 'created_at' => '2026-03-05 08:58:14'],
            ['id' => 713, 'product_name' => 'Ucet / Buncis 175gr', 'id_kategori' => 1, 'slug' => 'ucet-buncis-175gr', 'price' => 3000.00, 'description' => '-', 'stock' => 11, 'images' => NULL, 'created_at' => '2026-03-05 08:58:14'],
            ['id' => 773, 'product_name' => 'Kemangi', 'id_kategori' => 1, 'slug' => 'kemangi', 'price' => 1500.00, 'description' => '-', 'stock' => 13, 'images' => NULL, 'created_at' => '2026-03-05 08:58:14'],
            ['id' => 818, 'product_name' => 'PETE KUPAS', 'id_kategori' => 1, 'slug' => 'pete-kupas', 'price' => 8000.00, 'description' => '-', 'stock' => 0, 'images' => NULL, 'created_at' => '2026-03-05 08:58:14'],
            ['id' => 833, 'product_name' => 'Terong Bunder Ungu', 'id_kategori' => 1, 'slug' => 'terong-bunder-ungu', 'price' => 10000.00, 'description' => '-', 'stock' => 0, 'images' => NULL, 'created_at' => '2026-03-05 08:58:14'],
            ['id' => 877, 'product_name' => 'Cabe Rawit C 250gr', 'id_kategori' => 1, 'slug' => 'cabe-rawit-c-250gr', 'price' => 26000.00, 'description' => '-', 'stock' => 4, 'images' => NULL, 'created_at' => '2026-03-05 08:58:14'],
            ['id' => 897, 'product_name' => 'Daun Mint', 'id_kategori' => 1, 'slug' => 'daun-mint', 'price' => 5000.00, 'description' => '-', 'stock' => 4, 'images' => NULL, 'created_at' => '2026-03-05 08:58:14'],
            ['id' => 904, 'product_name' => 'Daun Kedondong', 'id_kategori' => 1, 'slug' => 'daun-kedondong', 'price' => 2000.00, 'description' => '-', 'stock' => 0, 'images' => NULL, 'created_at' => '2026-03-05 08:58:14'],
            ['id' => 915, 'product_name' => 'Sayur Asem', 'id_kategori' => 1, 'slug' => 'sayur-asem', 'price' => 5000.00, 'description' => '-', 'stock' => 0, 'images' => NULL, 'created_at' => '2026-03-05 08:58:14'],
            ['id' => 932, 'product_name' => 'Daun So', 'id_kategori' => 1, 'slug' => 'daun-so', 'price' => 2000.00, 'description' => '-', 'stock' => 1, 'images' => NULL, 'created_at' => '2026-03-05 08:58:15'],
            ['id' => 940, 'product_name' => 'Terong Ungu Panjang', 'id_kategori' => 1, 'slug' => 'terong-ungu-panjang', 'price' => 7000.00, 'description' => '-', 'stock' => 8, 'images' => NULL, 'created_at' => '2026-03-05 08:58:15'],
            ['id' => 964, 'product_name' => 'Tomat C Bsr', 'id_kategori' => 1, 'slug' => 'tomat-c-bsr', 'price' => 8500.00, 'description' => '-', 'stock' => 11, 'images' => NULL, 'created_at' => '2026-03-05 08:58:15'],
            ['id' => 1046, 'product_name' => 'Sayur Lompong', 'id_kategori' => 1, 'slug' => 'sayur-lompong', 'price' => 4000.00, 'description' => '-', 'stock' => 0, 'images' => NULL, 'created_at' => '2026-03-05 08:58:15'],
            ['id' => 1085, 'product_name' => 'Cabe Besar Mix', 'id_kategori' => 1, 'slug' => 'cabe-besar-mix', 'price' => 4000.00, 'description' => '-', 'stock' => 0, 'images' => NULL, 'created_at' => '2026-03-05 08:58:15'],
            ['id' => 1264, 'product_name' => 'Daun Pandan', 'id_kategori' => 1, 'slug' => 'daun-pandan', 'price' => 1000.00, 'description' => '-', 'stock' => 0, 'images' => NULL, 'created_at' => '2026-03-05 08:58:15'],
            ['id' => 1314, 'product_name' => 'Kangkung Hidroponik', 'id_kategori' => 1, 'slug' => 'kangkung-hidroponik', 'price' => 5000.00, 'description' => '-', 'stock' => 0, 'images' => NULL, 'created_at' => '2026-03-05 08:58:16'],
            ['id' => 1452, 'product_name' => 'Seledri Hidroponik', 'id_kategori' => 1, 'slug' => 'seledri-hidroponik', 'price' => 3500.00, 'description' => '-', 'stock' => 0, 'images' => NULL, 'created_at' => '2026-03-05 08:58:16'],
            ['id' => 1582, 'product_name' => 'BAYAM PETIK', 'id_kategori' => 1, 'slug' => 'bayam-petik', 'price' => 2000.00, 'description' => '-', 'stock' => 2, 'images' => NULL, 'created_at' => '2026-03-05 08:58:16'],
            ['id' => 1585, 'product_name' => 'Kacang Panjang 200', 'id_kategori' => 1, 'slug' => 'kacang-panjang-200', 'price' => 3000.00, 'description' => '-', 'stock' => 30, 'images' => NULL, 'created_at' => '2026-03-05 08:58:16'],
            ['id' => 1851, 'product_name' => 'Terong Pokak', 'id_kategori' => 1, 'slug' => 'terong-pokak', 'price' => 11000.00, 'description' => '-', 'stock' => 0, 'images' => NULL, 'created_at' => '2026-03-05 08:58:17'],
            ['id' => 1874, 'product_name' => 'Tempe Daun', 'id_kategori' => 1, 'slug' => 'tempe-daun', 'price' => 5000.00, 'description' => '-', 'stock' => 0, 'images' => NULL, 'created_at' => '2026-03-05 08:58:17'],
            ['id' => 2054, 'product_name' => 'BAYAM MERAH', 'id_kategori' => 1, 'slug' => 'bayam-merah', 'price' => 1500.00, 'description' => '-', 'stock' => 0, 'images' => NULL, 'created_at' => '2026-03-05 08:58:18'],
            ['id' => 2523, 'product_name' => 'Wortel', 'id_kategori' => 1, 'slug' => 'wortel', 'price' => 13000.00, 'description' => '-', 'stock' => 12, 'images' => NULL, 'created_at' => '2026-03-05 08:58:19'],
            ['id' => 2644, 'product_name' => 'Terong Gelatik(Bundar Hijau)', 'id_kategori' => 1, 'slug' => 'terong-gelatikbundar-hijau', 'price' => 10000.00, 'description' => '-', 'stock' => 0, 'images' => NULL, 'created_at' => '2026-03-05 08:58:19'],
            ['id' => 2712, 'product_name' => 'Kubis', 'id_kategori' => 1, 'slug' => 'kubis', 'price' => 11000.00, 'description' => '-', 'stock' => 20, 'images' => NULL, 'created_at' => '2026-03-05 08:58:19'],
            ['id' => 2815, 'product_name' => 'Sayuran Organik Mak Cemput', 'id_kategori' => 1, 'slug' => 'sayuran-organik-mak-cemput', 'price' => 7000.00, 'description' => '-', 'stock' => 3, 'images' => NULL, 'created_at' => '2026-03-05 08:58:20'],
            ['id' => 2820, 'product_name' => 'Daun Jeruk', 'id_kategori' => 1, 'slug' => 'daun-jeruk', 'price' => 1000.00, 'description' => '-', 'stock' => 15, 'images' => NULL, 'created_at' => '2026-03-05 08:58:20'],
            ['id' => 2834, 'product_name' => 'Cabe Rawit B 100gr', 'id_kategori' => 1, 'slug' => 'cabe-rawit-b-100gr', 'price' => 10500.00, 'description' => '-', 'stock' => 9, 'images' => NULL, 'created_at' => '2026-03-05 08:58:20'],
            ['id' => 2862, 'product_name' => 'Kacang Panjang LODEHAN', 'id_kategori' => 1, 'slug' => 'kacang-panjang-lodehan', 'price' => 1000.00, 'description' => '-', 'stock' => 8, 'images' => NULL, 'created_at' => '2026-03-05 08:58:20'],
            ['id' => 3264, 'product_name' => 'Cabe Lalap', 'id_kategori' => 1, 'slug' => 'cabe-lalap', 'price' => 3000.00, 'description' => '-', 'stock' => 20, 'images' => NULL, 'created_at' => '2026-03-05 08:58:21'],
            ['id' => 3265, 'product_name' => 'Cabe Rawit A 50gr', 'id_kategori' => 1, 'slug' => 'cabe-rawit-a-50gr', 'price' => 5500.00, 'description' => '-', 'stock' => 47, 'images' => NULL, 'created_at' => '2026-03-05 08:58:21'],
            ['id' => 3268, 'product_name' => 'BUNCIS POLONG', 'id_kategori' => 1, 'slug' => 'buncis-polong', 'price' => 4000.00, 'description' => '-', 'stock' => 8, 'images' => NULL, 'created_at' => '2026-03-05 08:58:21'],
            ['id' => 3269, 'product_name' => 'Kubis Ungu', 'id_kategori' => 1, 'slug' => 'kubis-ungu', 'price' => 45500.00, 'description' => '-', 'stock' => 0, 'images' => NULL, 'created_at' => '2026-03-05 08:58:21'],
            ['id' => 3281, 'product_name' => 'Tomat Hijau', 'id_kategori' => 1, 'slug' => 'tomat-hijau', 'price' => 10000.00, 'description' => '-', 'stock' => 1, 'images' => NULL, 'created_at' => '2026-03-05 08:58:21'],
            ['id' => 3286, 'product_name' => 'Bawang Putih Kating', 'id_kategori' => 1, 'slug' => 'bawang-putih-kating', 'price' => 37500.00, 'description' => '-', 'stock' => 45, 'images' => NULL, 'created_at' => '2026-03-05 08:58:21'],
            ['id' => 3288, 'product_name' => 'Sawi daging', 'id_kategori' => 1, 'slug' => 'sawi-daging', 'price' => 8000.00, 'description' => '-', 'stock' => 17, 'images' => NULL, 'created_at' => '2026-03-05 08:58:21'],
            ['id' => 3292, 'product_name' => 'Bawang Bombay', 'id_kategori' => 1, 'slug' => 'bawang-bombay', 'price' => 35500.00, 'description' => '-', 'stock' => 53, 'images' => NULL, 'created_at' => '2026-03-05 08:58:21'],
            ['id' => 3300, 'product_name' => 'Bawang Putih Sinco ECER', 'id_kategori' => 1, 'slug' => 'bawang-putih-sinco-ecer', 'price' => 34000.00, 'description' => '-', 'stock' => 47, 'images' => NULL, 'created_at' => '2026-03-05 08:58:21'],
            ['id' => 3316, 'product_name' => 'Terong Panjang', 'id_kategori' => 1, 'slug' => 'terong-panjang', 'price' => 8500.00, 'description' => '-', 'stock' => 3, 'images' => NULL, 'created_at' => '2026-03-05 08:58:21'],
            ['id' => 3321, 'product_name' => 'Cabe Merah', 'id_kategori' => 1, 'slug' => 'cabe-merah', 'price' => 3000.00, 'description' => '-', 'stock' => 19, 'images' => NULL, 'created_at' => '2026-03-05 08:58:21'],
            ['id' => 3389, 'product_name' => 'Cabe Hijau 75gr', 'id_kategori' => 1, 'slug' => 'cabe-hijau-75gr', 'price' => 2500.00, 'description' => '-', 'stock' => 21, 'images' => NULL, 'created_at' => '2026-03-05 08:58:21'],
            ['id' => 3412, 'product_name' => 'Cabe Keriting', 'id_kategori' => 1, 'slug' => 'cabe-keriting', 'price' => 3000.00, 'description' => '-', 'stock' => 21, 'images' => NULL, 'created_at' => '2026-03-05 08:58:21'],
            ['id' => 3465, 'product_name' => 'Brokoli Hijau', 'id_kategori' => 1, 'slug' => 'brokoli-hijau', 'price' => 32000.00, 'description' => '-', 'stock' => 24, 'images' => NULL, 'created_at' => '2026-03-05 08:58:22'],
            ['id' => 3470, 'product_name' => 'Sawi Putih/Sipu', 'id_kategori' => 1, 'slug' => 'sawi-putihsipu', 'price' => 8000.00, 'description' => '-', 'stock' => 50, 'images' => NULL, 'created_at' => '2026-03-05 08:58:22'],
            ['id' => 3482, 'product_name' => 'Brokoli Putih', 'id_kategori' => 1, 'slug' => 'brokoli-putih', 'price' => 26000.00, 'description' => '-', 'stock' => 1, 'images' => NULL, 'created_at' => '2026-03-05 08:58:22'],
            ['id' => 3483, 'product_name' => 'Kangkung', 'id_kategori' => 1, 'slug' => 'kangkung', 'price' => 1500.00, 'description' => '-', 'stock' => 15, 'images' => NULL, 'created_at' => '2026-03-05 08:58:22'],
            ['id' => 3484, 'product_name' => 'Sawi Hijau', 'id_kategori' => 1, 'slug' => 'sawi-hijau', 'price' => 1250.00, 'description' => '-', 'stock' => 18, 'images' => NULL, 'created_at' => '2026-03-05 08:58:22'],
            ['id' => 3534, 'product_name' => 'Bayam Fresh', 'id_kategori' => 1, 'slug' => 'bayam-fresh', 'price' => 2250.00, 'description' => '-', 'stock' => 13, 'images' => NULL, 'created_at' => '2026-03-05 08:58:22'],
        ];

        DB::table('products')->delete(); // Clear existing products first to avoid duplicates
        
        $count = 0;
        foreach (array_chunk($products, 100) as $chunk) {
            DB::table('products')->insert($chunk);
            $count += count($chunk);
            $this->command->info("Inserted $count vegetable products");
        }
    }
}
