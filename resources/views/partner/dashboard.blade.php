@extends('layouts.partner')

@section('title', 'Partner Dashboard')

@section('content')
<div class="space-y-8">
    <!-- Partner Account Approved Onboarding Banner -->
    <div class="bg-gradient-to-br from-emerald-950 via-slate-900 to-slate-950 border border-emerald-500/40 rounded-2xl p-6 sm:p-8 text-white shadow-xl relative overflow-hidden">
        <div class="absolute -right-10 -top-10 w-44 h-44 bg-emerald-500/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="relative z-10 flex flex-col lg:flex-row lg:items-center justify-between gap-6">
            <div class="space-y-4">
                <div class="flex items-start gap-3">
                    <span class="text-3xl sm:text-4xl">🎉</span>
                    <div>
                        <div class="inline-flex items-center gap-2 px-3 py-0.5 rounded-full bg-emerald-500/20 border border-emerald-500/30 text-emerald-300 text-xs font-bold mb-1">
                            <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                            Partner Account Approved
                        </div>
                        <h1 class="text-2xl sm:text-3xl font-black tracking-tight text-white">
                            Welcome, {{ auth()->user()->name }}
                        </h1>
                        <div class="flex items-center gap-2 mt-1">
                            <span class="text-xs uppercase font-extrabold tracking-wider text-slate-400">Commission:</span>
                            <span class="px-2.5 py-0.5 rounded-md text-xs font-extrabold bg-emerald-500/20 text-emerald-300 border border-emerald-500/40">
                                {{ (float) $partner->commission_rate }}%
                            </span>
                        </div>
                    </div>
                </div>

                <!-- How to earn 4 steps -->
                <div class="pt-2">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-2.5">How to earn:</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-2.5 text-xs">
                        <div class="flex items-center gap-2.5 bg-slate-900/80 border border-slate-800 rounded-lg p-2.5">
                            <span class="w-5 h-5 rounded-full bg-emerald-500/20 text-emerald-400 font-extrabold flex items-center justify-center shrink-0 text-[10px]">1</span>
                            <span class="text-slate-200 font-medium">Share your link</span>
                        </div>
                        <div class="flex items-center gap-2.5 bg-slate-900/80 border border-slate-800 rounded-lg p-2.5">
                            <span class="w-5 h-5 rounded-full bg-emerald-500/20 text-emerald-400 font-extrabold flex items-center justify-center shrink-0 text-[10px]">2</span>
                            <span class="text-slate-200 font-medium">Bring a business lead</span>
                        </div>
                        <div class="flex items-center gap-2.5 bg-slate-900/80 border border-slate-800 rounded-lg p-2.5">
                            <span class="w-5 h-5 rounded-full bg-emerald-500/20 text-emerald-400 font-extrabold flex items-center justify-center shrink-0 text-[10px]">3</span>
                            <span class="text-slate-200 font-medium">Startiz Labs closes the project</span>
                        </div>
                        <div class="flex items-center gap-2.5 bg-slate-900/80 border border-slate-800 rounded-lg p-2.5">
                            <span class="w-5 h-5 rounded-full bg-emerald-500/20 text-emerald-400 font-extrabold flex items-center justify-center shrink-0 text-[10px]">4</span>
                            <span class="text-slate-200 font-medium">You earn eligible commission</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Referral Link Box & Action Buttons -->
            <div class="bg-slate-900/90 border border-emerald-500/30 rounded-xl p-5 shrink-0 lg:min-w-[360px] max-w-md w-full space-y-3">
                <div class="flex items-center justify-between text-xs font-semibold">
                    <span class="text-emerald-400 font-bold uppercase tracking-wider">Your Referral Link</span>
                    <span class="font-mono text-[11px] bg-emerald-950 text-emerald-300 px-2 py-0.5 rounded border border-emerald-800">
                        {{ $partner->referral_code }}
                    </span>
                </div>
                <div>
                    <input type="text" readonly value="{{ $partner->referralUrl() }}" id="dash-ref-url" class="w-full bg-slate-950 border border-slate-700 text-xs font-mono text-emerald-300 rounded-lg px-3 py-2.5 select-all focus:outline-none focus:ring-1 focus:ring-emerald-500">
                </div>
                <div class="flex items-center gap-2 pt-1">
                    <button onclick="navigator.clipboard.writeText(document.getElementById('dash-ref-url').value); alert('Referral link copied to clipboard!');" class="flex-1 py-2 px-3 bg-emerald-600 hover:bg-emerald-500 text-white rounded-lg text-xs font-bold transition-colors flex items-center justify-center gap-1.5 shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                        Copy Link
                    </button>
                    @php
                        $waText = urlencode("Need custom software, web applications, or digital solutions for your business? Check out Startiz Labs: " . $partner->referralUrl());
                    @endphp
                    <a href="https://api.whatsapp.com/send?text={{ $waText }}" target="_blank" rel="noopener noreferrer" class="flex-1 py-2 px-3 bg-[#25D366] hover:bg-[#20ba59] text-white rounded-lg text-xs font-bold transition-colors flex items-center justify-center gap-1.5 shadow-sm">
                        <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/></svg>
                        Share on WhatsApp
                    </a>
                </div>
            </div>
        </div>
    </div>


    <!-- Core Metrics Grid -->
    <div>
        <h2 class="text-sm font-bold uppercase tracking-wider text-slate-500 mb-4">Pipeline & Conversion Metrics</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
            <!-- Total Referrals -->
            <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Total Referrals</span>
                    <div class="w-9 h-9 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    </div>
                </div>
                <div class="text-2xl font-extrabold text-slate-900">{{ number_format($totalReferrals) }}</div>
                <p class="text-xs text-slate-500 mt-1">Total attributed clients</p>
            </div>

            <!-- Active Leads -->
            <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Active Leads</span>
                    <div class="w-9 h-9 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    </div>
                </div>
                <div class="text-2xl font-extrabold text-slate-900">{{ number_format($activeLeads) }}</div>
                <p class="text-xs text-slate-500 mt-1">In progress & discussion</p>
            </div>

            <!-- Converted Clients -->
            <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Converted Clients</span>
                    <div class="w-9 h-9 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                </div>
                <div class="text-2xl font-extrabold text-slate-900">{{ number_format($convertedClients) }}</div>
                <p class="text-xs text-slate-500 mt-1">Signed contracts & deals</p>
            </div>

            <!-- Leaderboard Rank -->
            <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Partner Rank</span>
                    <div class="w-9 h-9 rounded-lg bg-purple-50 text-purple-600 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    </div>
                </div>
                <div class="text-2xl font-extrabold text-slate-900">
                    {{ $currentRank ? '#' . $currentRank : 'Unranked' }}
                </div>
                <p class="text-xs text-slate-500 mt-1">All-time leaderboard ranking</p>
            </div>
        </div>
    </div>

    <!-- Financial & Commission Breakdown -->
    <div>
        <h2 class="text-sm font-bold uppercase tracking-wider text-slate-500 mb-4">Earnings & Commission Overview</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
            <!-- Total Business Generated -->
            <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Business Generated</span>
                <div class="text-2xl font-extrabold text-slate-900 mt-2 font-mono">₹{{ number_format($totalBusinessGenerated, 2) }}</div>
                <p class="text-xs text-slate-500 mt-1">Verified deal base volume</p>
            </div>

            <!-- Pending Commission -->
            <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm">
                <span class="text-xs font-bold uppercase tracking-wider text-amber-600">Pending Review</span>
                <div class="text-2xl font-extrabold text-amber-600 mt-2 font-mono">₹{{ number_format($pendingCommission, 2) }}</div>
                <p class="text-xs text-slate-500 mt-1">Under verification by admin</p>
            </div>

            <!-- Approved Commission -->
            <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm">
                <span class="text-xs font-bold uppercase tracking-wider text-emerald-600">Approved (Payable)</span>
                <div class="text-2xl font-extrabold text-emerald-600 mt-2 font-mono">₹{{ number_format($approvedCommission, 2) }}</div>
                <p class="text-xs text-slate-500 mt-1">Scheduled for next payout</p>
            </div>

            <!-- Paid Commission -->
            <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm bg-gradient-to-br from-emerald-50/50 to-white">
                <span class="text-xs font-bold uppercase tracking-wider text-emerald-700">Paid Out</span>
                <div class="text-2xl font-extrabold text-emerald-800 mt-2 font-mono">₹{{ number_format($paidCommission, 2) }}</div>
                <p class="text-xs text-slate-500 mt-1">Total received payouts</p>
            </div>
        </div>
    </div>

    <!-- Dual Lists: Recent Referrals & Recent Commissions -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Recent Referrals -->
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h3 class="font-bold text-slate-900">Recent Referrals</h3>
                    <p class="text-xs text-slate-500">Your latest attributed leads</p>
                </div>
                <a href="{{ route('partner.referrals.index') }}" class="text-xs font-bold text-emerald-600 hover:text-emerald-700">View All &rarr;</a>
            </div>

            <div class="divide-y divide-slate-100">
                @forelse($recentReferrals as $ref)
                    <div class="p-4 flex items-center justify-between hover:bg-slate-50/75 transition-colors">
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="font-semibold text-sm text-slate-900">{{ $ref->maskedClientName() }}</span>
                                <span class="font-mono text-[10px] text-slate-400">#{{ $ref->reference_number }}</span>
                            </div>
                            <div class="text-xs text-slate-500 mt-0.5">
                                {{ $ref->maskedClientEmail() }} &bull; {{ $ref->created_at->diffForHumans() }}
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="inline-block px-2.5 py-0.5 rounded-full text-xs font-semibold
                                {{ $ref->status->value === 'converted' ? 'bg-emerald-100 text-emerald-800' : '' }}
                                {{ $ref->status->value === 'contacted' ? 'bg-blue-100 text-blue-800' : '' }}
                                {{ $ref->status->value === 'new' ? 'bg-amber-100 text-amber-800' : '' }}
                                {{ $ref->status->value === 'lost' ? 'bg-rose-100 text-rose-800' : '' }}
                            ">
                                {{ $ref->status->label() }}
                            </span>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center text-slate-500 text-sm">
                        No referrals yet. Share your link <span class="font-mono text-emerald-600">{{ $partner->referralUrl() }}</span> to get started!
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Recent Commissions -->
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h3 class="font-bold text-slate-900">Recent Commissions</h3>
                    <p class="text-xs text-slate-500">Credited earnings on deals</p>
                </div>
                <a href="{{ route('partner.earnings.index') }}" class="text-xs font-bold text-emerald-600 hover:text-emerald-700">View All &rarr;</a>
            </div>

            <div class="divide-y divide-slate-100">
                @forelse($recentCommissions as $comm)
                    <div class="p-4 flex items-center justify-between hover:bg-slate-50/75 transition-colors">
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="font-bold text-sm text-slate-900 font-mono">₹{{ number_format($comm->commission_amount, 2) }}</span>
                                <span class="text-xs text-slate-500">({{ $comm->commission_rate }}%)</span>
                            </div>
                            <div class="text-xs text-slate-500 mt-0.5">
                                Base: ₹{{ number_format($comm->base_amount, 2) }} &bull; {{ $comm->created_at->diffForHumans() }}
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="inline-block px-2.5 py-0.5 rounded-full text-xs font-semibold
                                {{ $comm->status->value === 'paid' ? 'bg-emerald-100 text-emerald-800' : '' }}
                                {{ in_array($comm->status->value, ['approved', 'payable']) ? 'bg-blue-100 text-blue-800' : '' }}
                                {{ $comm->status->value === 'pending' ? 'bg-amber-100 text-amber-800' : '' }}
                                {{ $comm->status->value === 'rejected' ? 'bg-rose-100 text-rose-800' : '' }}
                            ">
                                {{ $comm->status->label() }}
                            </span>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center text-slate-500 text-sm">
                        No commission entries yet. Commissions are created once referred clients convert and invoices/payments are verified.
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
