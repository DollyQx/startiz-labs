<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    /**
     * Seed Startiz Labs 11 core service offerings.
     */
    public function run(): void
    {
        $services = [
            [
                'name' => 'Website Development',
                'slug' => 'website-development',
                'short_description' => 'Modern, high-converting, responsive websites tailored for business growth and customer engagement.',
                'description' => 'We design and develop fast, secure, and SEO-optimized websites built for startups, retailers, restaurants, institutes, and enterprise businesses. Our websites feature modern responsive UX, glassmorphic UI elements, high page load speed, and seamless lead conversion funnels.',
                'icon' => 'globe',
                'display_order' => 1,
                'is_active' => true,
            ],
            [
                'name' => 'Mobile App Development',
                'slug' => 'mobile-app-development',
                'short_description' => 'Native and cross-platform iOS & Android mobile apps engineered for speed, usability, and scale.',
                'description' => 'Turn your business ideas into sleek iOS and Android mobile applications. We build intuitive mobile user interfaces, real-time push notification pipelines, offline sync capability, secure payment gateways, and backend RESTful API integrations.',
                'icon' => 'device-mobile',
                'display_order' => 2,
                'is_active' => true,
            ],
            [
                'name' => 'AI Automation',
                'slug' => 'ai-automation',
                'short_description' => 'Custom AI chatbots, intelligent workflow automation, and predictive data processing for operations.',
                'description' => 'Integrate cutting-edge Artificial Intelligence into your daily business operations. We build custom conversational AI assistants, automated document processing tools, smart customer support routing, and data analytics engines to eliminate repetitive manual work.',
                'icon' => 'cpu-chip',
                'display_order' => 3,
                'is_active' => true,
            ],
            [
                'name' => 'CRM Development',
                'slug' => 'crm-development',
                'short_description' => 'Custom customer relationship management tools to track leads, manage clients, and drive sales.',
                'description' => 'Manage your entire sales pipeline, client onboarding, support ticketing, quotation generation, and team performance in one unified CRM platform built specifically around your business rules.',
                'icon' => 'users',
                'display_order' => 4,
                'is_active' => true,
            ],
            [
                'name' => 'E-commerce Solutions',
                'slug' => 'e-commerce-solutions',
                'short_description' => 'Scalable online store platforms with digital payments, inventory tracking, and order management.',
                'description' => 'Launch and scale your online retail business with custom e-commerce web applications. Features include dynamic product catalogs, automated cart management, Razorpay payment gateway integration, automated invoice receipts, and inventory control.',
                'icon' => 'shopping-cart',
                'display_order' => 5,
                'is_active' => true,
            ],
            [
                'name' => 'Restaurant Solutions',
                'slug' => 'restaurant-solutions',
                'short_description' => 'Digital menus, online ordering systems, kitchen management, and billing software for food businesses.',
                'description' => 'Digitalize your restaurant, bakery, or cloud kitchen. We build mobile-friendly QR digital menus, online food ordering systems, kitchen display workflows, table reservation management, and automated daily sales reporting.',
                'icon' => 'cake',
                'display_order' => 6,
                'is_active' => true,
            ],
            [
                'name' => 'Institute / Education Solutions',
                'slug' => 'education-solutions',
                'short_description' => 'Comprehensive learning platforms, live class portals, student management, and digital libraries.',
                'description' => 'Complete digital infrastructure for coaching institutes, schools, and online educators. Includes student admission management, online examination engines, digital study note repositories, fee tracking, and live video class integration.',
                'icon' => 'academic-cap',
                'display_order' => 7,
                'is_active' => true,
            ],
            [
                'name' => 'Business Management Software',
                'slug' => 'business-management-software',
                'short_description' => 'All-in-one ERP platforms, milestone management, financial ledgers, and staff operations control.',
                'description' => 'Streamline operational workflows with customized business management systems. Gain real-time visibility into project status, financial performance, staff task allocation, change requests, and client sign-offs.',
                'icon' => 'briefcase',
                'display_order' => 8,
                'is_active' => true,
            ],
            [
                'name' => 'Custom Software Development',
                'slug' => 'custom-software-development',
                'short_description' => 'Bespoke web applications, APIs, and domain-specific digital tools built to match your operational logic.',
                'description' => 'When off-the-shelf software falls short, we engineer custom software solutions from scratch. We design clean database schemas, role-based security layers, scalable API architecture, and responsive user portals tailored to your exact specifications.',
                'icon' => 'code-bracket',
                'display_order' => 9,
                'is_active' => true,
            ],
            [
                'name' => 'Business Automation',
                'slug' => 'business-automation',
                'short_description' => 'Automated document processing, email notifications, status sync, and operational workflow speed.',
                'description' => 'Eliminate administrative bottlenecks by automating routine tasks. We automate quotation approvals, recurring invoice distribution, payment receipt issuing, client status notifications, and multi-department task assignments.',
                'icon' => 'sparkles',
                'display_order' => 10,
                'is_active' => true,
            ],
            [
                'name' => 'Cybersecurity / Technology Solutions',
                'slug' => 'cybersecurity-solutions',
                'short_description' => 'Security audits, data isolation strategies, secure auth architecture, and vulnerability assessments.',
                'description' => 'Protect your digital business assets with enterprise security practices. We implement server-side role validation, tenant data isolation, encrypted session handling, private storage download streams, and code security audits.',
                'icon' => 'shield-check',
                'display_order' => 11,
                'is_active' => true,
            ],
        ];

        foreach ($services as $service) {
            Service::updateOrCreate(
                ['slug' => $service['slug']],
                $service
            );
        }
    }
}
