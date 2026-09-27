@extends('layouts.client')

@section('title', 'Invoice #' . $invoice->reference_number)

@section('content')
<div class="space-y-6">

    <!-- Top Navigation Breadcrumb -->
    <div class="flex items-center justify-between">
        <a href="{{ route('client.invoices.index') }}" class="inline-flex items-center gap-2 text-xs font-semibold text-slate-500 hover:text-slate-800 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Back to Invoices Directory
        </a>
    </div>

    <!-- Header Workspace Banner -->
    <div class="bg-white p-6 sm:p-8 rounded-2xl border border-slate-200/90 shadow-sm space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-6 border-b border-slate-100 pb-6">
            <div class="space-y-2">
                <div class="flex items-center gap-3">
                    <span class="font-mono text-xs font-bold bg-blue-50 text-blue-700 px-3 py-1 rounded-md border border-blue-100 uppercase tracking-wide">
                        {{ $invoice->reference_number }}
                    </span>
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
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold border {{ $badgeStyles }}">
                        {{ is_object($invoice->status) ? $invoice->status->label() : ucfirst($invoice->status) }}
                    </span>
                </div>
                <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Commercial Billing Invoice</h1>
                <p class="text-xs text-slate-500">
                    Project: <span class="font-bold text-slate-700">{{ $invoice->project->title ?? 'General Engagement' }}</span>
                </p>
            </div>

            <!-- Razorpay Payment Trigger Button -->
            @if((float)$invoice->amount_due > 0 && !in_array($invoice->status->value ?? $invoice->status, ['paid', 'cancelled']))
                <div>
                    <button id="pay-now-btn" onclick="initiateRazorpayPayment()" class="px-6 py-3 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-sm shadow-md shadow-blue-500/20 transition-all flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        Pay Outstanding Balance (₹{{ number_format((float)$invoice->amount_due, 2) }})
                    </button>
                    <p class="text-[11px] text-slate-400 text-right mt-1.5 flex items-center justify-end gap-1">
                        <svg class="w-3 h-3 text-emerald-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                        256-Bit Encrypted Razorpay Checkout
                    </p>
                </div>
            @else
                <div class="text-right bg-emerald-50 border border-emerald-200 px-4 py-3 rounded-xl">
                    <span class="block text-xs font-bold text-emerald-800 uppercase tracking-wider">SETTLED & PAID</span>
                    <span class="text-xs text-emerald-600 font-medium">No outstanding balance due.</span>
                </div>
            @endif
        </div>

        <!-- Issue & Financial Snapshot Grid -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 bg-slate-50 p-4 rounded-xl border border-slate-200/70 text-xs">
            <div>
                <span class="block text-slate-400 font-semibold uppercase tracking-wider text-[10px]">Issue Date</span>
                <span class="font-bold text-slate-800 text-sm">{{ $invoice->issue_date ? $invoice->issue_date->format('M d, Y') : 'N/A' }}</span>
            </div>
            <div>
                <span class="block text-slate-400 font-semibold uppercase tracking-wider text-[10px]">Due Date</span>
                <span class="font-bold text-slate-800 text-sm">{{ $invoice->due_date ? $invoice->due_date->format('M d, Y') : 'N/A' }}</span>
            </div>
            <div>
                <span class="block text-slate-400 font-semibold uppercase tracking-wider text-[10px]">Total Amount</span>
                <span class="font-mono font-bold text-slate-900 text-sm">₹{{ number_format((float)$invoice->total, 2) }}</span>
            </div>
            <div>
                <span class="block text-slate-400 font-semibold uppercase tracking-wider text-[10px]">Balance Due</span>
                <span class="font-mono font-extrabold text-amber-600 text-sm">₹{{ number_format((float)$invoice->amount_due, 2) }}</span>
            </div>
        </div>
    </div>

    <!-- Financial Breakdown Box -->
    <div class="bg-white rounded-2xl border border-slate-200/90 shadow-sm overflow-hidden p-6 space-y-4">
        <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider">Financial Breakdown Statement</h2>

        <div class="flex justify-end pt-2">
            <div class="w-full max-w-sm bg-slate-50 p-5 rounded-xl border border-slate-200/80 space-y-2 text-sm">
                <div class="flex justify-between text-slate-600">
                    <span>Subtotal:</span>
                    <span class="font-mono font-medium">₹{{ number_format((float)$invoice->subtotal, 2) }}</span>
                </div>
                @if((float)$invoice->discount > 0)
                    <div class="flex justify-between text-emerald-600">
                        <span>Discount:</span>
                        <span class="font-mono font-medium">-₹{{ number_format((float)$invoice->discount, 2) }}</span>
                    </div>
                @endif
                @if((float)$invoice->tax > 0)
                    <div class="flex justify-between text-slate-600">
                        <span>Tax / GST:</span>
                        <span class="font-mono font-medium">+₹{{ number_format((float)$invoice->tax, 2) }}</span>
                    </div>
                @endif
                <div class="border-t border-slate-200 pt-2 flex justify-between text-slate-900 font-bold">
                    <span>Invoice Total:</span>
                    <span class="font-mono text-base">₹{{ number_format((float)$invoice->total, 2) }}</span>
                </div>
                <div class="flex justify-between text-emerald-600 font-semibold">
                    <span>Total Amount Paid:</span>
                    <span class="font-mono">-₹{{ number_format((float)$invoice->amount_paid, 2) }}</span>
                </div>
                <div class="border-t border-slate-200 pt-3 flex justify-between items-center text-base font-extrabold text-slate-900">
                    <span>Remaining Balance Due:</span>
                    <span class="font-mono text-xl text-amber-600">₹{{ number_format((float)$invoice->amount_due, 2) }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Payments Recorded for this Invoice -->
    @if($invoice->payments->count() > 0)
        <div class="bg-white rounded-2xl border border-slate-200/90 shadow-sm p-6 space-y-4">
            <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider">Payment Receipts & History</h2>

            <div class="border border-slate-200 rounded-xl overflow-hidden">
                <table class="w-full text-left text-sm text-slate-600">
                    <thead class="bg-slate-50 border-b border-slate-200 text-xs font-bold text-slate-500 uppercase tracking-wider">
                        <tr>
                            <th class="py-3 px-4">Payment Ref</th>
                            <th class="py-3 px-4">Method / Provider</th>
                            <th class="py-3 px-4">Date</th>
                            <th class="py-3 px-4 text-right">Amount</th>
                            <th class="py-3 px-4 text-center">Status</th>
                            <th class="py-3 px-4 text-right">Receipt</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium">
                        @foreach($invoice->payments as $payment)
                            <tr>
                                <td class="py-3.5 px-4 font-mono font-bold text-blue-600">{{ $payment->reference_number }}</td>
                                <td class="py-3.5 px-4 capitalize text-xs text-slate-700">{{ $payment->payment_method ?? 'Manual' }} ({{ $payment->provider ?? 'System' }})</td>
                                <td class="py-3.5 px-4 text-xs text-slate-500">{{ $payment->created_at->format('M d, Y') }}</td>
                                <td class="py-3.5 px-4 text-right font-mono font-bold text-slate-900">₹{{ number_format((float)$payment->amount, 2) }}</td>
                                <td class="py-3.5 px-4 text-center">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-bold {{ $payment->status->value === 'paid' ? 'bg-emerald-50 text-emerald-700' : 'bg-amber-50 text-amber-700' }}">
                                        {{ $payment->status->label() }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 text-right">
                                    @if($payment->status->value === 'paid')
                                        <a href="{{ route('client.payments.receipt', $payment->id) }}" target="_blank" class="inline-flex items-center gap-1 text-xs font-bold text-blue-600 hover:text-blue-800 hover:underline">
                                            Receipt PDF &rarr;
                                        </a>
                                    @else
                                        <span class="text-xs text-slate-400">N/A</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

</div>

<!-- Razorpay Checkout JS Integration -->
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script>
function initiateRazorpayPayment() {
    const btn = document.getElementById('pay-now-btn');
    if (btn) btn.disabled = true;

    fetch("{{ route('client.invoices.razorpay.order', $invoice->id) }}", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": "{{ csrf_token() }}"
        }
    })
    .then(response => response.json())
    .then(data => {
        if (!data.success) {
            alert(data.message || "Failed to initiate payment.");
            if (btn) btn.disabled = false;
            return;
        }

        const options = {
            "key": data.key_id,
            "amount": data.amount,
            "currency": data.currency,
            "name": "Startiz Labs",
            "description": "Invoice Payment #" + data.invoice_number,
            "order_id": data.order_id,
            "prefill": {
                "name": data.client_name,
                "email": data.client_email
            },
            "theme": {
                "color": "#2563eb"
            },
            "handler": function (response) {
                verifyPayment(response);
            },
            "modal": {
                "ondismiss": function() {
                    if (btn) btn.disabled = false;
                }
            }
        };

        const rzp1 = new Razorpay(options);
        rzp1.open();
    })
    .catch(error => {
        console.error("Payment Order Error:", error);
        alert("An error occurred while creating the payment order.");
        if (btn) btn.disabled = false;
    });
}

function verifyPayment(razorpayResponse) {
    fetch("{{ route('client.invoices.razorpay.verify', $invoice->id) }}", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": "{{ csrf_token() }}"
        },
        body: JSON.stringify({
            razorpay_order_id: razorpayResponse.razorpay_order_id,
            razorpay_payment_id: razorpayResponse.razorpay_payment_id,
            razorpay_signature: razorpayResponse.razorpay_signature
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            window.location.reload();
        } else {
            alert(data.message || "Payment verification failed.");
            window.location.reload();
        }
    })
    .catch(error => {
        console.error("Verification Error:", error);
        alert("An error occurred while verifying your payment signature.");
        window.location.reload();
    });
}
</script>
@endsection
