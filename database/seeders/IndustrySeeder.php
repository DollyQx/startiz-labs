<?php

namespace Database\Seeders;

use App\Models\Industry;
use Illuminate\Database\Seeder;

class IndustrySeeder extends Seeder
{
    /**
     * Seed Startiz Labs 8 target customer industries.
     */
    public function run(): void
    {
        $industries = [
            [
                'name' => 'Startups',
                'slug' => 'startups',
                'description' => 'Fast MVP development, custom web applications, mobile platforms, and scalable tech architecture for early-stage and growing startups.',
                'icon' => 'rocket-launch',
                'display_order' => 1,
                'is_active' => true,
            ],
            [
                'name' => 'Retail / Shops',
                'slug' => 'retail-shops',
                'description' => 'Digital store presence, point-of-sale inventory tools, billing management, and customer loyalty software for retail store owners.',
                'icon' => 'building-storefront',
                'display_order' => 2,
                'is_active' => true,
            ],
            [
                'name' => 'Restaurants',
                'slug' => 'restaurants',
                'description' => 'Digital QR menus, online ordering portals, kitchen order workflow management, and billing solutions for restaurants and food businesses.',
                'icon' => 'cake',
                'display_order' => 3,
                'is_active' => true,
            ],
            [
                'name' => 'Education / Institutes',
                'slug' => 'education-institutes',
                'description' => 'Digital study note portals, online exam engines, student admission tracking, and institute management software for coaching centers and schools.',
                'icon' => 'academic-cap',
                'display_order' => 4,
                'is_active' => true,
            ],
            [
                'name' => 'E-commerce',
                'slug' => 'e-commerce',
                'description' => 'Direct-to-consumer online stores, multi-category product catalogs, secure payment gateway integrations, and order tracking systems.',
                'icon' => 'shopping-bag',
                'display_order' => 5,
                'is_active' => true,
            ],
            [
                'name' => 'Professional Services',
                'slug' => 'professional-services',
                'description' => 'Client onboarding portals, project management dashboards, automated quotation engines, and support ticketing for service firms.',
                'icon' => 'briefcase',
                'display_order' => 6,
                'is_active' => true,
            ],
            [
                'name' => 'Small & Medium Businesses',
                'slug' => 'smb',
                'description' => 'All-in-one business management platforms, automated invoice generation, payment tracking, and workflow automation tailored for SMBs.',
                'icon' => 'chart-bar',
                'display_order' => 7,
                'is_active' => true,
            ],
            [
                'name' => 'Other Business Operations',
                'slug' => 'other-business-operations',
                'description' => 'Tailored software tools, customized operational dashboards, internal messaging systems, and document repositories for specialized organizational needs.',
                'icon' => 'cog-6-tooth',
                'display_order' => 8,
                'is_active' => true,
            ],
        ];

        foreach ($industries as $industry) {
            Industry::updateOrCreate(
                ['slug' => $industry['slug']],
                $industry
            );
        }
    }
}
