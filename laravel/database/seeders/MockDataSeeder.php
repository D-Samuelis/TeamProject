<?php

namespace Database\Seeders;

use App\Models\Business\Asset;
use App\Models\Business\Branch;
use App\Models\Business\Business;
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
    }
}
