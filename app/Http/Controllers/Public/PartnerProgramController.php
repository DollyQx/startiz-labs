<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class PartnerProgramController extends Controller
{
    /**
     * Display the public Partner Program landing page.
     */
    public function index(): View
    {
        $defaultCommissionRate = (float) config('partner.default_commission_rate', 20.00);

        $steps = [
            [
                'number' => '01',
                'title' => 'Join',
                'description' => 'Create your free Startiz Labs Partner account.',
                'detail' => 'Complete a simple registration form to get immediate access to your partner pending portal and referral assets.',
            ],
            [
                'number' => '02',
                'title' => 'Refer',
                'description' => 'Share your unique referral link with businesses.',
                'detail' => 'Distribute your personalized tracking link across your network, clients, social channels, or direct consultations.',
            ],
            [
                'number' => '03',
                'title' => 'Convert',
                'description' => 'Your referred client contacts Startiz Labs and becomes a qualifying customer.',
                'detail' => 'Our engineering team consults, scopes, and executes the digital project under standard commercial milestones.',
            ],
            [
                'number' => '04',
                'title' => 'Earn',
                'description' => 'Receive the applicable commission through the Partner Portal.',
                'detail' => 'Track your recorded, approved, and payable earnings with transparent financial ledgers and seamless payouts.',
            ],
        ];

        $serviceCategories = [
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

        $benefits = [
            [
                'title' => 'Unique Referral Link',
                'description' => 'Receive personalized tracking links and deep links to start attributing visitors immediately.',
            ],
            [
                'title' => 'Referral Tracking',
                'description' => 'Live tracking of clicks, registration events, and customer pipeline status in real time.',
            ],
            [
                'title' => 'Partner Dashboard',
                'description' => 'A purpose-built portal to manage all your partner activities, links, and profile details.',
            ],
            [
                'title' => 'Commission Tracking',
                'description' => 'Detailed financial breakdowns of pending, approved, and payable commissions with itemized vouchers.',
            ],
            [
                'title' => 'Leaderboard',
                'description' => 'Track your performance across monthly, quarterly, and all-time network partner rankings.',
            ],
            [
                'title' => 'Payout Management',
                'description' => 'Manage verified bank accounts and UPI IDs for hassle-free payment settlements.',
            ],
            [
                'title' => 'Transparent Referral Status',
                'description' => 'Clear milestone visibility as your referred leads progress from consultation to contract.',
            ],
        ];

        $faqs = [
            [
                'question' => 'How does the referral program work?',
                'answer' => 'Sign up for a free Startiz Labs partner account. Once registered, you receive a unique referral code and tracking links. When you share this link with businesses seeking digital solutions and they become qualifying paying clients, you earn a commission on the net project value.',
            ],
            [
                'question' => 'How much commission can I earn?',
                'answer' => 'The standard default commission rate is 20% on qualifying completed project payments. For instance, a referred client project with a qualifying value of ₹50,000 yields a ₹10,000 commission. There is no ceiling on the total volume of qualifying referrals you can introduce.',
            ],
            [
                'question' => 'How do I track referrals?',
                'answer' => 'All tracking is centralized within your dedicated Partner Portal. You can monitor link visitors, new leads, converted contracts, commission vouchers, and payout audit logs in real time.',
            ],
            [
                'question' => 'When does a commission become payable?',
                'answer' => 'A commission is recorded when a referred client makes an official project milestone payment. Following standard verification and administrative review, the commission is approved and marked payable according to our milestone settlement schedule.',
            ],
            [
                'question' => 'What happens if a client cancels/refunds?',
                'answer' => 'Commissions are calculated strictly on actual, qualifying, non-refunded payments. If a contract is cancelled or refunded prior to project milestone completion, any pending commission for that portion is adjusted accordingly to preserve program integrity.',
            ],
            [
                'question' => 'Can I refer myself?',
                'answer' => 'No. Self-referrals are strictly prohibited under our anti-fraud policy. Referrals must be genuine, independent third-party businesses or individuals seeking custom digital solutions.',
            ],
            [
                'question' => 'How are payouts processed?',
                'answer' => 'Payouts are disbursed directly via Bank Transfer (NEFT/IMPS) or UPI according to the verified payout preferences saved in your Partner Profile. Settlements are processed once commissions are marked payable by administration.',
            ],
        ];

        return view('public.partners', compact(
            'defaultCommissionRate',
            'steps',
            'serviceCategories',
            'benefits',
            'faqs'
        ));
    }

    /**
     * Display the Partner Program Terms & Conditions page.
     */
    public function terms(): View
    {
        return view('public.partner-terms');
    }
}
