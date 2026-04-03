<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Carbon\Carbon;
use App\Models\Auth\User;
use App\Models\Business\Branch;
use App\Models\Business\BranchService;
use App\Models\Business\Asset;
use App\Models\Business\Appointment;

class AppointmentSeeder extends Seeder
{
    public function run(): void
    {
        // Use last 4 users as customers
        $customers = User::where('is_admin', false)->skip(6)->take(4)->get();

        $customerIndex = 0;

        foreach (Branch::where('type', 'physical')->get() as $branch) {
            $instances = BranchService::where('branch_id', $branch->id)
                ->where('is_enabled', true)
                ->with('service')
                ->get();

            foreach ($instances as $instance) {
                $customer = $customers[$customerIndex % $customers->count()];
                $customerIndex++;

                // Find an asset attached to this branch service instance
                $asset = $instance->assets()->first()
                    ?? Asset::where('business_id', $branch->business_id)->first();

                if (! $asset) {
                    continue;
                }

                // Effective duration: branch override or base
                $duration = $instance->custom_duration_minutes
                    ?? $instance->service->base_duration_minutes;

                Appointment::create([
                    'user_id'           => $customer->id,
                    'branch_id'         => $branch->id,
                    'branch_service_id' => $instance->id,
                    'asset_id'          => $asset->id,
                    'status'            => fake()->randomElement(['pending', 'confirmed']),
                    'duration'          => $duration,
                    'date'              => Carbon::now()->addDays(rand(1, 14))->toDateString(),
                    'start_at'          => fake()->randomElement(['09:00:00', '10:00:00', '11:00:00', '14:00:00', '15:00:00']),
                ]);
            }
        }
    }
}
