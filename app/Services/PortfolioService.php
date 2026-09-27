<?php

namespace App\Services;

class PortfolioService
{
    /**
     * Get the 5 official Startiz Labs portfolio projects.
     */
    public static function getProjects(): array
    {
        return [
            'notes-study' => [
                'slug' => 'notes-study',
                'title' => 'Notes Study (NotesStudy.online)',
                'client' => 'Notes Study Team',
                'category' => 'Education / EdTech',
                'short_description' => 'Digital study notes and educational resource platform providing organized learning materials for students.',
                'description' => 'Notes Study is an online educational platform designed to streamline study material distribution. Startiz Labs engineered a high-performance digital repository supporting multi-category subject organization, fast document preview streaming, search filtering, and responsive mobile rendering for seamless learning.',
                'services_provided' => [
                    'Web Application Architecture',
                    'Digital Content & PDF Streaming System',
                    'Subject & Category Navigation Engine',
                    'Responsive Mobile Learning UI',
                ],
                'technologies' => ['Laravel API / Next.js', 'PostgreSQL', 'Tailwind CSS', 'Secure Storage'],
                'image_placeholder' => 'notes_study_preview.webp',
            ],
            'zomoggy' => [
                'slug' => 'zomoggy',
                'title' => 'Zomoggy',
                'client' => 'Zomoggy Food & Sweets',
                'category' => 'Food & Retail Platform',
                'short_description' => 'Digital ordering and management platform built for food, cake, and bakery businesses.',
                'description' => 'Zomoggy is a modern food and sweets commerce solution engineered to simplify digital product displays and customer orders. The system features dynamic menu catalogs, automated cart calculation, secure payment gateway processing, order status notifications, and daily sales dashboards.',
                'services_provided' => [
                    'Custom E-Commerce Storefront',
                    'Digital Menu & Product Catalog',
                    'Order Workflow & Receipt Generation',
                    'Razorpay Payment Gateway Integration',
                ],
                'technologies' => ['Custom Web Platform', 'Blade / JavaScript', 'MySQL', 'Razorpay SDK'],
                'image_placeholder' => 'zomoggy_preview.webp',
            ],
            'gurumantra' => [
                'slug' => 'gurumantra',
                'title' => 'Gurumantra',
                'client' => 'Gurumantra Academy',
                'category' => 'Education & Live Classes',
                'short_description' => 'Comprehensive learning platform featuring multi-role workflows for students, teachers, and management.',
                'description' => 'Gurumantra provides interactive online learning and class management. Startiz Labs structured dedicated multi-role user dashboards enabling teachers to post study modules, administrators to manage batches and student enrollment, and students to attend live sessions and access course archives.',
                'services_provided' => [
                    'Multi-Role Portal Architecture (Student, Teacher, Admin)',
                    'Course & Batch Management Engine',
                    'Live Class Scheduling & Media Integration',
                    'Attendance & Progress Tracking',
                ],
                'technologies' => ['Laravel Framework', 'Role-Based Access Control', 'Tailwind CSS', 'RESTful APIs'],
                'image_placeholder' => 'gurumantra_preview.webp',
            ],
            'gm-library' => [
                'slug' => 'gm-library',
                'title' => 'GM Library',
                'client' => 'GM Educational Trust',
                'category' => 'Digital Library Systems',
                'short_description' => 'Digital library and resource cataloging platform engineered for institutional book tracking.',
                'description' => 'GM Library is a specialized digital library system built for institutional resource management. The platform manages book inventories, digital edition uploads, member issue/return records, automated overdue notices, and catalog search indexing.',
                'services_provided' => [
                    'Digital Resource Cataloging',
                    'Book Inventory & Issue Tracking',
                    'Member Management & Activity Logs',
                    'Automated Search & Filter Workflows',
                ],
                'technologies' => ['Laravel Core', 'Eloquent ORM', 'SQLite / MySQL', 'PDF Document Viewer'],
                'image_placeholder' => 'gm_library_preview.webp',
            ],
            'gm-code-lab' => [
                'slug' => 'gm-code-lab',
                'title' => 'GM Code Lab Platform',
                'client' => 'Software Enterprise Operations',
                'category' => 'Enterprise CRM & Client Portal',
                'short_description' => 'All-in-one business management, client onboarding, quotation, invoicing, and support ticketing platform.',
                'description' => 'The GM Code Lab system is a comprehensive software agency platform and client relationship manager. It integrates public solution showcases, lead CRM pipelines, automated multi-line item quotations, invoice processing, Razorpay online payments, client support tickets, project change requests, and digital sign-off approvals.',
                'services_provided' => [
                    'Full-Stack CRM & Business Management Platform',
                    'Protected Client Portal & Project Timelines',
                    'Automated Quotation & Financial Ledger Engine',
                    'Support Desk, Change Request & Document Security',
                ],
                'technologies' => ['Laravel 12', 'Tailwind CSS v4', 'Razorpay Webhook Integration', 'PHPUnit Test Suite'],
                'image_placeholder' => 'gm_code_lab_preview.webp',
            ],
        ];
    }

    /**
     * Get structured client testimonials representing past projects.
     * Note: Includes clear structural review data for Notes Study, Zomoggy, Gurumantra, GM Library, and GM Code Lab.
     */
    public static function getTestimonials(): array
    {
        return [
            [
                'project_name' => 'Notes Study (NotesStudy.online)',
                'company' => 'Notes Study Platform',
                'client_name' => 'Notes Study Team',
                'role' => 'Founding Team',
                'rating' => 5,
                'quote' => 'Startiz Labs built a fast, clean, and reliable platform for our digital study notes. Document previews load instantly and our students love the mobile user experience.',
                'requires_verbatim_quote_approval' => true,
            ],
            [
                'project_name' => 'Zomoggy',
                'company' => 'Zomoggy Food & Sweets',
                'client_name' => 'Management Team',
                'role' => 'Operations Director',
                'rating' => 5,
                'quote' => 'The digital ordering system and digital menu created by Startiz Labs significantly improved our daily store ordering workflow and customer satisfaction.',
                'requires_verbatim_quote_approval' => true,
            ],
            [
                'project_name' => 'Gurumantra',
                'company' => 'Gurumantra Academy',
                'client_name' => 'Academic Lead',
                'role' => 'Head of Education',
                'rating' => 5,
                'quote' => 'Managing student batches, teacher uploads, and live class workflows became seamless with the multi-role platform engineered by Startiz Labs.',
                'requires_verbatim_quote_approval' => true,
            ],
            [
                'project_name' => 'GM Library',
                'company' => 'GM Educational Trust',
                'client_name' => 'Library Committee',
                'role' => 'System Administrator',
                'rating' => 5,
                'quote' => 'Our library resource tracking, book search, and member access logs are organized effortlessly thanks to the custom digital library architecture.',
                'requires_verbatim_quote_approval' => true,
            ],
            [
                'project_name' => 'GM Code Lab Platform',
                'company' => 'Enterprise Operations',
                'client_name' => 'Operations Lead',
                'role' => 'Lead Architect',
                'rating' => 5,
                'quote' => 'The enterprise CRM and client portal platform engineered with precision provided a complete, secure foundation for managing software delivery workflows.',
                'requires_verbatim_quote_approval' => true,
            ],
        ];
    }

    /**
     * Find a project by slug.
     */
    public static function findProject(string $slug): ?array
    {
        $projects = static::getProjects();
        return $projects[$slug] ?? null;
    }
}
