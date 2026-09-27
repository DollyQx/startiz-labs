@props([
    'title' => 'Ready to Build Your Custom Digital Solution?',
    'subtitle' => 'Partner with Startiz Labs — One place for your complete digital business solution: websites, mobile apps, AI automation, and custom software.',
    'buttonText' => 'Start a Project',
    'buttonUrl' => null
])

@php
    $url = $buttonUrl ?? route('start-project');
@endphp

<section class="py-20 bg-slate-900 text-white relative overflow-hidden">
    <div class="container-custom relative z-10 text-center max-w-4xl mx-auto">
        <h2 class="text-3xl md:text-4xl lg:text-5xl font-extrabold text-white tracking-tight mb-6">
            {{ $title }}
        </h2>
        <p class="text-lg md:text-xl text-slate-300 mb-10 leading-relaxed max-w-2xl mx-auto">
            {{ $subtitle }}
        </p>
        <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
            <a href="{{ $url }}" class="btn-base btn-primary btn-lg w-full sm:w-auto">
                {{ $buttonText }}
            </a>
            <a href="{{ route('portfolio.index') }}" class="btn-base btn-outline btn-lg w-full sm:w-auto text-white border-slate-700 hover:bg-slate-800 hover:text-white">
                View Featured Work
            </a>
        </div>
    </div>
</section>
