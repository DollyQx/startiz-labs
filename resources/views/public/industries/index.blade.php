@extends('layouts.public')

@section('title', 'Target Industries & Verticals — Startiz Labs')
@section('meta_description', 'Explore Startiz Labs solutions tailored for startups, retail shopkeepers, restaurants, coaching institutes, education, and growing organizations.')

@section('content')
    <section class="py-16 md:py-24 bg-slate-900 text-white">
        <div class="container-custom text-center max-w-3xl">
            <span class="badge-public mb-4 bg-blue-950 text-blue-400 border border-blue-800 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider">
                Target Customers & Verticals
            </span>
            <h1 class="text-4xl md:text-5xl font-extrabold text-white tracking-tight mb-6">
                Specialized Digital Tools for Every Industry
            </h1>
            <p class="text-lg text-slate-300 leading-relaxed">
                We understand the unique workflows of startups, retail stores, restaurants, institutes, and growing businesses.
            </p>
        </div>
    </section>

    <section class="py-20 bg-white">
        <div class="container-custom">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @forelse($industries as $industry)
                    <x-industry-card 
                        :title="$industry->name"
                        :description="$industry->description ?? 'Tailored digital solutions engineered specifically for industry workflows.'"
                        :slug="$industry->slug"
                    />
                @empty
                    <x-industry-card title="Education & Academic Institutes" description="Student portals, examination engines, digital notes libraries, and institute management." slug="education" />
                    <x-industry-card title="Healthcare & Hospitals" description="Patient records, appointment scheduling, billing modules, and clinic management software." slug="healthcare" />
                    <x-industry-card title="Hostel, Mess & Canteen Operations" description="Automated room allocation, mess meal tracking, inventory control, and billing receipts." slug="hostel-mess" />
                    <x-industry-card title="Retail & Shop Management" description="Point of sale (POS), stock tracking, barcode management, and customer CRM tools." slug="retail" />
                    <x-industry-card title="Enterprise Business & Operations" description="Custom workflow automation, financial ledgers, document repositories, and staff access control." slug="enterprise" />
                    <x-industry-card title="Hospitality & Services" description="Booking engines, guest service tracking, and staff management systems." slug="hospitality" />
                @endforelse
            </div>
        </div>
    </section>

    <x-cta-section />
@endsection
