@extends('layouts.admin')

@section('title', 'Invoice Management - Admin CRM')
@section('breadcrumb', 'Invoices & Payments')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Invoice Management</h1>
            <p class="text-slate-500 text-sm mt-1">Issue, track, and manage commercial invoices & payment collections.</p>
        </div>
        <div>
            <a href="{{ route('admin.invoices.create') }}" class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold text-sm px-4 py-2.5 rounded-xl transition-all shadow-sm shadow-blue-500/20">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Create Invoice
            </a>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-sm">
        <form method="GET" action="{{ route('admin.invoices.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-3">
            <!-- Search -->
            <div class="lg:col-span-2">
                <label for="search" class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Search</label>
                <div class="relative">
                    <input type="text" id="search" name="search" value="{{ request('search') }}" placeholder="Invoice # or client..." class="w-full pl-9 pr-3 py-2 text-sm rounded-xl border border-slate-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 text-slate-800 placeholder-slate-400">
                    <svg class="w-4 h-4 text-slate-400 absolute left-3 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
            </div>

            <!-- Status Filter -->
            <div>
                <label for="status" class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Status</label>
                <select id="status" name="status" class="w-full px-3 py-2 text-sm rounded-xl border border-slate-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 text-slate-800">
                    <option value="">All Statuses</option>
                    @foreach($statuses as $status)
                        <option value="{{ $status->value }}" {{ request('status') === $status->value ? 'selected' : '' }}>
                            {{ $status->label() }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Client Filter -->
            <div>
                <label for="client_id" class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Client</label>
                <select id="client_id" name="client_id" class="w-full px-3 py-2 text-sm rounded-xl border border-slate-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 text-slate-800">
                    <option value="">All Clients</option>
                    @foreach($clients as $client)
                        <option value="{{ $client->id }}" {{ request('client_id') == $client->id ? 'selected' : '' }}>
                            {{ $client->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Date From -->
            <div>
                <label for="date_from" class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Date From</label>
                <input type="date" id="date_from" name="date_from" value="{{ request('date_from') }}" class="w-full px-3 py-2 text-sm rounded-xl border border-slate-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 text-slate-800">
            </div>

            <!-- Filter Buttons -->
            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 bg-slate-900 hover:bg-slate-800 text-white font-medium text-sm py-2 px-3 rounded-xl transition-all">Filter</button>
                <a href="{{ route('admin.invoices.index') }}" class="bg-slate-100 hover:bg-slate-200 text-slate-600 font-medium text-sm py-2 px-3 rounded-xl transition-all">Reset</a>
            </div>
        </form>
    </div>

    <!-- Invoices Table Card -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/80 border-b border-slate-200/80 text-xs font-semibold text-slate-500 uppercase tracking-wider">
                        <th class="py-3.5 px-4">Invoice #</th>
                        <th class="py-3.5 px-4">Client</th>
                        <th class="py-3.5 px-4">Context</th>
                        <th class="py-3.5 px-4">Dates</th>
                        <th class="py-3.5 px-4 text-right">Total</th>
                        <th class="py-3.5 px-4 text-right">Paid</th>
                        <th class="py-3.5 px-4 text-right">Due</th>
                        <th class="py-3.5 px-4">Status</th>
                        <th class="py-3.5 px-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    @forelse($invoices as $invoice)
                        <tr class="hover:bg-slate-50/60 transition-colors">
                            <td class="py-3.5 px-4 font-mono font-semibold text-blue-600">
                                <a href="{{ route('admin.invoices.show', $invoice->id) }}" class="hover:underline">
                                    {{ $invoice->reference_number }}
                                </a>
                            </td>
                            <td class="py-3.5 px-4">
                                <p class="font-medium text-slate-900">{{ $invoice->client->name ?? 'N/A' }}</p>
                                @if($invoice->client?->clientProfile?->company_name)
                                    <p class="text-xs text-slate-500">{{ $invoice->client->clientProfile->company_name }}</p>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 space-y-1">
                                @if($invoice->project)
                                    <span class="inline-flex items-center gap-1 text-xs font-medium text-slate-700 bg-slate-100 px-2 py-0.5 rounded-md">
                                        <svg class="w-3 h-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/></svg>
                                        {{ Str::limit($invoice->project->title, 20) }}
                                    </span>
                                @endif
                                @if($invoice->quotation)
                                    <span class="inline-flex items-center gap-1 text-xs font-medium text-purple-700 bg-purple-50 px-2 py-0.5 rounded-md">
                                        Quote: {{ $invoice->quotation->reference_number }}
                                    </span>
                                @endif
                                @if(!$invoice->project && !$invoice->quotation)
                                    <span class="text-xs text-slate-400">Direct Invoice</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-xs">
                                <p class="text-slate-700">Issued: {{ $invoice->issue_date->format('M d, Y') }}</p>
                                <p class="text-slate-500">Due: {{ $invoice->due_date->format('M d, Y') }}</p>
                            </td>
                            <td class="py-3.5 px-4 text-right font-bold text-slate-900">
                                ₹{{ number_format((float)$invoice->total, 2) }}
                            </td>
                            <td class="py-3.5 px-4 text-right font-medium text-emerald-600">
                                ₹{{ number_format((float)$invoice->amount_paid, 2) }}
                            </td>
                            <td class="py-3.5 px-4 text-right font-bold text-amber-600">
                                ₹{{ number_format((float)$invoice->amount_due, 2) }}
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold {{ $invoice->status->badgeClass() }}">
                                    {{ $invoice->status->label() }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.invoices.show', $invoice->id) }}" class="p-1.5 text-slate-500 hover:text-blue-600 hover:bg-slate-100 rounded-lg transition-colors" title="View Details">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    </a>
                                    @if(!in_array($invoice->status->value, ['paid', 'cancelled']))
                                        <a href="{{ route('admin.invoices.edit', $invoice->id) }}" class="p-1.5 text-slate-500 hover:text-amber-600 hover:bg-slate-100 rounded-lg transition-colors" title="Edit Invoice">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="py-12 px-4 text-center">
                                <div class="max-w-sm mx-auto space-y-3">
                                    <div class="w-12 h-12 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mx-auto">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    </div>
                                    <p class="text-slate-900 font-semibold text-base">No Invoices Found</p>
                                    <p class="text-slate-500 text-xs">No commercial invoices match your criteria. Create a new invoice to get started.</p>
                                    <a href="{{ route('admin.invoices.create') }}" class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white font-medium text-xs px-3.5 py-2 rounded-xl transition-all shadow-sm">
                                        Create First Invoice
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($invoices->hasPages())
            <div class="p-4 border-t border-slate-200">
                {{ $invoices->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
