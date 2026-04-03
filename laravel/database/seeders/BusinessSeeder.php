<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Auth\User;
use App\Models\Business\Business;
use App\Domain\Business\Enums\BusinessRoleEnum;

class BusinessSeeder extends Seeder
{
    public function run(): void
    {
        // First 4 non-admin users become owners
        $owners = User::where('is_admin', false)->take(4)->get();

        foreach ($owners as $index => $owner) {
            $business = Business::create([
                'name'         => "Business " . ($index + 1),
                'description'  => "Demo business " . ($index + 1),
                'state'        => 'approved',
                'is_published' => true,
            ]);

            $business->users()->attach($owner->id, [
                'role' => BusinessRoleEnum::OWNER->value,
            ]);
        }
    }
}
