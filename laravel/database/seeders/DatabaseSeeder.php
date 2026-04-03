<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $this->call([
            AdminSeeder::class,
            UserSeeder::class,
            BusinessSeeder::class,
            BranchSeeder::class,
            ServiceSeeder::class,
            BranchServiceSeeder::class,
            AssetSeeder::class,
            RuleSeeder::class,
            AppointmentSeeder::class,
        ]);
    }
}
