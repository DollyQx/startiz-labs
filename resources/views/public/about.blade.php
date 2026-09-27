@extends('layouts.public')

@section('title', 'About Startiz Labs — Founder Dolly Mishra & Company Mission')
@section('meta_description', 'Learn about Startiz Labs, our founder Dolly Mishra, technology leadership, software engineering principles, and commitment to business digital solutions.')

@section('content')
    <!-- Hero Header -->
    <section class="py-16 md:py-24 bg-slate-900 text-white border-b border-slate-800">
        <div class="container-custom text-center max-w-3xl">
            <span class="badge-public mb-4 bg-blue-950 text-blue-400 border border-blue-800 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider">
                Our Foundation & Vision
            </span>
            <h1 class="text-4xl md:text-5xl font-extrabold text-white tracking-tight mb-6">
                About Startiz Labs
            </h1>
            <p class="text-lg text-slate-300 leading-relaxed">
                One place for complete digital business solutions: website development, mobile apps, custom business software, AI automation, and cybersecurity.
            </p>
        </div>
    </section>

    <!-- Founder Section -->
    <section class="py-20 bg-white">
        <div class="container-custom max-w-5xl">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-12 items-start">
                <div class="p-8 rounded-3xl bg-slate-900 text-white text-center space-y-4">
                    <div class="w-32 h-32 rounded-full bg-blue-600 text-white font-extrabold text-4xl flex items-center justify-center mx-auto shadow-xl shadow-blue-500/20">
                        DM
                    </div>
                    <div>
                        <h3 class="text-2xl font-extrabold text-white">{{ $founder['name'] ?? 'Dolly Mishra' }}</h3>
                        <p class="text-xs font-semibold text-blue-400 uppercase tracking-wider mt-1">{{ $founder['role'] ?? 'Founder & Technology Leader' }}</p>
                    </div>
                    <div class="pt-4 border-t border-slate-800 text-xs text-slate-300 space-y-2">
                        <p class="font-semibold text-white">Focus Areas:</p>
                        <p>Software Engineering & Web Apps</p>
                        <p>Mobile Application Product Development</p>
                        <p>Cybersecurity & Secure Session Systems</p>
                        <p>AI & Operational Business Automation</p>
                    </div>
                </div>

                <div class="md:col-span-2 space-y-6 text-slate-700 leading-relaxed">
                    <span class="text-xs font-extrabold text-blue-600 uppercase tracking-wider">Founder Profile</span>
                    <h2 class="text-3xl font-extrabold text-slate-900">Leadership & Technology Philosophy</h2>
                    
                    <p class="text-base">
                        {{ $founder['biography'] ?? 'Dolly Mishra is the Founder and Technology Leader at Startiz Labs. Specializing in software development, application architecture, cybersecurity, and business automation, Dolly leads the engineering of custom digital solutions tailored to startups, small businesses, restaurants, institutes, and growing enterprises.' }}
                    </p>

                    <h3 class="text-xl font-bold text-slate-900 pt-4">Engineering Capabilities & Approach</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        @foreach($founder['expertise'] ?? [
                            'Software & Web Application Architecture',
                            'Mobile Product Engineering (iOS & Android)',
                            'Cybersecurity & Secure Session Management',
                            'AI & Custom Business Process Automation',
                            'Product Execution & Operational Scalability'
                        ] as $item)
                            <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 flex items-center gap-3 text-sm font-semibold text-slate-800">
                                <svg class="w-5 h-5 text-blue-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                <span>{{ $item }}</span>
                            </div>
                        @endforeach
                    </div>

                    <div class="p-6 rounded-2xl bg-blue-50 border border-blue-100 text-blue-950 mt-6">
                        <p class="text-sm font-semibold italic">
                            "{{ $founder['philosophy'] ?? 'We believe technology should be practical, clean, and directly aligned with real business operations. Startiz Labs is dedicated to providing one reliable place for complete digital solutions.' }}"
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Company Mission & Core Principles -->
    <section class="py-20 bg-slate-50 border-t border-slate-200">
        <div class="container-custom max-w-4xl">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-slate-900">Startiz Labs Core Operating Principles</h2>
                <p class="text-sm text-slate-600 mt-2">How we guarantee quality, security, and performance in every project.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="p-6 bg-white border border-slate-200 rounded-2xl shadow-sm">
                    <h3 class="text-lg font-bold text-slate-900 mb-2">1. One Place for Digital Solutions</h3>
                    <p class="text-sm text-slate-600 leading-relaxed">
                        Business owners do not need to coordinate with multiple vendors. We handle web, mobile, custom software, AI automation, and payment gateways in one unified partner relationship.
                    </p>
                </div>

                <div class="p-6 bg-white border border-slate-200 rounded-2xl shadow-sm">
                    <h3 class="text-lg font-bold text-slate-900 mb-2">2. Strict Security & Tenant Isolation</h3>
                    <p class="text-sm text-slate-600 leading-relaxed">
                        Client data protection is enforced at the database policy layer, role middleware, private document stream routes, and Razorpay signature verification.
                    </p>
                </div>

                <div class="p-6 bg-white border border-slate-200 rounded-2xl shadow-sm">
                    <h3 class="text-lg font-bold text-slate-900 mb-2">3. Financial & Operational Precision</h3>
                    <p class="text-sm text-slate-600 leading-relaxed">
                        Every quotation item, invoice, payment status, and receipt is calculated server-side to guarantee absolute financial accuracy.
                    </p>
                </div>

                <div class="p-6 bg-white border border-slate-200 rounded-2xl shadow-sm">
                    <h3 class="text-lg font-bold text-slate-900 mb-2">4. Practical Problem Solving</h3>
                    <p class="text-sm text-slate-600 leading-relaxed">
                        We focus on tools that solve real operational bottlenecks for startups, shopkeepers, restaurants, institutes, and SMBs.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <x-cta-section />
@endsection
