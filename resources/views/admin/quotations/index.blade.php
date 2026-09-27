@extends('layouts.admin')

@section('title', 'Quotations Directory')
@section('breadcrumb', 'Quotations Directory')

@section('content')
<div class="space-y-6">
    <!-- Top Action Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Quotations Directory</h1>
            <p class="text-sm text-slate-500 mt-1">Manage client commercial proposals, price estimates, and status lifecycle.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.quotations.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold text-sm rounded-xl shadow-sm transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Create Quotation
            </a>
        </div>
    </div>

    <!-- Search & Filters Panel -->
    <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
        <form method="GET" action="{{ route('admin.quotations.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
            <!-- Search Keyword -->
            <div class="lg:col-span-2">
                <label for="search" class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1">Search</label>
                <div class="relative">
                    <input type="text" name="search" id="search" value="{{ request('search') }}" placeholder="Search by reference, client, or company..." class="w-full pl-10 pr-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                    <svg class="w-4 h-4 text-slate-400 absolute left-3.5 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
            </div>

            <!-- Status Filter -->
            <div>
                <label for="status" class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1">Status</label>
                <select name="status" id="status" class="w-full py-2 px-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                    <option value="">All Statuses</option>
                    @foreach($statuses as $st)
                        <option value="{{ $st->value }}" {{ request('status') === $st->value ? 'selected' : '' }}>
                            {{ $st->label() }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Client Filter -->
            <div>
                <label for="client_id" class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1">Client</label>
                <select name="client_id" id="client_id" class="w-full py-2 px-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                    <option value="">All Clients</option>
                    @foreach($clients as $client)
                        <option value="{{ $client->id }}" {{ (string) request('client_id') === (string) $client->id ? 'selected' : '' }}>
                            {{ $client->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 py-2 px-4 bg-slate-900 hover:bg-slate-800 text-white font-semibold text-sm rounded-xl transition-colors">
                    Filter
                </button>
                @if(request()->hasAny(['search', 'status', 'client_id', 'issue_date_from', 'issue_date_to']))
                    <a href="{{ route('admin.quotations.index') }}" class="py-2 px-3 bg-slate-100 hover:bg-slate-200 text-slate-600 font-semibold text-sm rounded-xl transition-colors">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Quotations Table -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-sm">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-xs font-bold uppercase tracking-wider text-slate-500">
                        <th class="px-6 py-3.5">Reference</th>
                        <th class="px-6 py-3.5">Client</th>
                        <th class="px-6 py-3.5">Project / Lead</th>
                        <th class="px-6 py-3.5">Issue Date</th>
                        <th class="px-6 py-3.5">Valid Until</th>
                        <th class="px-6 py-3.5">Grand Total</th>
                        <th class="px-6 py-3.5">Status</th>
                        <th class="px-6 py-3.5 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 text-slate-700">
                    @forelse($quotations as $quotation)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="px-6 py-4 font-mono font-bold text-blue-600">
                                <a href="{{ route('admin.quotations.show', $quotation->id) }}" class="hover:underline">
                                    {{ $quotation->reference_number }}
                                </a>
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-bold text-slate-900">{{ $quotation->client->name ?? 'Deleted Client' }}</div>
                                <div class="text-xs text-slate-500">{{ $quotation->client->clientProfile->company_name ?? $quotation->client->email ?? '' }}</div>
                            </td>
                            <td class="px-6 py-4 text-xs">
                                @if($quotation->project)
                                    <span class="font-semibold text-slate-800">{{ $quotation->project->title }}</span>
                                    <div class="font-mono text-slate-500">{{ $quotation->project->reference_number }}</div>
                                @elseif($quotation->lead)
                                    <span class="font-semibold text-slate-800">Lead: {{ $quotation->lead->name }}</span>
                                    <div class="font-mono text-slate-500">{{ $quotation->lead->reference_number }}</div>
                                @else
                                    <span class="text-slate-400 font-italic">Direct Quote</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 font-mono text-xs text-slate-600">
                                {{ $quotation->issue_date ? $quotation->issue_date->format('M d, Y') : 'N/A' }}
                            </td>
                            <td class="px-6 py-4 font-mono text-xs text-slate-600">
                                {{ $quotation->valid_until ? $quotation->valid_until->format('M d, Y') : 'N/A' }}
                            </td>
                            <td class="px-6 py-4 font-mono font-extrabold text-slate-900">
                                ₹{{ number_format($quotation->total, 2) }}
                            </td>
                            <td class="px-6 py-4">
                                @php
                                    $badgeStyle = match($quotation->status->value) {
                                        'draft' => 'bg-slate-100 text-slate-700 border-slate-200',
                                        'sent' => 'bg-blue-50 text-blue-700 border-blue-200',
                                        'viewed' => 'bg-indigo-50 text-indigo-700 border-indigo-200',
                                        'accepted' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                        'rejected' => 'bg-rose-50 text-rose-700 border-rose-200',
                                        'expired' => 'bg-amber-50 text-amber-700 border-amber-200',
                                        'cancelled' => 'bg-slate-100 text-slate-500 border-slate-200',
                                        default => 'bg-slate-100 text-slate-700 border-slate-200',
                                    };
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold border {{ $badgeStyle }}">
                                    {{ $quotation->status->label() }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-3">
                                    <a href="{{ route('admin.quotations.show', $quotation->id) }}" class="text-xs font-semibold text-blue-600 hover:text-blue-800">
                                        View
                                    </a>
                                    @if(!in_array($quotation->status->value, ['accepted', 'cancelled']))
                                        <a href="{{ route('admin.quotations.edit', $quotation->id) }}" class="text-xs font-semibold text-slate-600 hover:text-slate-900">
                                            Edit
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center text-slate-500 text-sm">
                                No quotations found matching your criteria.
                                <div class="mt-2">
                                    <a href="{{ route('admin.quotations.create') }}" class="text-blue-600 font-semibold hover:underline">
                                        Create a new quotation
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($quotations->hasPages())
            <div class="px-6 py-4 border-t border-slate-200 bg-slate-50">
                {{ $quotations->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
