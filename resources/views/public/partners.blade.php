@extends('layouts.public')

@section('title', 'Startiz Labs Partner Program | Earn 20% Commission')
@section('meta_description', 'Join the Startiz Labs Partner Program and earn 20% commission on qualifying digital client referrals. Refer businesses needing websites, apps, and custom software.')
@section('canonical', url('/partners'))

@section('content')
    <!-- 1. HERO SECTION -->
    <section class="relative bg-slate-900 text-white pt-20 pb-24 md:pt-28 md:pb-32 overflow-hidden border-b border-slate-800">
        <!-- Background subtle lighting effects -->
        <div class="absolute inset-0 pointer-events-none">
            <div class="absolute -top-40 -left-40 w-96 h-96 bg-blue-600/15 rounded-full blur-3xl"></div>
            <div class="absolute top-1/2 -right-40 w-96 h-96 bg-amber-500/10 rounded-full blur-3xl"></div>
        </div>

        <div class="container-custom relative z-10">
            <div class="max-w-3xl mx-auto text-center space-y-6">
                <!-- Trust Badge -->
                <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-slate-800/80 border border-slate-700/80 text-amber-400 text-xs font-bold tracking-wider uppercase shadow-sm">
                    <svg class="w-4 h-4 text-amber-400" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                    </svg>
                    <span>Official Startiz Labs Partner Program</span>
                </div>

                <!-- Main Hero Headline -->
                <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold text-white tracking-tight leading-tight">
                    Earn 20% Commission by Referring Clients to Startiz Labs
                </h1>

                <!-- Supporting Text -->
                <p class="text-lg sm:text-xl text-slate-300 leading-relaxed max-w-2xl mx-auto">
                    Refer businesses that need websites, applications, software or digital solutions. When your referred client becomes a qualifying customer, earn commission through the Startiz Labs Partner Program.
                </p>

                <!-- Primary & Secondary CTAs -->
                <div class="pt-4 flex flex-col sm:flex-row items-center justify-center gap-4">
                    <a href="{{ route('partner.register') }}" class="btn-base btn-primary btn-lg w-full sm:w-auto shadow-lg shadow-blue-600/30 font-bold">
                        Join as a Partner
                    </a>
                    <a href="{{ route('partner.login') }}" class="btn-base btn-outline btn-lg w-full sm:w-auto text-white border-slate-700 hover:bg-slate-800 hover:text-white font-semibold">
                        Partner Login
                    </a>
                </div>

                <!-- Key Value Bullets -->
                <div class="pt-8 grid grid-cols-2 sm:grid-cols-3 gap-4 border-t border-slate-800/80 text-left">
                    <div class="p-3 rounded-lg bg-slate-800/40 border border-slate-700/50">
                        <div class="text-xs text-slate-400">Commission Rate</div>
                        <div class="text-lg font-extrabold text-white">20% Standard</div>
                    </div>
                    <div class="p-3 rounded-lg bg-slate-800/40 border border-slate-700/50">
                        <div class="text-xs text-slate-400">Attribution Window</div>
                        <div class="text-lg font-extrabold text-white">30-Day Cookies</div>
                    </div>
                    <div class="col-span-2 sm:col-span-1 p-3 rounded-lg bg-slate-800/40 border border-slate-700/50">
                        <div class="text-xs text-slate-400">Partner Portal</div>
                        <div class="text-lg font-extrabold text-white">Real-Time Tracking</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 2. HOW IT WORKS SECTION -->
    <section class="py-20 bg-white border-b border-slate-200">
        <div class="container-custom">
            <x-section-heading
                badge="Simple 4-Step Process"
                title="How It Works"
                subtitle="From sharing your referral link to tracking payouts, our partner workflow is completely transparent."
            />

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                @foreach($steps as $step)
                    <div class="relative p-6 rounded-2xl bg-slate-50 border border-slate-200 hover:border-blue-400 hover:shadow-md transition-all flex flex-col justify-between">
                        <div>
                            <div class="w-12 h-12 rounded-xl bg-blue-600 text-white font-extrabold text-lg flex items-center justify-center mb-6 shadow-md shadow-blue-600/20">
                                {{ $step['number'] }}
                            </div>
                            <h3 class="text-xl font-bold text-slate-900 mb-2">{{ $step['title'] }}</h3>
                            <p class="text-sm font-semibold text-blue-600 mb-2">{{ $step['description'] }}</p>
                            <p class="text-xs text-slate-600 leading-relaxed">{{ $step['detail'] }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- 3. 20% COMMISSION SECTION (WITH MARKETING EXAMPLE) -->
    <section class="py-20 bg-slate-50 border-b border-slate-200">
        <div class="container-custom max-w-5xl">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 items-center">
                <div class="lg:col-span-7 space-y-5">
                    <span class="badge-public bg-blue-100 text-blue-700 border border-blue-200">
                        Transparent Earnings
                    </span>
                    <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight">
                        Earn 20% on Every Qualifying Digital Contract
                    </h2>
                    <p class="text-base text-slate-700 leading-relaxed">
                        Startiz Labs partners receive a default standard commission rate of <strong>20%</strong> on qualifying project revenues. Whether you introduce a local retail company looking for an online store or an enterprise requiring custom software automation, your commissions grow with project scope.
                    </p>
                    <ul class="space-y-2.5 text-sm text-slate-700">
                        <li class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-emerald-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <span>Standard default rate of 20% applied across all qualifying services</span>
                        </li>
                        <li class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-emerald-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <span>Calculated transparently on net client project payments</span>
                        </li>
                        <li class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-emerald-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <span>Itemized commission vouchers accessible in your partner ledger</span>
                        </li>
                    </ul>
                </div>

                <!-- Illustrative Commission Example Card -->
                <div class="lg:col-span-5">
                    <div class="p-8 rounded-3xl bg-slate-900 text-white shadow-xl border border-slate-800 space-y-6">
                        <div class="flex items-center justify-between border-b border-slate-800 pb-4">
                            <span class="text-xs font-bold text-amber-400 uppercase tracking-wider">Example Calculation</span>
                            <span class="text-xs font-semibold px-2 py-0.5 rounded bg-blue-500/20 text-blue-300 border border-blue-500/30">20% Rate</span>
                        </div>

                        <div class="space-y-4">
                            <div class="flex justify-between items-center text-sm">
                                <span class="text-slate-400">Project Value:</span>
                                <span class="font-bold text-lg text-white">₹50,000</span>
                            </div>
                            <div class="flex justify-between items-center text-sm">
                                <span class="text-slate-400">Commission Rate:</span>
                                <span class="font-bold text-emerald-400">20%</span>
                            </div>
                            <div class="pt-4 border-t border-slate-800 flex justify-between items-center">
                                <span class="text-sm font-semibold text-slate-300">Commission:</span>
                                <span class="text-2xl font-extrabold text-amber-400">₹10,000</span>
                            </div>
                        </div>

                        <div class="p-3 rounded-xl bg-slate-800/80 border border-slate-700/60 text-xs text-slate-400 leading-relaxed">
                            <strong class="text-slate-300">Note:</strong> Figures shown above represent an illustrative marketing example. Exact commissions correspond to actual qualifying contract values and verified milestone receipts under program terms.
                        </div>

                        <a href="{{ route('partner.register') }}" class="btn-base btn-primary w-full text-center font-bold">
                            Join as a Partner
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 4. WHAT CAN YOU REFER? SECTION -->
    <section class="py-20 bg-white border-b border-slate-200">
        <div class="container-custom">
            <x-section-heading
                badge="Solutions Portfolio"
                title="What Can You Refer?"
                subtitle="Refer businesses requiring any of our digital solutions and professional engineering capabilities."
            />

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach($serviceCategories as $service)
                    <div class="p-6 rounded-2xl bg-slate-50 border border-slate-200 hover:border-slate-300 hover:shadow-md transition-all flex flex-col justify-between">
                        <div class="space-y-3">
                            <div class="flex items-center justify-between">
                                <h3 class="text-lg font-bold text-slate-900">{{ $service['name'] }}</h3>
                                <span class="text-[11px] font-bold px-2 py-0.5 rounded-full bg-blue-100 text-blue-700">
                                    {{ $service['tag'] }}
                                </span>
                            </div>
                            <p class="text-sm text-slate-600 leading-relaxed">
                                {{ $service['description'] }}
                            </p>
                        </div>
                        <div class="pt-4 mt-4 border-t border-slate-200 text-xs font-semibold text-blue-600 flex items-center justify-between">
                            <span>Eligible for 20% Commission</span>
                            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- 5. PARTNER BENEFITS SECTION -->
    <section class="py-20 bg-slate-50 border-b border-slate-200">
        <div class="container-custom">
            <x-section-heading
                badge="Partner Tooling"
                title="Partner Benefits"
                subtitle="Everything you need to introduce clients, track progress, and manage your commissions seamlessly."
            />

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($benefits as $benefit)
                    <div class="p-6 rounded-2xl bg-white border border-slate-200 hover:border-blue-300 hover:shadow-sm transition-all space-y-2">
                        <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center font-bold mb-3 border border-blue-100">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                        <h3 class="text-base font-bold text-slate-900">{{ $benefit['title'] }}</h3>
                        <p class="text-xs text-slate-600 leading-relaxed">{{ $benefit['description'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- 6. PAYOUT PROCESS SECTION -->
    <section class="py-20 bg-white border-b border-slate-200">
        <div class="container-custom max-w-4xl">
            <div class="p-8 sm:p-12 rounded-3xl bg-slate-900 text-white space-y-6">
                <span class="badge-public bg-amber-500/20 text-amber-300 border border-amber-500/30">
                    Transparent Settlement
                </span>
                <h2 class="text-2xl sm:text-3xl font-extrabold text-white tracking-tight">
                    How Commissions & Payouts Are Handled
                </h2>
                <p class="text-sm sm:text-base text-slate-300 leading-relaxed">
                    Commissions are managed directly through the Partner Portal and become payable according to applicable commission and milestone payment rules. We believe in clear, reliable accounting without misleading guarantees.
                </p>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-2">
                    <div class="p-4 rounded-xl bg-slate-800 border border-slate-700/60 space-y-1">
                        <div class="text-xs text-amber-400 font-semibold">1. Milestone Confirmed</div>
                        <p class="text-xs text-slate-300">Client payment is received and cleared for the agreed project deliverables.</p>
                    </div>
                    <div class="p-4 rounded-xl bg-slate-800 border border-slate-700/60 space-y-1">
                        <div class="text-xs text-blue-400 font-semibold">2. Administrative Approval</div>
                        <p class="text-xs text-slate-300">The commission voucher is reviewed, verified, and officially marked payable.</p>
                    </div>
                    <div class="p-4 rounded-xl bg-slate-800 border border-slate-700/60 space-y-1">
                        <div class="text-xs text-emerald-400 font-semibold">3. Direct Disbursement</div>
                        <p class="text-xs text-slate-300">Funds are transferred to your saved Bank Account or UPI handle with transaction records.</p>
                    </div>
                </div>

                <div class="p-4 rounded-xl bg-slate-800/80 border border-slate-700 text-xs text-slate-400 leading-relaxed">
                    <strong>Payment Terms:</strong> Payouts are not automated instant credits; each transaction undergoes standard accounting verification to protect against cancellations, chargebacks, and invalid referrals before disbursement.
                </div>
            </div>
        </div>
    </section>

    <!-- 7. FREQUENTLY ASKED QUESTIONS (FAQ) -->
    <section class="py-20 bg-slate-50 border-b border-slate-200">
        <div class="container-custom max-w-4xl">
            <x-section-heading
                badge="Got Questions?"
                title="Frequently Asked Questions"
                subtitle="Clear answers about referral tracking, commission qualification, and payout processing."
            />

            <div class="space-y-4">
                @foreach($faqs as $faq)
                    <div class="p-6 rounded-2xl bg-white border border-slate-200 hover:border-slate-300 shadow-sm transition-all space-y-2">
                        <h3 class="text-base font-bold text-slate-900 flex items-start gap-3">
                            <span class="w-6 h-6 rounded-full bg-blue-100 text-blue-700 text-xs font-black flex items-center justify-center flex-shrink-0 mt-0.5">?</span>
                            <span>{{ $faq['question'] }}</span>
                        </h3>
                        <p class="text-sm text-slate-600 leading-relaxed pl-9">
                            {{ $faq['answer'] }}
                        </p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- 8. TERMS / TRUST SECTION -->
    <section class="py-14 bg-white border-b border-slate-200">
        <div class="container-custom max-w-3xl text-center space-y-4">
            <div class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-slate-100 text-slate-600 mb-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                </svg>
            </div>
            <h3 class="text-lg font-bold text-slate-900">Program Integrity & Anti-Fraud Compliance</h3>
            <p class="text-sm text-slate-600 leading-relaxed">
                Partner commissions are subject to the Startiz Labs Partner Terms & Conditions, referral attribution rules, qualifying payment conditions and anti-fraud policies.
            </p>
            <div class="pt-2">
                <a href="{{ route('partner.terms') }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-blue-600 hover:text-blue-700 underline">
                    <span>Read Complete Partner Program Terms & Conditions</span>
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                    </svg>
                </a>
            </div>
        </div>
    </section>

    <!-- 9. FINAL CALL TO ACTION (CTA) -->
    <section class="py-20 bg-slate-900 text-white relative overflow-hidden">
        <div class="container-custom relative z-10 text-center max-w-3xl mx-auto space-y-6">
            <span class="badge-public bg-blue-500/20 text-blue-300 border border-blue-500/30">
                Join Our Growing Partner Network
            </span>
            <h2 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold text-white tracking-tight">
                Start earning with Startiz Labs
            </h2>
            <p class="text-base sm:text-lg text-slate-300 leading-relaxed max-w-xl mx-auto">
                Partner with an established engineering team delivering websites, apps, and digital platforms across industries. Free to join with immediate referral tracking.
            </p>
            <div class="pt-4 flex flex-col sm:flex-row items-center justify-center gap-4">
                <a href="{{ route('partner.register') }}" class="btn-base btn-primary btn-lg w-full sm:w-auto font-bold shadow-lg shadow-blue-600/30">
                    Join as a Partner
                </a>
                <a href="{{ route('partner.login') }}" class="btn-base btn-outline btn-lg w-full sm:w-auto text-white border-slate-700 hover:bg-slate-800 hover:text-white font-semibold">
                    Partner Login
                </a>
            </div>
        </div>
    </section>
@endsection
