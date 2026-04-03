<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Business\Asset;
use App\Models\Business\Rule;

class RuleSeeder extends Seeder
{
    public function run(): void
    {
        foreach (Asset::all() as $asset) {
            if (str_contains($asset->name, 'Consultation Room')) {
                Rule::create([
                    'asset_id'    => $asset->id,
                    'priority'    => 1,
                    'title'       => 'Weekday Hours',
                    'description' => 'Available Monday–Friday, 9am–5pm.',
                    'valid_from'  => now()->startOfMonth(),
                    'valid_to'    => now()->endOfMonth(),
                    'rule_set'    => [
                        'days' => ['monday', 'tuesday', 'wednesday', 'thursday', 'friday'],
                        'from' => '09:00',
                        'to'   => '17:00',
                    ],
                ]);
            }

            if (str_contains($asset->name, 'Workshop Table')) {
                Rule::create([
                    'asset_id'    => $asset->id,
                    'priority'    => 1,
                    'title'       => 'Workshop Days',
                    'description' => 'Available Tuesday and Thursday only.',
                    'valid_from'  => now()->startOfMonth(),
                    'valid_to'    => now()->endOfMonth(),
                    'rule_set'    => [
                        'days' => ['tuesday', 'thursday'],
                        'from' => '10:00',
                        'to'   => '18:00',
                    ],
                ]);
            }
        }
    }
}
