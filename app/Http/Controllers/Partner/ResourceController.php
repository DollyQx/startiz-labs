<?php

namespace App\Http\Controllers\Partner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ResourceController extends Controller
{
    public function index(Request $request): View
    {
        $partner = $request->user()->partnerProfile;
        $referralUrl = $partner->referralUrl();

        $messages = [
            'whatsapp' => "Hi! If your business needs a website, web application, mobile app, e-commerce solution or custom software, Startiz Labs can help build it. You can explore their services here:\n" . $referralUrl,
            'telegram' => "Hey there! Thinking about upgrading your tech or launching a new product? Whether it's a sleek website, custom web application, or mobile app, Startiz Labs builds high-performance digital solutions. Check out what they do here:\n" . $referralUrl,
            'linkedin' => "Startiz Labs designs and delivers bespoke digital solutions—from enterprise web platforms and custom software to mobile applications and e-commerce. If your organization is planning its next technology initiative, explore their portfolio and capabilities here:\n" . $referralUrl,
        ];

        $services = [
            [
                'name' => 'Website Development',
                'description' => 'High-converting corporate websites, brand landing pages, and responsive web portals.',
                'tag' => 'High Demand',
            ],
            [
                'name' => 'Web Applications',
                'description' => 'Custom cloud platforms, SaaS products, database management systems, and client portals.',
                'tag' => 'Enterprise',
            ],
            [
                'name' => 'Mobile Applications',
                'description' => 'Native and cross-platform iOS and Android mobile solutions built for scale and retention.',
                'tag' => 'iOS & Android',
            ],
            [
                'name' => 'E-commerce',
                'description' => 'Scalable online stores, retail catalogs, secure payment gateways, and checkout funnels.',
                'tag' => 'Retail & B2B',
            ],
            [
                'name' => 'Custom Software',
                'description' => 'Bespoke ERP, internal operations software, hostel/institute management, and workflow automation.',
                'tag' => 'Tailored',
            ],
            [
                'name' => 'Cybersecurity',
                'description' => 'Vulnerability audits, code hardening, data privacy compliance, and secure session management.',
                'tag' => 'Audit & Hardening',
            ],
            [
                'name' => 'Other Digital Solutions',
                'description' => 'AI automation workflows, legacy software modernization, and custom API integrations.',
                'tag' => 'Automation',
            ],
        ];

        return view('partner.resources', compact('partner', 'referralUrl', 'messages', 'services'));
    }
}
