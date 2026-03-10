<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Auth\User;
use App\Models\Business\Branch;
use App\Models\Business\Business;
use App\Models\Business\Service;
use App\Domain\Business\Enums\BusinessRoleEnum;

class UsersBusinessesSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            // 1. Create 10 users
            $users = User::factory()->count(10)->create([
                'country' => 'Slovakia',
                'city' => 'Bratislava',
                'phone_number' => fake()->phoneNumber(),
            ]);

            $owners = $users->take(4);
            $remainingUsers = $users->slice(4); // 6 users left for Staff/Managers

            foreach ($owners as $index => $owner) {
                // 2. Create Business
                $business = Business::create([
                    'name' => "Business " . ($index + 1),
                    'description' => "Demo business " . ($index + 1),
                    'is_published' => true,
                ]);

                // NEW: Assign Owner via Polymorphic Pivot
                $business->users()->attach($owner->id, [
                    'role' => BusinessRoleEnum::OWNER->value
                ]);

                // 3. Create Branches
                $physicalBranch = Branch::create([
                    'business_id' => $business->id,
                    'name' => "Main Branch " . ($index + 1),
                    'type' => 'physical',
                    'city' => 'Bratislava',
                    'is_active' => true,
                ]);

                $onlineBranch = Branch::create([
                    'business_id' => $business->id,
                    'name' => "Online Branch " . ($index + 1),
                    'type' => 'online',
                    'is_active' => true,
                ]);

                // NEW: Assign a Manager to the Physical Branch
                // We'll take one of the remaining users for each business
                if ($manager = $remainingUsers->shift()) {
                    $physicalBranch->users()->attach($manager->id, [
                        'role' => 'manager' // Or BranchRoleEnum::MANAGER->value
                    ]);
                }

                // 4. Create Services
                $services = collect([
                    Service::create([
                        'business_id' => $business->id,
                        'name' => 'Consultation',
                        'duration_minutes' => 60,
                        'price' => 50,
                        'location_type' => 'branch',
                        'is_active' => true,
                    ]),
                    Service::create([
                        'business_id' => $business->id,
                        'name' => 'Online Session',
                        'duration_minutes' => 45,
                        'price' => 40,
                        'location_type' => 'online',
                        'is_active' => true,
                    ])
                ]);

                // Attach services to branches and assign Staff to the service
                foreach ($services as $service) {
                    if ($service->location_type === 'branch') {
                        $service->branches()->attach($physicalBranch->id);
                    } else {
                        $service->branches()->attach($onlineBranch->id);
                    }

                    // NEW: Assign "Staff" to specific Services
                    // Using the same owner as staff for demo, or take from remaining users
                    $service->users()->attach($owner->id, ['role' => 'staff']);
                }
            }
        });
    }
}
