@extends('layouts.public')

@section('title', 'Digital Solutions & Services — Startiz Labs')
@section('meta_description', 'Explore Startiz Labs core services: website development, mobile apps, AI automation, CRM development, e-commerce, restaurant software, and custom business tools.')

@section('content')
    <section class="py-16 md:py-24 bg-slate-900 text-white">
        <div class="container-custom text-center max-w-3xl">
            <span class="badge-public mb-4 bg-blue-950 text-blue-400 border border-blue-800 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider">
                Digital Solutions
            </span>
            <h1 class="text-4xl md:text-5xl font-extrabold text-white tracking-tight mb-6">
                Startiz Labs Core Services
            </h1>
            <p class="text-lg text-slate-300 leading-relaxed">
                One place for your complete digital business solution. We build custom websites, mobile apps, AI automation, CRM systems, and business management software.
            </p>
        </div>
    </section>

    <section class="py-20 bg-white">
        <div class="container-custom">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @forelse($services as $service)
                    <x-service-card 
                        :title="$service->name" 
                        :description="$service->short_description ?? 'Custom software tailored for business efficiency and scalability.'"
                        :slug="$service->slug"
                    />
                @empty
                    <x-service-card title="Web Application Development" description="Custom web platforms built with Laravel, Vue, React, and high-performance server architectures." slug="web-development" />
                    <x-service-card title="Mobile Application Development" description="Native and cross-platform mobile apps for iOS and Android built for seamless client engagement." slug="app-development" />
                    <x-service-card title="LMS & Online Testing Systems" description="Comprehensive learning management, automated online examination, and digital library platforms." slug="lms-education" />
                    <x-service-card title="Custom ERP & Business Software" description="Integrated hostel, mess, healthcare, shop retail, and administrative management software." slug="custom-erp" />
                    <x-service-card title="Cybersecurity & Audit Services" description="Vulnerability assessments, code audits, secure session architectures, and data isolation strategies." slug="cybersecurity" />
                    <x-service-card title="E-Commerce & Digital Commerce" description="Scalable online store systems with inventory management, cart systems, and payment integration." slug="e-commerce" />
                @endforelse
            </div>
        </div>
    </section>

    <x-cta-section />
@endsection
