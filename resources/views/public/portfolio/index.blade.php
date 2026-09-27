@extends('layouts.public')

@section('title', 'Portfolio & Software Case Studies — Startiz Labs')
@section('meta_description', 'Explore featured case studies delivered by Startiz Labs: Notes Study, Zomoggy, Gurumantra, GM Library, and GM Code Lab.')

@section('content')
    <section class="py-16 md:py-24 bg-slate-900 text-white">
        <div class="container-custom text-center max-w-3xl">
            <span class="badge-public mb-4 bg-blue-950 text-blue-400 border border-blue-800 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider">
                Proven Deliveries & Past Work
            </span>
            <h1 class="text-4xl md:text-5xl font-extrabold text-white tracking-tight mb-6">
                Official Portfolio & Case Studies
            </h1>
            <p class="text-lg text-slate-300 leading-relaxed">
                Explore custom web applications, mobile platforms, food delivery software, LMS systems, and business platforms built by Startiz Labs.
            </p>
        </div>
    </section>

    <section class="py-20 bg-white">
        <div class="container-custom">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach($projects as $project)
                    <x-portfolio-card 
                        :category="$project['category']"
                        :title="$project['title']"
                        :description="$project['short_description']"
                        :tags="$project['technologies']"
                        :slug="$project['slug']"
                    />
                @endforeach
            </div>
        </div>
    </section>

    <x-cta-section />
@endsection
