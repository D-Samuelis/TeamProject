<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

use App\Domain\Business\Enums\BusinessRoleEnum;

use App\Models\Auth\User;
use App\Models\Business\Branch;
use App\Models\Business\Business;
use App\Models\Business\Service;

class UsersBusinessesSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {

            // Create 10 users with all fields populated
            $users = User::factory()->count(10)->create([
                'country' => 'Slovakia',
                'city' => 'Bratislava',
                'title_prefix' => null,
                'birth_date' => now()->subYears(30),
                'title_suffix' => null,
                'phone_number' => fake()->phoneNumber(),
                'gender' => fake()->randomElement(['male', 'female', 'other', 'none']),
            ]);

            $owners = $users->take(4);

            foreach ($owners as $index => $owner) {

                $business = Business::create([
                    'name' => "Business " . ($index + 1),
                    'description' => "Demo business " . ($index + 1),
                    'is_published' => true,
                ]);

                $business->users()->attach($owner->id, [
                    'role' => BusinessRoleEnum::OWNER->value
                ]);

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

                $services = collect([
                    Service::create([
                        'business_id' => $business->id,
                        'name' => 'Consultation',
                        'description' => 'Standard consultation',
                        'duration_minutes' => 60,
                        'price' => 50,
                        'location_type' => 'branch',
                        'is_active' => true,
                    ]),
                    Service::create([
                        'business_id' => $business->id,
                        'name' => 'Online Session',
                        'description' => 'Zoom based session',
                        'duration_minutes' => 45,
                        'price' => 40,
                        'location_type' => 'online',
                        'is_active' => true,
                    ]),
                    Service::create([
                        'business_id' => $business->id,
                        'name' => 'Premium Package',
                        'description' => 'Extended service',
                        'duration_minutes' => 90,
                        'price' => 120,
                        'location_type' => 'hybrid',
                        'is_active' => true,
                    ])
                ]);

                // Attach services to correct branches
                foreach ($services as $service) {
                    if ($service->location_type === 'branch') {
                        $service->branches()->attach($physicalBranch->id);
                    } elseif ($service->location_type === 'online') {
                        $service->branches()->attach($onlineBranch->id);
                    } elseif ($service->location_type === 'hybrid') {
                        $service->branches()->attach([$physicalBranch->id, $onlineBranch->id]);
                    }
                }
            }
        });
    }
}
