<?php

namespace Database\Seeders;

use App\Models\Business\Business;
use App\Models\Business\Category;
use App\Models\Business\Service;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * Seeds service categories and assigns them to businesses + services.
 *
 * Depends on: BusinessSeeder (businesses and services must exist)
 */
class CategorySeeder extends Seeder
{
    public function run(): void
    {
        // -----------------------------------------------------------------------
        // 1. Create categories
        // -----------------------------------------------------------------------
        $categories = $this->createCategories();

        // -----------------------------------------------------------------------
        // 2. Assign category to each Business (primary category)
        // -----------------------------------------------------------------------
        $this->assignBusinessCategories($categories);

        // -----------------------------------------------------------------------
        // 3. Assign category_id to each Service
        // -----------------------------------------------------------------------
        $this->assignServiceCategories($categories);
    }

    // ---------------------------------------------------------------------------
    // Category definitions
    // ---------------------------------------------------------------------------

    private function createCategories(): array
    {
        $definitions = [
            'massage'     => ['name' => 'Massage',              'description' => 'Relaxation, sports and therapeutic massage services.'],
            'consulting'  => ['name' => 'Consulting',           'description' => 'Business, legal, and financial advisory services.'],
            'barbershop'  => ['name' => 'Barbershop & Grooming', 'description' => 'Haircuts, beard trims, and grooming for men.'],
            'fitness'     => ['name' => 'Fitness & Training',   'description' => 'Personal training, gym sessions, and sports coaching.'],
            'yoga'        => ['name' => 'Yoga & Mindfulness',   'description' => 'Yoga classes, guided meditation, and wellness sessions.'],
            'dental'      => ['name' => 'Dental & Healthcare',  'description' => 'Dental check-ups, treatments, and oral health services.'],
            'photography' => ['name' => 'Photography',          'description' => 'Portrait, product, and professional photo sessions.'],
            'automotive'  => ['name' => 'Automotive',           'description' => 'Car servicing, diagnostics, and detailing.'],
            'spa'         => ['name' => 'Spa & Wellness',       'description' => 'Sauna, jacuzzi, facials, and full-body spa treatments.'],
            'beauty'      => ['name' => 'Nails & Beauty',       'description' => 'Manicure, pedicure, lash extensions, and brow treatments.'],
        ];

        $created = [];

        foreach ($definitions as $key => $attrs) {
            $created[$key] = Category::firstOrCreate(
                ['slug' => Str::slug($attrs['name'])],
                [
                    'name'        => $attrs['name'],
                    'description' => $attrs['description'],
                ]
            );
        }

        return $created;
    }

    // ---------------------------------------------------------------------------
    // Business → primary category
    // ---------------------------------------------------------------------------

    private function assignBusinessCategories(array $cat): void
    {
        $map = [
            'Relax Studio Bratislava'    => $cat['massage'],
            'Bratislava Consulting Group' => $cat['consulting'],
            'Downtown Barbershop'        => $cat['barbershop'],
            'Yoga & Mind Studio'         => $cat['yoga'],
            'FitZone Bratislava'         => $cat['fitness'],
            'SmileCare Dental Clinic'    => $cat['dental'],
            'Studio Click Photography'   => $cat['photography'],
            'AutoPro Service Center'     => $cat['automotive'],
            'AquaRelax Wellness Center'  => $cat['spa'],
            'Luxe Nail & Beauty Studio'  => $cat['beauty'],
        ];

        foreach ($map as $businessName => $category) {
            Business::where('name', $businessName)
                ->update(['category_id' => $category->id]);
        }
    }

    // ---------------------------------------------------------------------------
    // Service → category (matched by name, scoped to avoid cross-business conflicts)
    // ---------------------------------------------------------------------------

    private function assignServiceCategories(array $cat): void
    {
        // Each entry: [service name pattern, category]
        // Scoped by the business name where the service name isn't globally unique.
        $assignments = [

            // ── Massage ──────────────────────────────────────────────────────────
            'Classic Massage'               => $cat['massage'],
            'Sports Massage'                => $cat['massage'],
            'Hot Stone Massage'             => $cat['massage'],
            'Aromatherapy Massage'          => $cat['massage'],
            'Couples Massage'               => $cat['massage'],
            'Reflexology'                   => $cat['massage'],
            'Online Massage Consultation'   => $cat['massage'],

            // ── Consulting ───────────────────────────────────────────────────────
            'Business Consultation'         => $cat['consulting'],
            'Legal Consultation'            => $cat['consulting'],
            'Tax Advisory'                  => $cat['consulting'],

            // ── Barbershop ───────────────────────────────────────────────────────
            'Haircut & Styling'             => $cat['barbershop'],
            'Beard Trim'                    => $cat['barbershop'],
            'Haircut & Beard Combo'         => $cat['barbershop'],
            'Straight Razor Shave'          => $cat['barbershop'],
            'Kids Haircut'                  => $cat['barbershop'],

            // ── Yoga ─────────────────────────────────────────────────────────────
            'Private Yoga Lesson'           => $cat['yoga'],
            'Guided Meditation'             => $cat['yoga'],
            'Prenatal Yoga'                 => $cat['yoga'],

            // ── Fitness ──────────────────────────────────────────────────────────
            'Personal Training Session'     => $cat['fitness'],
            'Body Composition Analysis'     => $cat['fitness'],
            'Nutrition Consultation'        => $cat['fitness'],
            'Boxing / MMA Training'         => $cat['fitness'],

            // ── Dental ───────────────────────────────────────────────────────────
            'Dental Check-up'               => $cat['dental'],
            'Professional Teeth Cleaning'   => $cat['dental'],
            'Teeth Whitening'               => $cat['dental'],
            'Dental Filling'                => $cat['dental'],
            'Tooth Extraction'              => $cat['dental'],

            // ── Photography ──────────────────────────────────────────────────────
            'Portrait Session'              => $cat['photography'],
            'Product Photography'           => $cat['photography'],
            'LinkedIn / CV Headshots'       => $cat['photography'],

            // ── Automotive ───────────────────────────────────────────────────────
            'Oil & Filter Change'           => $cat['automotive'],
            'Full Diagnostics'              => $cat['automotive'],
            'Tyre Change & Balancing'       => $cat['automotive'],
            'Interior & Exterior Detailing' => $cat['automotive'],
            'A/C Service & Refill'          => $cat['automotive'],

            // ── Spa & Wellness ───────────────────────────────────────────────────
            'Private Sauna Session'         => $cat['spa'],
            'Jacuzzi & Relax'               => $cat['spa'],
            'Luxury Facial Treatment'       => $cat['spa'],
            'Body Wrap & Scrub'             => $cat['spa'],

            // ── Nails & Beauty ───────────────────────────────────────────────────
            'Gel Manicure'                  => $cat['beauty'],
            'Luxury Pedicure'               => $cat['beauty'],
            'Lash Extensions'               => $cat['beauty'],
            'Eyebrow Lamination & Tint'     => $cat['beauty'],
        ];

        foreach ($assignments as $serviceName => $category) {
            Service::where('name', $serviceName)
                ->update(['category_id' => $category->id]);
        }
    }
}
