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
    /**
     * Fixed seed for deterministic random generation.
     * Everyone on the team will get identical appointment data.
     * If you want to regenerate with different data, change this number.
     */
    private const RANDOM_SEED = 42;

    /** Rotating list of client e-mails – deterministic round-robin index */
    private array $clientEmails = [
        'michal@klient.sk', 'katka@klient.sk', 'pavol@test.sk', 'simona@test.sk',
        'igor@auto.sk', 'beata@priklad.sk', 'martin@web.sk', 'elena@gmail.sk',
        'vlado@gmail.com', 'monika@gmail.com', 'stano@email.sk', 'gabi@email.sk',
        'jano@post.sk', 'renata@post.sk', 'ondrej@zoznam.sk', 'iveta@zoznam.sk',
        'patrik@centrum.sk', 'dagmar@centrum.sk', 'lubom@atlas.sk', 'petra.k@atlas.sk',
        'boris@email.com', 'zdenka@email.com', 'marian@icloud.com', 'sona@icloud.com',
        'tibor@outlook.com', 'alzbet@outlook.com', 'karol@yahoo.com', 'adriana@yahoo.com',
    ];

    /** Round-robin pointer for client assignment */
    private int $clientIndex = 0;

    public function run(): void
    {
        DB::transaction(function () {
            mt_srand(self::RANDOM_SEED);

            $this->seedAppointments();
        });
    }

    // -------------------------------------------------------------------------

    private function seedAppointments(): void
    {
        $clients = User::whereIn('email', $this->clientEmails)
            ->get()
            ->keyBy('email');

        $slots = $this->buildSlots();

        $serviceNames = collect($slots)->pluck('service')->unique()->values()->toArray();
        $assetNames   = collect($slots)->pluck('asset')->unique()->values()->toArray();

        $services = Service::whereIn('name', $serviceNames)->get()->keyBy('name');
        $assets   = Asset::whereIn('name', $assetNames)->get()->keyBy('name');

        $validSlots = collect($slots)->filter(
            fn ($slot) => isset($services[$slot['service']]) && isset($assets[$slot['asset']])
        )->values();

        $today = Carbon::today();
        $data  = [];

        // ------------------------------------------------------------------
        // HISTORY – 80 past appointments spread over last 60 days
        // Slot index cycles sequentially; day + status come from seeded mt_rand
        // ------------------------------------------------------------------
        $historyStatuses = ['confirmed', 'confirmed', 'confirmed', 'cancelled', 'no_show'];

        for ($i = 0; $i < 80; $i++) {
            $slot = $validSlots[$i % $validSlots->count()];

            $data[] = [
                'client'  => $clients->get($this->nextClient()),
                'service' => $services[$slot['service']],
                'asset'   => $assets[$slot['asset']],
                'status'  => $historyStatuses[mt_rand(0, 4)],
                'date'    => $today->copy()->subDays(mt_rand(1, 60))->format('Y-m-d'),
                'start'   => $this->deterministicTime($i),
            ];
        }

        // ------------------------------------------------------------------
        // TODAY – deterministic time slots per asset (no randomness)
        // ------------------------------------------------------------------
        $todayTimes = ['08:00', '09:30', '11:00', '13:00', '14:30', '16:00'];

        foreach ($validSlots as $slotIndex => $slot) {
            $timeCount = ($slotIndex % 3) + 2; // 2, 3, or 4 bookings per asset today

            for ($t = 0; $t < $timeCount; $t++) {
                $data[] = [
                    'client'  => $clients->get($this->nextClient()),
                    'service' => $services[$slot['service']],
                    'asset'   => $assets[$slot['asset']],
                    'status'  => ($slotIndex % 5 === 0) ? 'pending' : 'confirmed',
                    'date'    => $today->format('Y-m-d'),
                    'start'   => $todayTimes[($slotIndex + $t) % count($todayTimes)],
                ];
            }
        }

        // ------------------------------------------------------------------
        // FUTURE – next 30 days, ~40-50 % slot coverage per day
        // Step pattern and time assignment are fully deterministic
        // ------------------------------------------------------------------
        $futureDays  = [1, 2, 3, 4, 5, 7, 8, 9, 10, 12, 14, 15, 16, 18, 20, 21, 25, 28, 30];
        $futureTimes = ['09:00', '11:00', '14:00', '16:00'];

        foreach ($futureDays as $dayOffset) {
            $targetDate = $today->copy()->addDays($dayOffset)->format('Y-m-d');
            $step       = ($dayOffset % 2 === 0) ? 2 : 3; // even days denser, odd days sparser

            for ($slotIndex = 0; $slotIndex < $validSlots->count(); $slotIndex += $step) {
                $slot      = $validSlots[$slotIndex];
                $timeCount = ($slotIndex % 2 === 0) ? 2 : 1;

                for ($t = 0; $t < $timeCount; $t++) {
                    $data[] = [
                        'client'  => $clients->get($this->nextClient()),
                        'service' => $services[$slot['service']],
                        'asset'   => $assets[$slot['asset']],
                        'status'  => ($slotIndex % 7 === 0) ? 'pending' : 'confirmed',
                        'date'    => $targetDate,
                        'start'   => $futureTimes[($slotIndex + $t) % count($futureTimes)],
                    ];
                }
            }
        }

        // ------------------------------------------------------------------
        // Persist
        // ------------------------------------------------------------------
        foreach ($data as $item) {
            if (!$item['client'] || !$item['service'] || !$item['asset']) {
                continue;
            }

            Appointment::firstOrCreate(
                [
                    'asset_id' => $item['asset']->id,
                    'date'     => $item['date'],
                    'start_at' => $item['start'],
                ],
                [
                    'user_id'    => $item['client']->id,
                    'service_id' => $item['service']->id,
                    'status'     => $item['status'],
                    'duration'   => $item['service']->duration_minutes,
                ]
            );
        }
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    /**
     * Round-robin over the client list – no randomness, fully deterministic.
     */
    private function nextClient(): string
    {
        $email = $this->clientEmails[$this->clientIndex % count($this->clientEmails)];
        $this->clientIndex++;
        return $email;
    }

    /**
     * Maps an integer index to a fixed time slot.
     * Cycles through half-hour increments from 08:00 to 17:00.
     */
    private function deterministicTime(int $index): string
    {
        $times = [
            '08:00', '08:30', '09:00', '09:30', '10:00', '10:30',
            '11:00', '11:30', '12:00', '13:00', '13:30', '14:00',
            '14:30', '15:00', '15:30', '16:00', '16:30', '17:00',
        ];

        return $times[$index % count($times)];
    }

    /**
     * All service → asset slot combinations across all seeded businesses.
     */
    private function buildSlots(): array
    {
        return [
            // Massage Studio – Old Town
            ['service' => 'Classic Massage',               'asset' => 'Massage Table No. 1'],
            ['service' => 'Classic Massage',               'asset' => 'Massage Table No. 2'],
            ['service' => 'Classic Massage',               'asset' => 'Massage Table No. 3'],
            ['service' => 'Sports Massage',                'asset' => 'Massage Table No. 1'],
            ['service' => 'Sports Massage',                'asset' => 'Massage Table No. 3'],
            ['service' => 'Hot Stone Massage',             'asset' => 'Massage Table No. 3'],
            ['service' => 'Aromatherapy Massage',          'asset' => 'Massage Table No. 2'],
            ['service' => 'Couples Massage',               'asset' => 'Couples Suite'],
            // Massage Studio – Petrzalka
            ['service' => 'Classic Massage',               'asset' => 'Petrzalka Table No. 1'],
            ['service' => 'Classic Massage',               'asset' => 'Petrzalka Table No. 2'],
            ['service' => 'Sports Massage',                'asset' => 'Petrzalka Table No. 1'],
            ['service' => 'Reflexology',                   'asset' => 'Petrzalka Table No. 2'],
            // Massage Studio – Rača
            ['service' => 'Classic Massage',               'asset' => 'Rača Massage Table'],
            ['service' => 'Aromatherapy Massage',          'asset' => 'Rača Massage Table'],
            ['service' => 'Reflexology',                   'asset' => 'Rača Massage Table'],
            // Consulting
            ['service' => 'Business Consultation',         'asset' => 'Meeting Room'],
            ['service' => 'Business Consultation',         'asset' => 'Meeting Room B'],
            ['service' => 'Legal Consultation',            'asset' => 'Meeting Room'],
            ['service' => 'Tax Advisory',                  'asset' => 'Meeting Room B'],
            ['service' => 'Business Consultation',         'asset' => 'Aupark Conference Room'],
            ['service' => 'Tax Advisory',                  'asset' => 'Aupark Conference Room'],
            // Barbershop – Downtown
            ['service' => 'Haircut & Styling',             'asset' => 'Barber Chair #1'],
            ['service' => 'Haircut & Styling',             'asset' => 'Barber Chair #2'],
            ['service' => 'Haircut & Styling',             'asset' => 'Barber Chair #3'],
            ['service' => 'Beard Trim',                    'asset' => 'Barber Chair #1'],
            ['service' => 'Beard Trim',                    'asset' => 'Barber Chair #2'],
            ['service' => 'Haircut & Beard Combo',         'asset' => 'Barber Chair #2'],
            ['service' => 'Haircut & Beard Combo',         'asset' => 'Barber Chair #3'],
            ['service' => 'Straight Razor Shave',          'asset' => 'Barber Chair #3'],
            ['service' => 'Kids Haircut',                  'asset' => 'Barber Chair #1'],
            // Barbershop – Dúbravka
            ['service' => 'Haircut & Styling',             'asset' => 'Dúbravka Chair #1'],
            ['service' => 'Haircut & Styling',             'asset' => 'Dúbravka Chair #2'],
            ['service' => 'Beard Trim',                    'asset' => 'Dúbravka Chair #1'],
            ['service' => 'Haircut & Beard Combo',         'asset' => 'Dúbravka Chair #2'],
            // Yoga
            ['service' => 'Private Yoga Lesson',           'asset' => 'Yoga Zone Alpha'],
            ['service' => 'Private Yoga Lesson',           'asset' => 'Yoga Zone Beta'],
            ['service' => 'Guided Meditation',             'asset' => 'Yoga Zone Gamma'],
            ['service' => 'Prenatal Yoga',                 'asset' => 'Yoga Zone Beta'],
            // Fitness
            ['service' => 'Personal Training Session',     'asset' => 'Gym Floor – Station A'],
            ['service' => 'Personal Training Session',     'asset' => 'Gym Floor – Station B'],
            ['service' => 'Body Composition Analysis',     'asset' => 'Body Scan Room'],
            ['service' => 'Nutrition Consultation',        'asset' => 'Body Scan Room'],
            ['service' => 'Boxing / MMA Training',         'asset' => 'Boxing Ring'],
            ['service' => 'Personal Training Session',     'asset' => 'NM Gym Floor – A'],
            ['service' => 'Personal Training Session',     'asset' => 'NM Gym Floor – B'],
            // Dental
            ['service' => 'Dental Check-up',               'asset' => 'Dental Chair 1'],
            ['service' => 'Dental Check-up',               'asset' => 'Dental Chair 2'],
            ['service' => 'Professional Teeth Cleaning',   'asset' => 'Dental Chair 1'],
            ['service' => 'Teeth Whitening',               'asset' => 'Dental Chair 3'],
            ['service' => 'Dental Filling',                'asset' => 'Dental Chair 2'],
            ['service' => 'Tooth Extraction',              'asset' => 'Dental Chair 2'],
            ['service' => 'Dental Check-up',               'asset' => 'KV Dental Chair 1'],
            ['service' => 'Dental Check-up',               'asset' => 'KV Dental Chair 2'],
            ['service' => 'Professional Teeth Cleaning',   'asset' => 'KV Dental Chair 1'],
            ['service' => 'Dental Filling',                'asset' => 'KV Dental Chair 2'],
            // Photo Studio
            ['service' => 'Portrait Session',              'asset' => 'Studio A – White Cyclorama'],
            ['service' => 'Portrait Session',              'asset' => 'Studio B – Dark Background'],
            ['service' => 'Product Photography',           'asset' => 'Studio A – White Cyclorama'],
            ['service' => 'Product Photography',           'asset' => 'Product Light Table'],
            ['service' => 'LinkedIn / CV Headshots',       'asset' => 'Studio B – Dark Background'],
            // Auto Service
            ['service' => 'Oil & Filter Change',           'asset' => 'Lift Bay 1'],
            ['service' => 'Oil & Filter Change',           'asset' => 'Lift Bay 2'],
            ['service' => 'Full Diagnostics',              'asset' => 'Diagnostics Bay'],
            ['service' => 'Tyre Change & Balancing',       'asset' => 'Tyre Machine Station'],
            ['service' => 'Interior & Exterior Detailing', 'asset' => 'Detail / Wash Bay'],
            ['service' => 'A/C Service & Refill',          'asset' => 'Lift Bay 1'],
            // Wellness & Spa
            ['service' => 'Private Sauna Session',         'asset' => 'Finnish Sauna'],
            ['service' => 'Jacuzzi & Relax',               'asset' => 'Jacuzzi Suite'],
            ['service' => 'Luxury Facial Treatment',       'asset' => 'Treatment Room 1'],
            ['service' => 'Body Wrap & Scrub',             'asset' => 'Treatment Room 2'],
            // Nail Studio
            ['service' => 'Gel Manicure',                  'asset' => 'Nail Station 1'],
            ['service' => 'Gel Manicure',                  'asset' => 'Nail Station 2'],
            ['service' => 'Luxury Pedicure',               'asset' => 'Nail Station 1'],
            ['service' => 'Luxury Pedicure',               'asset' => 'Nail Station 2'],
            ['service' => 'Lash Extensions',               'asset' => 'Lash Bed'],
            ['service' => 'Eyebrow Lamination & Tint',     'asset' => 'Lash Bed'],
        ];
    }
}