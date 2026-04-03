<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Business\Business;
use App\Models\Business\Service;

class ServiceSeeder extends Seeder
{
    public function run(): void
    {
        $templates = [
            [
                'name'                  => 'Consultation',
                'description'           => 'One-on-one consultation session.',
                'base_duration_minutes' => 60,
                'base_price'            => 50.00,
                'location_type'         => 'branch',
            ],
            [
                'name'                  => 'Online Session',
                'description'           => 'Remote video session.',
                'base_duration_minutes' => 45,
                'base_price'            => 40.00,
                'location_type'         => 'online',
            ],
            [
                'name'                  => 'Workshop',
                'description'           => 'Group workshop session.',
                'base_duration_minutes' => 120,
                'base_price'            => 80.00,
                'location_type'         => 'branch',
            ],
        ];

        foreach (Business::all() as $business) {
            foreach ($templates as $template) {
                Service::create(array_merge($template, [
                    'business_id' => $business->id,
                    'is_active'   => true,
                ]));
            }
        }
    }
}
