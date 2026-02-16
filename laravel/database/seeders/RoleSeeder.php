<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Role::firstOrCreate(['name' => 'client']);
        Role::firstOrCreate(['name' => 'provider']);
        Role::firstOrCreate(['name' => 'admin']);
    }
}
