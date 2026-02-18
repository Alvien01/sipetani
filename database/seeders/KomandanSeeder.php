<?php

namespace Database\Seeders;

use App\Models\Personel;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class KomandanSeeder extends Seeder
{
    public function run(): void
    {
        Personel::create([
            'name' => 'Komandan Admin',
            'email' => 'komandan@siaga.app',
            'password' => Hash::make('password'),
            'rank' => 'Komandan',
            'nrp' => '00000001',
            'position' => 'Komandan',
            'role_id' => 1,
            'status' => 'Tersedia',
        ]);
    }
}
