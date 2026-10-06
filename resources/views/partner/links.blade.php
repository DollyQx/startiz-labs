@extends('layouts.partner')

@section('title', 'Referral Links & Marketing Assets')

@section('content')
<div class="space-y-8 max-w-5xl mx-auto">
    <!-- Header -->
    <div>
        <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Referral Links & Materials</h1>
        <p class="text-sm text-slate-500">Your unique referral tracking assets. Any visitor signing up through these links is automatically attributed to your partner account.</p>
    </div>

    <!-- Main Referral Link Showcase -->
    <div class="bg-white rounded-2xl border border-slate-200 p-6 sm:p-8 shadow-sm">
        <div class="flex items-center justify-between mb-4">
            <span class="text-xs font-bold uppercase tracking-wider text-emerald-600">Primary Referral Link</span>
            <span class="font-mono text-xs font-bold bg-emerald-50 text-emerald-800 border border-emerald-200 px-3 py-1 rounded-full">
                Code: {{ $partner->referral_code }}
            </span>
        </div>

        <div class="flex flex-col sm:flex-row gap-3">
            <input type="text" id="primary-ref-link" readonly value="{{ $referralUrl }}" class="flex-1 bg-slate-50 border border-slate-200 text-slate-800 font-mono text-sm rounded-xl px-4 py-3 select-all focus:outline-none focus:ring-2 focus:ring-emerald-500">
            <button onclick="navigator.clipboard.writeText(document.getElementById('primary-ref-link').value); alert('Primary link copied to clipboard!');" class="shrink-0 px-6 py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-sm rounded-xl transition-colors shadow-sm flex items-center justify-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                Copy Link
            </button>
            @php
                $waTextPrimary = urlencode("Need custom software, web applications, or digital solutions for your business? Check out Startiz Labs: " . $referralUrl);
            @endphp
            <a href="https://api.whatsapp.com/send?text={{ $waTextPrimary }}" target="_blank" rel="noopener noreferrer" class="shrink-0 px-6 py-3 bg-[#25D366] hover:bg-[#20ba59] text-white font-bold text-sm rounded-xl transition-colors shadow-sm flex items-center justify-center gap-2">
                <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/></svg>
                Share on WhatsApp
            </a>
        </div>


        <p class="text-xs text-slate-400 mt-3">
            Points to: <span class="font-mono text-slate-600">https://startizlabs.com/?ref={{ $partner->referral_code }}</span> (Cookie attribution valid for 60 days)
        </p>
    </div>

    <!-- Deep-Link Referral Destinations -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-slate-100">
            <h3 class="font-bold text-slate-900 text-lg">Targeted Deep Links</h3>
            <p class="text-xs text-slate-500">Direct your prospects to specific high-converting landing pages</p>
        </div>

        <div class="divide-y divide-slate-100">
            <!-- Start Project Page -->
            <div class="p-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4 hover:bg-slate-50/50 transition-colors">
                <div>
                    <h4 class="font-bold text-slate-900 text-sm">Start Project / Brief Builder</h4>
                    <p class="text-xs text-slate-500 mt-0.5">Direct link to interactive proposal and quote request wizard</p>
                    <code class="block text-xs font-mono text-emerald-700 mt-1 select-all">{{ url('/start-project?ref=' . $partner->referral_code) }}</code>
                </div>
                <button onclick="navigator.clipboard.writeText('{{ url('/start-project?ref=' . $partner->referral_code) }}'); alert('Start Project link copied!');" class="shrink-0 px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-lg transition-colors">
                    Copy Link
                </button>
            </div>

            <!-- Client Registration Page -->
            <div class="p-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4 hover:bg-slate-50/50 transition-colors">
                <div>
                    <h4 class="font-bold text-slate-900 text-sm">Client Portal Sign Up</h4>
                    <p class="text-xs text-slate-500 mt-0.5">Direct onboarding to Client Portal</p>
                    <code class="block text-xs font-mono text-emerald-700 mt-1 select-all">{{ url('/register?ref=' . $partner->referral_code) }}</code>
                </div>
                <button onclick="navigator.clipboard.writeText('{{ url('/register?ref=' . $partner->referral_code) }}'); alert('Client Registration link copied!');" class="shrink-0 px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-lg transition-colors">
                    Copy Link
                </button>
            </div>

            <!-- Services Portfolio Page -->
            <div class="p-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4 hover:bg-slate-50/50 transition-colors">
                <div>
                    <h4 class="font-bold text-slate-900 text-sm">Services Catalog</h4>
                    <p class="text-xs text-slate-500 mt-0.5">Browse all bespoke engineering, cloud, and AI development capabilities</p>
                    <code class="block text-xs font-mono text-emerald-700 mt-1 select-all">{{ url('/services?ref=' . $partner->referral_code) }}</code>
                </div>
                <button onclick="navigator.clipboard.writeText('{{ url('/services?ref=' . $partner->referral_code) }}'); alert('Services link copied!');" class="shrink-0 px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-lg transition-colors">
                    Copy Link
                </button>
            </div>
        </div>
    </div>

    <!-- Referral Rules & Guidelines -->
    <div class="bg-amber-50/70 border border-amber-200/80 rounded-2xl p-6">
        <h4 class="font-bold text-amber-900 text-sm mb-2 flex items-center gap-2">
            <svg class="w-4 h-4 text-amber-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            Referral Tracking Rules & Integrity Policy
        </h4>
        <ul class="list-disc list-inside space-y-1.5 text-xs text-amber-800">
            <li><strong>Self-referrals are strictly prohibited:</strong> A partner cannot use their own referral link to create client accounts.</li>
            <li><strong>Duplicate attribution prevention:</strong> A client is permanently attributed to the first verified referral link they use.</li>
            <li><strong>Cookie Window:</strong> Referral tracking cookies remain active in visitor browsers for 60 days.</li>
            <li><strong>Fair Commission Settlement:</strong> Commissions become eligible for payout once client invoices are paid and verified.</li>
        </ul>
    </div>
</div>
@endsection
