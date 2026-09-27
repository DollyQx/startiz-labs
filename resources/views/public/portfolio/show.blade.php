@extends('layouts.public')

@section('title', 'Case Study: ' . ($project['title'] ?? \Illuminate\Support\Str::headline($slug)) . ' — Startiz Labs')
@section('meta_description', $project['short_description'] ?? 'Detailed software development case study by Startiz Labs.')

@section('content')
    <section class="py-16 md:py-24 bg-slate-900 text-white">
        <div class="container-custom max-w-4xl">
            <a href="{{ route('portfolio.index') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-blue-400 hover:text-blue-300 mb-6">
                &larr; Back to All Portfolio Projects
            </a>
            <div class="mb-4">
                <span class="px-3 py-1 rounded-full text-xs font-bold bg-blue-950 text-blue-400 border border-blue-800 uppercase tracking-wider">
                    {{ $project['category'] ?? 'Digital Case Study' }}
                </span>
            </div>
            <h1 class="text-4xl md:text-5xl font-extrabold text-white tracking-tight mb-6">
                {{ $project['title'] ?? \Illuminate\Support\Str::headline($slug) }}
            </h1>
            <p class="text-xl text-slate-300 leading-relaxed">
                {{ $project['short_description'] ?? 'An end-to-end software delivery case study by Startiz Labs.' }}
            </p>
        </div>
    </section>

    <section class="py-20 bg-white">
        <div class="container-custom max-w-4xl">
            <div class="prose prose-lg max-w-none text-slate-700 leading-relaxed space-y-8">
                <div>
                    <h2 class="text-2xl font-bold text-slate-900 mb-3">Project Challenge & Objective</h2>
                    <p class="text-slate-700">
                        {{ $project['challenge'] ?? 'The client required a modern, reliable digital platform to handle daily operations, multi-user interactions, and secure data transactions.' }}
                    </p>
                </div>

                <div>
                    <h2 class="text-2xl font-bold text-slate-900 mb-3">Startiz Labs Engineering Solution</h2>
                    <p class="text-slate-700">
                        {{ $project['solution'] ?? 'Startiz Labs engineered a custom software architecture with dedicated database models, role isolation, automated reference tracking, and secure server delivery.' }}
                    </p>
                </div>

                @if(isset($project['outcomes']) && count($project['outcomes']) > 0)
                    <div>
                        <h2 class="text-2xl font-bold text-slate-900 mb-4">Key Outcomes & Measurable Results</h2>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 my-6">
                            @foreach($project['outcomes'] as $outcome)
                                <div class="p-6 bg-slate-50 border border-slate-200 rounded-2xl text-center">
                                    <svg class="w-8 h-8 text-blue-600 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    <div class="text-sm font-bold text-slate-900">{{ $outcome }}</div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if(isset($project['technologies']) && count($project['technologies']) > 0)
                    <div class="pt-6 border-t border-slate-200">
                        <h3 class="text-lg font-bold text-slate-900 mb-3">Technologies & Architecture Stack</h3>
                        <div class="flex flex-wrap gap-2">
                            @foreach($project['technologies'] as $tech)
                                <span class="px-3 py-1 rounded-lg bg-slate-100 border border-slate-200 text-xs font-semibold text-slate-700">
                                    {{ $tech }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            <div class="mt-12 pt-8 border-t border-slate-200 flex flex-col sm:flex-row items-center justify-between gap-4">
                <a href="{{ route('portfolio.index') }}" class="btn-base btn-outline w-full sm:w-auto text-center">
                    &larr; View Other Projects
                </a>
                <a href="{{ route('start-project') }}" class="btn-base btn-primary w-full sm:w-auto text-center">
                    Start a Project Like {{ $project['title'] ?? 'This' }}
                </a>
            </div>
        </div>
    </section>

    <x-cta-section />
@endsection
