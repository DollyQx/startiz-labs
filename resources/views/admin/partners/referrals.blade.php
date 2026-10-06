@extends('layouts.admin')

@section('title', 'Partner Referrals Management')
@section('breadcrumb', 'Partner Referrals')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">Partner Referrals & Attribution</h1>
            <p class="text-xs text-slate-500 mt-1">Audit leads and client registrations attributed to partner referral codes</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.partners.commissions') }}" class="px-3 py-1.5 bg-slate-900 hover:bg-slate-800 text-white rounded-lg text-xs font-bold transition-colors">
                Commissions &rarr;
            </a>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
        <div class="bg-white rounded-xl border border-slate-200 p-4 shadow-sm">
            <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Total Referrals</span>
            <div class="text-2xl font-black text-slate-900 mt-1">{{ number_format($stats['total']) }}</div>
        </div>
        <div class="bg-white rounded-xl border border-slate-200 p-4 shadow-sm">
            <span class="text-xs font-bold uppercase tracking-wider text-amber-600">New Leads</span>
            <div class="text-2xl font-black text-amber-600 mt-1">{{ number_format($stats['new']) }}</div>
        </div>
        <div class="bg-white rounded-xl border border-slate-200 p-4 shadow-sm">
            <span class="text-xs font-bold uppercase tracking-wider text-blue-600">Contacted</span>
            <div class="text-2xl font-black text-blue-600 mt-1">{{ number_format($stats['contacted']) }}</div>
        </div>
        <div class="bg-white rounded-xl border border-slate-200 p-4 shadow-sm">
            <span class="text-xs font-bold uppercase tracking-wider text-emerald-600">Converted</span>
            <div class="text-2xl font-black text-emerald-600 mt-1">{{ number_format($stats['converted']) }}</div>
        </div>
        <div class="bg-white rounded-xl border border-slate-200 p-4 shadow-sm">
            <span class="text-xs font-bold uppercase tracking-wider text-rose-600">Lost</span>
            <div class="text-2xl font-black text-rose-600 mt-1">{{ number_format($stats['lost']) }}</div>
        </div>
    </div>

    <!-- Filters & Search -->
    <div class="bg-white rounded-xl border border-slate-200 p-4 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
        <!-- Status Tabs -->
        <div class="flex flex-wrap items-center gap-1.5">
            <a href="{{ route('admin.partners.referrals') }}" class="px-3 py-1.5 rounded-lg text-xs font-bold transition-colors {{ empty($statusFilter) ? 'bg-slate-900 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">All</a>
            <a href="{{ route('admin.partners.referrals', ['status' => 'new']) }}" class="px-3 py-1.5 rounded-lg text-xs font-bold transition-colors {{ $statusFilter === 'new' ? 'bg-amber-600 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">New ({{ $stats['new'] }})</a>
            <a href="{{ route('admin.partners.referrals', ['status' => 'contacted']) }}" class="px-3 py-1.5 rounded-lg text-xs font-bold transition-colors {{ $statusFilter === 'contacted' ? 'bg-blue-600 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">Contacted ({{ $stats['contacted'] }})</a>
            <a href="{{ route('admin.partners.referrals', ['status' => 'converted']) }}" class="px-3 py-1.5 rounded-lg text-xs font-bold transition-colors {{ $statusFilter === 'converted' ? 'bg-emerald-600 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">Converted ({{ $stats['converted'] }})</a>
            <a href="{{ route('admin.partners.referrals', ['status' => 'lost']) }}" class="px-3 py-1.5 rounded-lg text-xs font-bold transition-colors {{ $statusFilter === 'lost' ? 'bg-rose-600 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">Lost ({{ $stats['lost'] }})</a>
        </div>

        <!-- Search Bar -->
        <form method="GET" action="{{ route('admin.partners.referrals') }}" class="flex items-center gap-2">
            @if($statusFilter)
                <input type="hidden" name="status" value="{{ $statusFilter }}">
            @endif
            <input type="text" name="search" value="{{ $search }}" placeholder="Search ref, code, client email..." class="bg-slate-50 border border-slate-200 rounded-lg px-3 py-1.5 text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500 w-64">
            <button type="submit" class="px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-xs font-bold">Search</button>
            @if($search)
                <a href="{{ route('admin.partners.referrals', ['status' => $statusFilter]) }}" class="text-xs text-slate-400 hover:text-slate-600">Clear</a>
            @endif
        </form>
    </div>

    <!-- Referrals Table -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-slate-500 font-bold uppercase tracking-wider">
                        <th class="py-3 px-4">Ref Number</th>
                        <th class="py-3 px-4">Attributed Partner</th>
                        <th class="py-3 px-4">Client / Prospect Details</th>
                        <th class="py-3 px-4">Service</th>
                        <th class="py-3 px-4">Status & Transition</th>
                        <th class="py-3 px-4">Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    @forelse($referrals as $referral)
                        <tr class="hover:bg-slate-50/75">
                            <td class="py-3 px-4 font-mono font-bold text-slate-900">
                                #{{ $referral->reference_number }}
                            </td>
                            <td class="py-3 px-4">
                                <a href="{{ route('admin.partners.show', $referral->partner) }}" class="font-bold text-blue-600 hover:underline">
                                    {{ $referral->partner?->user?->name ?? 'Partner' }}
                                </a>
                                <span class="block text-[11px] font-mono text-emerald-700 font-semibold">{{ $referral->referral_code }}</span>
                            </td>
                            <td class="py-3 px-4">
                                <div class="font-bold text-slate-900">{{ $referral->displayCompanyName() }}</div>
                                <div class="text-[11px] text-slate-700 font-medium">Contact: {{ $referral->client_name ?? $referral->client?->name ?? 'Lead' }}</div>
                                <div class="text-[11px] text-slate-500 font-mono">{{ $referral->client_email ?? $referral->client?->email ?? '—' }}</div>
                                <div class="text-[11px] text-slate-400">{{ $referral->client_phone ?? $referral->client?->phone ?? '—' }}</div>
                            </td>
                            <td class="py-3 px-4 text-slate-600">
                                <div class="font-medium text-slate-900">{{ $referral->service_requested ?? 'General Inquiries' }}</div>
                                @if($referral->estimated_budget)
                                    <div class="text-[11px] text-emerald-700 font-semibold">Budget: {{ $referral->estimated_budget }}</div>
                                @endif
                                @if($referral->notes)
                                    <div class="text-[10px] text-slate-400 mt-0.5 truncate max-w-[200px]" title="{{ $referral->notes }}">{{ $referral->notes }}</div>
                                @endif
                            </td>
                            <td class="py-3 px-4">
                                <form method="POST" action="{{ route('admin.partners.referrals.status', $referral) }}" class="flex items-center gap-2">
                                    @csrf
                                    @method('PATCH')
                                    <select name="status" onchange="this.form.submit()" class="bg-slate-50 border border-slate-200 rounded px-2 py-1 text-xs font-bold text-slate-800 focus:outline-none focus:ring-1 focus:ring-blue-500
                                        {{ $referral->status->value === 'converted' ? 'text-emerald-700 font-black' : '' }}
                                        {{ $referral->status->value === 'new' ? 'text-amber-700' : '' }}
                                    ">
                                        <option value="new" {{ $referral->status->value === 'new' ? 'selected' : '' }}>New</option>
                                        <option value="contacted" {{ $referral->status->value === 'contacted' ? 'selected' : '' }}>Contacted</option>
                                        <option value="converted" {{ $referral->status->value === 'converted' ? 'selected' : '' }}>Converted</option>
                                        <option value="lost" {{ $referral->status->value === 'lost' ? 'selected' : '' }}>Lost</option>
                                    </select>
                                </form>
                            </td>
                            <td class="py-3 px-4 text-slate-500 text-[11px]">
                                {{ $referral->created_at->format('M d, Y') }}
                                <span class="block text-slate-400 text-[10px]">{{ $referral->created_at->diffForHumans() }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 px-4 text-center text-slate-400">No referral records found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($referrals->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $referrals->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
