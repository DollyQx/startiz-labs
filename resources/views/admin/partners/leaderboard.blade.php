@extends('layouts.admin')

@section('title', 'Partner Program Leaderboard Audit')
@section('breadcrumb', 'Partner Leaderboard')

@section('content')
<div class="space-y-6">
    <!-- Header & Period Filter -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">Partner Leaderboard</h1>
            <p class="text-xs text-slate-500 mt-1">Verified partner performance audit based exclusively on real database transactions</p>
        </div>

        <div class="inline-flex rounded-lg p-1 bg-slate-200 border border-slate-300">
            <a href="{{ route('admin.partners.leaderboard', ['period' => 'month']) }}" class="px-3 py-1.5 rounded-md text-xs font-bold transition-all {{ $period === 'month' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-600 hover:text-slate-900' }}">
                This Month
            </a>
            <a href="{{ route('admin.partners.leaderboard', ['period' => 'year']) }}" class="px-3 py-1.5 rounded-md text-xs font-bold transition-all {{ $period === 'year' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-600 hover:text-slate-900' }}">
                This Year
            </a>
            <a href="{{ route('admin.partners.leaderboard', ['period' => 'all']) }}" class="px-3 py-1.5 rounded-md text-xs font-bold transition-all {{ $period === 'all' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-600 hover:text-slate-900' }}">
                All Time
            </a>
        </div>
    </div>

    <!-- Leaderboard Table -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-4 border-b border-slate-100 flex items-center justify-between">
            <h3 class="font-bold text-slate-900 text-sm">Rankings for {{ ucfirst($period) }}</h3>
            <span class="text-xs text-slate-400">Total ranked partners: {{ $leaderboard->count() }}</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-slate-500 font-bold uppercase tracking-wider">
                        <th class="py-3 px-4 w-16">Rank</th>
                        <th class="py-3 px-4">Partner</th>
                        <th class="py-3 px-4">Referral Code</th>
                        <th class="py-3 px-4 text-center">Successful Conversions</th>
                        <th class="py-3 px-4 text-right">Verified Earnings</th>
                        <th class="py-3 px-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    @forelse($leaderboard as $entry)
                        @php $rank = $entry['rank']; @endphp
                        <tr class="hover:bg-slate-50/75">
                            <td class="py-3 px-4 font-bold">
                                @if($rank === 1)
                                    <span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-amber-100 text-amber-800 font-extrabold text-xs border border-amber-300">🥇</span>
                                @elseif($rank === 2)
                                    <span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-slate-200 text-slate-700 font-extrabold text-xs border border-slate-300">🥈</span>
                                @elseif($rank === 3)
                                    <span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-amber-50 text-amber-800 font-extrabold text-xs border border-amber-200">🥉</span>
                                @else
                                    <span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-slate-100 text-slate-600 font-bold text-xs">#{{ $rank }}</span>
                                @endif
                            </td>
                            <td class="py-3 px-4">
                                <div class="font-bold text-slate-900">{{ $entry['name'] }}</div>
                                @if(!empty($entry['company_name']))
                                    <div class="text-[11px] text-slate-400">{{ $entry['company_name'] }}</div>
                                @endif
                            </td>
                            <td class="py-3 px-4 font-mono font-bold text-emerald-700">
                                {{ $entry['referral_code'] }}
                            </td>
                            <td class="py-3 px-4 text-center font-bold text-slate-800">
                                {{ number_format($entry['conversions']) }}
                            </td>
                            <td class="py-3 px-4 text-right font-mono font-black text-emerald-600 text-sm">
                                ₹{{ number_format($entry['earnings'], 2) }}
                            </td>
                            <td class="py-3 px-4 text-right">
                                <a href="{{ route('admin.partners.show', $entry['partner_id']) }}" class="text-blue-600 hover:underline font-bold">
                                    View Dossier &rarr;
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 px-4 text-center text-slate-400">
                                No partners with conversions recorded for this time range.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
