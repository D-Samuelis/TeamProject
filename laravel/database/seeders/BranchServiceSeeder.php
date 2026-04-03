<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Business\Branch;
use App\Models\Business\Service;
use App\Models\Business\BranchService;

class BranchServiceSeeder extends Seeder
{
    public function run(): void
    {
        foreach (Branch::with('business')->get() as $branch) {
            $services = Service::where('business_id', $branch->business_id)->get();

            foreach ($services as $service) {
                // Online branches only get online services, physical get the rest
                if ($branch->type === 'online' && $service->location_type !== 'online') {
                    continue;
                }
                if ($branch->type === 'physical' && $service->location_type === 'online') {
                    continue;
                }

                BranchService::create([
                    'branch_id'               => $branch->id,
                    'service_id'              => $service->id,
                    // Physical branches slightly override price; online uses base
                    'custom_price'            => $branch->type === 'physical' ? $service->base_price + 5 : null,
                    'custom_duration_minutes' => $service->name === 'Workshop' && $branch->type === 'physical' ? 90 : null,
                    'custom_description'      => $service->name === 'Workshop' && $branch->type === 'physical'
                                                    ? 'Intensive 90-minute workshop.' : null,
                    'location_type'           => null, // inherit from template
                    'is_enabled'              => true,
                ]);
            }
        }
    }
}
