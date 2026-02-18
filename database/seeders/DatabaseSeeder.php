<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create default admin user
        User::firstOrCreate(
            ['email' => 'admin@sipetani.com'],
            [
                'name'     => 'Admin SiPetani',
                'password' => Hash::make('password'),
            ]
        );
    }
}
