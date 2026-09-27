@props([
    'title' => '',
    'description' => '',
    'slug' => ''
])

<div class="card-public group">
    <div class="w-12 h-12 rounded-lg bg-slate-100 text-slate-800 flex items-center justify-center mb-6 group-hover:bg-slate-900 group-hover:text-white transition-colors">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5m0 0h5m-5 0V9m0 0h5m-5 0H7" />
        </svg>
    </div>
    <h3 class="h3 mb-3 text-slate-900 group-hover:text-blue-600 transition-colors">
        {{ $title }}
    </h3>
    <p class="text-sm text-slate-600 mb-6 flex-grow leading-relaxed">
        {{ $description }}
    </p>
    <div>
        <a href="{{ route('industries.show', $slug) }}" class="inline-flex items-center gap-1.5 text-sm font-semibold text-slate-900 group-hover:text-blue-600 transition-colors">
            Explore Industry Solutions
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
        </a>
    </div>
</div>
