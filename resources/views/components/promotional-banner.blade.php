@props([
    'offer' => null
])

@php
    $title = $offer ? $offer->name : 'Custom Solution Launch Strategy Package';
    $description = $offer ? $offer->description : 'Get a complimentary technical architecture blueprint and milestone delivery roadmap for your enterprise digital transformation.';
    $discountBadge = $offer ? ($offer->discount_type->value === 'percentage' ? intval($offer->discount_value) . '% OFF' : '₹' . number_format($offer->discount_value) . ' OFF') : 'SPECIAL INITIATIVE';
    $ctaText = $offer && $offer->cta_text ? $offer->cta_text : 'Claim Consultation & Proposal';
    $ctaUrl = $offer && $offer->cta_url ? $offer->cta_url : route('start-project');
@endphp

<div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-slate-900 via-blue-950 to-slate-900 p-8 md:p-12 text-white border border-blue-900/50 shadow-2xl">
    <div class="absolute -right-12 -top-12 w-64 h-64 bg-blue-600/10 rounded-full blur-3xl pointer-events-none"></div>
    <div class="relative z-10 flex flex-col lg:flex-row items-start lg:items-center justify-between gap-8">
        <div class="max-w-2xl">
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-extrabold uppercase tracking-wider bg-blue-600 text-white mb-4 shadow-sm">
                {{ $discountBadge }}
            </span>
            <h3 class="text-2xl md:text-3xl font-bold tracking-tight text-white mb-3">
                {{ $title }}
            </h3>
            <p class="text-slate-300 text-base md:text-lg leading-relaxed">
                {{ $description }}
            </p>
        </div>
        <div class="flex-shrink-0">
            <a href="{{ $ctaUrl }}" class="btn-base btn-primary btn-lg shadow-lg shadow-blue-600/30">
                {{ $ctaText }}
            </a>
        </div>
    </div>
</div>
