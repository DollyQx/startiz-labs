@extends('layouts.client')

@section('title', 'Payment #' . $payment->reference_number)

@section('content')
<div class="space-y-6">

    <!-- Top Navigation Breadcrumb -->
    <div class="flex items-center justify-between">
        <a href="{{ route('client.payments.index') }}" class="inline-flex items-center gap-2 text-xs font-semibold text-slate-500 hover:text-slate-800 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Back to Payments History
        </a>
    </div>

    <!-- Payment Header Card -->
    <div class="bg-white p-6 sm:p-8 rounded-2xl border border-slate-200/90 shadow-sm space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-6 border-b border-slate-100 pb-6">
            <div class="space-y-2">
                <div class="flex items-center gap-3">
                    <span class="font-mono text-xs font-bold bg-blue-50 text-blue-700 px-3 py-1 rounded-md border border-blue-100 uppercase tracking-wide">
                        {{ $payment->reference_number }}
                    </span>
                    @php
                        $badgeStyles = match($payment->status->value ?? $payment->status) {
                            'paid' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                            'pending' => 'bg-amber-50 text-amber-700 border-amber-200',
                            'failed' => 'bg-rose-50 text-rose-700 border-rose-200',
                            default => 'bg-slate-100 text-slate-700 border-slate-200'
                        };
                    @endphp
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold border {{ $badgeStyles }}">
                        {{ is_object($payment->status) ? $payment->status->label() : ucfirst($payment->status) }}
                    </span>
                </div>
                <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Transaction Detail Workspace</h1>
                <p class="text-xs text-slate-500">
                    Linked Invoice:
                    @if($payment->invoice)
                        <a href="{{ route('client.invoices.show', $payment->invoice->id) }}" class="font-mono font-bold text-blue-600 hover:underline">
                            #{{ $payment->invoice->reference_number }}
                        </a>
                    @else
                        <span class="font-bold text-slate-700">General Account Settlement</span>
                    @endif
                </p>
            </div>

            @if(($payment->status->value ?? $payment->status) === 'paid')
                <div>
                    <a href="{{ route('client.payments.receipt', $payment->id) }}" target="_blank" class="px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-md shadow-blue-500/20 transition-all flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                        Download Official Receipt PDF
                    </a>
                </div>
            @endif
        </div>

        <!-- Payment Details Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 bg-slate-50 p-6 rounded-xl border border-slate-200/70 text-xs">
            <div>
                <span class="block text-slate-400 font-semibold uppercase tracking-wider text-[10px] mb-1">Transaction Amount</span>
                <span class="font-mono text-2xl font-extrabold text-slate-900 block">₹{{ number_format((float)$payment->amount, 2) }}</span>
                <span class="text-slate-500 text-[11px]">Currency: {{ $payment->currency ?? 'INR' }}</span>
            </div>
            <div>
                <span class="block text-slate-400 font-semibold uppercase tracking-wider text-[10px] mb-1">Payment Method & Provider</span>
                <span class="font-bold text-slate-800 text-sm capitalize block">{{ $payment->payment_method ?? 'Manual' }}</span>
                <span class="text-slate-500 text-[11px] capitalize">Provider: {{ $payment->provider ?? 'System' }}</span>
            </div>
            <div>
                <span class="block text-slate-400 font-semibold uppercase tracking-wider text-[10px] mb-1">Payment Date</span>
                <span class="font-bold text-slate-800 text-sm block">{{ $payment->paid_at ? $payment->paid_at->format('F d, Y h:i A') : $payment->created_at->format('F d, Y h:i A') }}</span>
                <span class="text-slate-500 text-[11px]">Recorded by system</span>
            </div>
            @if($payment->receipt_number)
                <div>
                    <span class="block text-slate-400 font-semibold uppercase tracking-wider text-[10px] mb-1">Receipt Number</span>
                    <span class="font-mono font-bold text-slate-800 text-sm">{{ $payment->receipt_number }}</span>
                </div>
            @endif
            @if($payment->provider_payment_id)
                <div>
                    <span class="block text-slate-400 font-semibold uppercase tracking-wider text-[10px] mb-1">Razorpay Payment ID</span>
                    <span class="font-mono font-bold text-slate-800 text-xs">{{ $payment->provider_payment_id }}</span>
                </div>
            @endif
            @if($payment->provider_order_id)
                <div>
                    <span class="block text-slate-400 font-semibold uppercase tracking-wider text-[10px] mb-1">Razorpay Order ID</span>
                    <span class="font-mono text-slate-600 text-xs">{{ $payment->provider_order_id }}</span>
                </div>
            @endif
        </div>
    </div>

</div>
@endsection
