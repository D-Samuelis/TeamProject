<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

use App\Models\Auth\User;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin',
                'password' => Hash::make('password'),
                'is_admin' => true,
                'country' => 'Slovakia',
                'city' => 'Bratislava',
                'title_prefix' => null,
                'birth_date' => null,
                'title_suffix' => null,
                'phone_number' => '123456789',
                'gender' => 'other',
            ]
        );
    }
}
