@extends('layouts.public')

@section('title', 'Startiz Labs — One Place for Your Complete Digital Business Solution')
@section('meta_description', 'Startiz Labs builds websites, mobile apps, custom software, AI automation, CRM systems, e-commerce, restaurant software, and institute management for ambitious businesses.')

@section('content')
    <!-- 1. HERO SECTION -->
    <section class="relative bg-slate-950 text-white pt-12 pb-20 lg:pt-20 lg:pb-28 overflow-hidden border-b border-slate-800/80">
        <!-- Subtle Background Glows -->
        <div class="absolute -top-24 -left-24 w-96 h-96 bg-blue-600/15 rounded-full blur-3xl pointer-events-none animate-pulse-subtle"></div>
        <div class="absolute top-1/2 -right-24 w-96 h-96 bg-indigo-600/15 rounded-full blur-3xl pointer-events-none animate-pulse-subtle"></div>

        <div class="container-custom relative z-10">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-8 items-center">
                
                <!-- Left Column: Friendly & Direct Business Messaging -->
                <div class="lg:col-span-6 text-left">
                    <!-- Eyebrow Badge -->
                    <div class="inline-flex items-center gap-2.5 bg-slate-900/90 border border-blue-500/30 px-3.5 py-1.5 rounded-full text-xs font-semibold tracking-wider text-blue-400 mb-6 shadow-inner">
                        <span class="relative flex h-2 w-2">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-blue-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-blue-500"></span>
                        </span>
                        <span class="uppercase text-[11px] tracking-widest font-bold">DIGITAL SOLUTIONS FOR YOUR BUSINESS</span>
                    </div>

                    <!-- Headline -->
                    <h1 class="text-3xl sm:text-4xl lg:text-5xl xl:text-6xl font-extrabold text-white tracking-tight leading-[1.18] mb-6">
                        Aapke Business Ko <span class="bg-gradient-to-r from-blue-400 via-indigo-300 to-cyan-400 bg-clip-text text-transparent">Digital Banane Ka Kaam,</span> Humara.
                    </h1>

                    <!-- Supporting Paragraph -->
                    <p class="text-base sm:text-lg text-slate-300 max-w-xl mb-8 leading-relaxed font-normal">
                        Website, mobile app, online store, CRM, AI automation ya custom software. Aapke business ki need ke hisaab se simple, practical aur scalable digital solutions.
                    </p>

                    <!-- CTAs -->
                    <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-4 mb-10">
                        <a href="{{ route('start-project') }}" class="btn-base btn-primary btn-lg justify-center shadow-lg shadow-blue-600/20 group">
                            <span>Start Your Project</span>
                            <svg class="w-4 h-4 ml-2 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                            </svg>
                        </a>
                        <a href="{{ route('portfolio.index') }}" class="btn-base btn-outline btn-lg justify-center !text-white border-slate-700/80 hover:bg-slate-800/80 hover:!text-white transition-all">
                            View Our Work
                        </a>
                    </div>

                    <!-- Quick Audience Badges -->
                    <div class="pt-6 border-t border-slate-800/80 flex flex-wrap gap-2 text-xs text-slate-300">
                        <span class="px-2.5 py-1 rounded-md bg-slate-900 border border-slate-800">Startups & Founders</span>
                        <span class="px-2.5 py-1 rounded-md bg-slate-900 border border-slate-800">Shops & Retailers</span>
                        <span class="px-2.5 py-1 rounded-md bg-slate-900 border border-slate-800">Restaurants & Food</span>
                        <span class="px-2.5 py-1 rounded-md bg-slate-900 border border-slate-800">Institutes & Coaching</span>
                    </div>
                </div>

                <!-- Right Column: Business Digital Ecosystem Visual -->
                <div class="lg:col-span-6 relative mt-8 lg:mt-0">
                    <div class="relative mx-auto max-w-md lg:max-w-none min-h-[380px] flex items-center justify-center p-4">
                        
                        <!-- Connecting Radial Ring -->
                        <div class="absolute inset-4 rounded-full border border-dashed border-slate-800 pointer-events-none"></div>

                        <!-- Central Core: YOUR BUSINESS -->
                        <div class="relative z-10 bg-gradient-to-br from-blue-600 to-indigo-700 text-white rounded-2xl p-6 shadow-2xl shadow-blue-900/50 border border-blue-400/30 text-center w-48">
                            <div class="w-12 h-12 rounded-xl bg-white/10 mx-auto mb-3 flex items-center justify-center border border-white/20">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5m0 0h4m-4 0V11m0 0h4m-4 0H9m4 0V7m0 0h4m-4 0H9"/>
                                </svg>
                            </div>
                            <div class="font-extrabold text-sm uppercase tracking-wider">YOUR BUSINESS</div>
                            <div class="text-[10px] text-blue-100/80 mt-1">Growth Ecosystem</div>
                        </div>

                        <!-- Solution Card 1: Website (Top Left) -->
                        <div class="absolute top-0 left-0 bg-slate-900/90 backdrop-blur-md border border-slate-800 rounded-xl p-3 shadow-lg flex items-center gap-3 w-40 sm:w-44 animate-float-slow">
                            <div class="p-2 rounded-lg bg-blue-500/10 text-blue-400 shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9"/></svg>
                            </div>
                            <div>
                                <div class="font-bold text-xs text-white">Website</div>
                                <div class="text-[10px] text-slate-400">High Converting</div>
                            </div>
                        </div>

                        <!-- Solution Card 2: Mobile App (Top Right) -->
                        <div class="absolute top-0 right-0 bg-slate-900/90 backdrop-blur-md border border-slate-800 rounded-xl p-3 shadow-lg flex items-center gap-3 w-40 sm:w-44 animate-float-delayed">
                            <div class="p-2 rounded-lg bg-indigo-500/10 text-indigo-400 shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                            </div>
                            <div>
                                <div class="font-bold text-xs text-white">Mobile App</div>
                                <div class="text-[10px] text-slate-400">Android & iOS</div>
                            </div>
                        </div>

                        <!-- Solution Card 3: Online Store (Middle Left) -->
                        <div class="absolute top-1/2 -translate-y-1/2 -left-4 sm:left-0 bg-slate-900/90 backdrop-blur-md border border-slate-800 rounded-xl p-3 shadow-lg flex items-center gap-3 w-40 sm:w-44">
                            <div class="p-2 rounded-lg bg-emerald-500/10 text-emerald-400 shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                            </div>
                            <div>
                                <div class="font-bold text-xs text-white">Online Store</div>
                                <div class="text-[10px] text-slate-400">E-Commerce</div>
                            </div>
                        </div>

                        <!-- Solution Card 4: AI Automation (Middle Right) -->
                        <div class="absolute top-1/2 -translate-y-1/2 -right-4 sm:right-0 bg-slate-900/90 backdrop-blur-md border border-slate-800 rounded-xl p-3 shadow-lg flex items-center gap-3 w-40 sm:w-44">
                            <div class="p-2 rounded-lg bg-amber-500/10 text-amber-400 shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                            </div>
                            <div>
                                <div class="font-bold text-xs text-white">AI Automation</div>
                                <div class="text-[10px] text-slate-400">Smart Workflows</div>
                            </div>
                        </div>

                        <!-- Solution Card 5: CRM System (Bottom Left) -->
                        <div class="absolute bottom-0 left-0 bg-slate-900/90 backdrop-blur-md border border-slate-800 rounded-xl p-3 shadow-lg flex items-center gap-3 w-40 sm:w-44 animate-float-delayed">
                            <div class="p-2 rounded-lg bg-cyan-500/10 text-cyan-400 shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                            </div>
                            <div>
                                <div class="font-bold text-xs text-white">CRM</div>
                                <div class="text-[10px] text-slate-400">Client & Sales</div>
                            </div>
                        </div>

                        <!-- Solution Card 6: Business Management (Bottom Right) -->
                        <div class="absolute bottom-0 right-0 bg-slate-900/90 backdrop-blur-md border border-slate-800 rounded-xl p-3 shadow-lg flex items-center gap-3 w-40 sm:w-44 animate-float-slow">
                            <div class="p-2 rounded-lg bg-purple-500/10 text-purple-400 shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            </div>
                            <div>
                                <div class="font-bold text-xs text-white">Business Software</div>
                                <div class="text-[10px] text-slate-400">Billing & ERP</div>
                            </div>
                        </div>

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
