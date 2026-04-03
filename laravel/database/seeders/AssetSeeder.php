<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Business\Business;
use App\Models\Business\Branch;
use App\Models\Business\Asset;
use App\Models\Business\BranchService;

class AssetSeeder extends Seeder
{
    public function run(): void
    {
        foreach (Business::all() as $business) {
            $physicalBranch = Branch::where('business_id', $business->id)
                ->where('type', 'physical')
                ->first();

            if (! $physicalBranch) {
                continue;
            }

            // Create assets for this business
            $room = Asset::create([
                'business_id' => $business->id,
                'name'        => "Consultation Room ({$business->name})",
                'description' => 'Private room for in-person sessions.',
            ]);

            $table = Asset::create([
                'business_id' => $business->id,
                'name'        => "Workshop Table ({$business->name})",
                'description' => 'Large table for group workshops.',
            ]);

            // Attach assets to the physical branch
            $physicalBranch->assets()->attach([$room->id, $table->id]);

            // Attach assets to the relevant branch service instances
            $consultationInstance = BranchService::where('branch_id', $physicalBranch->id)
                ->whereHas('service', fn($q) => $q->where('name', 'Consultation'))
                ->first();

            $workshopInstance = BranchService::where('branch_id', $physicalBranch->id)
                ->whereHas('service', fn($q) => $q->where('name', 'Workshop'))
                ->first();

            if ($consultationInstance) {
                $consultationInstance->assets()->attach($room->id);
            }

            if ($workshopInstance) {
                $workshopInstance->assets()->attach($table->id);
            }
        }
    }
}
