@extends('layouts.public')

@section('title', 'Startiz Labs — One Place for Your Complete Digital Business Solution')
@section('meta_description', 'Startiz Labs builds websites, mobile apps, custom software, AI automation, CRM systems, e-commerce, restaurant software, and institute management for ambitious businesses.')

@section('content')
    <!-- 1. HERO SECTION -->
    <section class="relative bg-slate-900 text-white pt-16 pb-24 lg:pt-24 lg:pb-32 overflow-hidden border-b border-slate-800">
        <div class="absolute inset-0 bg-[radial-gradient(#1e293b_1px,transparent_1px)] [background-size:16px_16px] opacity-40 pointer-events-none"></div>
        <div class="container-custom relative z-10">
            <div class="max-w-4xl mx-auto text-center">
                <span class="badge-public mb-6 inline-block bg-blue-950 text-blue-400 border border-blue-800/60 px-4 py-1.5 rounded-full text-xs font-bold uppercase tracking-wider">
                    One Place for Your Complete Digital Business Solution
                </span>
                
                <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold text-white tracking-tight leading-tight mb-8">
                    Build Websites, Mobile Apps & AI Automation for Your Business
                </h1>
                
                <p class="text-lg sm:text-xl text-slate-300 max-w-2xl mx-auto mb-10 leading-relaxed">
                    Startiz Labs provides digital solutions in one place for startups, retailers, restaurants, coaching institutes, and organizations requiring modern technology.
                </p>
                
                <div class="flex flex-col sm:flex-row items-center justify-center gap-4 mb-16">
                    <a href="{{ route('start-project') }}" class="btn-base btn-primary btn-lg w-full sm:w-auto">
                        Start Your Project
                    </a>
                    <a href="{{ route('portfolio.index') }}" class="btn-base btn-outline btn-lg w-full sm:w-auto text-white border-slate-700 hover:bg-slate-800">
                        View Past Work
                    </a>
                </div>

                <!-- Core Capabilities Pills -->
                <div class="pt-8 border-t border-slate-800/80 grid grid-cols-2 sm:grid-cols-4 gap-4 text-xs font-semibold text-slate-400 uppercase tracking-wider">
                    <div class="flex items-center justify-center gap-2">
                        <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Website & Mobile Apps
                    </div>
                    <div class="flex items-center justify-center gap-2">
                        <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        AI & Automation
                    </div>
                    <div class="flex items-center justify-center gap-2">
                        <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Custom CRM & ERP
                    </div>
                    <div class="flex items-center justify-center gap-2">
                        <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Retail & Institute Tech
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 2. TRUSTED / PAST WORK HIGHLIGHT -->
    <section class="py-10 bg-slate-950 border-b border-slate-900">
        <div class="container-custom text-center">
            <p class="text-xs font-bold uppercase tracking-widest text-slate-400 mb-6">
                Proven Technology Behind Successful Platforms & Solutions
            </p>
            <div class="flex flex-wrap items-center justify-center gap-8 md:gap-14 text-slate-300 font-bold text-sm md:text-base">
                <div class="flex items-center gap-2 hover:text-white transition-colors">
                    <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                    <span>Notes Study</span>
                </div>
                <div class="flex items-center gap-2 hover:text-white transition-colors">
                    <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                    <span>Zomoggy</span>
                </div>
                <div class="flex items-center gap-2 hover:text-white transition-colors">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                    <span>Gurumantra</span>
                </div>
                <div class="flex items-center gap-2 hover:text-white transition-colors">
                    <span class="w-2 h-2 rounded-full bg-purple-500"></span>
                    <span>GM Library</span>
                </div>
                <div class="flex items-center gap-2 hover:text-white transition-colors">
                    <span class="w-2 h-2 rounded-full bg-indigo-500"></span>
                    <span>GM Code Lab</span>
                </div>
            </div>
        </div>
    </section>

    <!-- 3. SERVICES SECTION -->
    <section class="py-20 md:py-28 bg-white">
        <div class="container-custom">
            <x-section-heading 
                badge="Digital Solutions"
                title="Complete Services for Modern Businesses"
                subtitle="We build digital products from scratch to streamline your operations, drive sales, and automate daily tasks."
            />

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach($services as $service)
                    <x-service-card 
                        :title="$service->name" 
                        :description="$service->short_description ?? 'Tailored digital solutions engineered for high business performance.'"
                        :slug="$service->slug"
                    />
                @endforeach
            </div>

            <div class="mt-12 text-center">
                <a href="{{ route('services.index') }}" class="btn-base btn-outline btn-lg">
                    Explore All Services &rarr;
                </a>
            </div>
        </div>
    </section>

    <!-- 4. INDUSTRIES / TARGET CUSTOMERS -->
    <section class="py-20 md:py-28 bg-slate-50 border-y border-slate-200">
        <div class="container-custom">
            <x-section-heading 
                badge="Target Customers"
                title="Specialized Solutions for Every Industry"
                subtitle="We deliver custom digital tools designed specifically for the unique workflows of your business sector."
            />

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach($industries as $industry)
                    <x-industry-card 
                        :title="$industry->name"
                        :description="$industry->description ?? 'Tailored software tools engineered for business efficiency.'"
                        :slug="$industry->slug"
                    />
                @endforeach
            </div>

            <div class="mt-12 text-center">
                <a href="{{ route('industries.index') }}" class="btn-base btn-secondary btn-lg">
                    View All Target Industries &rarr;
                </a>
            </div>
        </div>
    </section>

    <!-- 5. WHY STARTIZ LABS -->
    <section class="py-20 md:py-28 bg-white">
        <div class="container-custom">
            <x-section-heading 
                badge="Why Choose Us"
                title="One Place for Your Complete Digital Solution"
                subtitle="We do not build generic templates. We partner with you to engineer durable digital assets."
            />

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="p-8 rounded-2xl bg-slate-50 border border-slate-200">
                    <div class="w-12 h-12 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center mb-6 font-extrabold text-lg">
                        01
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mb-3">Custom Architecture</h3>
                    <p class="text-sm text-slate-600 leading-relaxed">
                        Every web platform, mobile app, or CRM is tailored precisely to match your business workflow without unnecessary software bloat.
                    </p>
                </div>

                <div class="p-8 rounded-2xl bg-slate-50 border border-slate-200">
                    <div class="w-12 h-12 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center mb-6 font-extrabold text-lg">
                        02
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mb-3">Security & Data Isolation</h3>
                    <p class="text-sm text-slate-600 leading-relaxed">
                        Built-in role authorization, tenant isolation, private document storage streams, and secure online payment signatures.
                    </p>
                </div>

                <div class="p-8 rounded-2xl bg-slate-50 border border-slate-200">
                    <div class="w-12 h-12 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center mb-6 font-extrabold text-lg">
                        03
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mb-3">Full Lifecycle Transparency</h3>
                    <p class="text-sm text-slate-600 leading-relaxed">
                        Clear quotation breakdowns, dedicated client dashboard tracking, formal milestone sign-offs, and automated payment receipts.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- 6. AI & AUTOMATION HIGHLIGHT -->
    <section class="py-20 bg-slate-950 text-white relative overflow-hidden">
        <div class="container-custom relative z-10">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                <div class="space-y-6">
                    <span class="px-3 py-1 rounded-full text-xs font-extrabold bg-blue-500/20 text-blue-400 border border-blue-500/30 uppercase tracking-wider">
                        Next-Gen Technology
                    </span>
                    <h2 class="text-3xl sm:text-4xl font-extrabold text-white tracking-tight leading-tight">
                        Power Your Business Operations with AI & Custom Automation
                    </h2>
                    <p class="text-slate-300 leading-relaxed">
                        Startiz Labs builds smart AI tools and automated pipelines that handle lead processing, client query responses, automated PDF invoicing, and daily operational reports.
                    </p>
                    <ul class="space-y-3 text-sm text-slate-300">
                        <li class="flex items-center gap-3">
                            <svg class="w-5 h-5 text-blue-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            <span>Conversational AI assistants & smart customer support</span>
                        </li>
                        <li class="flex items-center gap-3">
                            <svg class="w-5 h-5 text-blue-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            <span>Automated quotation generation & digital PDF receipts</span>
                        </li>
                        <li class="flex items-center gap-3">
                            <svg class="w-5 h-5 text-blue-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            <span>Inventory & order workflow automation</span>
                        </li>
                    </ul>
                    <div class="pt-4">
                        <a href="{{ route('start-project') }}" class="btn-base btn-primary btn-md">
                            Automate Your Workflow
                        </a>
                    </div>
                </div>

                <div class="p-8 rounded-2xl bg-slate-900 border border-slate-800 space-y-6">
                    <div class="flex items-center justify-between pb-4 border-b border-slate-800">
                        <span class="text-xs font-mono text-blue-400">AI Automation Engine</span>
                        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    </div>
                    <div class="space-y-4 text-xs font-mono">
                        <div class="p-4 rounded-lg bg-slate-950 border border-slate-800 text-slate-300">
                            <span class="text-emerald-400">[SYSTEM]</span> Lead captured & classified via Startiz AI Gateway.
                        </div>
                        <div class="p-4 rounded-lg bg-slate-950 border border-slate-800 text-slate-300">
                            <span class="text-blue-400">[AUTOMATION]</span> STZ-QUO generated with automated pricing parameters.
                        </div>
                        <div class="p-4 rounded-lg bg-slate-950 border border-slate-800 text-slate-300">
                            <span class="text-purple-400">[SECURITY]</span> Encrypted client storage stream & RBAC isolation verified.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 7. FEATURED PORTFOLIO -->
    <section class="py-20 md:py-28 bg-white">
        <div class="container-custom">
            <x-section-heading 
                badge="Past Deliveries"
                title="Featured Digital Projects & Work"
                subtitle="Explore recent applications and platforms built with enterprise-grade architecture."
            />

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                @foreach(array_slice($portfolioProjects, 0, 3) as $proj)
                    <x-portfolio-card 
                        :category="$proj['category']"
                        :title="$proj['title']"
                        :description="$proj['short_description']"
                        :tags="$proj['technologies']"
                        :slug="$proj['slug']"
                    />
                @endforeach
            </div>

            <div class="mt-12 text-center">
                <a href="{{ route('portfolio.index') }}" class="btn-base btn-outline btn-lg">
                    View All Portfolio Projects &rarr;
                </a>
            </div>
        </div>
    </section>

    <!-- 8. HOW WE WORK -->
    <section class="py-20 md:py-28 bg-slate-900 text-white">
        <div class="container-custom">
            <x-section-heading 
                badge="Structured Process"
                title="How Startiz Labs Builds Your Digital Solution"
                subtitle="From requirement analysis to final production launch, we ensure total clarity and engineering precision."
            />

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
                <div class="p-6 rounded-xl bg-slate-800/60 border border-slate-700/60">
                    <div class="w-10 h-10 rounded-full bg-blue-600 text-white font-bold flex items-center justify-center mb-6 text-lg">
                        1
                    </div>
                    <h3 class="text-xl font-bold text-white mb-2">Requirement & Scope</h3>
                    <p class="text-sm text-slate-300 leading-relaxed">
                        We analyze your operational goals, target users, and digital requirements to formulate a comprehensive specification.
                    </p>
                </div>

                <div class="p-6 rounded-xl bg-slate-800/60 border border-slate-700/60">
                    <div class="w-10 h-10 rounded-full bg-blue-600 text-white font-bold flex items-center justify-center mb-6 text-lg">
                        2
                    </div>
                    <h3 class="text-xl font-bold text-white mb-2">Architecture & Quotation</h3>
                    <p class="text-sm text-slate-300 leading-relaxed">
                        We design modular database schemas, role access policies, milestone roadmaps, and transparent cost estimates.
                    </p>
                </div>

                <div class="p-6 rounded-xl bg-slate-800/60 border border-slate-700/60">
                    <div class="w-10 h-10 rounded-full bg-blue-600 text-white font-bold flex items-center justify-center mb-6 text-lg">
                        3
                    </div>
                    <h3 class="text-xl font-bold text-white mb-2">Agile Engineering</h3>
                    <p class="text-sm text-slate-300 leading-relaxed">
                        We develop clean, secure code with milestone demos and real-time updates inside your client dashboard.
                    </p>
                </div>

                <div class="p-6 rounded-xl bg-slate-800/60 border border-slate-700/60">
                    <div class="w-10 h-10 rounded-full bg-blue-600 text-white font-bold flex items-center justify-center mb-6 text-lg">
                        4
                    </div>
                    <h3 class="text-xl font-bold text-white mb-2">Launch & Support</h3>
                    <p class="text-sm text-slate-300 leading-relaxed">
                        We execute production deployment, environment hardening, speed optimization, and ongoing technical support.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- 9. CLIENT TESTIMONIALS -->
    <section class="py-20 md:py-28 bg-slate-50 border-t border-slate-200">
        <div class="container-custom">
            <x-section-heading 
                badge="Client Feedback"
                title="Trusted by Past Projects & Clients"
                subtitle="Read feedback from teams and platforms built on our technology architecture."
            />

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                @foreach(array_slice($testimonials, 0, 3) as $testimonial)
                    <div class="p-8 rounded-2xl bg-white border border-slate-200 shadow-sm flex flex-col justify-between">
                        <div class="space-y-4">
                            <div class="flex items-center gap-1 text-amber-400">
                                @for($i = 0; $i < $testimonial['rating']; $i++)
                                    <svg class="w-5 h-5 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                @endfor
                            </div>
                            <p class="text-sm text-slate-700 italic leading-relaxed">
                                "{{ $testimonial['quote'] }}"
                            </p>
                        </div>
                        <div class="pt-6 mt-6 border-t border-slate-100 flex items-center justify-between">
                            <div>
                                <p class="text-sm font-bold text-slate-900">{{ $testimonial['client_name'] }}</p>
                                <p class="text-xs text-slate-500">{{ $testimonial['role'] }}, {{ $testimonial['project_name'] }}</p>
                            </div>
                            <span class="text-[10px] uppercase font-bold text-slate-400 px-2 py-1 bg-slate-100 rounded">
                                Verified
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- 10. FOUNDER PREVIEW -->
    <section class="py-20 md:py-28 bg-white border-t border-slate-200">
        <div class="container-custom">
            <div class="max-w-4xl mx-auto p-8 md:p-12 rounded-3xl bg-slate-900 text-white relative overflow-hidden">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8 items-center">
                    <div class="text-center md:text-left">
                        <div class="w-24 h-24 rounded-full bg-blue-600 text-white font-extrabold text-3xl flex items-center justify-center mx-auto md:mx-0 shadow-lg shadow-blue-500/20">
                            DM
                        </div>
                        <h3 class="text-2xl font-extrabold text-white mt-4">Dolly Mishra</h3>
                        <p class="text-xs font-semibold text-blue-400 uppercase tracking-wider mt-1">Founder & Tech Leader</p>
                    </div>

                    <div class="md:col-span-2 space-y-4 text-center md:text-left">
                        <span class="text-xs font-extrabold text-blue-400 uppercase tracking-wider">Leadership & Vision</span>
                        <h4 class="text-xl font-bold text-white">Dedicated to Practical Software & Technology Engineering</h4>
                        <p class="text-sm text-slate-300 leading-relaxed">
                            "Startiz Labs was created to provide business owners with one trustworthy place for all digital solutions. We combine clean software architecture, cybersecurity, and practical AI automation to solve real business challenges."
                        </p>
                        <div class="pt-2">
                            <a href="{{ route('about') }}" class="text-xs font-bold text-blue-400 hover:text-blue-300 underline">
                                Read Full Founder Biography & About Startiz Labs &rarr;
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 11. PARTNER PROGRAM CTA -->
    <section class="py-16 bg-gradient-to-br from-amber-950 via-slate-900 to-slate-950 text-white border-t border-amber-900/30">
        <div class="container-custom">
            <div class="max-w-4xl mx-auto text-center space-y-6">
                <span class="px-3 py-1 rounded-full text-xs font-extrabold bg-amber-500/20 text-amber-300 border border-amber-500/30 uppercase tracking-wider">
                    Partner with Startiz Labs
                </span>
                <h2 class="text-3xl sm:text-4xl font-extrabold text-white tracking-tight">
                    Bring Businesses. Earn Commissions. Grow with Startiz Labs.
                </h2>
                <p class="text-base sm:text-lg text-slate-300 max-w-2xl mx-auto leading-relaxed">
                    Are you an agency, consultant, or professional connected with business owners? Join our partner ecosystem to refer digital projects and earn attractive commissions.
                </p>
                <div>
                    <a href="{{ config('services.partner_portal.url', '#') }}" class="btn-base btn-primary btn-lg bg-amber-500 hover:bg-amber-400 text-slate-950 font-extrabold border-none">
                        Join as a Partner
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- 12. PROMOTIONAL OFFER IF ACTIVE -->
    @if($activeOffer)
        <section class="py-12 bg-slate-100 border-t border-slate-200">
            <div class="container-custom max-w-5xl">
                <x-promotional-banner :offer="$activeOffer" />
            </div>
        </section>
    @endif

    <!-- 13. FINAL CTA -->
    <x-cta-section />
@endsection
