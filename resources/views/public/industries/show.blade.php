@extends('layouts.public')

@section('title', ($industry->name ?? 'Industry Detail') . ' — Startiz Labs')
@section('meta_description', $industry->description ?? 'Industry digital solution detail.')

@section('content')
    <section class="py-16 md:py-24 bg-slate-900 text-white">
        <div class="container-custom max-w-4xl">
            <a href="{{ route('industries.index') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-blue-400 hover:text-blue-300 mb-6">
                &larr; Back to All Industries
            </a>
            <h1 class="text-4xl md:text-5xl font-extrabold text-white tracking-tight mb-6">
                {{ $industry->name ?? 'Industry Solutions' }}
            </h1>
            <p class="text-xl text-slate-300 leading-relaxed">
                {{ $industry->description ?? 'Custom software engineering tailored to solve complex sector-specific challenges.' }}
            </p>
        </div>
    </section>

    <section class="py-20 bg-white">
        <div class="container-custom max-w-4xl">
            <div class="prose prose-lg max-w-none text-slate-700 leading-relaxed space-y-6">
                <h2 class="text-2xl font-bold text-slate-900">Industry Capabilities</h2>
                <p>
                    Startiz Labs designs vertical software tools focused on automating routine operational tasks, enhancing security, and facilitating administrative oversight.
                </p>
                <div class="p-6 bg-slate-50 border border-slate-200 rounded-xl my-8">
                    <h3 class="text-lg font-bold text-slate-900 mb-3">Key Solution Features</h3>
                    <ul class="space-y-2 text-sm text-slate-600">
                        <li class="flex items-center gap-2">&bull; Dedicated role-based access for staff, administrators, and clients</li>
                        <li class="flex items-center gap-2">&bull; Automated quotation, milestone tracking, and receipt generation</li>
                        <li class="flex items-center gap-2">&bull; Multi-tenant style data isolation ensuring privacy</li>
                        <li class="flex items-center gap-2">&bull; Real-time audit logs and activity histories</li>
                    </ul>
                </div>
            </div>

            <div class="mt-12 pt-8 border-t border-slate-200 flex items-center justify-between">
                <a href="{{ route('industries.index') }}" class="btn-base btn-outline">
                    &larr; Explore Other Industries
                </a>
                <a href="{{ route('start-project') }}" class="btn-base btn-primary">
                    Start a Project for {{ $industry->name ?? 'this Industry' }}
                </a>
            </div>
        </div>
    </section>

    <x-cta-section />
@endsection
