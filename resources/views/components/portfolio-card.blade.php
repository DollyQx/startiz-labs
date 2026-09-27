@props([
    'category' => 'Web App',
    'title' => '',
    'description' => '',
    'tags' => [],
    'slug' => 'project'
])

<div class="card-public group">
    <div class="mb-4">
        <span class="text-xs font-bold uppercase tracking-wider text-blue-600 bg-blue-50 px-2.5 py-1 rounded">
            {{ $category }}
        </span>
    </div>
    <h3 class="h3 mb-3 text-slate-900 group-hover:text-blue-600 transition-colors">
        {{ $title }}
    </h3>
    <p class="text-sm text-slate-600 mb-6 flex-grow leading-relaxed">
        {{ $description }}
    </p>
    @if(count($tags) > 0)
        <div class="flex flex-wrap gap-2 mb-6">
            @foreach($tags as $tag)
                <span class="text-xs bg-slate-100 text-slate-700 px-2 py-0.5 rounded border border-slate-200 font-medium">
                    {{ $tag }}
                </span>
            @endforeach
        </div>
    @endif
    <div>
        <a href="{{ route('portfolio.show', $slug) }}" class="inline-flex items-center gap-1.5 text-sm font-semibold text-blue-600 hover:text-blue-700 transition-colors">
            View Case Study
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7-7 7" />
            </svg>
        </a>
    </div>
</div>
