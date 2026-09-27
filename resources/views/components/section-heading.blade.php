@props([
    'badge' => null,
    'title' => '',
    'subtitle' => null,
    'centered' => true
])

<div class="{{ $centered ? 'text-center max-w-3xl mx-auto' : 'max-w-2xl' }} mb-12 md:mb-16">
    @if($badge)
        <span class="badge-public mb-3 inline-block">
            {{ $badge }}
        </span>
    @endif
    <h2 class="h2 tracking-tight">
        {{ $title }}
    </h2>
    @if($subtitle)
        <p class="text-lead mt-4 text-slate-600">
            {{ $subtitle }}
        </p>
    @endif
</div>
