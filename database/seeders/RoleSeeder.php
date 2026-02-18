<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        Role::create(['id' => 1, 'name' => 'komandan']);
        Role::create(['id' => 2, 'name' => 'personel']);
    }
}
