<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use function Laravel\Prompts\warning;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        //$this->call(MockDataSeeder::class);
        $this->call([
            AdminSeeder::class,
            UserSeeder::class,
            BusinessSeeder::class,
            AppointmentSeeder::class,
        ]);
    }
}
