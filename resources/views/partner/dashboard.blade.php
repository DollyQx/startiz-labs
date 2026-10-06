@extends('layouts.partner')

@section('title', 'Partner Dashboard')

@section('content')
<div class="space-y-8">
    <!-- Welcome Header & Referral Quick Copy Banner -->
    <div class="bg-gradient-to-r from-slate-900 via-slate-800 to-emerald-950 rounded-2xl p-6 sm:p-8 text-white shadow-xl relative overflow-hidden">
        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-500/20 border border-emerald-500/30 text-emerald-300 text-xs font-semibold mb-3">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    Tier: {{ $partner->commission_rate }}% Commission Partner
                </div>
                <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight">Welcome back, {{ auth()->user()->name }}</h1>
                <p class="text-slate-300 text-sm mt-1 max-w-xl">Track your referrals, monitor lead progress, and view verified earnings in real time.</p>
            </div>

            <!-- Share Referral Link Widget -->
            <div class="bg-white/10 backdrop-blur-md border border-white/10 rounded-xl p-4 sm:min-w-[340px]">
                <div class="flex items-center justify-between text-xs text-emerald-300 font-semibold mb-2">
                    <span>YOUR UNIQUE REFERRAL LINK</span>
                    <span class="font-mono bg-emerald-900/60 text-emerald-200 px-2 py-0.5 rounded">{{ $partner->referral_code }}</span>
                </div>
                <div class="flex items-center gap-2">
                    <input type="text" readonly value="{{ $partner->referralUrl() }}" id="dash-ref-url" class="w-full bg-slate-900/80 border border-white/10 text-xs font-mono text-slate-200 rounded-lg px-3 py-2 select-all focus:outline-none">
                    <button onclick="navigator.clipboard.writeText(document.getElementById('dash-ref-url').value); alert('Referral link copied!');" class="shrink-0 px-3 py-2 bg-emerald-600 hover:bg-emerald-500 text-white rounded-lg text-xs font-bold transition-colors">
                        Copy
                    </button>
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
