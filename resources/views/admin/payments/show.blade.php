@extends('layouts.admin')

@section('title', 'Payment Detail - ' . $payment->reference_number)
@section('breadcrumb', 'Payment Detail')

@section('content')
<div class="space-y-6 max-w-6xl mx-auto">
    <!-- Header & Action Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.payments.index') }}" class="p-2 text-slate-400 hover:text-slate-600 hover:bg-slate-100 rounded-xl transition-all">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            <div>
                <div class="flex items-center gap-3">
                    <h1 class="text-2xl font-mono font-bold text-slate-900 tracking-tight">{{ $payment->reference_number }}</h1>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold {{ $payment->status->badgeClass() }}">
                        {{ $payment->status->label() }}
                    </span>
                </div>
                <p class="text-slate-500 text-xs mt-1">Recorded on {{ $payment->created_at->format('F d, Y \a\t H:i') }}</p>
            </div>
        </div>

        <div class="flex items-center gap-3">
            @if($payment->status === \App\Enums\PaymentStatus::PAID)
                <a href="{{ route('admin.payments.receipt', $payment->id) }}" target="_blank" class="inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-sm px-4 py-2.5 rounded-xl transition-all shadow-sm shadow-emerald-500/20">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    Print / Download Receipt
                </a>
            @endif
            @if($payment->invoice)
                <a href="{{ route('admin.invoices.show', $payment->invoice->id) }}" class="inline-flex items-center gap-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-sm px-4 py-2.5 rounded-xl transition-all">
                    View Related Invoice
                </a>
            @endif
        </div>
    </div>

    <!-- Main Detail Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Payment Information & Breakdown -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Summary Card -->
            <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm space-y-6">
                <h2 class="text-base font-bold text-slate-900 border-b border-slate-100 pb-3">Payment Summary</h2>

                <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                    <div>
                        <p class="text-xs font-semibold uppercase text-slate-400">Payment Amount</p>
                        <p class="text-2xl font-bold text-slate-900 mt-1">₹{{ number_format((float)$payment->amount, 2) }}</p>
                    </div>

                    <div>
                        <p class="text-xs font-semibold uppercase text-slate-400">Currency</p>
                        <p class="text-lg font-semibold text-slate-800 mt-1">{{ $payment->currency ?? 'INR' }}</p>
                    </div>

                    <div>
                        <p class="text-xs font-semibold uppercase text-slate-400">Payment Method</p>
                        <p class="text-lg font-semibold text-slate-800 capitalize mt-1">{{ $payment->payment_method ?? 'Manual' }}</p>
                    </div>

                    <div>
                        <p class="text-xs font-semibold uppercase text-slate-400">Provider</p>
                        <p class="text-sm font-semibold text-slate-800 capitalize mt-1">{{ $payment->provider ?? 'System' }}</p>
                    </div>

                    <div>
                        <p class="text-xs font-semibold uppercase text-slate-400">Paid Date</p>
                        <p class="text-sm font-semibold text-slate-800 mt-1">
                            {{ $payment->paid_at ? $payment->paid_at->format('M d, Y H:i') : 'Pending' }}
                        </p>
                    </div>

                    <div>
                        <p class="text-xs font-semibold uppercase text-slate-400">Receipt Reference</p>
                        <p class="text-sm font-mono font-semibold text-emerald-600 mt-1">
                            {{ $payment->receipt_number ?? 'Not Generated' }}
                        </p>
                    </div>
                </div>

                @if($payment->notes)
                    <div class="bg-slate-50 p-4 rounded-xl border border-slate-200/60">
                        <p class="text-xs font-semibold text-slate-500 uppercase">Payment Notes</p>
                        <p class="text-sm text-slate-700 mt-1">{{ $payment->notes }}</p>
                    </div>
                @endif
            </div>

            <!-- Razorpay Gateway Integration Info -->
            @if($payment->provider === 'razorpay' || $payment->provider_order_id || $payment->provider_payment_id)
                <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm space-y-4">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                        <h2 class="text-base font-bold text-slate-900 flex items-center gap-2">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                            Razorpay Gateway Metadata
                        </h2>
                        <span class="text-xs font-semibold text-blue-700 bg-blue-50 px-2.5 py-1 rounded-full">Online Checkout</span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                        <div>
                            <p class="text-xs font-semibold text-slate-400 uppercase">Razorpay Order ID</p>
                            <p class="font-mono text-slate-800 font-semibold mt-0.5">{{ $payment->provider_order_id ?? 'N/A' }}</p>
                        </div>

                        <div>
                            <p class="text-xs font-semibold text-slate-400 uppercase">Razorpay Payment ID</p>
                            <p class="font-mono text-slate-800 font-semibold mt-0.5">{{ $payment->provider_payment_id ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Audit Trail Log -->
            <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm space-y-4">
                <h2 class="text-base font-bold text-slate-900 border-b border-slate-100 pb-3">Activity & Reconciliation Trail</h2>

                <div class="space-y-3">
                    @forelse($activityLogs as $log)
                        <div class="flex items-start gap-3 text-xs p-3 rounded-xl bg-slate-50 border border-slate-100">
                            <div class="w-6 h-6 rounded-full bg-blue-100 text-blue-600 font-bold flex items-center justify-center shrink-0">
                                {{ strtoupper(substr($log->causer?->name ?? 'S', 0, 1)) }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-slate-900 font-semibold">{{ $log->description }}</p>
                                <p class="text-slate-400 text-[11px] mt-0.5">by {{ $log->causer?->name ?? 'System' }} • {{ $log->created_at->diffForHumans() }}</p>
                            </div>
                        </div>
                    @empty
                        <p class="text-xs text-slate-400 italic">No specific activity logs recorded for this payment yet.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Right Side: Related Entities & Relationships -->
        <div class="space-y-6">
            <!-- Client Info Card -->
            <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm space-y-3">
                <h3 class="text-xs font-semibold uppercase text-slate-400 tracking-wider">Client Context</h3>
                @if($payment->client)
                    <div>
                        <p class="font-bold text-slate-900 text-base">{{ $payment->client->name }}</p>
                        <p class="text-xs text-slate-500">{{ $payment->client->email }}</p>
                        @if($payment->client->clientProfile?->company_name)
                            <p class="text-xs text-slate-600 font-medium mt-1">{{ $payment->client->clientProfile->company_name }}</p>
                        @endif
                    </div>
                @else
                    <p class="text-xs text-slate-400">No client linked.</p>
                @endif
            </div>

            <!-- Related Invoice Card -->
            @if($payment->invoice)
                <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm space-y-3">
                    <div class="flex items-center justify-between">
                        <h3 class="text-xs font-semibold uppercase text-slate-400 tracking-wider">Related Invoice</h3>
                        <span class="text-xs font-semibold px-2 py-0.5 rounded-full {{ $payment->invoice->status->badgeClass() }}">
                            {{ $payment->invoice->status->label() }}
                        </span>
                    </div>

                    <div>
                        <a href="{{ route('admin.invoices.show', $payment->invoice->id) }}" class="font-mono font-bold text-blue-600 hover:underline text-base block">
                            {{ $payment->invoice->reference_number }}
                        </a>
                        <div class="mt-2 space-y-1 text-xs">
                            <div class="flex justify-between">
                                <span class="text-slate-500">Invoice Total:</span>
                                <span class="font-bold text-slate-900">₹{{ number_format((float)$payment->invoice->total, 2) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-500">Total Paid:</span>
                                <span class="font-semibold text-emerald-600">₹{{ number_format((float)$payment->invoice->amount_paid, 2) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-500">Balance Due:</span>
                                <span class="font-bold text-amber-600">₹{{ number_format((float)$payment->invoice->amount_due, 2) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Related Project & Milestone -->
            @if($payment->project || $payment->quotation)
                <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm space-y-3 text-xs">
                    <h3 class="text-xs font-semibold uppercase text-slate-400 tracking-wider">Commercial Context</h3>

                    @if($payment->project)
                        <div>
                            <p class="text-slate-400 uppercase text-[10px]">Project</p>
                            <p class="font-bold text-slate-800 text-sm">{{ $payment->project->title }}</p>
                        </div>
                    @endif

                    @if($payment->quotation)
                        <div>
                            <p class="text-slate-400 uppercase text-[10px]">Quotation</p>
                            <p class="font-mono font-semibold text-purple-700">{{ $payment->quotation->reference_number }}</p>
                        </div>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
