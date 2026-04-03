<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Auth\User;
use App\Models\Business\Business;
use App\Models\Business\Branch;

class BranchSeeder extends Seeder
{
    public function run(): void
    {
        $businesses     = Business::all();
        $remainingUsers = User::where('is_admin', false)->skip(4)->take(6)->get();
        $userPool       = $remainingUsers->values();
        $userIndex      = 0;

        foreach ($businesses as $index => $business) {
            $physicalBranch = Branch::create([
                'business_id'    => $business->id,
                'name'           => "Main Branch " . ($index + 1),
                'type'           => 'physical',
                'address_line_1' => fake()->streetAddress(),
                'city'           => 'Bratislava',
                'postal_code'    => '811 0' . ($index + 1),
                'country'        => 'Slovakia',
                'latitude'       => 48.1486 + ($index * 0.001),
                'longitude'      => 17.1077 + ($index * 0.001),
                'is_active'      => true,
            ]);

            $onlineBranch = Branch::create([
                'business_id' => $business->id,
                'name'        => "Online Branch " . ($index + 1),
                'type'        => 'online',
                'is_active'   => true,
            ]);

            // Assign manager to physical branch
            if (isset($userPool[$userIndex])) {
                $physicalBranch->users()->attach($userPool[$userIndex]->id, ['role' => 'manager']);
                $userIndex++;
            }

            // Assign staff to physical branch
            if (isset($userPool[$userIndex])) {
                $physicalBranch->users()->attach($userPool[$userIndex]->id, ['role' => 'staff']);
                $userIndex++;
            }
        }
    }
}
