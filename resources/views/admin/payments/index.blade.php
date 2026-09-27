@extends('layouts.admin')

@section('title', 'Payment History - Admin CRM')
@section('breadcrumb', 'Payment History')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Payment History & Ledger</h1>
            <p class="text-slate-500 text-sm mt-1">Audit all online Razorpay & manual payments, download receipts, and view payment status.</p>
        </div>
        <div>
            <a href="{{ route('admin.invoices.index') }}" class="inline-flex items-center gap-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-sm px-4 py-2.5 rounded-xl transition-all">
                <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                View Invoices
            </a>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-sm">
        <form method="GET" action="{{ route('admin.payments.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-3">
            <!-- Search -->
            <div class="lg:col-span-2">
                <label for="search" class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Search</label>
                <div class="relative">
                    <input type="text" id="search" name="search" value="{{ request('search') }}" placeholder="Pay Ref, Receipt #, Client, Invoice #, Razorpay ID..." class="w-full pl-9 pr-3 py-2 text-sm rounded-xl border border-slate-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 text-slate-800 placeholder-slate-400">
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

            <!-- Provider Filter -->
            <div>
                <label for="provider" class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Provider</label>
                <select id="provider" name="provider" class="w-full px-3 py-2 text-sm rounded-xl border border-slate-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 text-slate-800">
                    <option value="">All Providers</option>
                    @foreach($providers as $prov)
                        <option value="{{ $prov }}" {{ request('provider') === $prov ? 'selected' : '' }}>
                            {{ ucfirst($prov) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Date From -->
            <div>
                <label for="date_from" class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Date From</label>
                <input type="date" id="date_from" name="date_from" value="{{ request('date_from') }}" class="w-full px-3 py-2 text-sm rounded-xl border border-slate-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 text-slate-800">
            </div>

            <!-- Filter Actions -->
            <div class="flex items-end gap-2 lg:col-span-6 justify-end">
                <button type="submit" class="bg-slate-900 hover:bg-slate-800 text-white font-medium text-sm py-2 px-4 rounded-xl transition-all">Filter</button>
                <a href="{{ route('admin.payments.index') }}" class="bg-slate-100 hover:bg-slate-200 text-slate-600 font-medium text-sm py-2 px-4 rounded-xl transition-all">Reset</a>
            </div>
        </form>
    </div>

    <!-- Payment Directory Table Card -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/80 border-b border-slate-200/80 text-xs font-semibold text-slate-500 uppercase tracking-wider">
                        <th class="py-3.5 px-4">Payment Ref / Receipt #</th>
                        <th class="py-3.5 px-4">Client</th>
                        <th class="py-3.5 px-4">Invoice / Context</th>
                        <th class="py-3.5 px-4">Method & Provider</th>
                        <th class="py-3.5 px-4 text-right">Amount</th>
                        <th class="py-3.5 px-4">Status</th>
                        <th class="py-3.5 px-4">Date</th>
                        <th class="py-3.5 px-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    @forelse($payments as $payment)
                        <tr class="hover:bg-slate-50/60 transition-colors">
                            <td class="py-3.5 px-4">
                                <a href="{{ route('admin.payments.show', $payment->id) }}" class="font-mono font-semibold text-blue-600 hover:underline block">
                                    {{ $payment->reference_number }}
                                </a>
                                @if($payment->receipt_number)
                                    <span class="inline-flex items-center gap-1 text-xs font-mono text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded mt-1">
                                        Receipt: {{ $payment->receipt_number }}
                                    </span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4">
                                <p class="font-medium text-slate-900">{{ $payment->client->name ?? 'N/A' }}</p>
                                <p class="text-xs text-slate-500">{{ $payment->client->email ?? '' }}</p>
                            </td>
                            <td class="py-3.5 px-4 space-y-1">
                                @if($payment->invoice)
                                    <a href="{{ route('admin.invoices.show', $payment->invoice->id) }}" class="text-xs font-mono font-medium text-slate-700 hover:text-blue-600 block">
                                        Invoice: {{ $payment->invoice->reference_number }}
                                    </a>
                                @endif
                                @if($payment->project)
                                    <span class="inline-flex items-center text-xs font-medium text-slate-600 bg-slate-100 px-2 py-0.5 rounded">
                                        Proj: {{ Str::limit($payment->project->title, 18) }}
                                    </span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="capitalize text-slate-800 font-medium text-xs block">{{ $payment->payment_method ?? 'Unknown' }}</span>
                                <span class="text-xs text-slate-400 capitalize block">Provider: {{ $payment->provider ?? 'Manual' }}</span>
                                @if($payment->provider_payment_id)
                                    <span class="text-[11px] font-mono text-slate-500 truncate max-w-[140px] block" title="{{ $payment->provider_payment_id }}">
                                        ID: {{ Str::limit($payment->provider_payment_id, 14) }}
                                    </span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-right font-bold text-slate-900">
                                ₹{{ number_format((float)$payment->amount, 2) }}
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold {{ $payment->status->badgeClass() }}">
                                    {{ $payment->status->label() }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-xs text-slate-600 whitespace-nowrap">
                                {{ $payment->paid_at ? $payment->paid_at->format('M d, Y H:i') : $payment->created_at->format('M d, Y H:i') }}
                            </td>
                            <td class="py-3.5 px-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.payments.show', $payment->id) }}" class="p-1.5 text-slate-500 hover:text-blue-600 hover:bg-slate-100 rounded-lg transition-colors" title="View Detail">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    </a>
                                    @if($payment->status === \App\Enums\PaymentStatus::PAID)
                                        <a href="{{ route('admin.payments.receipt', $payment->id) }}" target="_blank" class="p-1.5 text-emerald-600 hover:text-emerald-700 hover:bg-emerald-50 rounded-lg transition-colors" title="Print/View Receipt">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-12 px-4 text-center">
                                <div class="max-w-sm mx-auto space-y-3">
                                    <div class="w-12 h-12 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mx-auto">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                    </div>
                                    <p class="text-slate-900 font-semibold text-base">No Payments Found</p>
                                    <p class="text-slate-500 text-xs">No payment records match your filters.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($payments->hasPages())
            <div class="p-4 border-t border-slate-200">
                {{ $payments->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
