@extends('layouts.client')

@section('title', 'Invoices Directory')

@section('content')
<div class="space-y-6">

    <!-- Header & Action Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Billing & Commercial Invoices</h1>
            <p class="text-sm text-slate-500 mt-1">View your project invoices, payment due dates, and settlement status.</p>
        </div>
    </div>

    <!-- Search & Filter Controls -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-sm">
        <form method="GET" action="{{ route('client.invoices.index') }}" class="grid grid-cols-1 sm:grid-cols-3 gap-3">
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
                    <a href="{{ route('client.invoices.index') }}" class="px-3 py-2.5 rounded-xl border border-slate-200 text-slate-500 hover:text-slate-900 bg-slate-50 hover:bg-slate-100 text-xs font-semibold flex items-center gap-1 transition-all" title="Clear Filters">
                        Clear
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Invoices Data Table -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50 border-b border-slate-200/80 text-xs font-bold text-slate-500 uppercase tracking-wider">
                    <tr>
                        <th class="py-3.5 px-6">Reference</th>
                        <th class="py-3.5 px-6">Project / Milestone</th>
                        <th class="py-3.5 px-6">Issue Date</th>
                        <th class="py-3.5 px-6">Due Date</th>
                        <th class="py-3.5 px-6 text-right">Total</th>
                        <th class="py-3.5 px-6 text-right">Paid</th>
                        <th class="py-3.5 px-6 text-right">Balance Due</th>
                        <th class="py-3.5 px-6 text-center">Status</th>
                        <th class="py-3.5 px-6 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @forelse($invoices as $invoice)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="py-4 px-6 font-mono font-bold text-blue-600">
                                {{ $invoice->reference_number }}
                            </td>
                            <td class="py-4 px-6">
                                <span class="font-bold text-slate-900 block">{{ $invoice->project->title ?? 'General Engagement' }}</span>
                            </td>
                            <td class="py-4 px-6 text-xs text-slate-500">
                                {{ $invoice->issue_date ? $invoice->issue_date->format('M d, Y') : 'N/A' }}
                            </td>
                            <td class="py-4 px-6 text-xs text-slate-500">
                                {{ $invoice->due_date ? $invoice->due_date->format('M d, Y') : 'N/A' }}
                            </td>
                            <td class="py-4 px-6 text-right font-mono font-bold text-slate-900">
                                ₹{{ number_format((float) $invoice->total, 2) }}
                            </td>
                            <td class="py-4 px-6 text-right font-mono font-bold text-emerald-600">
                                ₹{{ number_format((float) $invoice->amount_paid, 2) }}
                            </td>
                            <td class="py-4 px-6 text-right font-mono font-bold text-amber-600">
                                ₹{{ number_format((float) $invoice->amount_due, 2) }}
                            </td>
                            <td class="py-4 px-6 text-center">
                                @php
                                    $badgeStyles = match($invoice->status->value ?? $invoice->status) {
                                        'paid' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                        'partially_paid' => 'bg-blue-50 text-blue-700 border-blue-200',
                                        'issued' => 'bg-indigo-50 text-indigo-700 border-indigo-200',
                                        'overdue' => 'bg-rose-50 text-rose-700 border-rose-200',
                                        'cancelled' => 'bg-slate-100 text-slate-600 border-slate-200',
                                        default => 'bg-slate-100 text-slate-700 border-slate-200'
                                    };
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold border {{ $badgeStyles }}">
                                    {{ is_object($invoice->status) ? $invoice->status->label() : ucfirst($invoice->status) }}
                                </span>
                            </td>
                            <td class="py-4 px-6 text-right">
                                <a href="{{ route('client.invoices.show', $invoice->id) }}" class="inline-flex items-center gap-1 text-xs font-bold text-blue-600 hover:text-blue-800 hover:underline">
                                    View Invoice &rarr;
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="py-12 text-center text-slate-400 text-sm">
                                <svg class="w-10 h-10 mx-auto mb-3 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h14a2 2 0 012 2v14a2 2 0 01-2 2z"/></svg>
                                No commercial invoices found matching your criteria.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($invoices->hasPages())
            <div class="px-6 py-4 border-t border-slate-200/80 bg-slate-50/50">
                {{ $invoices->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
