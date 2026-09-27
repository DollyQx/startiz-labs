@extends('layouts.client')

@section('title', 'Payment History')

@section('content')
<div class="space-y-6">

    <!-- Header & Action Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Payment Ledger & Receipts</h1>
            <p class="text-sm text-slate-500 mt-1">Review complete transaction history and download official payment receipts.</p>
        </div>
    </div>

    <!-- Search & Filter Controls -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-sm">
        <form method="GET" action="{{ route('client.payments.index') }}" class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <!-- Search Input -->
            <div class="sm:col-span-2 relative">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by payment ref, receipt #, or invoice ref..." class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition-all">
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
                    <a href="{{ route('client.payments.index') }}" class="px-3 py-2.5 rounded-xl border border-slate-200 text-slate-500 hover:text-slate-900 bg-slate-50 hover:bg-slate-100 text-xs font-semibold flex items-center gap-1 transition-all" title="Clear Filters">
                        Clear
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Payments Data Table -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50 border-b border-slate-200/80 text-xs font-bold text-slate-500 uppercase tracking-wider">
                    <tr>
                        <th class="py-3.5 px-6">Payment Ref</th>
                        <th class="py-3.5 px-6">Invoice</th>
                        <th class="py-3.5 px-6">Method / Provider</th>
                        <th class="py-3.5 px-6">Date Paid</th>
                        <th class="py-3.5 px-6 text-right">Amount</th>
                        <th class="py-3.5 px-6 text-center">Status</th>
                        <th class="py-3.5 px-6 text-right">Receipt / Details</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @forelse($payments as $payment)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="py-4 px-6 font-mono font-bold text-blue-600">
                                {{ $payment->reference_number }}
                            </td>
                            <td class="py-4 px-6">
                                <span class="font-bold text-slate-900 font-mono block">
                                    {{ $payment->invoice->reference_number ?? 'Direct Payment' }}
                                </span>
                                @if($payment->project)
                                    <span class="text-xs text-slate-400 block">{{ $payment->project->title }}</span>
                                @endif
                            </td>
                            <td class="py-4 px-6 capitalize text-xs text-slate-700">
                                {{ $payment->payment_method ?? 'Manual' }}
                                <span class="text-slate-400">({{ $payment->provider ?? 'System' }})</span>
                            </td>
                            <td class="py-4 px-6 text-xs text-slate-500">
                                {{ $payment->created_at ? $payment->created_at->format('M d, Y') : 'N/A' }}
                            </td>
                            <td class="py-4 px-6 text-right font-mono font-bold text-slate-900">
                                ₹{{ number_format((float) $payment->amount, 2) }}
                            </td>
                            <td class="py-4 px-6 text-center">
                                @php
                                    $badgeStyles = match($payment->status->value ?? $payment->status) {
                                        'paid' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                        'pending' => 'bg-amber-50 text-amber-700 border-amber-200',
                                        'failed' => 'bg-rose-50 text-rose-700 border-rose-200',
                                        default => 'bg-slate-100 text-slate-700 border-slate-200'
                                    };
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold border {{ $badgeStyles }}">
                                    {{ is_object($payment->status) ? $payment->status->label() : ucfirst($payment->status) }}
                                </span>
                            </td>
                            <td class="py-4 px-6 text-right space-x-2">
                                <a href="{{ route('client.payments.show', $payment->id) }}" class="inline-flex items-center gap-1 text-xs font-bold text-slate-600 hover:text-slate-900">
                                    Details
                                </a>
                                @if(($payment->status->value ?? $payment->status) === 'paid')
                                    <span class="text-slate-300">|</span>
                                    <a href="{{ route('client.payments.receipt', $payment->id) }}" target="_blank" class="inline-flex items-center gap-1 text-xs font-bold text-blue-600 hover:text-blue-800 hover:underline">
                                        Receipt PDF &rarr;
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-slate-400 text-sm">
                                <svg class="w-10 h-10 mx-auto mb-3 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                No payment records found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($payments->hasPages())
            <div class="px-6 py-4 border-t border-slate-200/80 bg-slate-50/50">
                {{ $payments->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
