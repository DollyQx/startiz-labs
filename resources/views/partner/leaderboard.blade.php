@extends('layouts.partner')

@section('title', 'Partner Leaderboard')

@section('content')
<div class="space-y-8">
    <!-- Header & Period Filter -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Partner Leaderboard</h1>
            <p class="text-sm text-slate-500">Real performance rankings based strictly on verified conversions and earnings</p>
        </div>

        <!-- Period Toggle -->
        <div class="inline-flex rounded-xl p-1 bg-slate-200/80 border border-slate-300/60 shadow-inner">
            <a href="{{ route('partner.leaderboard', ['period' => 'month']) }}" class="px-4 py-1.5 rounded-lg text-xs font-bold transition-all {{ $period === 'month' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-600 hover:text-slate-900' }}">
                This Month
            </a>
            <a href="{{ route('partner.leaderboard', ['period' => 'year']) }}" class="px-4 py-1.5 rounded-lg text-xs font-bold transition-all {{ $period === 'year' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-600 hover:text-slate-900' }}">
                This Year
            </a>
            <a href="{{ route('partner.leaderboard', ['period' => 'all']) }}" class="px-4 py-1.5 rounded-lg text-xs font-bold transition-all {{ $period === 'all' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-600 hover:text-slate-900' }}">
                All Time
            </a>
        </div>
    </div>

    <!-- Your Standing Banner -->
    <div class="bg-gradient-to-r from-emerald-600 via-emerald-700 to-teal-800 rounded-2xl p-6 text-white shadow-lg flex flex-col sm:flex-row sm:items-center justify-between gap-6">
        <div>
            <span class="text-xs font-bold uppercase tracking-wider text-emerald-200">Your Current Position</span>
            <div class="text-3xl font-black mt-1 flex items-center gap-3">
                @if($myRank)
                    <span>#{{ $myRank }} Rank</span>
                @else
                    <span>Unranked</span>
                @endif
                <span class="text-sm font-semibold text-emerald-100 bg-emerald-900/40 px-3 py-1 rounded-full">
                    {{ ucfirst($period) }} Performance
                </span>
            </div>
            <p class="text-xs text-emerald-100 mt-2">
                Conversions: <span class="font-bold text-white">{{ $myEntry['conversions'] ?? 0 }}</span> &bull; 
                Earnings: <span class="font-bold text-white font-mono">₹{{ number_format($myEntry['earnings'] ?? 0, 2) }}</span>
            </p>
        </div>

        <div class="shrink-0">
            <a href="{{ route('partner.links') }}" class="inline-block px-5 py-2.5 bg-white text-emerald-800 hover:bg-emerald-50 rounded-xl text-xs font-bold shadow-md transition-all">
                Boost Your Rank &rarr;
            </a>
        </div>
    </div>

    <!-- Leaderboard Table -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-slate-100 flex items-center justify-between">
            <h3 class="font-bold text-slate-900">Official Leaderboard Rankings</h3>
            <span class="text-xs text-slate-400">Strictly verified transaction data</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-sm">
                <thead>
                    <tr class="bg-slate-50/75 border-b border-slate-200 text-slate-500 text-xs font-bold uppercase tracking-wider">
                        <th class="py-3.5 px-6 w-20">Rank</th>
                        <th class="py-3.5 px-6">Partner</th>
                        <th class="py-3.5 px-6 text-center">Successful Conversions</th>
                        <th class="py-3.5 px-6 text-right">Verified Earnings</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    @forelse($leaderboard as $entry)
                        @php
                            $isMe = $entry['partner_id'] === $partner->id;
                            $rank = $entry['rank'];
                        @endphp
                        <tr class="{{ $isMe ? 'bg-emerald-50/70 font-semibold' : 'hover:bg-slate-50/50' }} transition-colors">
                            <td class="py-4 px-6 font-bold">
                                @if($rank === 1)
                                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-amber-100 text-amber-700 font-extrabold text-sm border border-amber-300">
                                        🥇
                                    </span>
                                @elseif($rank === 2)
                                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-slate-200 text-slate-700 font-extrabold text-sm border border-slate-300">
                                        🥈
                                    </span>
                                @elseif($rank === 3)
                                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-amber-50 text-amber-800 font-extrabold text-sm border border-amber-200">
                                        🥉
                                    </span>
                                @else
                                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-slate-100 text-slate-600 font-extrabold text-xs">
                                        #{{ $rank }}
                                    </span>
                                @endif
                            </td>
                            <td class="py-4 px-6">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-slate-200 text-slate-700 font-bold flex items-center justify-center text-xs">
                                        {{ strtoupper(substr($entry['name'], 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="font-bold text-slate-900 flex items-center gap-2">
                                            <span>{{ $entry['name'] }}</span>
                                            @if($isMe)
                                                <span class="text-[10px] bg-emerald-600 text-white px-2 py-0.5 rounded-full font-bold">YOU</span>
                                            @endif
                                        </div>
                                        @if(!empty($entry['company_name']))
                                            <div class="text-xs text-slate-400">{{ $entry['company_name'] }}</div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="py-4 px-6 text-center font-bold text-slate-800">
                                {{ number_format($entry['conversions']) }}
                            </td>
                            <td class="py-4 px-6 text-right font-mono font-extrabold text-emerald-600">
                                ₹{{ number_format($entry['earnings'], 2) }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="py-12 px-6 text-center text-slate-400">
                                No partner conversions recorded for this period yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
