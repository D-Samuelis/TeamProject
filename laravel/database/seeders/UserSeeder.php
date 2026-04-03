<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Auth\User;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::factory()->count(10)->create([
            'country'      => 'Slovakia',
            'city'         => 'Bratislava',
            'phone_number' => fake()->phoneNumber(),
        ]);
    }
}
