<?php

namespace Database\Seeders;

use App\Models\Auth\User;
use App\Models\Business\Asset;
use App\Models\Business\Service;
use App\Models\Business\Appointment;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class AppointmentSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            $this->seedAppointments();
        });
    }

    private function seedAppointments(): void
    {
        $clients = User::whereIn('email', [
            'michal@klient.sk', 'katka@klient.sk', 'pavol@test.sk', 'simona@test.sk',
            'igor@auto.sk', 'beata@priklad.sk', 'martin@web.sk', 'elena@gmail.sk',
            'vlado@gmail.com', 'monika@gmail.com', 'stano@email.sk', 'gabi@email.sk',
            'jano@post.sk', 'renata@post.sk', 'ondrej@zoznam.sk', 'iveta@zoznam.sk',
            'patrik@centrum.sk', 'dagmar@centrum.sk', 'lubom@atlas.sk', 'petra.k@atlas.sk',
            'boris@email.com', 'zdenka@email.com', 'marian@icloud.com', 'sona@icloud.com',
            'tibor@outlook.com', 'alzbet@outlook.com', 'karol@yahoo.com', 'adriana@yahoo.com',
        ])->get()->keyBy('email');

        $allClients = $clients->values();

        // ------------------------------------------------------------------
        // Build asset + service pairs grouped by business
        // ------------------------------------------------------------------

        $slots = [
            // Massage Studio – Old Town
            ['service' => 'Classic Massage',          'asset' => 'Massage Table No. 1'],
            ['service' => 'Classic Massage',          'asset' => 'Massage Table No. 2'],
            ['service' => 'Classic Massage',          'asset' => 'Massage Table No. 3'],
            ['service' => 'Sports Massage',           'asset' => 'Massage Table No. 1'],
            ['service' => 'Sports Massage',           'asset' => 'Massage Table No. 3'],
            ['service' => 'Hot Stone Massage',        'asset' => 'Massage Table No. 3'],
            ['service' => 'Aromatherapy Massage',     'asset' => 'Massage Table No. 2'],
            ['service' => 'Couples Massage',          'asset' => 'Couples Suite'],
            // Massage Studio – Petrzalka
            ['service' => 'Classic Massage',          'asset' => 'Petrzalka Table No. 1'],
            ['service' => 'Classic Massage',          'asset' => 'Petrzalka Table No. 2'],
            ['service' => 'Sports Massage',           'asset' => 'Petrzalka Table No. 1'],
            ['service' => 'Reflexology',              'asset' => 'Petrzalka Table No. 2'],
            // Massage Studio – Rača
            ['service' => 'Classic Massage',          'asset' => 'Rača Massage Table'],
            ['service' => 'Aromatherapy Massage',     'asset' => 'Rača Massage Table'],
            ['service' => 'Reflexology',              'asset' => 'Rača Massage Table'],
            // Consulting Firm
            ['service' => 'Business Consultation',    'asset' => 'Meeting Room'],
            ['service' => 'Business Consultation',    'asset' => 'Meeting Room B'],
            ['service' => 'Legal Consultation',       'asset' => 'Meeting Room'],
            ['service' => 'Tax Advisory',             'asset' => 'Meeting Room B'],
            ['service' => 'Business Consultation',    'asset' => 'Aupark Conference Room'],
            ['service' => 'Tax Advisory',             'asset' => 'Aupark Conference Room'],
            // Barbershop – Downtown
            ['service' => 'Haircut & Styling',        'asset' => 'Barber Chair #1'],
            ['service' => 'Haircut & Styling',        'asset' => 'Barber Chair #2'],
            ['service' => 'Haircut & Styling',        'asset' => 'Barber Chair #3'],
            ['service' => 'Beard Trim',               'asset' => 'Barber Chair #1'],
            ['service' => 'Beard Trim',               'asset' => 'Barber Chair #2'],
            ['service' => 'Haircut & Beard Combo',    'asset' => 'Barber Chair #2'],
            ['service' => 'Haircut & Beard Combo',    'asset' => 'Barber Chair #3'],
            ['service' => 'Straight Razor Shave',     'asset' => 'Barber Chair #3'],
            ['service' => 'Kids Haircut',             'asset' => 'Barber Chair #1'],
            // Barbershop – Dúbravka
            ['service' => 'Haircut & Styling',        'asset' => 'Dúbravka Chair #1'],
            ['service' => 'Haircut & Styling',        'asset' => 'Dúbravka Chair #2'],
            ['service' => 'Beard Trim',               'asset' => 'Dúbravka Chair #1'],
            ['service' => 'Haircut & Beard Combo',    'asset' => 'Dúbravka Chair #2'],
            // Yoga Studio
            ['service' => 'Private Yoga Lesson',      'asset' => 'Yoga Zone Alpha'],
            ['service' => 'Private Yoga Lesson',      'asset' => 'Yoga Zone Beta'],
            ['service' => 'Guided Meditation',        'asset' => 'Yoga Zone Gamma'],
            ['service' => 'Prenatal Yoga',            'asset' => 'Yoga Zone Beta'],
            // Fitness Center
            ['service' => 'Personal Training Session','asset' => 'Gym Floor – Station A'],
            ['service' => 'Personal Training Session','asset' => 'Gym Floor – Station B'],
            ['service' => 'Body Composition Analysis','asset' => 'Body Scan Room'],
            ['service' => 'Nutrition Consultation',   'asset' => 'Body Scan Room'],
            ['service' => 'Boxing / MMA Training',    'asset' => 'Boxing Ring'],
            ['service' => 'Personal Training Session','asset' => 'NM Gym Floor – A'],
            ['service' => 'Personal Training Session','asset' => 'NM Gym Floor – B'],
            // Dental
            ['service' => 'Dental Check-up',          'asset' => 'Dental Chair 1'],
            ['service' => 'Dental Check-up',          'asset' => 'Dental Chair 2'],
            ['service' => 'Professional Teeth Cleaning','asset' => 'Dental Chair 1'],
            ['service' => 'Teeth Whitening',          'asset' => 'Dental Chair 3'],
            ['service' => 'Dental Filling',           'asset' => 'Dental Chair 2'],
            ['service' => 'Tooth Extraction',         'asset' => 'Dental Chair 2'],
            ['service' => 'Dental Check-up',          'asset' => 'KV Dental Chair 1'],
            ['service' => 'Dental Check-up',          'asset' => 'KV Dental Chair 2'],
            ['service' => 'Professional Teeth Cleaning','asset' => 'KV Dental Chair 1'],
            ['service' => 'Dental Filling',           'asset' => 'KV Dental Chair 2'],
            // Photo Studio
            ['service' => 'Portrait Session',         'asset' => 'Studio A – White Cyclorama'],
            ['service' => 'Portrait Session',         'asset' => 'Studio B – Dark Background'],
            ['service' => 'Product Photography',      'asset' => 'Studio A – White Cyclorama'],
            ['service' => 'Product Photography',      'asset' => 'Product Light Table'],
            ['service' => 'LinkedIn / CV Headshots',  'asset' => 'Studio B – Dark Background'],
            // Auto Service
            ['service' => 'Oil & Filter Change',      'asset' => 'Lift Bay 1'],
            ['service' => 'Oil & Filter Change',      'asset' => 'Lift Bay 2'],
            ['service' => 'Full Diagnostics',         'asset' => 'Diagnostics Bay'],
            ['service' => 'Tyre Change & Balancing',  'asset' => 'Tyre Machine Station'],
            ['service' => 'Interior & Exterior Detailing', 'asset' => 'Detail / Wash Bay'],
            ['service' => 'A/C Service & Refill',     'asset' => 'Lift Bay 1'],
            // Wellness & Spa
            ['service' => 'Private Sauna Session',    'asset' => 'Finnish Sauna'],
            ['service' => 'Jacuzzi & Relax',          'asset' => 'Jacuzzi Suite'],
            ['service' => 'Luxury Facial Treatment',  'asset' => 'Treatment Room 1'],
            ['service' => 'Body Wrap & Scrub',        'asset' => 'Treatment Room 2'],
            // Nail Studio
            ['service' => 'Gel Manicure',             'asset' => 'Nail Station 1'],
            ['service' => 'Gel Manicure',             'asset' => 'Nail Station 2'],
            ['service' => 'Luxury Pedicure',          'asset' => 'Nail Station 1'],
            ['service' => 'Luxury Pedicure',          'asset' => 'Nail Station 2'],
            ['service' => 'Lash Extensions',          'asset' => 'Lash Bed'],
            ['service' => 'Eyebrow Lamination & Tint','asset' => 'Lash Bed'],
        ];

        // Pre-load all referenced services and assets once
        $serviceNames = collect($slots)->pluck('service')->unique()->values()->toArray();
        $assetNames   = collect($slots)->pluck('asset')->unique()->values()->toArray();

        $services = Service::whereIn('name', $serviceNames)->get()->keyBy('name');
        $assets   = Asset::whereIn('name', $assetNames)->get()->keyBy('name');

        // Filter out any slots where the service or asset wasn't found
        $validSlots = collect($slots)->filter(function ($slot) use ($services, $assets) {
            return isset($services[$slot['service']]) && isset($assets[$slot['asset']]);
        })->values();

        $today = Carbon::today();
        $data  = [];

        // ------------------------------------------------------------------
        // HISTORY – 80 random past appointments spread over last 60 days
        // ------------------------------------------------------------------
        for ($i = 0; $i < 80; $i++) {
            $slot = $validSlots->random();
            $data[] = [
                'user'    => $allClients->random(),
                'service' => $services[$slot['service']],
                'asset'   => $assets[$slot['asset']],
                'status'  => collect(['confirmed', 'confirmed', 'confirmed', 'cancelled', 'no_show'])->random(),
                'date'    => $today->copy()->subDays(rand(1, 60))->format('Y-m-d'),
                'start'   => $this->randomTime(),
            ];
        }

        // ------------------------------------------------------------------
        // TODAY – dense booking for each slot
        // ------------------------------------------------------------------
        foreach ($validSlots as $slot) {
            foreach (['08:00', '09:30', '11:00', '13:00', '14:30', '16:00'] as $time) {
                if (rand(0, 10) > 3) {
                    $data[] = [
                        'user'    => $allClients->random(),
                        'service' => $services[$slot['service']],
                        'asset'   => $assets[$slot['asset']],
                        'status'  => rand(0, 10) > 2 ? 'confirmed' : 'pending',
                        'date'    => $today->format('Y-m-d'),
                        'start'   => $time,
                    ];
                }
            }
        }

        // ------------------------------------------------------------------
        // FUTURE – next 30 days, spread across all slots
        // ------------------------------------------------------------------
        $futureDays = [1, 2, 3, 4, 5, 7, 8, 9, 10, 12, 14, 15, 16, 18, 20, 21, 25, 28, 30];
        foreach ($futureDays as $dayOffset) {
            $targetDate = $today->copy()->addDays($dayOffset)->format('Y-m-d');
            // Pick ~40% of slots for each future day
            foreach ($validSlots->shuffle()->take((int) ceil($validSlots->count() * 0.4)) as $slot) {
                foreach (['09:00', '11:00', '14:00', '16:00'] as $time) {
                    if (rand(0, 10) > 4) {
                        $data[] = [
                            'user'    => $allClients->random(),
                            'service' => $services[$slot['service']],
                            'asset'   => $assets[$slot['asset']],
                            'status'  => rand(0, 10) > 2 ? 'confirmed' : 'pending',
                            'date'    => $targetDate,
                            'start'   => $time,
                        ];
                    }
                }
            }
        }

        // ------------------------------------------------------------------
        // Persist
        // ------------------------------------------------------------------
        foreach ($data as $item) {
            if (!$item['service'] || !$item['asset']) {
                continue;
            }

            Appointment::firstOrCreate(
                [
                    'asset_id' => $item['asset']->id,
                    'date'     => $item['date'],
                    'start_at' => $item['start'],
                ],
                [
                    'user_id'    => $item['user']->id,
                    'service_id' => $item['service']->id,
                    'status'     => $item['status'],
                    'duration'   => $item['service']->duration_minutes,
                ]
            );
        }
    }

    private function randomTime(): string
    {
        $hours   = rand(8, 17);
        $minutes = collect(['00', '30'])->random();

        return sprintf('%02d:%s', $hours, $minutes);
    }
}