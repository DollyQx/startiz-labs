@extends('layouts.public')

@section('title', 'Partner Program Terms & Conditions — Startiz Labs')
@section('meta_description', 'Official Startiz Labs Partner Program Terms & Conditions, commission qualification rules, referral attribution windows, payout conditions, and anti-fraud guidelines.')
@section('canonical', url('/partner-terms'))

@section('content')
    <!-- Header -->
    <section class="py-16 md:py-20 bg-slate-900 text-white border-b border-slate-800">
        <div class="container-custom text-center max-w-3xl">
            <span class="badge-public mb-4 bg-blue-950 text-blue-400 border border-blue-800 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider">
                Official Policy
            </span>
            <h1 class="text-3xl md:text-5xl font-extrabold text-white tracking-tight mb-4">
                Partner Program Terms & Conditions
            </h1>
            <p class="text-base md:text-lg text-slate-300 leading-relaxed">
                Clear rules, attribution guidelines, qualifying payment requirements, and anti-fraud policies governing the Startiz Labs Partner Network.
            </p>
        </div>
    </section>

    <!-- Main Terms Content -->
    <section class="py-16 md:py-20 bg-white">
        <div class="container-custom max-w-4xl space-y-12 text-slate-700 leading-relaxed text-sm md:text-base">
            <div class="p-6 rounded-2xl bg-slate-50 border border-slate-200 text-xs text-slate-600">
                <strong>Last Updated:</strong> October 2026 &bull; By applying for or participating in the Startiz Labs Partner Program, you agree to comply with the operational conditions and standards outlined below.
            </div>

            <!-- Section 1 -->
            <div class="space-y-3">
                <h2 class="text-xl md:text-2xl font-bold text-slate-900">1. Program Overview & Acceptance</h2>
                <p>
                    The Startiz Labs Partner Program allows registered individuals, consultants, and companies ("Partners") to introduce prospective clients ("Referred Clients") to Startiz Labs for digital engineering and software development services in exchange for commissions on qualifying contracts.
                </p>
            </div>

            <!-- Section 2 -->
            <div class="space-y-3">
                <h2 class="text-xl md:text-2xl font-bold text-slate-900">2. Partner Eligibility & Account Approval</h2>
                <p>
                    Registration is free and open to digital creators, agencies, and enterprise referrers. All applications are initially placed in a "Pending" review state. Startiz Labs reserves the absolute right to evaluate, approve, or reject any application based on reputational, compliance, and suitability standards.
                </p>
            </div>

            <!-- Section 3 -->
            <div class="space-y-3">
                <h2 class="text-xl md:text-2xl font-bold text-slate-900">3. Referral Attribution & Tracking Window</h2>
                <p>
                    Referrals are tracked through unique referral codes and deep links. Our tracking mechanism utilizes a standard <strong>30-day attribution window</strong>:
                </p>
                <ul class="list-disc pl-5 space-y-1.5 text-slate-600 text-sm">
                    <li>Attribution is established when a visitor accesses Startiz Labs via your unique link and creates a verified client profile or submits a qualifying project inquiry within 30 days.</li>
                    <li>Attribution operates on a <em>first-touch</em> principle: a lead cannot be attributed to multiple partners simultaneously.</li>
                    <li>Pre-existing clients who have previously engaged Startiz Labs within the prior 12 months are not eligible for new partner referral attribution.</li>
                </ul>
            </div>

            <!-- Section 4 -->
            <div class="space-y-3">
                <h2 class="text-xl md:text-2xl font-bold text-slate-900">4. Qualifying Projects & Commission Rates</h2>
                <p>
                    The default commission rate is <strong>20%</strong> of the net qualifying project value, unless a distinct enterprise rate has been contractually authorized in the Partner Profile:
                </p>
                <ul class="list-disc pl-5 space-y-1.5 text-slate-600 text-sm">
                    <li>Commissions are calculated strictly on <em>net collected revenue</em> (excluding applicable governmental taxes, third-party licensing fees, server hosting expenses, and payment processing charges).</li>
                    <li>Eligible services encompass custom website development, mobile application development, e-commerce engineering, custom ERP software, and cybersecurity audits.</li>
                </ul>
            </div>

            <!-- Section 5 -->
            <div class="space-y-3">
                <h2 class="text-xl md:text-2xl font-bold text-slate-900">5. Settlement Timelines & Payout Rules</h2>
                <p>
                    Commissions are governed through the Partner Portal and transition through distinct verification phases:
                </p>
                <ul class="list-disc pl-5 space-y-1.5 text-slate-600 text-sm">
                    <li><strong>Recorded:</strong> Created when an attributed client executes an agreed project milestone payment.</li>
                    <li><strong>Approved:</strong> Verified by administration following milestone completion and payment clearance.</li>
                    <li><strong>Payable:</strong> Cleared for disbursement according to the standard settlement schedule.</li>
                </ul>
                <div class="p-4 rounded-xl bg-amber-50 border border-amber-200 text-amber-900 text-xs">
                    <strong>Notice:</strong> Startiz Labs does not provide automated instant or guaranteed payouts. Each disbursement is verified manually by administration to safeguard against fraudulent chargebacks and uncollected milestone funds.
                </div>
            </div>

            <!-- Section 6 -->
            <div class="space-y-3">
                <h2 class="text-xl md:text-2xl font-bold text-slate-900">6. Cancellations, Refunds & Adjustments</h2>
                <p>
                    If a Referred Client project is cancelled, refunded, or subject to a banking dispute prior to completion, any unpaid commission attributable to that transaction shall be cancelled or adjusted accordingly. In the event of a post-disbursement refund, Startiz Labs may offset corresponding amounts against future partner earnings.
                </p>
            </div>

            <!-- Section 7 -->
            <div class="space-y-3">
                <h2 class="text-xl md:text-2xl font-bold text-slate-900">7. Strict Anti-Fraud Policy & Prohibited Conduct</h2>
                <p>
                    Partners must adhere strictly to fair, transparent marketing practices. The following practices result in immediate partner suspension and forfeiture of unpaid commissions:
                </p>
                <ul class="list-disc pl-5 space-y-1.5 text-slate-600 text-sm">
                    <li><strong>Self-Referral Prohibition:</strong> Partners may not refer their own companies, personal projects, or businesses in which they hold majority ownership.</li>
                    <li><strong>Spam & Misleading Advertising:</strong> Unsolicited bulk messaging, trademark infringement, bidding on Startiz Labs branded keywords in search ads, or making false technical/delivery claims.</li>
                    <li><strong>Cookie Stuffing:</strong> Forcing tracking cookies via iframes, automated bots, or deceptive redirects.</li>
                </ul>
            </div>

            <!-- Section 8 -->
            <div class="space-y-3">
                <h2 class="text-xl md:text-2xl font-bold text-slate-900">8. Program Modification & Termination</h2>
                <p>
                    Startiz Labs reserves the right to update program guidelines, adjust standard commission rates for future referrals, or terminate the Partner Program upon reasonable advance notice posted on the platform.
                </p>
            </div>

            <!-- Actions Back to Partner Hub -->
            <div class="pt-8 border-t border-slate-200 flex flex-col sm:flex-row items-center justify-between gap-4">
                <a href="{{ route('partners') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-slate-600 hover:text-slate-900">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    <span>Back to Partner Program Landing</span>
                </a>

                <div class="flex items-center gap-3">
                    <a href="{{ route('partner.register') }}" class="btn-base btn-primary btn-sm font-bold">
                        Join as a Partner
                    </a>
                    <a href="{{ route('partner.login') }}" class="btn-base btn-outline btn-sm font-semibold">
                        Partner Login
                    </a>
                </div>
            </div>
        </div>
    </section>
@endsection
