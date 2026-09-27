<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Receipt - {{ $payment->receipt_number }} - Startiz Labs</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            .no-print {
                display: none !important;
            }
            body {
                background-color: #ffffff !important;
                color: #000000 !important;
            }
            .receipt-card {
                border: none !important;
                box-shadow: none !important;
                padding: 0 !important;
            }
        }
    </style>
</head>
<body class="bg-slate-100 text-slate-900 font-sans min-h-screen p-4 sm:p-8 flex flex-col items-center justify-start">

    <!-- Top Action Bar for Screen view -->
    <div class="no-print w-full max-w-3xl mb-6 flex items-center justify-between">
        <a href="{{ auth()->user() && auth()->user()->isClient() ? route('client.payments.show', $payment->id) : route('admin.payments.show', $payment->id) }}" class="inline-flex items-center gap-2 text-sm text-slate-600 hover:text-slate-900 bg-white px-4 py-2 rounded-xl border border-slate-200 shadow-sm transition-all">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Back to Payment
        </a>

        <button onclick="window.print()" class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold text-sm px-5 py-2.5 rounded-xl shadow-md shadow-blue-500/20 transition-all">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
            Print / Save PDF Receipt
        </button>
    </div>

    <!-- Official Printable Receipt Card -->
    <div class="receipt-card bg-white w-full max-w-3xl p-8 sm:p-12 rounded-2xl border border-slate-200/90 shadow-lg space-y-8">
        
        <!-- Header & Company Branding -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-6 border-b border-slate-200 pb-6">
            <div class="space-y-1">
                <div class="flex items-center gap-2">
                    <span class="w-9 h-9 rounded-xl bg-blue-600 text-white font-black text-lg flex items-center justify-center tracking-tighter shadow-sm">STZ</span>
                    <span class="text-xl font-bold tracking-tight text-slate-900">STARTIZ LABS</span>
                </div>
                <p class="text-xs text-slate-500">Enterprise Software Development & Digital Transformation Services</p>
                <p class="text-xs text-slate-500">Email: billing@gmcodelab.com | Web: gmcodelab.com</p>
            </div>

            <div class="text-left sm:text-right space-y-1">
                <span class="inline-block bg-emerald-100 text-emerald-800 text-xs font-bold uppercase tracking-widest px-3 py-1 rounded-full mb-1">
                    OFFICIAL PAYMENT RECEIPT
                </span>
                <p class="text-2xl font-mono font-bold text-slate-900">{{ $payment->receipt_number }}</p>
                <p class="text-xs text-slate-500">Date: {{ $payment->paid_at ? $payment->paid_at->format('F d, Y') : now()->format('F d, Y') }}</p>
            </div>
        </div>

        <!-- Receipt Summary Metadata Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 bg-slate-50 p-6 rounded-xl border border-slate-200/70 text-sm">
            <div>
                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1">Received From (Client)</p>
                <p class="font-bold text-slate-900 text-base">{{ $payment->client->name ?? 'Client' }}</p>
                <p class="text-slate-600 text-xs mt-0.5">{{ $payment->client->email ?? '' }}</p>
                @if($payment->client?->clientProfile?->company_name)
                    <p class="text-slate-700 text-xs font-medium mt-1">{{ $payment->client->clientProfile->company_name }}</p>
                @endif
            </div>

            <div class="space-y-2">
                <div class="flex justify-between border-b border-slate-200/60 pb-1.5">
                    <span class="text-xs text-slate-500 font-semibold uppercase">Payment Reference:</span>
                    <span class="font-mono text-slate-800 font-medium">{{ $payment->reference_number }}</span>
                </div>
                <div class="flex justify-between border-b border-slate-200/60 pb-1.5">
                    <span class="text-xs text-slate-500 font-semibold uppercase">Payment Method:</span>
                    <span class="capitalize text-slate-800 font-medium">{{ $payment->payment_method ?? 'Manual' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-xs text-slate-500 font-semibold uppercase">Provider:</span>
                    <span class="capitalize text-slate-800 font-medium">{{ $payment->provider ?? 'System' }}</span>
                </div>
            </div>
        </div>

        <!-- Line Item & Breakdown -->
        <div class="space-y-4">
            <h3 class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Payment Details</h3>

            <div class="border border-slate-200 rounded-xl overflow-hidden">
                <table class="w-full text-left text-sm">
                    <thead class="bg-slate-100/80 text-xs font-semibold text-slate-600 uppercase border-b border-slate-200">
                        <tr>
                            <th class="py-3 px-4">Description / Reference</th>
                            <th class="py-3 px-4 text-right">Amount</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <tr>
                            <td class="py-4 px-4">
                                <p class="font-semibold text-slate-900">
                                    @if($payment->invoice)
                                        Payment for Invoice #{{ $payment->invoice->reference_number }}
                                    @else
                                        Commercial Service Payment
                                    @endif
                                </p>
                                @if($payment->project)
                                    <p class="text-xs text-slate-500 mt-0.5">Project: {{ $payment->project->title }}</p>
                                @endif
                                @if($payment->provider_payment_id)
                                    <p class="text-[11px] font-mono text-slate-400 mt-1">Razorpay Payment ID: {{ $payment->provider_payment_id }}</p>
                                @endif
                            </td>
                            <td class="py-4 px-4 text-right font-mono font-bold text-slate-900 text-base">
                                ₹{{ number_format((float)$payment->amount, 2) }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Total Paid Banner & Invoice Reconciliation State -->
        <div class="bg-slate-900 text-white p-6 rounded-xl flex flex-col sm:flex-row items-center justify-between gap-4 shadow-md">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Total Amount Paid Received</p>
                <p class="text-xs text-slate-300 mt-0.5">Status: <span class="text-emerald-400 font-bold uppercase">PAID & VERIFIED</span></p>
            </div>
            <div class="text-right">
                <p class="text-3xl font-mono font-bold text-emerald-400">₹{{ number_format((float)$payment->amount, 2) }}</p>
            </div>
        </div>

        <!-- Invoice Balance Snapshot (if linked) -->
        @if($payment->invoice)
            <div class="border-t border-slate-200 pt-6 space-y-2 text-xs">
                <h4 class="font-semibold text-slate-700 uppercase tracking-wider">Invoice Statement Snapshot</h4>
                <div class="grid grid-cols-3 gap-4 bg-slate-50 p-4 rounded-xl border border-slate-200/60 text-slate-700">
                    <div>
                        <span class="block text-slate-400 uppercase text-[10px]">Invoice Total</span>
                        <span class="font-bold text-slate-900 text-sm">₹{{ number_format((float)$payment->invoice->total, 2) }}</span>
                    </div>
                    <div>
                        <span class="block text-slate-400 uppercase text-[10px]">Cumulative Amount Paid</span>
                        <span class="font-bold text-emerald-600 text-sm">₹{{ number_format((float)$payment->invoice->amount_paid, 2) }}</span>
                    </div>
                    <div>
                        <span class="block text-slate-400 uppercase text-[10px]">Remaining Balance Due</span>
                        <span class="font-bold text-amber-600 text-sm">₹{{ number_format((float)$payment->invoice->amount_due, 2) }}</span>
                    </div>
                </div>
            </div>
        @endif

        <!-- Footer Sign-off -->
        <div class="border-t border-slate-200 pt-6 flex flex-col sm:flex-row items-start sm:items-end justify-between gap-4 text-xs text-slate-500">
            <div>
                <p class="font-semibold text-slate-700">Thank you for your business!</p>
                <p>This is a computer-generated receipt requiring no physical signature.</p>
            </div>
            <div class="text-left sm:text-right">
                <p class="font-mono text-[11px]">Startiz Labs Financial Systems</p>
                <p class="text-[10px] text-slate-400">Generated on {{ now()->format('Y-m-d H:i:s') }}</p>
            </div>
        </div>
    </div>
</body>
</html>
