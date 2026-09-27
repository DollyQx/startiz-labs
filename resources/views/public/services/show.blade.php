@extends('layouts.public')

@section('title', ($service->name ?? 'Service Detail') . ' — Startiz Labs')
@section('meta_description', $service->short_description ?? 'Custom software engineering service detail.')

@section('content')
    <section class="py-16 md:py-24 bg-slate-900 text-white">
        <div class="container-custom max-w-4xl">
            <a href="{{ route('services.index') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-blue-400 hover:text-blue-300 mb-6">
                &larr; Back to All Services
            </a>
            <h1 class="text-4xl md:text-5xl font-extrabold text-white tracking-tight mb-6">
                {{ $service->name ?? 'Service Detail' }}
            </h1>
            <p class="text-xl text-slate-300 leading-relaxed">
                {{ $service->short_description ?? 'Custom software solution built with modern technology standards and scalable database architecture.' }}
            </p>
        </div>
    </section>

    <section class="py-20 bg-white">
        <div class="container-custom max-w-4xl">
            <div class="prose prose-lg max-w-none text-slate-700 leading-relaxed space-y-6">
                <h2 class="text-2xl font-bold text-slate-900">Engineering Overview</h2>
                <p>
                    {{ $service->description ?? 'We specialize in engineering robust digital systems. Our development lifecycle emphasizes data isolation, secure authentication, financial precision, and high-performance server execution.' }}
                </p>
                <div class="p-6 bg-slate-50 border border-slate-200 rounded-xl my-8">
                    <h3 class="text-lg font-bold text-slate-900 mb-3">Key Solution Deliverables</h3>
                    <ul class="space-y-2 text-sm text-slate-600">
                        <li class="flex items-center gap-2">&bull; Fully customized database schema & domain models</li>
                        <li class="flex items-center gap-2">&bull; Server-side role-based access control & security audit log</li>
                        <li class="flex items-center gap-2">&bull; Responsive client portal integration & interactive dashboards</li>
                        <li class="flex items-center gap-2">&bull; Production environment setup & hosting optimization</li>
                    </ul>
                </div>
            </div>

            <div class="mt-12 pt-8 border-t border-slate-200 flex items-center justify-between">
                <a href="{{ route('services.index') }}" class="btn-base btn-outline">
                    &larr; Explore Other Services
                </a>
                <a href="{{ route('start-project') }}" class="btn-base btn-primary">
                    Start a Project for {{ $service->name ?? 'this Service' }}
                </a>
            </div>
        </div>
    </section>

    <x-cta-section />
@endsection
