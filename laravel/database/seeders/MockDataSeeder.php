<?php

namespace Database\Seeders;

use App\Models\Business\Asset;
use App\Models\Business\Branch;
use App\Models\Business\Business;
use App\Models\Business\Rule;
use App\Models\Business\Service;
use Illuminate\Database\Seeder;

class MockDataSeeder extends Seeder
{
    public function run(): void
    {
        $b1 = Business::create([
            'name' => 'Konn Food Bar',
            'description' => 'Restaurant with the most delicious homemade burgers, hot dogs, specialties, desserts and great coffee.',
            'state' => 'approved',
            'is_published' => 1
        ]);

        $b1s1 = Service::create([
            'business_id' => $b1->id,
            'name' => 'Food reservation',
            'description' => '',
            'duration_minutes' => 90,
            'price' => '0',
            'location_type' => 'branch',
            'is_active' => 1
        ]);

        $b1b1 = Branch::create([
            'business_id' => $b1->id,
            'name' => 'Old Town',
            'type' => 'physical',
            'address_line_1' => 'Ventúrska 5',
            'address_line_2' => '',
            'city' => 'Bratislava',
            'postal_code' => '811 01',
            'country' => 'Slovakia',
            'is_active' => 1
        ]);

        $b1b2 = Branch::create([
            'business_id' => $b1->id,
            'name' => 'Eurovea',
            'type' => 'physical',
            'address_line_1' => 'Pribinova 8',
            'address_line_2' => '',
            'city' => 'Bratislava',
            'postal_code' => '811 09',
            'country' => 'Slovakia',
            'is_active' => 1
        ]);

        $b1b1a1 = Asset::create([
            'name' => 'Table 1',
            'description' => '2 seats',
            'delete_after' => null,
        ]);

        $b1b1a2 = Asset::create([
            'name' => 'Table 2',
            'description' => '4 seats',
            'delete_after' => null,
        ]);

        $b1b1a3 = Asset::create([
            'name' => 'Table 3',
            'description' => '4 seats',
            'delete_after' => null,
        ]);

        $b1b1a4 = Asset::create([
            'name' => 'Table 4',
            'description' => '6 seats',
            'delete_after' => null,
        ]);

        $b1b1a5 = Asset::create([
            'name' => 'Table 5',
            'description' => '8 seats',
            'delete_after' => null,
        ]);

        $b1b2a1 = Asset::create([
            'name' => 'Table 1',
            'description' => '2 seats',
            'delete_after' => null,
        ]);

        $b1b2a2 = Asset::create([
            'name' => 'Table 2',
            'description' => '4 seats',
            'delete_after' => null,
        ]);

        $b1b2a3 = Asset::create([
            'name' => 'Table 3',
            'description' => '4 seats',
            'delete_after' => null,
        ]);

        $b1b1->services()->attach([$b1s1->id]);
        $b1b2->services()->attach([$b1s1->id]);

        $b1b1->assets()->attach([
            $b1b1a1->id,
            $b1b1a2->id,
            $b1b1a3->id,
            $b1b1a4->id,
            $b1b1a5->id
        ]);

        $b1s1->assets()->attach([
            $b1b1a1->id,
            $b1b1a2->id,
            $b1b1a3->id,
            $b1b1a4->id,
            $b1b1a5->id,
            $b1b2a1->id,
            $b1b2a2->id,
            $b1b2a3->id,
        ]);

        #-----------------------------------------------------------

        $b2 = Business::create([
            'name' => 'ENVA medic & beauty saloon for beautiful skin',
            'description' => 'Enva is a specialized medical-cosmetic facility providing advanced skin rejuvenation and deep nourishment treatments.',
            'state' => 'approved',
            'is_published' => 1
        ]);

        $b2s1 = Service::create([
            'business_id' => $b2->id,
            'name' => 'Skin treatment',
            'description' => '',
            'duration_minutes' => 60,
            'price' => '55',
            'location_type' => 'branch',
            'is_active' => 1
        ]);

        $b2b1 = Branch::create([
            'business_id' => $b2->id,
            'name' => 'Main Clinic',
            'type' => 'physical',
            'address_line_1' => 'Laurinská 10',
            'address_line_2' => '',
            'city' => 'Bratislava',
            'postal_code' => '811 01',
            'country' => 'Slovakia',
            'is_active' => 1
        ]);

        $b2b1a1 = Asset::create([
            'name' => 'Benjamin Netanyahu',
            'description' => 'Beautician',
            'delete_after' => null,
        ]);

        $b2b1a2 = Asset::create([
            'name' => 'Andrej Danko',
            'description' => 'Beautician',
            'delete_after' => null,
        ]);

        $b2b1->services()->attach([$b2s1->id]);

        $b2b1->assets()->attach([
            $b2b1a1->id,
            $b2b1a2->id
        ]);

        $b2s1->assets()->attach([
            $b2b1a1->id,
            $b2b1a2->id
        ]);

        #-----------------------------------------------------------

        $b3 = Business::create([
            'name' => 'Bebe Hair',
            'description' => 'Visit the Zuckermandel - hair salon and beauty salon in Bratislava and feel exceptional thanks to professional products and expert staff, which will give you the best haircut.',
            'state' => 'approved',
            'is_published' => 1
        ]);

        $b3s1 = Service::create([
            'business_id' => $b3->id,
            'name' => 'Women\'s haircut',
            'description' => '',
            'duration_minutes' => 60,
            'price' => '30',
            'location_type' => 'branch',
            'is_active' => 1
        ]);

        $b3s2 = Service::create([
            'business_id' => $b3->id,
            'name' => 'Men\'s haircut',
            'description' => '',
            'duration_minutes' => 30,
            'price' => '20',
            'location_type' => 'branch',
            'is_active' => 1
        ]);

        $b3s3 = Service::create([
            'business_id' => $b3->id,
            'name' => 'Hair color',
            'description' => '',
            'duration_minutes' => 120,
            'price' => '40',
            'location_type' => 'branch',
            'is_active' => 1
        ]);

        $b3b1 = Branch::create([
            'business_id' => $b3->id,
            'name' => 'AVION Shopping Park',
            'type' => 'physical',
            'address_line_1' => 'Ivanská cesta 16',
            'address_line_2' => '',
            'city' => 'Bratislava',
            'postal_code' => '821 04',
            'country' => 'Slovakia',
            'is_active' => 1
        ]);

        $b3b2 = Branch::create([
            'business_id' => $b3->id,
            'name' => 'BA Centrum',
            'type' => 'physical',
            'address_line_1' => 'Kolárska 473/2',
            'address_line_2' => 'Suite 305',
            'city' => 'Bratislava',
            'postal_code' => '811 06',
            'country' => 'Slovakia',
            'is_active' => 1
        ]);

        $b3b3 = Branch::create([
            'business_id' => $b3->id,
            'name' => 'Tornaľa',
            'type' => 'physical',
            'address_line_1' => 'Mierová 1205',
            'address_line_2' => '',
            'city' => 'Tornaľa',
            'postal_code' => '982 01',
            'country' => 'Slovakia',
            'is_active' => 1
        ]);

        $b3b1a1 = Asset::create([
            'name' => 'Chair 1',
            'description' => 'Chair 1',
            'delete_after' => null,
        ]);

        $b3b1a2 = Asset::create([
            'name' => 'Chair 2',
            'description' => 'Chair 2',
            'delete_after' => null,
        ]);

        $b3b1a3 = Asset::create([
            'name' => 'Chair 3',
            'description' => 'Chair 3',
            'delete_after' => null,
        ]);

        $b3b2a1 = Asset::create([
            'name' => 'Chair 1',
            'description' => 'Chair 1',
            'delete_after' => null,
        ]);

        $b3b2a2 = Asset::create([
            'name' => 'Chair 2',
            'description' => 'Chair 2',
            'delete_after' => null,
        ]);

        $b3b2a3 = Asset::create([
            'name' => 'Chair 3',
            'description' => 'Chair 3',
            'delete_after' => null,
        ]);

        $b3b2a4 = Asset::create([
            'name' => 'Chair 4',
            'description' => 'Chair 4',
            'delete_after' => null,
        ]);

        $b3b2a5 = Asset::create([
            'name' => 'Chair 5',
            'description' => 'Chair 5',
            'delete_after' => null,
        ]);

        $b3b3a1 = Asset::create([
            'name' => 'Chair 1',
            'description' => 'Chair 1',
            'delete_after' => null,
        ]);

        $b3b3a2 = Asset::create([
            'name' => 'Chair 2',
            'description' => 'Chair 2',
            'delete_after' => null,
        ]);

        $b3b1->services()->attach([$b3s2->id, $b3s3->id]);
        $b3b2->services()->attach([$b3s1->id, $b3s2->id, $b3s3->id]);
        $b3b3->services()->attach([$b3s1->id, $b3s2->id]);

        $b3b1->assets()->attach([$b3b1a1->id, $b3b1a2->id, $b3b1a3->id]);
        $b3b2->assets()->attach([$b3b2a1->id, $b3b2a2->id, $b3b2a3->id, $b3b2a4->id, $b3b2a5->id]);
        $b3b3->assets()->attach([$b3b3a1->id, $b3b3a2->id]);

        $b3s2->assets()->attach([$b3b1a1->id, $b3b1a2->id, $b3b1a3->id]);
        $b3s3->assets()->attach([$b3b1a1->id, $b3b1a2->id, $b3b1a3->id]);

        $b3s1->assets()->attach([$b3b2a1->id, $b3b2a2->id, $b3b2a3->id, $b3b2a4->id, $b3b2a5->id]);
        $b3s2->assets()->attach([$b3b2a1->id, $b3b2a2->id, $b3b2a3->id, $b3b2a4->id, $b3b2a5->id]);
        $b3s3->assets()->attach([$b3b2a1->id, $b3b2a2->id, $b3b2a3->id, $b3b2a4->id, $b3b2a5->id]);

        $b3s1->assets()->attach([$b3b3a1->id, $b3b3a2->id]);
        $b3s2->assets()->attach([$b3b3a1->id, $b3b3a2->id]);

        #-----------------------------------------------------------

        $b4 = Business::create([
            'name' => 'Swiss Life',
            'description' => 'The Swiss Life Group is a leading provider of life and pensions and financial solutions in Europe. We enable people to lead a self-determined life.',
            'state' => 'approved',
            'is_published' => 1
        ]);

        $b4s1 = Service::create([
            'business_id' => $b4->id,
            'name' => 'Financial advisory for individuals',
            'description' => 'We provide expert guidance to help individuals manage, grow, and protect their finances effectively.',
            'duration_minutes' => 60,
            'price' => '30',
            'location_type' => 'online',
            'is_active' => 1
        ]);

        $b4s2 = Service::create([
            'business_id' => $b4->id,
            'name' => 'Financial advisory for businesses',
            'description' => 'We provide expert guidance to help businesses manage, grow, and protect their finances effectively.',
            'duration_minutes' => 60,
            'price' => '100',
            'location_type' => 'online',
            'is_active' => 1
        ]);

        $b4a1 = Asset::create([
            'name' => 'Andrew Tate',
            'description' => 'Finance expert',
            'delete_after' => null,
        ]);

        $b4s1->assets()->attach([$b4a1->id]);
        $b4s2->assets()->attach([$b4a1->id]);

        #-----------------------------------------------------------

        $b5 = Business::create([
            'name' => 'Shicker Technik',
            'description' => 'Reliable plumbing and installation services for homes and businesses. From leaks to full bathroom setups, we deliver fast, professional, and high-quality solutions.',
            'state' => 'approved',
            'is_published' => 1
        ]);


        $b5s1 = Service::create([
            'business_id' => $b5->id,
            'name' => 'Plumbing',
            'description' => 'The system of pipes, fixtures, and fittings that delivers water, removes waste, and keeps buildings running smoothly.',
            'duration_minutes' => 120,
            'price' => '15',
            'location_type' => 'home',
            'is_active' => 1
        ]);

        $b5s2 = Service::create([
            'business_id' => $b5->id,
            'name' => 'Installation',
            'description' => 'The professional setup of equipment, systems, or fixtures to ensure they work safely and efficiently.',
            'duration_minutes' => 240,
            'price' => '45',
            'location_type' => 'home',
            'is_active' => 1
        ]);

        $b5a1 = Asset::create([
            'name' => 'John Pork',
            'description' => 'Plumber',
            'delete_after' => null,
        ]);

        $b5a2 = Asset::create([
            'name' => 'Ballerina Cappuccina',
            'description' => 'Plumber',
            'delete_after' => null,
        ]);

        $b5a3 = Asset::create([
            'name' => 'Tung Sahur',
            'description' => 'Installer',
            'delete_after' => null,
        ]);

        $b5a4 = Asset::create([
            'name' => 'Galileo Galilei',
            'description' => 'Installer',
            'delete_after' => null,
        ]);

        $b5s1->assets()->attach([$b5a1->id, $b5a2->id]);
        $b5s2->assets()->attach([$b5a3->id, $b5a4->id]);

        $standardWeekdayRanges  = [['from_time' => '11:00', 'to_time' => '22:00']];
        $standardWeekendRanges  = [['from_time' => '10:00', 'to_time' => '23:00']];

        $b1WeekdayRule = [
            'days' => [
                0 => $standardWeekdayRanges,
                1 => $standardWeekdayRanges,
                2 => $standardWeekdayRanges,
                3 => $standardWeekdayRanges,
                4 => $standardWeekdayRanges,
                5 => $standardWeekendRanges,
                6 => $standardWeekendRanges,
            ]
        ];

        foreach ([$b1b1a1, $b1b1a2, $b1b1a3, $b1b1a4, $b1b1a5] as $i => $asset) {
            Rule::create([
                'asset_id'    => $asset->id,
                'title'       => 'Standard Opening Hours',
                'description' => 'Regular weekly schedule for Konn Food Bar – Old Town',
                'valid_from'  => null,
                'valid_to'    => null,
                'priority'    => 1,
                'rule_set'    => json_encode($b1WeekdayRule),
            ]);
        }

        $b1SummerRule = [
            'days' => [
                0 => [['from_time' => '10:00', 'to_time' => '23:00']],
                1 => [['from_time' => '10:00', 'to_time' => '23:00']],
                2 => [['from_time' => '10:00', 'to_time' => '23:00']],
                3 => [['from_time' => '10:00', 'to_time' => '23:00']],
                4 => [['from_time' => '10:00', 'to_time' => '23:00']],
                5 => [['from_time' => '09:00', 'to_time' => '00:00']],
                6 => [['from_time' => '09:00', 'to_time' => '00:00']],
            ]
        ];

        foreach ([$b1b2a1, $b1b2a2, $b1b2a3] as $asset) {
            Rule::create([
                'asset_id'    => $asset->id,
                'title'       => 'Summer Extended Hours',
                'description' => 'Extended opening hours during summer season',
                'valid_from'  => '2025-06-01',
                'valid_to'    => '2025-08-31',
                'priority'    => 1,
                'rule_set'    => json_encode($b1SummerRule),
            ]);

            Rule::create([
                'asset_id'    => $asset->id,
                'title'       => 'Standard Opening Hours',
                'description' => 'Regular weekly schedule for Konn Food Bar – Eurovea',
                'valid_from'  => null,
                'valid_to'    => null,
                'priority'    => 2,
                'rule_set'    => json_encode($b1WeekdayRule),
            ]);
        }

        $envaWeekdayRanges = [
            ['from_time' => '09:00', 'to_time' => '12:00'],
            ['from_time' => '13:00', 'to_time' => '18:00'],
        ];

        $envaRule = [
            'days' => [
                0 => $envaWeekdayRanges,
                1 => $envaWeekdayRanges,
                2 => $envaWeekdayRanges,
                3 => $envaWeekdayRanges,
                4 => $envaWeekdayRanges,
                5 => [['from_time' => '09:00', 'to_time' => '13:00']],
            ]
        ];

        foreach ([$b2b1a1, $b2b1a2] as $asset) {
            Rule::create([
                'asset_id'    => $asset->id,
                'title'       => 'Clinic Hours',
                'description' => 'Standard clinic schedule with lunch break, closed Sundays',
                'valid_from'  => null,
                'valid_to'    => null,
                'priority'    => 1,
                'rule_set'    => json_encode($envaRule),
            ]);
        }

        $avionRule = [
            'days' => [
                0 => [['from_time' => '09:00', 'to_time' => '21:00']],
                1 => [['from_time' => '09:00', 'to_time' => '21:00']],
                2 => [['from_time' => '09:00', 'to_time' => '21:00']],
                3 => [['from_time' => '09:00', 'to_time' => '21:00']],
                4 => [['from_time' => '09:00', 'to_time' => '21:00']],
                5 => [['from_time' => '09:00', 'to_time' => '21:00']],
                6 => [['from_time' => '10:00', 'to_time' => '20:00']],
            ]
        ];

        foreach ([$b3b1a1, $b3b1a2, $b3b1a3] as $asset) {
            Rule::create([
                'asset_id'    => $asset->id,
                'title'       => 'Mall Hours',
                'description' => 'AVION Shopping Park opening hours',
                'valid_from'  => null,
                'valid_to'    => null,
                'priority'    => 1,
                'rule_set'    => json_encode($avionRule),
            ]);
        }

        $baCentrumRule = [
            'days' => [
                0 => [['from_time' => '08:00', 'to_time' => '19:00']],
                1 => [['from_time' => '08:00', 'to_time' => '19:00']],
                2 => [['from_time' => '08:00', 'to_time' => '19:00']],
                3 => [['from_time' => '08:00', 'to_time' => '19:00']],
                4 => [['from_time' => '08:00', 'to_time' => '19:00']],
                5 => [['from_time' => '09:00', 'to_time' => '16:00']],
                // Sunday closed
            ]
        ];

        foreach ([$b3b2a1, $b3b2a2, $b3b2a3, $b3b2a4, $b3b2a5] as $asset) {
            Rule::create([
                'asset_id'    => $asset->id,
                'title'       => 'Standard Hours',
                'description' => 'BA Centrum standard weekly hours',
                'valid_from'  => null,
                'valid_to'    => null,
                'priority'    => 1,
                'rule_set'    => json_encode($baCentrumRule),
            ]);
        }

        $tornalaRule = [
            'days' => [
                0 => [['from_time' => '09:00', 'to_time' => '17:00']],
                1 => [['from_time' => '09:00', 'to_time' => '17:00']],
                2 => [['from_time' => '09:00', 'to_time' => '17:00']],
                3 => [['from_time' => '09:00', 'to_time' => '17:00']],
                4 => [['from_time' => '09:00', 'to_time' => '17:00']],
                5 => [['from_time' => '09:00', 'to_time' => '12:00']],
                // Sunday closed
            ]
        ];

        foreach ([$b3b3a1, $b3b3a2] as $asset) {
            Rule::create([
                'asset_id'    => $asset->id,
                'title'       => 'Standard Hours',
                'description' => 'Tornaľa branch standard weekly hours',
                'valid_from'  => null,
                'valid_to'    => null,
                'priority'    => 1,
                'rule_set'    => json_encode($tornalaRule),
            ]);
        }

        $advisorStandardRule = [
            'days' => [
                0 => [['from_time' => '08:00', 'to_time' => '17:00']],
                1 => [['from_time' => '08:00', 'to_time' => '17:00']],
                2 => [['from_time' => '08:00', 'to_time' => '17:00']],
                3 => [['from_time' => '08:00', 'to_time' => '17:00']],
                4 => [['from_time' => '08:00', 'to_time' => '16:00']],
            ]
        ];

        $advisorPremiumWindowRule = [
            'days' => [
                0 => [['from_time' => '07:00', 'to_time' => '18:00']],
                1 => [['from_time' => '07:00', 'to_time' => '18:00']],
                2 => [['from_time' => '07:00', 'to_time' => '18:00']],
                3 => [['from_time' => '07:00', 'to_time' => '18:00']],
                4 => [['from_time' => '07:00', 'to_time' => '18:00']],
            ]
        ];

        Rule::create([
            'asset_id'    => $b4a1->id,
            'title'       => 'Q2 Extended Availability',
            'description' => 'Extended advisory hours during Q2 peak season',
            'valid_from'  => '2025-04-01',
            'valid_to'    => '2025-06-30',
            'priority'    => 1,
            'rule_set'    => json_encode($advisorPremiumWindowRule),
        ]);

        Rule::create([
            'asset_id'    => $b4a1->id,
            'title'       => 'Standard Advisory Hours',
            'description' => 'Regular availability, early Friday finish',
            'valid_from'  => null,
            'valid_to'    => null,
            'priority'    => 2,
            'rule_set'    => json_encode($advisorStandardRule),
        ]);

        $plumberRule = [
            'days' => [
                0 => [['from_time' => '07:00', 'to_time' => '18:00']],
                1 => [['from_time' => '07:00', 'to_time' => '18:00']],
                2 => [['from_time' => '07:00', 'to_time' => '18:00']],
                3 => [['from_time' => '07:00', 'to_time' => '18:00']],
                4 => [['from_time' => '07:00', 'to_time' => '18:00']],
                5 => [['from_time' => '07:00', 'to_time' => '15:00']],
                6 => [['from_time' => '08:00', 'to_time' => '13:00']],
            ]
        ];

        foreach ([$b5a1, $b5a2] as $asset) {
            Rule::create([
                'asset_id'    => $asset->id,
                'title'       => 'Plumber Schedule',
                'description' => 'Mon–Sat full day, Sunday emergency morning window',
                'valid_from'  => null,
                'valid_to'    => null,
                'priority'    => 1,
                'rule_set'    => json_encode($plumberRule),
            ]);
        }

        $installerRule = [
            'days' => [
                0 => [['from_time' => '08:00', 'to_time' => '17:00']],
                1 => [['from_time' => '08:00', 'to_time' => '17:00']],
                2 => [['from_time' => '08:00', 'to_time' => '17:00']],
                3 => [['from_time' => '08:00', 'to_time' => '17:00']],
                4 => [['from_time' => '08:00', 'to_time' => '17:00']],
            ]
        ];

        foreach ([$b5a3, $b5a4] as $asset) {
            Rule::create([
                'asset_id'    => $asset->id,
                'title'       => 'Installer Schedule',
                'description' => 'Weekdays only — installation jobs require full-day access',
                'valid_from'  => null,
                'valid_to'    => null,
                'priority'    => 1,
                'rule_set'    => json_encode($installerRule),
            ]);
        }
    }
}
