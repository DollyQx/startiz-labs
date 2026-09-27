@extends('layouts.client')

@section('title', 'Quotations Directory')

@section('content')
<div class="space-y-6">

    <!-- Header & Action Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Quotations & Proposals</h1>
            <p class="text-sm text-slate-500 mt-1">Review, track, and approve formal project price estimates.</p>
        </div>
    </div>

    <!-- Search & Filter Controls -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-sm">
        <form method="GET" action="{{ route('client.quotations.index') }}" class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <!-- Search Input -->
            <div class="sm:col-span-2 relative">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by reference or project title..." class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition-all">
                <svg class="w-4 h-4 text-slate-400 absolute left-3.5 top-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>

            <!-- Status Filter -->
            <div class="flex gap-2">
                <select name="status" onchange="this.form.submit()" class="w-full px-3 py-2.5 rounded-xl border border-slate-200 text-sm bg-white text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition-all">
                    <option value="">All Statuses</option>
                    @foreach($statuses as $st)
                        <option value="{{ $st->value }}" {{ request('status') === $st->value ? 'selected' : '' }}>
                            {{ $st->label() }}
                        </option>
                    @endforeach
                </select>

                @if(request('search') || request('status'))
                    <a href="{{ route('client.quotations.index') }}" class="px-3 py-2.5 rounded-xl border border-slate-200 text-slate-500 hover:text-slate-900 bg-slate-50 hover:bg-slate-100 text-xs font-semibold flex items-center gap-1 transition-all" title="Clear Filters">
                        Clear
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Quotations Data Table -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50 border-b border-slate-200/80 text-xs font-bold text-slate-500 uppercase tracking-wider">
                    <tr>
                        <th class="py-3.5 px-6">Reference</th>
                        <th class="py-3.5 px-6">Project / Initiative</th>
                        <th class="py-3.5 px-6">Issue Date</th>
                        <th class="py-3.5 px-6">Valid Until</th>
                        <th class="py-3.5 px-6 text-right">Total Amount</th>
                        <th class="py-3.5 px-6 text-center">Status</th>
                        <th class="py-3.5 px-6 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @forelse($quotations as $quotation)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="py-4 px-6 font-mono font-bold text-blue-600">
                                {{ $quotation->reference_number }}
                            </td>
                            <td class="py-4 px-6">
                                <span class="font-bold text-slate-900 block">{{ $quotation->project->title ?? 'General Engagement' }}</span>
                                <span class="text-xs text-slate-400">{{ $quotation->items_count ?? $quotation->items->count() }} line items</span>
                            </td>
                            <td class="py-4 px-6 text-xs text-slate-500">
                                {{ $quotation->issue_date ? $quotation->issue_date->format('M d, Y') : 'N/A' }}
                            </td>
                            <td class="py-4 px-6 text-xs text-slate-500">
                                {{ $quotation->valid_until ? $quotation->valid_until->format('M d, Y') : 'N/A' }}
                            </td>
                            <td class="py-4 px-6 text-right font-mono font-bold text-slate-900">
                                ₹{{ number_format((float) $quotation->total, 2) }}
                            </td>
                            <td class="py-4 px-6 text-center">
                                @php
                                    $badgeStyles = match($quotation->status->value ?? $quotation->status) {
                                        'accepted' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                        'sent', 'viewed' => 'bg-blue-50 text-blue-700 border-blue-200',
                                        'rejected' => 'bg-rose-50 text-rose-700 border-rose-200',
                                        'expired' => 'bg-amber-50 text-amber-700 border-amber-200',
                                        'cancelled' => 'bg-slate-100 text-slate-600 border-slate-200',
                                        default => 'bg-slate-100 text-slate-700 border-slate-200'
                                    };
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold border {{ $badgeStyles }}">
                                    {{ is_object($quotation->status) ? $quotation->status->label() : ucfirst($quotation->status) }}
                                </span>
                            </td>
                            <td class="py-4 px-6 text-right">
                                <a href="{{ route('client.quotations.show', $quotation->id) }}" class="inline-flex items-center gap-1 text-xs font-bold text-blue-600 hover:text-blue-800 hover:underline">
                                    View Proposal &rarr;
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-slate-400 text-sm">
                                <svg class="w-10 h-10 mx-auto mb-3 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                No quotations found matching your criteria.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($quotations->hasPages())
            <div class="px-6 py-4 border-t border-slate-200/80 bg-slate-50/50">
                {{ $quotations->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
