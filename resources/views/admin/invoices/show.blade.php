@extends('layouts.admin')

@section('title', 'Invoice ' . $invoice->reference_number . ' - Admin CRM')
@section('breadcrumb', 'Invoice Workspace')

@section('content')
<div class="space-y-6">
    <!-- Top Action Bar -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm">
        <div class="flex items-center gap-3">
            <span class="p-3 bg-blue-50 text-blue-600 rounded-xl font-mono text-lg font-bold border border-blue-100">
                INV
            </span>
            <div>
                <div class="flex items-center gap-2">
                    <h1 class="text-2xl font-bold text-slate-900 tracking-tight font-mono">{{ $invoice->reference_number }}</h1>
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold {{ $invoice->status->badgeClass() }}">
                        {{ $invoice->status->label() }}
                    </span>
                </div>
                <p class="text-slate-500 text-xs mt-0.5">
                    Issued to <span class="font-semibold text-slate-700">{{ $invoice->client->name ?? 'N/A' }}</span> on {{ $invoice->issue_date->format('M d, Y') }} (Due: {{ $invoice->due_date->format('M d, Y') }})
                </p>
            </div>
        </div>

        <div class="flex flex-wrap items-center gap-3">
            <a href="{{ route('admin.invoices.index') }}" class="bg-slate-100 hover:bg-slate-200 text-slate-700 font-medium text-xs px-3.5 py-2.5 rounded-xl transition-all">
                Back to Invoices
            </a>

            <!-- Status Transition Form -->
            <form action="{{ route('admin.invoices.status', $invoice->id) }}" method="POST" class="flex items-center gap-2">
                @csrf
                @method('PATCH')
                <select name="status" onchange="this.form.submit()" class="text-xs font-semibold px-3 py-2 rounded-xl border border-slate-200 bg-slate-50 hover:bg-white text-slate-800 focus:ring-2 focus:ring-blue-500/20">
                    @foreach($statuses as $status)
                        <option value="{{ $status->value }}" {{ $invoice->status->value === $status->value ? 'selected' : '' }}>
                            Status: {{ $status->label() }}
                        </option>
                    @endforeach
                </select>
            </form>

            @if(!in_array($invoice->status->value, ['paid', 'cancelled']) && (float)$invoice->amount_due > 0)
                <a href="{{ route('admin.invoices.edit', $invoice->id) }}" class="bg-amber-50 hover:bg-amber-100 border border-amber-200 text-amber-800 font-medium text-xs px-3.5 py-2.5 rounded-xl transition-all">
                    Edit Invoice
                </a>

                <!-- Pay Online via Razorpay Button -->
                <button type="button" id="pay-online-btn" onclick="initiateRazorpayPayment()" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold text-xs px-4 py-2.5 rounded-xl transition-all shadow-sm shadow-blue-500/20 flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                    Pay Online (Razorpay)
                </button>

                <!-- Record Payment Button -->
                <button type="button" onclick="document.getElementById('record-payment-modal').classList.remove('hidden')" class="bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-xs px-4 py-2.5 rounded-xl transition-all shadow-sm shadow-emerald-500/20 flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                    Record Payment
                </button>
            @endif
        </div>
    </div>

    <!-- Main Workspace Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left 2 Cols: Letterhead Invoice Paper Document -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white p-8 sm:p-10 rounded-2xl border border-slate-200/80 shadow-sm space-y-8">
                <!-- Commercial Header -->
                <div class="flex flex-col sm:flex-row justify-between items-start border-b border-slate-100 pb-8 gap-6">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="w-8 h-8 rounded-lg bg-blue-600 text-white font-black flex items-center justify-center text-sm">STZ</span>
                            <span class="text-xl font-bold tracking-tight text-slate-900">STARTIZ LABS</span>
                        </div>
                        <p class="text-xs text-slate-500 mt-2">Enterprise Software Development & Digital Transformation Services</p>
                        <p class="text-xs text-slate-500">Email: billing@gmcodelab.com | Web: gmcodelab.com</p>
                    </div>
                    <div class="text-left sm:text-right space-y-1">
                        <span class="text-xs uppercase font-bold tracking-wider text-blue-600 bg-blue-50 px-3 py-1 rounded-md">INVOICE</span>
                        <p class="text-lg font-mono font-bold text-slate-900 mt-1">{{ $invoice->reference_number }}</p>
                        <p class="text-xs text-slate-500">Date: <span class="font-medium text-slate-700">{{ $invoice->issue_date->format('F d, Y') }}</span></p>
                        <p class="text-xs text-slate-500">Due Date: <span class="font-medium text-slate-700">{{ $invoice->due_date->format('F d, Y') }}</span></p>
                    </div>
                </div>

                <!-- Client & Context Section -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 bg-slate-50/60 p-5 rounded-xl border border-slate-100 text-xs">
                    <div>
                        <h4 class="font-bold text-slate-900 uppercase tracking-wider mb-2 text-[11px] text-slate-500">Billed To</h4>
                        <p class="text-sm font-bold text-slate-900">{{ $invoice->client->name ?? 'N/A' }}</p>
                        @if($invoice->client?->clientProfile?->company_name)
                            <p class="font-semibold text-slate-700 mt-0.5">{{ $invoice->client->clientProfile->company_name }}</p>
                        @endif
                        <p class="text-slate-600 mt-1">{{ $invoice->client->email }}</p>
                        @if($invoice->client?->clientProfile?->phone)
                            <p class="text-slate-600">{{ $invoice->client->clientProfile->phone }}</p>
                        @endif
                    </div>
                    <div class="space-y-2">
                        <h4 class="font-bold text-slate-900 uppercase tracking-wider mb-2 text-[11px] text-slate-500">Reference Info</h4>
                        @if($invoice->project)
                            <p class="text-slate-600">Project: <span class="font-semibold text-slate-900">{{ $invoice->project->title }}</span></p>
                        @endif
                        @if($invoice->quotation)
                            <p class="text-slate-600">Source Quotation: <a href="{{ route('admin.quotations.show', $invoice->quotation->id) }}" class="font-mono text-blue-600 hover:underline font-semibold">{{ $invoice->quotation->reference_number }}</a></p>
                        @endif
                        @if($invoice->milestone)
                            <p class="text-slate-600">Project Milestone: <span class="font-semibold text-slate-900">{{ $invoice->milestone->title }}</span></p>
                        @endif
                    </div>
                </div>

                <!-- Financial Calculation Table Breakdown -->
                <div class="border border-slate-200/80 rounded-xl overflow-hidden">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-50 text-slate-500 uppercase tracking-wider font-semibold">
                            <tr>
                                <th class="py-3 px-4">Description</th>
                                <th class="py-3 px-4 text-right">Amount</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-slate-800">
                            <tr>
                                <td class="py-3.5 px-4 font-medium">
                                    Base Service Fees (Subtotal)
                                </td>
                                <td class="py-3.5 px-4 text-right font-mono font-semibold">
                                    ₹{{ number_format((float)$invoice->subtotal, 2) }}
                                </td>
                            </tr>
                            @if((float)$invoice->discount > 0)
                                <tr class="text-emerald-700 bg-emerald-50/40">
                                    <td class="py-3 px-4 font-medium">Commercial Discount / Reduction</td>
                                    <td class="py-3 px-4 text-right font-mono font-semibold">- ₹{{ number_format((float)$invoice->discount, 2) }}</td>
                                </tr>
                            @endif
                            @if((float)$invoice->tax > 0)
                                <tr>
                                    <td class="py-3 px-4 text-slate-600 font-medium">Applicable Taxes (GST / Statutory)</td>
                                    <td class="py-3 px-4 text-right font-mono font-semibold text-slate-700">+ ₹{{ number_format((float)$invoice->tax, 2) }}</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>

                    <!-- Total Summary Bar -->
                    <div class="bg-slate-900 text-white p-5 flex flex-col sm:flex-row items-center justify-between gap-4">
                        <div>
                            <p class="text-xs uppercase tracking-wider text-slate-400 font-semibold">Total Invoice Amount</p>
                            <p class="text-[11px] text-slate-400">All prices inclusive of applicable adjustments</p>
                        </div>
                        <div class="text-right">
                            <span class="text-2xl font-extrabold text-emerald-400 tracking-tight font-mono">
                                ₹{{ number_format((float)$invoice->total, 2) }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Notes & Terms -->
                @if($invoice->notes)
                    <div class="space-y-2 pt-2 border-t border-slate-100">
                        <h4 class="text-xs font-bold text-slate-900 uppercase tracking-wider">Notes & Payment Instructions</h4>
                        <p class="text-xs text-slate-600 leading-relaxed whitespace-pre-line bg-slate-50 p-4 rounded-xl border border-slate-100">{{ $invoice->notes }}</p>
                    </div>
                @endif
            </div>

            <!-- Payments History Table -->
            <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm space-y-4">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <h3 class="text-base font-bold text-slate-900 flex items-center gap-2">
                        <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        Recorded Payments History
                    </h3>
                    <span class="text-xs font-semibold px-2.5 py-1 bg-slate-100 rounded-full text-slate-600">
                        {{ count($invoice->payments) }} Payment(s)
                    </span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs border-collapse">
                        <thead>
                            <tr class="bg-slate-50 text-slate-500 uppercase tracking-wider font-semibold border-b border-slate-200">
                                <th class="py-2.5 px-3">Reference #</th>
                                <th class="py-2.5 px-3">Method</th>
                                <th class="py-2.5 px-3">Date</th>
                                <th class="py-2.5 px-3 text-right">Amount</th>
                                <th class="py-2.5 px-3">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-slate-800">
                            @forelse($invoice->payments as $payment)
                                <tr class="hover:bg-slate-50/60 transition-colors">
                                    <td class="py-3 px-3 font-mono font-semibold text-slate-900">
                                        {{ $payment->reference_number }}
                                    </td>
                                    <td class="py-3 px-3 capitalize font-medium text-slate-700">
                                        {{ str_replace('_', ' ', $payment->payment_method) }}
                                    </td>
                                    <td class="py-3 px-3 text-slate-600">
                                        {{ $payment->paid_at ? $payment->paid_at->format('M d, Y H:i') : $payment->created_at->format('M d, Y') }}
                                    </td>
                                    <td class="py-3 px-3 text-right font-bold text-emerald-600 font-mono">
                                        ₹{{ number_format((float)$payment->amount, 2) }}
                                    </td>
                                    <td class="py-3 px-3">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold {{ $payment->status->badgeClass() }}">
                                            {{ $payment->status->label() }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-6 text-center text-slate-500">
                                        No payments recorded yet for this invoice.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Right 1 Col: Financial Summary Card & Activity Log -->
        <div class="space-y-6">
            <!-- Financial Collection Card -->
            <div class="bg-slate-900 text-white p-6 rounded-2xl border border-slate-800 shadow-md space-y-5">
                <h3 class="text-sm font-bold uppercase tracking-wider text-slate-400 border-b border-slate-800 pb-3">Financial Collection Status</h3>

                <div class="space-y-3">
                    <div class="flex justify-between items-center text-xs">
                        <span class="text-slate-400">Total Invoice Amount:</span>
                        <span class="font-bold text-white font-mono text-sm">₹{{ number_format((float)$invoice->total, 2) }}</span>
                    </div>

                    <div class="flex justify-between items-center text-xs">
                        <span class="text-slate-400">Amount Paid:</span>
                        <span class="font-bold text-emerald-400 font-mono text-sm">₹{{ number_format((float)$invoice->amount_paid, 2) }}</span>
                    </div>

                    <div class="pt-3 border-t border-slate-800 flex justify-between items-center">
                        <span class="text-xs uppercase tracking-wider text-slate-400 font-bold">Outstanding Balance:</span>
                        <span class="text-2xl font-extrabold text-amber-400 font-mono">₹{{ number_format((float)$invoice->amount_due, 2) }}</span>
                    </div>
                </div>

                @if((float)$invoice->amount_due > 0 && !in_array($invoice->status->value, ['paid', 'cancelled']))
                    <button type="button" onclick="document.getElementById('record-payment-modal').classList.remove('hidden')" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs py-3 px-4 rounded-xl transition-all shadow-lg shadow-emerald-600/30 text-center">
                        Record Payment (₹{{ number_format((float)$invoice->amount_due, 2) }})
                    </button>
                @endif
            </div>

            <!-- Activity Audit Timeline -->
            <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm space-y-4">
                <h3 class="text-base font-bold text-slate-900 border-b border-slate-100 pb-3 flex items-center gap-2">
                    <svg class="w-5 h-5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Audit Activity Log
                </h3>

                <div class="space-y-4 max-h-96 overflow-y-auto pr-1">
                    @forelse($activityLogs as $log)
                        <div class="flex gap-3 text-xs">
                            <div class="w-7 h-7 rounded-full bg-slate-100 text-slate-600 flex items-center justify-center font-bold text-[10px] shrink-0 mt-0.5">
                                {{ strtoupper(substr($log->actor->name ?? 'S', 0, 1)) }}
                            </div>
                            <div class="space-y-0.5 flex-1">
                                <p class="font-medium text-slate-800">{{ $log->description }}</p>
                                <div class="flex items-center justify-between text-[10px] text-slate-400">
                                    <span>by {{ $log->actor->name ?? 'System' }}</span>
                                    <span>{{ $log->created_at->diffForHumans() }}</span>
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="text-xs text-slate-400 text-center py-4">No activity logged for this invoice yet.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Record Payment Modal -->
<div id="record-payment-modal" class="hidden fixed inset-0 z-50 bg-slate-950/60 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white w-full max-w-lg rounded-2xl shadow-2xl border border-slate-200 overflow-hidden">
        <div class="p-6 border-b border-slate-100 flex items-center justify-between">
            <h3 class="text-lg font-bold text-slate-900">Record Payment for {{ $invoice->reference_number }}</h3>
            <button type="button" onclick="document.getElementById('record-payment-modal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 p-1">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <form action="{{ route('admin.invoices.payments.store', $invoice->id) }}" method="POST" class="p-6 space-y-4">
            @csrf

            <div>
                <label for="amount" class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-1">Payment Amount (₹) <span class="text-red-500">*</span></label>
                <input type="number" step="0.01" min="0.01" max="{{ $invoice->amount_due }}" id="amount" name="amount" value="{{ old('amount', $invoice->amount_due) }}" required class="w-full px-3.5 py-2.5 text-sm rounded-xl border border-slate-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 text-slate-900 font-bold font-mono">
                <p class="text-[11px] text-slate-500 mt-1">Maximum payable: ₹{{ number_format((float)$invoice->amount_due, 2) }}</p>
            </div>

            <div>
                <label for="payment_method" class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-1">Payment Method <span class="text-red-500">*</span></label>
                <select id="payment_method" name="payment_method" required class="w-full px-3.5 py-2.5 text-sm rounded-xl border border-slate-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 text-slate-900">
                    <option value="bank_transfer" selected>Bank Transfer (NEFT/RTGS/IMPS)</option>
                    <option value="upi">UPI / GPay / PhonePe</option>
                    <option value="cheque">Cheque</option>
                    <option value="cash">Cash</option>
                    <option value="offline">Other Offline Method</option>
                </select>
            </div>

            <div>
                <label for="paid_at" class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-1">Payment Date & Time <span class="text-red-500">*</span></label>
                <input type="datetime-local" id="paid_at" name="paid_at" value="{{ old('paid_at', now()->format('Y-m-d\TH:i')) }}" required class="w-full px-3.5 py-2.5 text-sm rounded-xl border border-slate-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 text-slate-900">
            </div>

            <div>
                <label for="payment_notes" class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-1">Transaction Ref / Notes</label>
                <textarea id="payment_notes" name="notes" rows="2" placeholder="Bank transaction ID, cheque number, etc..." class="w-full px-3.5 py-2.5 text-sm rounded-xl border border-slate-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 text-slate-900">{{ old('notes') }}</textarea>
            </div>

            <div class="pt-4 flex items-center justify-end gap-3">
                <button type="button" onclick="document.getElementById('record-payment-modal').classList.add('hidden')" class="bg-slate-100 hover:bg-slate-200 text-slate-700 font-medium text-sm py-2 px-4 rounded-xl">Cancel</button>
                <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-sm py-2 px-5 rounded-xl shadow-md shadow-emerald-500/20">
                    Submit Payment Record
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Razorpay Checkout Integration -->
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script>
function initiateRazorpayPayment() {
    const btn = document.getElementById('pay-online-btn');
    if (btn) {
        btn.disabled = true;
        btn.innerHTML = 'Processing...';
    }

    fetch("{{ route('admin.invoices.razorpay.order', $invoice->id) }}", {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (!data.success) {
            alert(data.message || 'Could not initiate Razorpay order.');
            if (btn) {
                btn.disabled = false;
                btn.innerHTML = 'Pay Online (Razorpay)';
            }
            return;
        }

        const options = {
            key: data.key_id,
            amount: data.amount,
            currency: data.currency || "INR",
            name: "STARTIZ LABS",
            description: "Invoice #" + data.invoice_number + " Payment",
            order_id: data.order_id,
            prefill: {
                name: data.client_name,
                email: data.client_email
            },
            theme: {
                color: "#2563eb"
            },
            handler: function (response) {
                // Submit signature to server for verification
                fetch("{{ route('admin.invoices.razorpay.verify', $invoice->id) }}", {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        razorpay_order_id: response.razorpay_order_id,
                        razorpay_payment_id: response.razorpay_payment_id,
                        razorpay_signature: response.razorpay_signature
                    })
                })
                .then(res => res.json())
                .then(verifyResult => {
                    if (verifyResult.success) {
                        window.location.reload();
                    } else {
                        alert(verifyResult.message || 'Signature verification failed.');
                        if (btn) {
                            btn.disabled = false;
                            btn.innerHTML = 'Pay Online (Razorpay)';
                        }
                    }
                })
                .catch(err => {
                    alert('Error verifying payment.');
                    if (btn) {
                        btn.disabled = false;
                        btn.innerHTML = 'Pay Online (Razorpay)';
                    }
                });
            },
            modal: {
                ondismiss: function () {
                    if (btn) {
                        btn.disabled = false;
                        btn.innerHTML = 'Pay Online (Razorpay)';
                    }
                }
            }
        };

        const rzp = new Razorpay(options);
        rzp.open();
    })
    .catch(err => {
        alert('Failed to connect to server.');
        if (btn) {
            btn.disabled = false;
            btn.innerHTML = 'Pay Online (Razorpay)';
        }
    });
}
</script>
@endsection
