<?php

namespace Database\Seeders;

use App\Enums\BusinessRole;
use App\Models\Branch;
use App\Models\Business;
use App\Models\Service;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UsersBusinessesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::transaction(function () {

            // Create 10 users
            $users = User::factory()->count(10)->create();

            // We'll make first 4 users business owners
            $owners = $users->take(4);

            foreach ($owners as $index => $owner) {

                // Create Business
                $business = Business::create([
                    'name' => "Business " . ($index + 1),
                    'description' => "Demo business " . ($index + 1),
                    'is_published' => true,
                ]);

                // Attach owner
                $business->users()->attach($owner->id, [
                    'role' => BusinessRole::OWNER->value
                ]);

                // Create 2 branches per business
                $physicalBranch = Branch::create([
                    'business_id' => $business->id,
                    'name' => "Main Branch",
                    'type' => 'physical',
                    'address_line_1' => 'Main Street 1',
                    'city' => 'Bratislava',
                    'postal_code' => '81101',
                    'country' => 'Slovakia',
                    'is_active' => true,
                ]);

                $onlineBranch = Branch::create([
                    'business_id' => $business->id,
                    'name' => "Online Branch",
                    'type' => 'online',
                    'is_active' => true,
                ]);

                // Create 3 services per business
                $services = collect();

                $services->push(Service::create([
                    'business_id' => $business->id,
                    'name' => 'Consultation',
                    'description' => 'Standard consultation',
                    'duration_minutes' => 60,
                    'price' => 50,
                    'location_type' => 'branch',
                    'is_active' => true,
                ]));

                $services->push(Service::create([
                    'business_id' => $business->id,
                    'name' => 'Online Session',
                    'description' => 'Zoom based session',
                    'duration_minutes' => 45,
                    'price' => 40,
                    'location_type' => 'online',
                    'is_active' => true,
                ]));

                $services->push(Service::create([
                    'business_id' => $business->id,
                    'name' => 'Premium Package',
                    'description' => 'Extended service',
                    'duration_minutes' => 90,
                    'price' => 120,
                    'location_type' => 'hybrid',
                    'is_active' => true,
                ]));

                // Attach services to compatible branches
                foreach ($services as $service) {

                    if ($service->location_type === 'branch') {
                        $service->branches()->attach($physicalBranch->id);
                    }

                    if ($service->location_type === 'online') {
                        $service->branches()->attach($onlineBranch->id);
                    }

                    if ($service->location_type === 'hybrid') {
                        $service->branches()->attach([
                            $physicalBranch->id,
                            $onlineBranch->id
                        ]);
                    }
                }
            }

        });
    }
}
