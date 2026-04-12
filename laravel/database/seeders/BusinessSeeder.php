<?php

namespace Database\Seeders;

use App\Models\Auth\User;
use App\Models\Business\Asset;
use App\Models\Business\Branch;
use App\Models\Business\Business;
use App\Models\Business\Rule;
use App\Models\Business\Service;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Domain\Business\Enums\BusinessStateEnum;

/**
 * Seeds two realistic businesses, each with:
 * - 1 physical branch + 1 online branch
 * - 2-3 services attached to the correct branch typeConsultation
 * - 2 assets per physical branch, each with availability rules
 * - owner + manager + staff assigned via model_has_users
 *
 * Depends on: UserSeeder (users must exist)
 */
class BusinessSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            $this->seedMassageStudio();
            $this->seedConsultingFirm();
            $this->seedBarberShop();
            $this->seedYogaStudio();
            $this->seedFitnessCenter();
            $this->seedDentalClinic();
            $this->seedPhotoStudio();
            $this->seedAutoService();
            $this->seedWellnessCenter();
            $this->seedNailStudio();
        });
    }
 
    // -------------------------------------------------------------------------
 
    private function seedMassageStudio(): void
    {
        $owner   = User::where('email', 'jana@example.com')->firstOrFail();
        $manager = User::where('email', 'peter@example.com')->firstOrFail();
        $staff1  = User::where('email', 'maria@example.com')->firstOrFail();
        $staff2  = User::where('email', 'mirka@relax.sk')->firstOrFail();
        $staff3  = User::where('email', 'filip@relax.sk')->firstOrFail();
 
        $business = Business::firstOrCreate(
            ['name' => 'Relax Studio Bratislava'],
            [
                'description'  => 'Professional massages and relaxation procedures in the center of Bratislava.',
                'state'        => BusinessStateEnum::APPROVED->value,
                'is_published' => true,
            ]
        );
 
        $this->attachUser($business, $owner->id, 'owner');
 
        // Physical branch – Old Town
        $physical = Branch::firstOrCreate(
            ['business_id' => $business->id, 'name' => 'Main Branch – Old Town'],
            [
                'type'           => 'physical',
                'address_line_1' => 'Obchodna 42',
                'city'           => 'Bratislava',
                'postal_code'    => '811 06',
                'country'        => 'Slovakia',
                'latitude'       => 48.1462,
                'longitude'      => 17.1067,
                'is_active'      => true,
            ]
        );
 
        // Physical branch – Petrzalka
        $physical2 = Branch::firstOrCreate(
            ['business_id' => $business->id, 'name' => 'Branch – Petrzalka'],
            [
                'type'           => 'physical',
                'address_line_1' => 'Panónska cesta 12',
                'city'           => 'Bratislava',
                'postal_code'    => '851 04',
                'country'        => 'Slovakia',
                'latitude'       => 48.1108,
                'longitude'      => 17.1021,
                'is_active'      => true,
            ]
        );
 
        // Physical branch – Rača
        $physical3 = Branch::firstOrCreate(
            ['business_id' => $business->id, 'name' => 'Branch – Rača'],
            [
                'type'           => 'physical',
                'address_line_1' => 'Alstrova 3',
                'city'           => 'Bratislava',
                'postal_code'    => '831 06',
                'country'        => 'Slovakia',
                'latitude'       => 48.2032,
                'longitude'      => 17.1341,
                'is_active'      => true,
            ]
        );
 
        // Online branch
        $online = Branch::firstOrCreate(
            ['business_id' => $business->id, 'name' => 'Online Consultation'],
            [
                'type'      => 'online',
                'is_active' => true,
            ]
        );
 
        $this->attachUser($physical, $manager->id, 'manager');
        $this->attachUser($physical2, $staff2->id, 'manager');
 
        // Services
        $classic = Service::firstOrCreate(
            ['business_id' => $business->id, 'name' => 'Classic Massage'],
            [
                'description'      => 'Full body relaxation massage, 60 minutes.',
                'duration_minutes' => 60,
                'price'            => 35.00,
                'location_type'    => 'branch',
                'is_active'        => true,
            ]
        );
 
        $sport = Service::firstOrCreate(
            ['business_id' => $business->id, 'name' => 'Sports Massage'],
            [
                'description'      => 'Regenerative massage for athletes, 90 minutes.',
                'duration_minutes' => 90,
                'price'            => 50.00,
                'location_type'    => 'branch',
                'is_active'        => true,
            ]
        );
 
        $hot = Service::firstOrCreate(
            ['business_id' => $business->id, 'name' => 'Hot Stone Massage'],
            [
                'description'      => 'Deep relaxation with heated volcanic stones, 75 minutes.',
                'duration_minutes' => 75,
                'price'            => 55.00,
                'location_type'    => 'branch',
                'is_active'        => true,
            ]
        );
 
        $aroma = Service::firstOrCreate(
            ['business_id' => $business->id, 'name' => 'Aromatherapy Massage'],
            [
                'description'      => 'Relaxing massage with essential oils, 60 minutes.',
                'duration_minutes' => 60,
                'price'            => 45.00,
                'location_type'    => 'branch',
                'is_active'        => true,
            ]
        );
 
        $couples = Service::firstOrCreate(
            ['business_id' => $business->id, 'name' => 'Couples Massage'],
            [
                'description'      => 'Side-by-side massage for two, 60 minutes.',
                'duration_minutes' => 60,
                'price'            => 80.00,
                'location_type'    => 'branch',
                'is_active'        => true,
            ]
        );
 
        $reflexo = Service::firstOrCreate(
            ['business_id' => $business->id, 'name' => 'Reflexology'],
            [
                'description'      => 'Foot and hand reflexology therapy, 45 minutes.',
                'duration_minutes' => 45,
                'price'            => 30.00,
                'location_type'    => 'branch',
                'is_active'        => true,
            ]
        );
 
        $onlineConsultation = Service::firstOrCreate(
            ['business_id' => $business->id, 'name' => 'Online Wellness Coaching'],
            [
                'description'      => 'Individual body care plan via video call.',
                'duration_minutes' => 45,
                'price'            => 25.00,
                'location_type'    => 'online',
                'is_active'        => true,
            ]
        );
 
        // Attach services to branches
        $this->syncPivot($classic->branches(), $physical->id);
        $this->syncPivot($classic->branches(), $physical2->id);
        $this->syncPivot($classic->branches(), $physical3->id);
        $this->syncPivot($sport->branches(), $physical->id);
        $this->syncPivot($sport->branches(), $physical2->id);
        $this->syncPivot($hot->branches(), $physical->id);
        $this->syncPivot($aroma->branches(), $physical->id);
        $this->syncPivot($aroma->branches(), $physical3->id);
        $this->syncPivot($couples->branches(), $physical->id);
        $this->syncPivot($reflexo->branches(), $physical2->id);
        $this->syncPivot($reflexo->branches(), $physical3->id);
        $this->syncPivot($onlineConsultation->branches(), $online->id);
 
        // Staff
        $this->attachUser($classic, $staff1->id, 'staff');
        $this->attachUser($classic, $staff2->id, 'staff');
        $this->attachUser($classic, $staff3->id, 'staff');
        $this->attachUser($sport, $staff1->id, 'staff');
        $this->attachUser($sport, $staff2->id, 'staff');
        $this->attachUser($hot, $staff1->id, 'staff');
        $this->attachUser($aroma, $staff3->id, 'staff');
        $this->attachUser($couples, $staff1->id, 'staff');
        $this->attachUser($reflexo, $staff2->id, 'staff');
        $this->attachUser($onlineConsultation, $owner->id, 'staff');
 
        // Assets – Old Town
        $table1 = Asset::firstOrCreate(
            ['name' => 'Massage Table No. 1'],
            ['description' => 'Electrically adjustable table, Room A.', 'branch_id' => $physical->id, 'is_active' => true]
        );
        $table2 = Asset::firstOrCreate(
            ['name' => 'Massage Table No. 2'],
            ['description' => 'Standard table, Room B.', 'branch_id' => $physical->id, 'is_active' => true]
        );
        $table3 = Asset::firstOrCreate(
            ['name' => 'Massage Table No. 3'],
            ['description' => 'Premium heated table, VIP Room.', 'branch_id' => $physical->id, 'is_active' => true]
        );
        $table4 = Asset::firstOrCreate(
            ['name' => 'Couples Suite'],
            ['description' => 'Double-table suite for couples massages.', 'branch_id' => $physical->id, 'is_active' => true]
        );
 
        // Assets – Petrzalka
        $table5 = Asset::firstOrCreate(
            ['name' => 'Petrzalka Table No. 1'],
            ['description' => 'Standard table, Room 1.', 'branch_id' => $physical2->id, 'is_active' => true]
        );
        $table6 = Asset::firstOrCreate(
            ['name' => 'Petrzalka Table No. 2'],
            ['description' => 'Standard table, Room 2.', 'branch_id' => $physical2->id, 'is_active' => true]
        );
 
        // Assets – Rača
        $table7 = Asset::firstOrCreate(
            ['name' => 'Rača Massage Table'],
            ['description' => 'Multi-purpose table.', 'branch_id' => $physical3->id, 'is_active' => true]
        );
 
        // Attach assets
        $this->syncPivot($classic->assets(), $table1->id);
        $this->syncPivot($classic->assets(), $table2->id);
        $this->syncPivot($classic->assets(), $table5->id);
        $this->syncPivot($classic->assets(), $table6->id);
        $this->syncPivot($classic->assets(), $table7->id);
        $this->syncPivot($sport->assets(), $table1->id);
        $this->syncPivot($sport->assets(), $table3->id);
        $this->syncPivot($sport->assets(), $table5->id);
        $this->syncPivot($hot->assets(), $table3->id);
        $this->syncPivot($aroma->assets(), $table2->id);
        $this->syncPivot($aroma->assets(), $table7->id);
        $this->syncPivot($couples->assets(), $table4->id);
        $this->syncPivot($reflexo->assets(), $table6->id);
        $this->syncPivot($reflexo->assets(), $table7->id);
 
        $standardSchedule = [
            '0' => [['from_time' => '07:00', 'to_time' => '12:00'], ['from_time' => '13:00', 'to_time' => '15:00']],
            '1' => [['from_time' => '06:30', 'to_time' => '17:00']],
            '2' => [['from_time' => '06:30', 'to_time' => '17:00']],
            '3' => [['from_time' => '07:00', 'to_time' => '15:00']],
            '4' => [['from_time' => '07:00', 'to_time' => '15:00']],
            '5' => [],
            '6' => [],
        ];
 
        $extendedSchedule = [
            '0' => [['from_time' => '08:00', 'to_time' => '20:00']],
            '1' => [['from_time' => '08:00', 'to_time' => '20:00']],
            '2' => [['from_time' => '08:00', 'to_time' => '20:00']],
            '3' => [['from_time' => '08:00', 'to_time' => '20:00']],
            '4' => [['from_time' => '08:00', 'to_time' => '20:00']],
            '5' => [['from_time' => '09:00', 'to_time' => '16:00']],
            '6' => [['from_time' => '10:00', 'to_time' => '14:00']],
        ];
 
        $this->createWorkingHoursRule($table1, 1, $standardSchedule);
        $this->createWorkingHoursRule($table2, 1, $standardSchedule);
        $this->createWorkingHoursRule($table3, 1, $extendedSchedule);
        $this->createWorkingHoursRule($table4, 1, $extendedSchedule);
        $this->createWorkingHoursRule($table5, 1, $standardSchedule);
        $this->createWorkingHoursRule($table6, 1, $standardSchedule);
        $this->createWorkingHoursRule($table7, 1, $extendedSchedule);
    }
 
    // -------------------------------------------------------------------------
 
    private function seedConsultingFirm(): void
    {
        $owner   = User::where('email', 'tomas@example.com')->firstOrFail();
        $manager = User::where('email', 'zuzana@example.com')->firstOrFail();
 
        $business = Business::firstOrCreate(
            ['name' => 'BizConsult Ltd.'],
            [
                'description'  => 'Business and legal consulting for small and medium enterprises.',
                'state'        => BusinessStateEnum::APPROVED->value,
                'is_published' => true,
            ]
        );
 
        $this->attachUser($business, $owner->id, 'owner');
 
        $physical = Branch::firstOrCreate(
            ['business_id' => $business->id, 'name' => 'Office – Ruzinov'],
            [
                'type'           => 'physical',
                'address_line_1' => 'Ruzinovska 1',
                'city'           => 'Bratislava',
                'postal_code'    => '821 02',
                'country'        => 'Slovakia',
                'latitude'       => 48.1453,
                'longitude'      => 17.1355,
                'is_active'      => true,
            ]
        );
 
        $physical2 = Branch::firstOrCreate(
            ['business_id' => $business->id, 'name' => 'Office – Aupark'],
            [
                'type'           => 'physical',
                'address_line_1' => 'Einsteinova 18',
                'city'           => 'Bratislava',
                'postal_code'    => '851 01',
                'country'        => 'Slovakia',
                'latitude'       => 48.1189,
                'longitude'      => 17.1005,
                'is_active'      => true,
            ]
        );
 
        $online = Branch::firstOrCreate(
            ['business_id' => $business->id, 'name' => 'Online Branch'],
            [
                'type'      => 'online',
                'is_active' => true,
            ]
        );
 
        $this->attachUser($physical, $manager->id, 'manager');
        $this->attachUser($physical2, $owner->id, 'manager');
 
        $consultation = Service::firstOrCreate(
            ['business_id' => $business->id, 'name' => 'Business Consultation'],
            [
                'description'      => 'Personal consultation regarding business plans or contracts.',
                'duration_minutes' => 60,
                'price'            => 80.00,
                'location_type'    => 'branch',
                'is_active'        => true,
            ]
        );
 
        $legalConsultation = Service::firstOrCreate(
            ['business_id' => $business->id, 'name' => 'Legal Consultation'],
            [
                'description'      => 'Legal advice for contracts, trademarks and compliance.',
                'duration_minutes' => 90,
                'price'            => 120.00,
                'location_type'    => 'branch',
                'is_active'        => true,
            ]
        );
 
        $taxConsultation = Service::firstOrCreate(
            ['business_id' => $business->id, 'name' => 'Tax Advisory'],
            [
                'description'      => 'Corporate and personal tax advisory session.',
                'duration_minutes' => 60,
                'price'            => 90.00,
                'location_type'    => 'branch',
                'is_active'        => true,
            ]
        );
 
        $onlineConsultation = Service::firstOrCreate(
            ['business_id' => $business->id, 'name' => 'Online Consultation'],
            [
                'description'      => 'Video call – quick legal or business advice.',
                'duration_minutes' => 30,
                'price'            => 40.00,
                'location_type'    => 'online',
                'is_active'        => true,
            ]
        );
 
        $onlineTax = Service::firstOrCreate(
            ['business_id' => $business->id, 'name' => 'Online Tax Filing Assistance'],
            [
                'description'      => 'Guided online session to help with annual tax return.',
                'duration_minutes' => 45,
                'price'            => 55.00,
                'location_type'    => 'online',
                'is_active'        => true,
            ]
        );
 
        $this->syncPivot($consultation->branches(), $physical->id);
        $this->syncPivot($consultation->branches(), $physical2->id);
        $this->syncPivot($legalConsultation->branches(), $physical->id);
        $this->syncPivot($taxConsultation->branches(), $physical->id);
        $this->syncPivot($taxConsultation->branches(), $physical2->id);
        $this->syncPivot($onlineConsultation->branches(), $online->id);
        $this->syncPivot($onlineTax->branches(), $online->id);
 
        $this->attachUser($consultation, $owner->id, 'staff');
        $this->attachUser($consultation, $manager->id, 'staff');
        $this->attachUser($legalConsultation, $owner->id, 'staff');
        $this->attachUser($taxConsultation, $manager->id, 'staff');
        $this->attachUser($onlineConsultation, $owner->id, 'staff');
        $this->attachUser($onlineTax, $manager->id, 'staff');
 
        $room = Asset::firstOrCreate(
            ['name' => 'Meeting Room'],
            ['description' => 'Capacity 6 people, projector, whiteboard.', 'branch_id' => $physical->id, 'is_active' => true]
        );
 
        $roomB = Asset::firstOrCreate(
            ['name' => 'Meeting Room B'],
            ['description' => 'Capacity 3 people, whiteboard only.', 'branch_id' => $physical->id, 'is_active' => true]
        );
 
        $auparkRoom = Asset::firstOrCreate(
            ['name' => 'Aupark Conference Room'],
            ['description' => 'Modern conference room, capacity 8.', 'branch_id' => $physical2->id, 'is_active' => true]
        );
 
        $this->syncPivot($consultation->assets(), $room->id);
        $this->syncPivot($consultation->assets(), $auparkRoom->id);
        $this->syncPivot($legalConsultation->assets(), $room->id);
        $this->syncPivot($taxConsultation->assets(), $roomB->id);
        $this->syncPivot($taxConsultation->assets(), $auparkRoom->id);
 
        $schedule = [
            '0' => [['from_time' => '08:00', 'to_time' => '12:00'], ['from_time' => '13:00', 'to_time' => '17:00']],
            '1' => [['from_time' => '08:00', 'to_time' => '17:00']],
            '2' => [['from_time' => '08:00', 'to_time' => '17:00']],
            '3' => [['from_time' => '08:00', 'to_time' => '17:00'], ['from_time' => '18:00', 'to_time' => '22:00']],
            '4' => [['from_time' => '08:00', 'to_time' => '17:00']],
            '5' => [['from_time' => '10:00', 'to_time' => '14:00']],
            '6' => [],
        ];
 
        $this->createWorkingHoursRule($room, 1, $schedule);
        $this->createWorkingHoursRule($roomB, 1, $schedule);
        $this->createWorkingHoursRule($auparkRoom, 1, $schedule);
    }
 
    // -------------------------------------------------------------------------
 
    private function seedBarberShop(): void
    {
        $owner  = User::where('email', 'andrej@barber.sk')->firstOrFail();
        $staff1 = User::where('email', 'michal@klient.sk')->firstOrFail();
        $staff2 = User::where('email', 'dominik@barber.sk')->firstOrFail();
        $staff3 = User::where('email', 'natalia@barber.sk')->firstOrFail();
 
        $business = Business::firstOrCreate(
            ['name' => 'Gentleman\'s Cut Barbershop'],
            ['description' => 'Premium grooming for modern men.', 'state' => BusinessStateEnum::APPROVED->value, 'is_published' => true]
        );
 
        $this->attachUser($business, $owner->id, 'owner');
 
        $branch = Branch::firstOrCreate(
            ['business_id' => $business->id, 'name' => 'Downtown Barber'],
            ['type' => 'physical', 'address_line_1' => 'Laurinská 5', 'city' => 'Bratislava', 'postal_code' => '811 01', 'country' => 'Slovakia', 'latitude' => 48.1432, 'longitude' => 17.1076, 'is_active' => true]
        );
 
        $branch2 = Branch::firstOrCreate(
            ['business_id' => $business->id, 'name' => 'Barber – Dúbravka'],
            ['type' => 'physical', 'address_line_1' => 'Saratovská 28', 'city' => 'Bratislava', 'postal_code' => '841 02', 'country' => 'Slovakia', 'latitude' => 48.1799, 'longitude' => 17.0456, 'is_active' => true]
        );
 
        $cut = Service::firstOrCreate(
            ['business_id' => $business->id, 'name' => 'Haircut & Styling'],
            ['description' => 'Classic haircut with styling finish.', 'duration_minutes' => 45, 'price' => 25.00, 'location_type' => 'branch', 'is_active' => true]
        );
 
        $beard = Service::firstOrCreate(
            ['business_id' => $business->id, 'name' => 'Beard Trim'],
            ['description' => 'Precision beard shaping and trim.', 'duration_minutes' => 30, 'price' => 15.00, 'location_type' => 'branch', 'is_active' => true]
        );
 
        $combo = Service::firstOrCreate(
            ['business_id' => $business->id, 'name' => 'Haircut & Beard Combo'],
            ['description' => 'Full grooming package – cut and beard in one session.', 'duration_minutes' => 60, 'price' => 35.00, 'location_type' => 'branch', 'is_active' => true]
        );
 
        $shave = Service::firstOrCreate(
            ['business_id' => $business->id, 'name' => 'Straight Razor Shave'],
            ['description' => 'Traditional hot towel straight razor shave.', 'duration_minutes' => 40, 'price' => 20.00, 'location_type' => 'branch', 'is_active' => true]
        );
 
        $kids = Service::firstOrCreate(
            ['business_id' => $business->id, 'name' => 'Kids Haircut'],
            ['description' => 'Haircut for children up to 12 years.', 'duration_minutes' => 30, 'price' => 12.00, 'location_type' => 'branch', 'is_active' => true]
        );
 
        $this->syncPivot($cut->branches(), $branch->id);
        $this->syncPivot($cut->branches(), $branch2->id);
        $this->syncPivot($beard->branches(), $branch->id);
        $this->syncPivot($beard->branches(), $branch2->id);
        $this->syncPivot($combo->branches(), $branch->id);
        $this->syncPivot($combo->branches(), $branch2->id);
        $this->syncPivot($shave->branches(), $branch->id);
        $this->syncPivot($kids->branches(), $branch->id);
        $this->syncPivot($kids->branches(), $branch2->id);
 
        $this->attachUser($cut, $owner->id, 'staff');
        $this->attachUser($cut, $staff1->id, 'staff');
        $this->attachUser($cut, $staff2->id, 'staff');
        $this->attachUser($beard, $owner->id, 'staff');
        $this->attachUser($beard, $staff2->id, 'staff');
        $this->attachUser($combo, $owner->id, 'staff');
        $this->attachUser($combo, $staff1->id, 'staff');
        $this->attachUser($shave, $owner->id, 'staff');
        $this->attachUser($kids, $staff3->id, 'staff');
 
        $chair1 = Asset::firstOrCreate(['name' => 'Barber Chair #1'], ['description' => 'Classic leather barber chair.', 'branch_id' => $branch->id, 'is_active' => true]);
        $chair2 = Asset::firstOrCreate(['name' => 'Barber Chair #2'], ['description' => 'Classic leather barber chair.', 'branch_id' => $branch->id, 'is_active' => true]);
        $chair3 = Asset::firstOrCreate(['name' => 'Barber Chair #3'], ['description' => 'Premium hydraulic chair.', 'branch_id' => $branch->id, 'is_active' => true]);
        $dubChair1 = Asset::firstOrCreate(['name' => 'Dúbravka Chair #1'], ['description' => 'Standard barber chair.', 'branch_id' => $branch2->id, 'is_active' => true]);
        $dubChair2 = Asset::firstOrCreate(['name' => 'Dúbravka Chair #2'], ['description' => 'Standard barber chair.', 'branch_id' => $branch2->id, 'is_active' => true]);
 
        $this->syncPivot($cut->assets(), $chair1->id);
        $this->syncPivot($cut->assets(), $chair2->id);
        $this->syncPivot($cut->assets(), $chair3->id);
        $this->syncPivot($cut->assets(), $dubChair1->id);
        $this->syncPivot($cut->assets(), $dubChair2->id);
        $this->syncPivot($beard->assets(), $chair1->id);
        $this->syncPivot($beard->assets(), $chair2->id);
        $this->syncPivot($beard->assets(), $dubChair1->id);
        $this->syncPivot($combo->assets(), $chair2->id);
        $this->syncPivot($combo->assets(), $chair3->id);
        $this->syncPivot($combo->assets(), $dubChair2->id);
        $this->syncPivot($shave->assets(), $chair3->id);
        $this->syncPivot($kids->assets(), $chair1->id);
 
        $barberSchedule = [
            '0' => [['from_time' => '09:00', 'to_time' => '19:00']],
            '1' => [['from_time' => '09:00', 'to_time' => '19:00']],
            '2' => [['from_time' => '09:00', 'to_time' => '19:00']],
            '3' => [['from_time' => '09:00', 'to_time' => '19:00']],
            '4' => [['from_time' => '09:00', 'to_time' => '19:00']],
            '5' => [['from_time' => '09:00', 'to_time' => '15:00']],
            '6' => [],
        ];
 
        $this->createWorkingHoursRule($chair1, 1, $barberSchedule);
        $this->createWorkingHoursRule($chair2, 1, $barberSchedule);
        $this->createWorkingHoursRule($chair3, 1, $barberSchedule);
        $this->createWorkingHoursRule($dubChair1, 1, $barberSchedule);
        $this->createWorkingHoursRule($dubChair2, 1, $barberSchedule);
    }
 
    // -------------------------------------------------------------------------
 
    private function seedYogaStudio(): void
    {
        $owner  = User::where('email', 'lucia@joga.sk')->firstOrFail();
        $staff1 = User::where('email', 'kristina@wellness.sk')->firstOrFail();
 
        $business = Business::firstOrCreate(
            ['name' => 'Zen Flow Yoga'],
            ['description' => 'Find your inner peace.', 'state' => BusinessStateEnum::APPROVED->value, 'is_published' => true]
        );
 
        $this->attachUser($business, $owner->id, 'owner');
 
        $branch = Branch::firstOrCreate(
            ['business_id' => $business->id, 'name' => 'Main Studio'],
            ['type' => 'physical', 'address_line_1' => 'Mlynské Nivy 10', 'city' => 'Bratislava', 'postal_code' => '821 09', 'country' => 'Slovakia', 'latitude' => 48.1449, 'longitude' => 17.1254, 'is_active' => true]
        );
 
        $online = Branch::firstOrCreate(
            ['business_id' => $business->id, 'name' => 'Online Yoga'],
            ['type' => 'online', 'is_active' => true]
        );
 
        $lesson = Service::firstOrCreate(
            ['business_id' => $business->id, 'name' => 'Private Yoga Lesson'],
            ['description' => 'One-on-one yoga session tailored to your level.', 'duration_minutes' => 60, 'price' => 40.00, 'location_type' => 'branch', 'is_active' => true]
        );
 
        $meditation = Service::firstOrCreate(
            ['business_id' => $business->id, 'name' => 'Guided Meditation'],
            ['description' => 'Mindfulness and breathwork session, 30 minutes.', 'duration_minutes' => 30, 'price' => 20.00, 'location_type' => 'branch', 'is_active' => true]
        );
 
        $prenatal = Service::firstOrCreate(
            ['business_id' => $business->id, 'name' => 'Prenatal Yoga'],
            ['description' => 'Gentle yoga for expectant mothers.', 'duration_minutes' => 60, 'price' => 35.00, 'location_type' => 'branch', 'is_active' => true]
        );
 
        $onlineLesson = Service::firstOrCreate(
            ['business_id' => $business->id, 'name' => 'Online Yoga Session'],
            ['description' => 'Live yoga session via video call.', 'duration_minutes' => 60, 'price' => 25.00, 'location_type' => 'online', 'is_active' => true]
        );
 
        $this->syncPivot($lesson->branches(), $branch->id);
        $this->syncPivot($meditation->branches(), $branch->id);
        $this->syncPivot($prenatal->branches(), $branch->id);
        $this->syncPivot($onlineLesson->branches(), $online->id);
 
        $this->attachUser($lesson, $owner->id, 'staff');
        $this->attachUser($lesson, $staff1->id, 'staff');
        $this->attachUser($meditation, $owner->id, 'staff');
        $this->attachUser($prenatal, $staff1->id, 'staff');
        $this->attachUser($onlineLesson, $owner->id, 'staff');
 
        $mat1 = Asset::firstOrCreate(['name' => 'Yoga Zone Alpha'], ['description' => 'Main studio zone, mat included.', 'branch_id' => $branch->id, 'is_active' => true]);
        $mat2 = Asset::firstOrCreate(['name' => 'Yoga Zone Beta'], ['description' => 'Secondary studio zone.', 'branch_id' => $branch->id, 'is_active' => true]);
        $mat3 = Asset::firstOrCreate(['name' => 'Yoga Zone Gamma'], ['description' => 'Quiet corner zone, ideal for meditation.', 'branch_id' => $branch->id, 'is_active' => true]);
 
        $this->syncPivot($lesson->assets(), $mat1->id);
        $this->syncPivot($lesson->assets(), $mat2->id);
        $this->syncPivot($meditation->assets(), $mat3->id);
        $this->syncPivot($prenatal->assets(), $mat2->id);
 
        $yogaSchedule = [
            '0' => [['from_time' => '07:00', 'to_time' => '20:00']],
            '1' => [['from_time' => '07:00', 'to_time' => '20:00']],
            '2' => [['from_time' => '07:00', 'to_time' => '20:00']],
            '3' => [['from_time' => '07:00', 'to_time' => '20:00']],
            '4' => [['from_time' => '07:00', 'to_time' => '20:00']],
            '5' => [['from_time' => '08:00', 'to_time' => '14:00']],
            '6' => [['from_time' => '09:00', 'to_time' => '12:00']],
        ];
 
        $this->createWorkingHoursRule($mat1, 1, $yogaSchedule);
        $this->createWorkingHoursRule($mat2, 1, $yogaSchedule);
        $this->createWorkingHoursRule($mat3, 1, $yogaSchedule);
    }
 
    // -------------------------------------------------------------------------
 
    private function seedFitnessCenter(): void
    {
        $owner   = User::where('email', 'rasto@fitzone.sk')->firstOrFail();
        $manager = User::where('email', 'vero@fitzone.sk')->firstOrFail();
 
        $business = Business::firstOrCreate(
            ['name' => 'FitZone Performance Center'],
            [
                'description'  => 'State-of-the-art gym and personal training center in Bratislava.',
                'state'        => BusinessStateEnum::APPROVED->value,
                'is_published' => true,
            ]
        );
 
        $this->attachUser($business, $owner->id, 'owner');
 
        $branch = Branch::firstOrCreate(
            ['business_id' => $business->id, 'name' => 'FitZone – Ružinov'],
            [
                'type'           => 'physical',
                'address_line_1' => 'Miletičova 7',
                'city'           => 'Bratislava',
                'postal_code'    => '821 08',
                'country'        => 'Slovakia',
                'latitude'       => 48.1449,
                'longitude'      => 17.1304,
                'is_active'      => true,
            ]
        );
 
        $branch2 = Branch::firstOrCreate(
            ['business_id' => $business->id, 'name' => 'FitZone – Nové Mesto'],
            [
                'type'           => 'physical',
                'address_line_1' => 'Vajnorská 100',
                'city'           => 'Bratislava',
                'postal_code'    => '831 04',
                'country'        => 'Slovakia',
                'latitude'       => 48.1786,
                'longitude'      => 17.1321,
                'is_active'      => true,
            ]
        );
 
        $online = Branch::firstOrCreate(
            ['business_id' => $business->id, 'name' => 'FitZone Online'],
            ['type' => 'online', 'is_active' => true]
        );
 
        $this->attachUser($branch, $manager->id, 'manager');
 
        $pt = Service::firstOrCreate(
            ['business_id' => $business->id, 'name' => 'Personal Training Session'],
            ['description' => '1-on-1 personal training with certified trainer.', 'duration_minutes' => 60, 'price' => 50.00, 'location_type' => 'branch', 'is_active' => true]
        );
 
        $bodyScan = Service::firstOrCreate(
            ['business_id' => $business->id, 'name' => 'Body Composition Analysis'],
            ['description' => 'Full body scan and fitness assessment.', 'duration_minutes' => 30, 'price' => 20.00, 'location_type' => 'branch', 'is_active' => true]
        );
 
        $nutrition = Service::firstOrCreate(
            ['business_id' => $business->id, 'name' => 'Nutrition Consultation'],
            ['description' => 'Personalised nutrition plan from certified nutritionist.', 'duration_minutes' => 45, 'price' => 40.00, 'location_type' => 'branch', 'is_active' => true]
        );
 
        $boxing = Service::firstOrCreate(
            ['business_id' => $business->id, 'name' => 'Boxing / MMA Training'],
            ['description' => 'Combat sports training session.', 'duration_minutes' => 60, 'price' => 45.00, 'location_type' => 'branch', 'is_active' => true]
        );
 
        $onlinePt = Service::firstOrCreate(
            ['business_id' => $business->id, 'name' => 'Online Personal Training'],
            ['description' => 'Live coached training session via video.', 'duration_minutes' => 60, 'price' => 30.00, 'location_type' => 'online', 'is_active' => true]
        );
 
        $this->syncPivot($pt->branches(), $branch->id);
        $this->syncPivot($pt->branches(), $branch2->id);
        $this->syncPivot($bodyScan->branches(), $branch->id);
        $this->syncPivot($nutrition->branches(), $branch->id);
        $this->syncPivot($nutrition->branches(), $branch2->id);
        $this->syncPivot($boxing->branches(), $branch->id);
        $this->syncPivot($onlinePt->branches(), $online->id);
 
        $this->attachUser($pt, $owner->id, 'staff');
        $this->attachUser($pt, $manager->id, 'staff');
        $this->attachUser($bodyScan, $manager->id, 'staff');
        $this->attachUser($nutrition, $manager->id, 'staff');
        $this->attachUser($boxing, $owner->id, 'staff');
        $this->attachUser($onlinePt, $owner->id, 'staff');
 
        $gymFloor = Asset::firstOrCreate(['name' => 'Gym Floor – Station A'], ['description' => 'Free weights and cable machines.', 'branch_id' => $branch->id, 'is_active' => true]);
        $gymFloor2 = Asset::firstOrCreate(['name' => 'Gym Floor – Station B'], ['description' => 'Squat racks and benches.', 'branch_id' => $branch->id, 'is_active' => true]);
        $boxingRing = Asset::firstOrCreate(['name' => 'Boxing Ring'], ['description' => 'Full-size boxing ring.', 'branch_id' => $branch->id, 'is_active' => true]);
        $scanRoom = Asset::firstOrCreate(['name' => 'Body Scan Room'], ['description' => 'InBody 770 scanner.', 'branch_id' => $branch->id, 'is_active' => true]);
        $nvGym = Asset::firstOrCreate(['name' => 'NM Gym Floor – A'], ['description' => 'Open training area.', 'branch_id' => $branch2->id, 'is_active' => true]);
        $nvGym2 = Asset::firstOrCreate(['name' => 'NM Gym Floor – B'], ['description' => 'Cardio and functional zone.', 'branch_id' => $branch2->id, 'is_active' => true]);
 
        $this->syncPivot($pt->assets(), $gymFloor->id);
        $this->syncPivot($pt->assets(), $gymFloor2->id);
        $this->syncPivot($pt->assets(), $nvGym->id);
        $this->syncPivot($pt->assets(), $nvGym2->id);
        $this->syncPivot($bodyScan->assets(), $scanRoom->id);
        $this->syncPivot($nutrition->assets(), $scanRoom->id);
        $this->syncPivot($boxing->assets(), $boxingRing->id);
 
        $gymSchedule = [
            '0' => [['from_time' => '06:00', 'to_time' => '22:00']],
            '1' => [['from_time' => '06:00', 'to_time' => '22:00']],
            '2' => [['from_time' => '06:00', 'to_time' => '22:00']],
            '3' => [['from_time' => '06:00', 'to_time' => '22:00']],
            '4' => [['from_time' => '06:00', 'to_time' => '22:00']],
            '5' => [['from_time' => '08:00', 'to_time' => '18:00']],
            '6' => [['from_time' => '09:00', 'to_time' => '16:00']],
        ];
 
        $this->createWorkingHoursRule($gymFloor, 1, $gymSchedule);
        $this->createWorkingHoursRule($gymFloor2, 1, $gymSchedule);
        $this->createWorkingHoursRule($boxingRing, 1, $gymSchedule);
        $this->createWorkingHoursRule($scanRoom, 1, $gymSchedule);
        $this->createWorkingHoursRule($nvGym, 1, $gymSchedule);
        $this->createWorkingHoursRule($nvGym2, 1, $gymSchedule);
    }
 
    // -------------------------------------------------------------------------
 
    private function seedDentalClinic(): void
    {
        $owner  = User::where('email', 'lubos@dentist.sk')->firstOrFail();
        $staff1 = User::where('email', 'andrea@dentist.sk')->firstOrFail();
        $staff2 = User::where('email', 'tvlcek@dentist.sk')->firstOrFail();
 
        $business = Business::firstOrCreate(
            ['name' => 'SmilePro Dental Clinic'],
            [
                'description'  => 'Modern dental care with gentle approach for adults and children.',
                'state'        => BusinessStateEnum::APPROVED->value,
                'is_published' => true,
            ]
        );
 
        $this->attachUser($business, $owner->id, 'owner');
 
        $branch = Branch::firstOrCreate(
            ['business_id' => $business->id, 'name' => 'SmilePro – Staré Mesto'],
            [
                'type'           => 'physical',
                'address_line_1' => 'Špitálska 20',
                'city'           => 'Bratislava',
                'postal_code'    => '811 08',
                'country'        => 'Slovakia',
                'latitude'       => 48.1498,
                'longitude'      => 17.1184,
                'is_active'      => true,
            ]
        );
 
        $branch2 = Branch::firstOrCreate(
            ['business_id' => $business->id, 'name' => 'SmilePro – Karlova Ves'],
            [
                'type'           => 'physical',
                'address_line_1' => 'Molecova 2',
                'city'           => 'Bratislava',
                'postal_code'    => '841 04',
                'country'        => 'Slovakia',
                'latitude'       => 48.1572,
                'longitude'      => 17.0621,
                'is_active'      => true,
            ]
        );
 
        $this->attachUser($branch, $staff1->id, 'manager');
        $this->attachUser($branch2, $staff2->id, 'manager');
 
        $checkup = Service::firstOrCreate(
            ['business_id' => $business->id, 'name' => 'Dental Check-up'],
            ['description' => 'Comprehensive dental examination and X-ray.', 'duration_minutes' => 30, 'price' => 30.00, 'location_type' => 'branch', 'is_active' => true]
        );
 
        $cleaning = Service::firstOrCreate(
            ['business_id' => $business->id, 'name' => 'Professional Teeth Cleaning'],
            ['description' => 'Ultrasonic scaling and polishing.', 'duration_minutes' => 45, 'price' => 60.00, 'location_type' => 'branch', 'is_active' => true]
        );
 
        $whitening = Service::firstOrCreate(
            ['business_id' => $business->id, 'name' => 'Teeth Whitening'],
            ['description' => 'In-office Zoom whitening, 90 minutes.', 'duration_minutes' => 90, 'price' => 180.00, 'location_type' => 'branch', 'is_active' => true]
        );
 
        $filling = Service::firstOrCreate(
            ['business_id' => $business->id, 'name' => 'Dental Filling'],
            ['description' => 'Composite resin filling for cavities.', 'duration_minutes' => 45, 'price' => 70.00, 'location_type' => 'branch', 'is_active' => true]
        );
 
        $extraction = Service::firstOrCreate(
            ['business_id' => $business->id, 'name' => 'Tooth Extraction'],
            ['description' => 'Simple or surgical tooth removal.', 'duration_minutes' => 30, 'price' => 50.00, 'location_type' => 'branch', 'is_active' => true]
        );
 
        $this->syncPivot($checkup->branches(), $branch->id);
        $this->syncPivot($checkup->branches(), $branch2->id);
        $this->syncPivot($cleaning->branches(), $branch->id);
        $this->syncPivot($cleaning->branches(), $branch2->id);
        $this->syncPivot($whitening->branches(), $branch->id);
        $this->syncPivot($filling->branches(), $branch->id);
        $this->syncPivot($filling->branches(), $branch2->id);
        $this->syncPivot($extraction->branches(), $branch->id);
 
        $this->attachUser($checkup, $owner->id, 'staff');
        $this->attachUser($checkup, $staff1->id, 'staff');
        $this->attachUser($checkup, $staff2->id, 'staff');
        $this->attachUser($cleaning, $staff1->id, 'staff');
        $this->attachUser($cleaning, $staff2->id, 'staff');
        $this->attachUser($whitening, $staff1->id, 'staff');
        $this->attachUser($filling, $owner->id, 'staff');
        $this->attachUser($filling, $staff2->id, 'staff');
        $this->attachUser($extraction, $owner->id, 'staff');
 
        $chair1 = Asset::firstOrCreate(['name' => 'Dental Chair 1'], ['description' => 'Sirona C8+ unit, Room 1.', 'branch_id' => $branch->id, 'is_active' => true]);
        $chair2 = Asset::firstOrCreate(['name' => 'Dental Chair 2'], ['description' => 'Sirona C8+ unit, Room 2.', 'branch_id' => $branch->id, 'is_active' => true]);
        $chair3 = Asset::firstOrCreate(['name' => 'Dental Chair 3'], ['description' => 'Whitening unit, Room 3.', 'branch_id' => $branch->id, 'is_active' => true]);
        $kvChair1 = Asset::firstOrCreate(['name' => 'KV Dental Chair 1'], ['description' => 'Standard dental unit.', 'branch_id' => $branch2->id, 'is_active' => true]);
        $kvChair2 = Asset::firstOrCreate(['name' => 'KV Dental Chair 2'], ['description' => 'Standard dental unit.', 'branch_id' => $branch2->id, 'is_active' => true]);
 
        $this->syncPivot($checkup->assets(), $chair1->id);
        $this->syncPivot($checkup->assets(), $chair2->id);
        $this->syncPivot($checkup->assets(), $kvChair1->id);
        $this->syncPivot($checkup->assets(), $kvChair2->id);
        $this->syncPivot($cleaning->assets(), $chair1->id);
        $this->syncPivot($cleaning->assets(), $kvChair1->id);
        $this->syncPivot($whitening->assets(), $chair3->id);
        $this->syncPivot($filling->assets(), $chair2->id);
        $this->syncPivot($filling->assets(), $kvChair2->id);
        $this->syncPivot($extraction->assets(), $chair2->id);
 
        $dentalSchedule = [
            '0' => [['from_time' => '08:00', 'to_time' => '12:00'], ['from_time' => '13:00', 'to_time' => '17:00']],
            '1' => [['from_time' => '08:00', 'to_time' => '17:00']],
            '2' => [['from_time' => '08:00', 'to_time' => '17:00']],
            '3' => [['from_time' => '08:00', 'to_time' => '17:00']],
            '4' => [['from_time' => '08:00', 'to_time' => '17:00']],
            '5' => [],
            '6' => [],
        ];
 
        $this->createWorkingHoursRule($chair1, 1, $dentalSchedule);
        $this->createWorkingHoursRule($chair2, 1, $dentalSchedule);
        $this->createWorkingHoursRule($chair3, 1, $dentalSchedule);
        $this->createWorkingHoursRule($kvChair1, 1, $dentalSchedule);
        $this->createWorkingHoursRule($kvChair2, 1, $dentalSchedule);
    }
 
    // -------------------------------------------------------------------------
 
    private function seedPhotoStudio(): void
    {
        $owner  = User::where('email', 'marek@photo.sk')->firstOrFail();
        $staff1 = User::where('email', 'denisa@photo.sk')->firstOrFail();
 
        $business = Business::firstOrCreate(
            ['name' => 'PixelArt Photo Studio'],
            [
                'description'  => 'Professional photography for portraits, events, and products.',
                'state'        => BusinessStateEnum::APPROVED->value,
                'is_published' => true,
            ]
        );
 
        $this->attachUser($business, $owner->id, 'owner');
 
        $branch = Branch::firstOrCreate(
            ['business_id' => $business->id, 'name' => 'Main Studio – Ružinov'],
            [
                'type'           => 'physical',
                'address_line_1' => 'Záhradnícka 70',
                'city'           => 'Bratislava',
                'postal_code'    => '821 08',
                'country'        => 'Slovakia',
                'latitude'       => 48.1436,
                'longitude'      => 17.1392,
                'is_active'      => true,
            ]
        );
 
        $online = Branch::firstOrCreate(
            ['business_id' => $business->id, 'name' => 'Online – Retouching & Editing'],
            ['type' => 'online', 'is_active' => true]
        );
 
        $portrait = Service::firstOrCreate(
            ['business_id' => $business->id, 'name' => 'Portrait Session'],
            ['description' => 'Professional portrait shoot, 1 hour, 10 edited photos.', 'duration_minutes' => 60, 'price' => 90.00, 'location_type' => 'branch', 'is_active' => true]
        );
 
        $product = Service::firstOrCreate(
            ['business_id' => $business->id, 'name' => 'Product Photography'],
            ['description' => 'Product shoot for e-commerce or catalog, per session.', 'duration_minutes' => 90, 'price' => 150.00, 'location_type' => 'branch', 'is_active' => true]
        );
 
        $headshot = Service::firstOrCreate(
            ['business_id' => $business->id, 'name' => 'LinkedIn / CV Headshots'],
            ['description' => 'Quick professional headshot session, 30 min, 3 photos.', 'duration_minutes' => 30, 'price' => 40.00, 'location_type' => 'branch', 'is_active' => true]
        );
 
        $onlineEdit = Service::firstOrCreate(
            ['business_id' => $business->id, 'name' => 'Online Photo Editing'],
            ['description' => 'Send your RAW files and we return edited JPEGs.', 'duration_minutes' => 60, 'price' => 30.00, 'location_type' => 'online', 'is_active' => true]
        );
 
        $this->syncPivot($portrait->branches(), $branch->id);
        $this->syncPivot($product->branches(), $branch->id);
        $this->syncPivot($headshot->branches(), $branch->id);
        $this->syncPivot($onlineEdit->branches(), $online->id);
 
        $this->attachUser($portrait, $owner->id, 'staff');
        $this->attachUser($portrait, $staff1->id, 'staff');
        $this->attachUser($product, $owner->id, 'staff');
        $this->attachUser($headshot, $staff1->id, 'staff');
        $this->attachUser($onlineEdit, $staff1->id, 'staff');
 
        $studio1 = Asset::firstOrCreate(['name' => 'Studio A – White Cyclorama'], ['description' => 'White infinity wall, 5x5m.', 'branch_id' => $branch->id, 'is_active' => true]);
        $studio2 = Asset::firstOrCreate(['name' => 'Studio B – Dark Background'], ['description' => 'Black and grey backdrop set.', 'branch_id' => $branch->id, 'is_active' => true]);
        $lightSet = Asset::firstOrCreate(['name' => 'Product Light Table'], ['description' => 'Tabletop lightbox for small products.', 'branch_id' => $branch->id, 'is_active' => true]);
 
        $this->syncPivot($portrait->assets(), $studio1->id);
        $this->syncPivot($portrait->assets(), $studio2->id);
        $this->syncPivot($product->assets(), $studio1->id);
        $this->syncPivot($product->assets(), $lightSet->id);
        $this->syncPivot($headshot->assets(), $studio2->id);
 
        $photoSchedule = [
            '0' => [['from_time' => '09:00', 'to_time' => '18:00']],
            '1' => [['from_time' => '09:00', 'to_time' => '18:00']],
            '2' => [['from_time' => '09:00', 'to_time' => '18:00']],
            '3' => [['from_time' => '09:00', 'to_time' => '18:00']],
            '4' => [['from_time' => '09:00', 'to_time' => '18:00']],
            '5' => [['from_time' => '10:00', 'to_time' => '16:00']],
            '6' => [],
        ];
 
        $this->createWorkingHoursRule($studio1, 1, $photoSchedule);
        $this->createWorkingHoursRule($studio2, 1, $photoSchedule);
        $this->createWorkingHoursRule($lightSet, 1, $photoSchedule);
    }
 
    // -------------------------------------------------------------------------
 
    private function seedAutoService(): void
    {
        $owner  = User::where('email', 'robert@autoservis.sk')->firstOrFail();
        $staff1 = User::where('email', 'silvia@autoservis.sk')->firstOrFail();
 
        $business = Business::firstOrCreate(
            ['name' => 'AutoMax Service Center'],
            [
                'description'  => 'Car servicing, diagnostics and detailing for all makes and models.',
                'state'        => BusinessStateEnum::APPROVED->value,
                'is_published' => true,
            ]
        );
 
        $this->attachUser($business, $owner->id, 'owner');
 
        $branch = Branch::firstOrCreate(
            ['business_id' => $business->id, 'name' => 'AutoMax – Podunajské Biskupice'],
            [
                'type'           => 'physical',
                'address_line_1' => 'Senecká 5',
                'city'           => 'Bratislava',
                'postal_code'    => '825 07',
                'country'        => 'Slovakia',
                'latitude'       => 48.1243,
                'longitude'      => 17.1724,
                'is_active'      => true,
            ]
        );
 
        $oilChange = Service::firstOrCreate(
            ['business_id' => $business->id, 'name' => 'Oil & Filter Change'],
            ['description' => 'Engine oil and oil filter replacement, all types.', 'duration_minutes' => 45, 'price' => 60.00, 'location_type' => 'branch', 'is_active' => true]
        );
 
        $diagnostics = Service::firstOrCreate(
            ['business_id' => $business->id, 'name' => 'Full Diagnostics'],
            ['description' => 'Computer diagnostics with error code printout.', 'duration_minutes' => 60, 'price' => 40.00, 'location_type' => 'branch', 'is_active' => true]
        );
 
        $tires = Service::firstOrCreate(
            ['business_id' => $business->id, 'name' => 'Tyre Change & Balancing'],
            ['description' => 'Seasonal tyre swap and wheel balancing.', 'duration_minutes' => 60, 'price' => 50.00, 'location_type' => 'branch', 'is_active' => true]
        );
 
        $detailing = Service::firstOrCreate(
            ['business_id' => $business->id, 'name' => 'Interior & Exterior Detailing'],
            ['description' => 'Full car detail – wash, wax, vacuum and interior clean.', 'duration_minutes' => 120, 'price' => 100.00, 'location_type' => 'branch', 'is_active' => true]
        );
 
        $ac = Service::firstOrCreate(
            ['business_id' => $business->id, 'name' => 'A/C Service & Refill'],
            ['description' => 'Air conditioning system check and refrigerant top-up.', 'duration_minutes' => 45, 'price' => 55.00, 'location_type' => 'branch', 'is_active' => true]
        );
 
        $this->syncPivot($oilChange->branches(), $branch->id);
        $this->syncPivot($diagnostics->branches(), $branch->id);
        $this->syncPivot($tires->branches(), $branch->id);
        $this->syncPivot($detailing->branches(), $branch->id);
        $this->syncPivot($ac->branches(), $branch->id);
 
        $this->attachUser($oilChange, $owner->id, 'staff');
        $this->attachUser($oilChange, $staff1->id, 'staff');
        $this->attachUser($diagnostics, $owner->id, 'staff');
        $this->attachUser($tires, $staff1->id, 'staff');
        $this->attachUser($detailing, $staff1->id, 'staff');
        $this->attachUser($ac, $owner->id, 'staff');
 
        $lift1 = Asset::firstOrCreate(['name' => 'Lift Bay 1'], ['description' => '4-post hydraulic lift.', 'branch_id' => $branch->id, 'is_active' => true]);
        $lift2 = Asset::firstOrCreate(['name' => 'Lift Bay 2'], ['description' => '2-post hydraulic lift.', 'branch_id' => $branch->id, 'is_active' => true]);
        $diagBay = Asset::firstOrCreate(['name' => 'Diagnostics Bay'], ['description' => 'OBD2 + full scanner station.', 'branch_id' => $branch->id, 'is_active' => true]);
        $washBay = Asset::firstOrCreate(['name' => 'Detail / Wash Bay'], ['description' => 'Pressure wash + interior cleaning bay.', 'branch_id' => $branch->id, 'is_active' => true]);
        $tyreMachine = Asset::firstOrCreate(['name' => 'Tyre Machine Station'], ['description' => 'Tyre changer + balancer.', 'branch_id' => $branch->id, 'is_active' => true]);
 
        $this->syncPivot($oilChange->assets(), $lift1->id);
        $this->syncPivot($oilChange->assets(), $lift2->id);
        $this->syncPivot($diagnostics->assets(), $diagBay->id);
        $this->syncPivot($tires->assets(), $tyreMachine->id);
        $this->syncPivot($detailing->assets(), $washBay->id);
        $this->syncPivot($ac->assets(), $lift1->id);
 
        $autoSchedule = [
            '0' => [['from_time' => '08:00', 'to_time' => '17:00']],
            '1' => [['from_time' => '08:00', 'to_time' => '17:00']],
            '2' => [['from_time' => '08:00', 'to_time' => '17:00']],
            '3' => [['from_time' => '08:00', 'to_time' => '17:00']],
            '4' => [['from_time' => '08:00', 'to_time' => '17:00']],
            '5' => [['from_time' => '08:00', 'to_time' => '12:00']],
            '6' => [],
        ];
 
        $this->createWorkingHoursRule($lift1, 1, $autoSchedule);
        $this->createWorkingHoursRule($lift2, 1, $autoSchedule);
        $this->createWorkingHoursRule($diagBay, 1, $autoSchedule);
        $this->createWorkingHoursRule($washBay, 1, $autoSchedule);
        $this->createWorkingHoursRule($tyreMachine, 1, $autoSchedule);
    }
 
    // -------------------------------------------------------------------------
 
    private function seedWellnessCenter(): void
    {
        $owner  = User::where('email', 'jakub@wellness.sk')->firstOrFail();
        $staff1 = User::where('email', 'kristina@wellness.sk')->firstOrFail();
 
        $business = Business::firstOrCreate(
            ['name' => 'Aura Wellness & Spa'],
            [
                'description'  => 'Premium wellness, sauna and spa services in the heart of Bratislava.',
                'state'        => BusinessStateEnum::APPROVED->value,
                'is_published' => true,
            ]
        );
 
        $this->attachUser($business, $owner->id, 'owner');
 
        $branch = Branch::firstOrCreate(
            ['business_id' => $business->id, 'name' => 'Aura Wellness – City Centre'],
            [
                'type'           => 'physical',
                'address_line_1' => 'Ventúrska 9',
                'city'           => 'Bratislava',
                'postal_code'    => '811 01',
                'country'        => 'Slovakia',
                'latitude'       => 48.1428,
                'longitude'      => 17.1056,
                'is_active'      => true,
            ]
        );
 
        $sauna = Service::firstOrCreate(
            ['business_id' => $business->id, 'name' => 'Private Sauna Session'],
            ['description' => 'Private Finnish sauna for 1–4 people, 90 minutes.', 'duration_minutes' => 90, 'price' => 65.00, 'location_type' => 'branch', 'is_active' => true]
        );
 
        $jacuzzi = Service::firstOrCreate(
            ['business_id' => $business->id, 'name' => 'Jacuzzi & Relax'],
            ['description' => 'Private jacuzzi room, 60 minutes.', 'duration_minutes' => 60, 'price' => 50.00, 'location_type' => 'branch', 'is_active' => true]
        );
 
        $facial = Service::firstOrCreate(
            ['business_id' => $business->id, 'name' => 'Luxury Facial Treatment'],
            ['description' => 'Deep cleansing and hydration facial, 60 minutes.', 'duration_minutes' => 60, 'price' => 70.00, 'location_type' => 'branch', 'is_active' => true]
        );
 
        $body = Service::firstOrCreate(
            ['business_id' => $business->id, 'name' => 'Body Wrap & Scrub'],
            ['description' => 'Exfoliating scrub and detox body wrap, 75 minutes.', 'duration_minutes' => 75, 'price' => 80.00, 'location_type' => 'branch', 'is_active' => true]
        );
 
        $this->syncPivot($sauna->branches(), $branch->id);
        $this->syncPivot($jacuzzi->branches(), $branch->id);
        $this->syncPivot($facial->branches(), $branch->id);
        $this->syncPivot($body->branches(), $branch->id);
 
        $this->attachUser($sauna, $owner->id, 'staff');
        $this->attachUser($jacuzzi, $owner->id, 'staff');
        $this->attachUser($facial, $staff1->id, 'staff');
        $this->attachUser($body, $staff1->id, 'staff');
 
        $saunaRoom = Asset::firstOrCreate(['name' => 'Finnish Sauna'], ['description' => 'Traditional wood-burning sauna, up to 4 people.', 'branch_id' => $branch->id, 'is_active' => true]);
        $jacuzziRoom = Asset::firstOrCreate(['name' => 'Jacuzzi Suite'], ['description' => 'Private jacuzzi with ambient lighting.', 'branch_id' => $branch->id, 'is_active' => true]);
        $treatmentRoom1 = Asset::firstOrCreate(['name' => 'Treatment Room 1'], ['description' => 'Beauty treatment room.', 'branch_id' => $branch->id, 'is_active' => true]);
        $treatmentRoom2 = Asset::firstOrCreate(['name' => 'Treatment Room 2'], ['description' => 'Body wrap / scrub room.', 'branch_id' => $branch->id, 'is_active' => true]);
 
        $this->syncPivot($sauna->assets(), $saunaRoom->id);
        $this->syncPivot($jacuzzi->assets(), $jacuzziRoom->id);
        $this->syncPivot($facial->assets(), $treatmentRoom1->id);
        $this->syncPivot($body->assets(), $treatmentRoom2->id);
 
        $spaSchedule = [
            '0' => [['from_time' => '10:00', 'to_time' => '21:00']],
            '1' => [['from_time' => '10:00', 'to_time' => '21:00']],
            '2' => [['from_time' => '10:00', 'to_time' => '21:00']],
            '3' => [['from_time' => '10:00', 'to_time' => '21:00']],
            '4' => [['from_time' => '10:00', 'to_time' => '21:00']],
            '5' => [['from_time' => '10:00', 'to_time' => '22:00']],
            '6' => [['from_time' => '11:00', 'to_time' => '20:00']],
        ];
 
        $this->createWorkingHoursRule($saunaRoom, 1, $spaSchedule);
        $this->createWorkingHoursRule($jacuzziRoom, 1, $spaSchedule);
        $this->createWorkingHoursRule($treatmentRoom1, 1, $spaSchedule);
        $this->createWorkingHoursRule($treatmentRoom2, 1, $spaSchedule);
    }
 
    // -------------------------------------------------------------------------
 
    private function seedNailStudio(): void
    {
        $owner = User::where('email', 'vero@fitzone.sk')->firstOrFail();
 
        $business = Business::firstOrCreate(
            ['name' => 'Luxe Nail & Beauty Studio'],
            [
                'description'  => 'Professional nail care, lashes and beauty treatments.',
                'state'        => BusinessStateEnum::APPROVED->value,
                'is_published' => true,
            ]
        );
 
        $this->attachUser($business, $owner->id, 'owner');
 
        $branch = Branch::firstOrCreate(
            ['business_id' => $business->id, 'name' => 'Luxe Nail – Avion Shopping'],
            [
                'type'           => 'physical',
                'address_line_1' => 'Ivanská cesta 16',
                'city'           => 'Bratislava',
                'postal_code'    => '821 04',
                'country'        => 'Slovakia',
                'latitude'       => 48.1611,
                'longitude'      => 17.1700,
                'is_active'      => true,
            ]
        );
 
        $manicure = Service::firstOrCreate(
            ['business_id' => $business->id, 'name' => 'Gel Manicure'],
            ['description' => 'Gel polish application with nail prep.', 'duration_minutes' => 60, 'price' => 35.00, 'location_type' => 'branch', 'is_active' => true]
        );
 
        $pedicure = Service::firstOrCreate(
            ['business_id' => $business->id, 'name' => 'Luxury Pedicure'],
            ['description' => 'Full pedicure with callus removal and gel polish.', 'duration_minutes' => 75, 'price' => 45.00, 'location_type' => 'branch', 'is_active' => true]
        );
 
        $lashes = Service::firstOrCreate(
            ['business_id' => $business->id, 'name' => 'Lash Extensions'],
            ['description' => 'Classic or volume lash extension set.', 'duration_minutes' => 90, 'price' => 60.00, 'location_type' => 'branch', 'is_active' => true]
        );
 
        $brows = Service::firstOrCreate(
            ['business_id' => $business->id, 'name' => 'Eyebrow Lamination & Tint'],
            ['description' => 'Brow lamination with tinting.', 'duration_minutes' => 60, 'price' => 40.00, 'location_type' => 'branch', 'is_active' => true]
        );
 
        $this->syncPivot($manicure->branches(), $branch->id);
        $this->syncPivot($pedicure->branches(), $branch->id);
        $this->syncPivot($lashes->branches(), $branch->id);
        $this->syncPivot($brows->branches(), $branch->id);
 
        $this->attachUser($manicure, $owner->id, 'staff');
        $this->attachUser($pedicure, $owner->id, 'staff');
        $this->attachUser($lashes, $owner->id, 'staff');
        $this->attachUser($brows, $owner->id, 'staff');
 
        $nail1 = Asset::firstOrCreate(['name' => 'Nail Station 1'], ['description' => 'Manicure & pedicure station.', 'branch_id' => $branch->id, 'is_active' => true]);
        $nail2 = Asset::firstOrCreate(['name' => 'Nail Station 2'], ['description' => 'Manicure & pedicure station.', 'branch_id' => $branch->id, 'is_active' => true]);
        $lashBed = Asset::firstOrCreate(['name' => 'Lash Bed'], ['description' => 'Reclined beauty bed for lash and brow work.', 'branch_id' => $branch->id, 'is_active' => true]);
 
        $this->syncPivot($manicure->assets(), $nail1->id);
        $this->syncPivot($manicure->assets(), $nail2->id);
        $this->syncPivot($pedicure->assets(), $nail1->id);
        $this->syncPivot($pedicure->assets(), $nail2->id);
        $this->syncPivot($lashes->assets(), $lashBed->id);
        $this->syncPivot($brows->assets(), $lashBed->id);
 
        $nailSchedule = [
            '0' => [['from_time' => '09:00', 'to_time' => '19:00']],
            '1' => [['from_time' => '09:00', 'to_time' => '19:00']],
            '2' => [['from_time' => '09:00', 'to_time' => '19:00']],
            '3' => [['from_time' => '09:00', 'to_time' => '19:00']],
            '4' => [['from_time' => '09:00', 'to_time' => '19:00']],
            '5' => [['from_time' => '09:00', 'to_time' => '17:00']],
            '6' => [['from_time' => '10:00', 'to_time' => '15:00']],
        ];
 
        $this->createWorkingHoursRule($nail1, 1, $nailSchedule);
        $this->createWorkingHoursRule($nail2, 1, $nailSchedule);
        $this->createWorkingHoursRule($lashBed, 1, $nailSchedule);
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    private function attachUser(object $model, int $userId, string $role): void
    {
        if (! $model->users()->where('user_id', $userId)->exists()) {
            $model->users()->attach($userId, ['role' => $role]);
        }
    }

    private function syncPivot($relation, int $id): void
    {
        $column = $relation->getQualifiedRelatedKeyName();

        if (! $relation->where($column, $id)->exists()) {
            $relation->attach($id);
        }
    }

    private function createWorkingHoursRule(Asset $asset, int $priority = 1, ?array $customSchedule = null): void
    {
        if ($asset->rules()->where('priority', $priority)->exists()) {
            return;
        }

        $defaultSchedule = [
            '0' => [['from_time' => '09:00', 'to_time' => '18:00']],
            '1' => [['from_time' => '09:00', 'to_time' => '18:00']],
            '2' => [['from_time' => '09:00', 'to_time' => '18:00']],
            '3' => [['from_time' => '09:00', 'to_time' => '18:00']],
            '4' => [['from_time' => '09:00', 'to_time' => '18:00']],
            '5' => [],
            '6' => [],
        ];

        $ruleSet = [
            'days' => $customSchedule ?? $defaultSchedule
        ];

        Rule::create([
            'asset_id'    => $asset->id,
            'priority'    => $priority,
            'title'       => 'Default',
            'description' => 'Standard operating hours.',
            'valid_from'  => now()->startOfYear(),
            'valid_to'    => now()->addYears(2),
            'rule_set'    => $ruleSet,
        ]);
    }
}