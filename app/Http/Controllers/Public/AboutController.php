<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class AboutController extends Controller
{
    public function index(): View
    {
        $founder = [
            'name' => 'Dolly Mishra',
            'role' => 'Founder & Technology Leader',
            'short_intro' => 'Leading software engineering, product execution, cybersecurity, and digital automation at Startiz Labs.',
            'biography' => 'Dolly Mishra is the Founder and Technology Leader at Startiz Labs. Specializing in software development, application architecture, cybersecurity, and business automation, Dolly leads the engineering of custom digital solutions tailored to startups, small businesses, restaurants, institutes, and growing enterprises. With a practical, problem-solving approach to technology, Dolly ensures every digital product built by Startiz Labs emphasizes robust architecture, data security, fast performance, and tangible business value.',
            'expertise' => [
                'Software & Web Application Architecture',
                'Mobile Product Engineering (iOS & Android)',
                'Cybersecurity & Secure Session Management',
                'AI & Custom Business Process Automation',
                'Product Execution & Operational Scalability',
            ],
            'philosophy' => 'We believe technology should be practical, clean, and directly aligned with real business operations. Startiz Labs is dedicated to providing one reliable place for complete digital solutions.',
        ];

        return view('public.about', compact('founder'));
    }
}
