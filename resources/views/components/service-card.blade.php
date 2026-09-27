@props([
    'title' => '',
    'description' => '',
    'slug' => '',
    'icon' => 'code'
])

<div class="card-public group">
    <div class="w-12 h-12 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center mb-6 group-hover:bg-blue-600 group-hover:text-white transition-colors">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4" />
        </svg>
    </div>
    <h3 class="h3 mb-3 text-slate-900 group-hover:text-blue-600 transition-colors">
        {{ $title }}
    </h3>
    <p class="text-sm text-slate-600 mb-6 flex-grow leading-relaxed">
        {{ $description }}
    </p>
    <div>
        <a href="{{ route('services.show', $slug) }}" class="inline-flex items-center gap-1.5 text-sm font-semibold text-blue-600 hover:text-blue-700 transition-colors group-hover:translate-x-1 duration-200">
            Learn More
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
        </a>
    </div>
</div>
